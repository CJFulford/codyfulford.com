<?php
class exerciseModel
{
    private $mysqli = null;

    public function __construct(mysqli $databaseConnection)
    {
        $this->mysqli = $databaseConnection;
    }

    public function getExerciseDetails(int $exerciseId) :array
    {
        $exercise = [];
        $query = $this->mysqli->prepare(
            'SELECT
                exercise__exercises.id,
                exercise__exercises.exercise_name,
                exercise__exercises.exercise_description,
                muscles.muscle_id
            FROM
                exercise__exercises
                LEFT JOIN
                    (SELECT
                        exercise__match_exercise_muscle.id,
                        exercise_id,
                        muscle_id
                    FROM exercise__match_exercise_muscle, exercise__muscles
                    WHERE exercise__match_exercise_muscle.muscle_id = exercise__muscles.id
                    ORDER BY exercise__muscles.muscle_name) as muscles
                ON exercise__exercises.id = muscles.exercise_id
            WHERE exercise__exercises.id = ?
            ORDER BY exercise__exercises.exercise_name ASC');
        if($query)
        {
            $query->bind_param('i', $exerciseId);
            $query->execute();
            $query->bind_result($id, $exerciseName, $exerciseDescription, $muscleId);
            $query->store_result();
            while ($query->fetch())
            {
                $exercise['name'] = $muscleName;
                $exercise['description'] = $exerciseDescription;
                if (is_null($muscleId))
                    $exercise['muscles'] = [];
                else
                    $exercise['muscles'][] = $muscleId;
            }
            $query->close();
        }
        return $exercise;
    }

    public function getExercises() : array
    {
        $exercises = [];
        $query = $this->mysqli->prepare(
            'SELECT
                exercise__exercises.id,
                exercise__exercises.exercise_name,
                exercise__exercises.exercise_description,
                muscles.muscle_id
            FROM
                exercise__exercises
                LEFT JOIN
                    (SELECT
                        exercise__match_exercise_muscle.id,
                        exercise_id,
                        muscle_id
                    FROM exercise__match_exercise_muscle, exercise__muscles
                    WHERE exercise__match_exercise_muscle.muscle_id = exercise__muscles.id
                    ORDER BY exercise__muscles.muscle_name) as muscles
                ON exercise__exercises.id = muscles.exercise_id
            ORDER BY exercise__exercises.exercise_name ASC');
        if($query)
        {
            $query->execute();
            $query->bind_result($id, $exerciseName, $exerciseDescription, $muscleId);
            $query->store_result();
            while ($query->fetch())
            {
                $exercises[$id]['name'] = $exerciseName;
                $exercises[$id]['description'] = $exerciseDescription;
                $exercises[$id]['muscles'][] = $muscleId;
            }
            $query->close();
        }
        return $exercises;
    }

    public function saveExercise(string $exerciseName, string $exerciseDescription, array $relatedMuscles, int $exerciseId = null) : bool
    {
        $success = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__exercises (id, exercise_name, exercise_description) VALUES (?,?,?) ON DUPLICATE KEY UPDATE exercise_name = ?, exercise_description = ?');
        if($query)
        {
            $query->bind_param('issss', $exerciseId, $exerciseName, $exerciseDescription, $exerciseName, $exerciseDescription);
            $query->execute();
            if (!$query->error)
            {
                if (!is_numeric($exerciseId))
                    $exerciseId = $query->insert_id;

                $currentDetails = $this->getExerciseDetails($exerciseId);
                $currentMuscles = $currentDetails['muscles'];
                // list of muscles that were not set but are now
                $musclesAdded = array_diff($relatedMuscles, $currentMuscles);
                // list of muscles that used to be set, but are not anymore
                $musclesRemoved = array_diff($currentMuscles, $relatedMuscles);

                $insertQuery = $this->mysqli->prepare('INSERT INTO exercise__match_exercise_muscle (exercise_id, muscle_id) VALUES (?,?)');
                $deleteQuery = $this->mysqli->prepare('DELETE FROM exercise__match_exercise_muscle WHERE exercise_id = ? && muscle_id = ?');

                if ($insertQuery && $deleteQuery)
                {
                    $insertSuccess = true;
                    foreach ($musclesAdded as $muscleId)
                    {
                        $insertQuery->bind_param('ii', $exerciseId, $muscleId);
                        $insertQuery->execute();
                        if ($insertQuery->error)
                            $insertSuccess = false;
                    }
                    $deleteSuccess = true;
                    foreach ($musclesRemoved as $muscleId)
                    {
                        $deleteQuery->bind_param('ii', $exerciseId, $muscleId);
                        $deleteQuery->execute();
                        if ($deleteQuery->error)
                            $deleteSuccess = false;
                    }
                    $success = $insertSuccess && $deleteSuccess;
                }
            }
            $query->close();
        }
        return $success;
    }

    public function getMuscles() : array
    {
        $muscles = [];
        $query = $this->mysqli->prepare(
            'SELECT
                exercise__muscles.id,
                exercise__muscles.muscle_name,
                exercises.exercise_id
            FROM exercise__muscles
                LEFT JOIN (SELECT
                        exercise__match_exercise_muscle.id,
                        exercise_id,
                        muscle_id
                    FROM exercise__match_exercise_muscle, exercise__exercises
                    WHERE exercise__match_exercise_muscle.exercise_id = exercise__exercises.id
                    ORDER BY exercise__exercises.exercise_name) as exercises
                ON exercise__muscles.id = exercises.muscle_id
            ORDER BY muscle_name ASC');
        if($query)
        {
            $query->execute();
            $query->bind_result($id, $muscleName, $exerciseId);
            $query->store_result();
            while ($query->fetch())
            {
                $muscles[$id]['name'] = $muscleName;
                if (is_numeric($exerciseId))
                    $muscles[$id]['exercise_id_numbers'][] = $exerciseId;
            }
            $query->close();
        }
        return $muscles;
    }

    public function saveMuscle(string $muscleName, int $muscleId = null) : bool
    {
        $successs = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__muscles (id, muscle_name) VALUES (?,?) ON DUPLICATE KEY UPDATE muscle_name = ?');
        if($query)
        {
            $query->bind_param('iss', $muscleId, $muscleName, $muscleName);
            $query->execute();
            if (!$query->error)
                $success = true;
            else
                echo $query->error;
            $query->close();
        }
        else
            echo $this->mysqli->error;
        return $success;
    }

    public function saveWorkout(string $workoutName, bool $isAllUserWorkout) : int
    {
        $workoutId = null;
        $query = $this->mysqli->prepare('INSERT INTO exercise__workout_templates (user_id, workout_name) VALUES (?,?)');
        if ($query)
        {
            $userId = !$isAllUserWorkout ? $_SESSION['user_id'] : null;
            $query->bind_param('is', $userId, $workoutName);
            $query->execute();
            $workoutId = $query->insert_id;
            $query->close();
        }
        return $workoutId;
    }

    public function saveSet(bool $isSuperSet) : int
    {
        $setId = null;
        $query = $this->mysqli->prepare('INSERT INTO exercise__sets (is_superset) VALUES (?)');
        if ($query)
        {
            $query->bind_param('i', $isSuperSet);
            $query->execute();
            $setId = $query->insert_id;
            $query->close();
        }
        return $setId;
    }

    public function createWorkoutSetLink(int $workoutId, int $setId) : bool
    {
        $success = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__match_workout_set (workout_id, set_id) VALUES (?,?)');
        if($query)
        {
            $query->bind_param('ii', $workoutId, $setId);
            $query->execute();
            if (!$query->error)
                $success = true;
            $query->close();
        }
        return $success;
    }

    public function createSetExerciseLink(int $setId, int $exerciseId) : bool
    {
        $success = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__match_set_exercise (set_id, exercise_id) VALUES (?,?)');
        if($query)
        {
            $query->bind_param('ii', $setId, $exerciseId);
            $query->execute();
            if (!$query->error)
                $success = true;
            $query->close();
        }
        return $success;
    }

    public function getWorkouts() : array
    {
        $workouts = [];
        $query = $this->mysqli->prepare(
            'SELECT
                w.id, w.user_id, w.workout_name, w.created,
                s.id, s.is_superset,
                mse.exercise_id
            FROM
                exercise__workout_templates as w,
                exercise__sets as s,
                exercise__match_workout_set as mws,
                exercise__match_set_exercise as mse
            WHERE
                # user is related to the user or the user id is null and therefore is an all user workout
                (w.user_id = ? || w.user_id IS NULL) &&
                # link the workouts and sets
                w.id = mws.workout_id && mws.set_id = s.id &&
                # link the sets and their exercises
                s.id = mse.set_id
            ORDER BY
                w.id DESC,
                s.id ASC,
                mse.exercise_id ASC
            ');
        if ($query)
        {
            $query->bind_param('i', $_SESSION['user_id']);
            $query->execute();
            if ($query->error)
                print_r($query);
            else
            {
                $query->bind_result($workoutId, $workoutUser, $workoutName, $created, $setId, $isSuperSet, $exerciseId);
                $query->store_result();
                while($query->fetch())
                {
                    $workouts[$workoutId]['name'] = $workoutName;
                    $workouts[$workoutId]['is_default_workout'] = is_null($workoutUser);
                    $workouts[$workoutId]['created'] = $created;
                    $workouts[$workoutId]['sets'][$setId]['is_superset'] = boolval($isSuperSet);
                    $workouts[$workoutId]['sets'][$setId]['exercise_id_numbers'][] = $exerciseId;
                }
            }
            $query->close();

            foreach ($workouts as $workout)
            {
                $workout['sets'] = array_values($workout['sets']);
            }
        }
        else
            echo $this->mysqli->error;

        return $workouts;
    }

    public function getMeasurements() : array
    {
        $measurements = [];
        $query = $this->mysqli->prepare('SELECT * FROM exercise__measurements');
        if ($query)
        {
            $query->execute();
            $results = $query->get_result();
            while($row = $results->fetch_assoc())
                $measurements[$row['id']] = $row;
            $query->close();
        }
        return $measurements;
    }

    public function saveMeasurement(int $measurementId, float $measurementValue) : bool
    {
        $success = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__match_user_measurement (user_id, measurement_id, measurement_value) VALUES (?,?,?)');
        if ($query)
        {
            $query->bind_param('iid', $_SESSION['user_id'], $measurementId, $measurementValue);
            $query->execute();
            if (!$query->error)
                $success = true;
            $query->close();
        }
        else
            echo $this->mysqli->error;
        return $success;
    }

    public function getUserMeasurements(int $userId) : array
    {
        $measuremeents = [];
        $query = $this->mysqli->prepare('SELECT id, measurement_id, measurement_value, created FROM exercise__match_user_measurement WHERE user_id = ? ORDER BY id DESC');
        if ($query)
        {
            $query->bind_param('i', $userId);
            $query->execute();
            $query->bind_result($id, $measurementId, $measurementValue, $created);
            $query->store_result();
            while($query->fetch())
            {
                $measuerments[$measurementId][] = [
                    'measurement_value'=> $measurementValue,
                    'created' => $created
                ];
            }
            $query->close();
        }
        return $measuerments;
    }

    public function getWorkout(int $workoutId) : array
    {
        $workout = [];

        $query = $this->mysqli->prepare(
            'SELECT
                w.workout_name, w.created,
                s.id, s.is_superset,
                mse.exercise_id
            FROM
                exercise__workout_templates as w,
                exercise__sets as s,
                exercise__match_workout_set as mws,
                exercise__match_set_exercise as mse
            WHERE
                # link the requested workout id
                w.id = ? &&
                # user is related to the user or the user id is null and therefore is an all user workout
                (w.user_id = ? || w.user_id IS NULL) &&
                # link the workouts and sets
                w.id = mws.workout_id && mws.set_id = s.id &&
                # link the sets and their exercises
                s.id = mse.set_id
            ORDER BY
                w.id DESC,
                s.id ASC,
                mse.exercise_id ASC
            ');
        if ($query)
        {
            $query->bind_param('ii', $workoutId, $_SESSION['user_id']);
            $query->execute();
            if ($query->error)
                print_r($query);
            else
            {
                $query->bind_result($workoutName, $created, $setId, $isSuperSet, $exerciseId);
                $query->store_result();
                while($query->fetch())
                {
                    $workout['name'] = $workoutName;
                    $workout['created'] = $created;
                    $workout['sets'][$setId]['is_superset'] = boolval($isSuperSet);
                    $workout['sets'][$setId]['exercise_id_numbers'][] = $exerciseId;
                }
            }
            $query->close();

            $workout['sets'] = array_values($workout['sets']);
        }
        else
            echo $this->mysqli->error;

        return $workout;
    }
}