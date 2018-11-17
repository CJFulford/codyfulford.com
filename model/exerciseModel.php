<?php
class exerciseModel
{
    private $mysqli = null;

    public function __construct(mysqli $databaseConnection)
    {
        $this->mysqli = $databaseConnection;
    }

    public function getExerciseDetails($exerciseId)
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

    public function getExercises()
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

    public function saveExercise($exerciseName, $exerciseDescription, $relatedMuscles, $exerciseId)
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

    public function getMuscles()
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

    public function saveMuscle($muscleName, $muscleId)
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

    public function getMeasurements()
    {
        $measurements = [];
        $query = $this->mysqli->prepare('SELECT id, measurement_name FROM exercise__measurements');
        if ($query)
        {
            $query->execute();
            if (!$query->error)
            {
                $query->bind_result($id, $measurementName);
                $query->store_result();
                while($query->fetch)
                {
                    $measuerments[$id]['id'] = $id;
                    $measuerments[$id]['measurement_name'] = $measurementName;
                }
            }
            $query->close();
        }
        return $measurements;
    }

    public function saveMeasurement($measurementId, $measurementValue)
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

    public function getUserMeasurements($userId)
    {
        $measuerments = [];
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

    public function createUserWorkout($date, $startTime, $endTime)
    {
        $userWorkoutId = -1;
        $query = $this->mysqli->prepare('INSERT INTO exercise__user_workouts (user_id, date, start_time, finish_time) VALUES (?,?,?,?)');
        if ($query)
        {
            $query->bind_param('isss', $_SESSION['user_id'], $date, $startTime, $endTime);
            $query->execute();
            if (!$query->error)
                $userWorkoutId = $query->insert_id;
            else
                echo $query->error;
            $query->close();
        }
        else
            echo $this->mysqli->error;
        return $userWorkoutId;
    }

    public function createUserWorkoutSet($userWorkoutId, $isSuperset, $lapCount)
    {
        $userSetId = -1;
        $query = $this->mysqli->prepare('INSERT INTO exercise__user_workout_sets (user_workout_id, is_superset, lap_count) VALUES (?,?,?)');
        if ($query)
        {
            $query->bind_param('iii', $userWorkoutId, $isSuperset, $lapCount);
            $query->execute();
            if (!$query->error)
                $userSetId = $query->insert_id;
            else
                echo $query->error;
            $query->close();
        }
        else
            echo $this->mysqli->error;
        return $userSetId;
    }

    public function createUserWorkoutSetExercise($userWorkoutSetId, $exerciseId, $weight, $repetitions)
    {
        $success = false;
        $query = $this->mysqli->prepare('INSERT INTO exercise__user_workout_set_exercises (user_workout_set_id, exercise_id, weight, repetitions) VALUES (?,?,?,?)');
        if ($query)
        {
            $query->bind_param('iidi', $userWorkoutSetId, $exerciseId, $weight, $repetitions);
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

    public function getUserWorkouts()
    {
        $workouts = [];
        $query = $this->mysqli->prepare(
            'SELECT
                w.id,
                w.date,
                w.start_time,
                w.finish_time,
                s.id,
                s.is_superset,
                s.lap_count,
                e.id,
                e.exercise_id,
                e.weight,
                e.repetitions
            FROM
                exercise__user_workouts as w,
                exercise__user_workout_sets as s,
                exercise__user_workout_set_exercises as e
            WHERE
                # link the workout and its sets
                w.id = s.user_workout_id &&
                # link the sets and their exercises
                s.id = e.user_workout_set_id &&
                # get only the workouts for this user
                w.user_id = ?
            ORDER BY
                w.id DESC,
                s.id ASC,
                e.id ASC
            ');
            if ($query)
            {
                $query->bind_param('i', $_SESSION['user_id']);
                $query->execute();
                $query->bind_result($userWorkoutId, $date, $startTime, $finishTime, $userWorkoutSetId, $isSuperset, $lapCount, $userWorkoutSetExerciseId, $exerciseId, $weight, $repetitions);
                $query->store_result();
                while($query->fetch())
                {
                    $workouts[$userWorkoutId]['user_workout_id'] = $userWorkoutId;
                    $workouts[$userWorkoutId]['date'] = $date;
                    $workouts[$userWorkoutId]['start_time'] = $startTime;
                    $workouts[$userWorkoutId]['finish_time'] = $finishTime;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['user_workout_set_id'] = $userWorkoutSetId;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['is_superset'] = boolval($isSuperset);
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['lap_count'] = $lapCount;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercise_id_numbers'][] = $exerciseId;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['user_workout_set_exercise_id'] = $userWorkoutSetExerciseId;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['weight'] = $weight;
                    $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['repetitions'] = $repetitions;
                }
                $query->close();

                foreach ($workouts as $userWorkoutId => $workoutDetails)
                {
                    foreach ($workoutDetails['sets'] as $userWorkoutSetId => $setDetails)
                    {
                        $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercises'] = array_values($workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercises']);
                        $workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercise_id_numbers'] = array_values(array_unique($workouts[$userWorkoutId]['sets'][$userWorkoutSetId]['exercise_id_numbers']));
                    }
                    $workouts[$userWorkoutId]['sets'] = array_values($workouts[$userWorkoutId]['sets']);
                }
                $workouts = array_values($workouts);
            }
        return $workouts;
    }

    public function getUserWorkout($workoutId)
    {
        $workout = [];
        $query = $this->mysqli->prepare(
            'SELECT
                w.date,
                w.start_time,
                w.finish_time,
                s.id,
                s.is_superset,
                s.lap_count,
                e.id,
                e.exercise_id,
                e.weight,
                e.repetitions
            FROM
                exercise__user_workouts as w,
                exercise__user_workout_sets as s,
                exercise__user_workout_set_exercises as e
            WHERE
                # link the workout and its sets
                w.id = s.user_workout_id &&
                # link the sets and their exercises
                s.id = e.user_workout_set_id &&
                # get only the workouts for this user
                w.id = ?
            ORDER BY
                s.id ASC,
                e.id ASC
            ');
            if ($query)
            {
                $query->bind_param('i', $workoutId);
                $query->execute();
                $query->bind_result($date, $startTime, $finishTime, $userWorkoutSetId, $isSuperset, $lapCount, $userWorkoutSetExerciseId, $exerciseId, $weight, $repetitions);
                $query->store_result();
                while($query->fetch())
                {
                    $workout['date'] = $date;
                    $workout['start_time'] = $startTime;
                    $workout['finish_time'] = $finishTime;
                    $workout['sets'][$userWorkoutSetId]['user_workout_set_id'] = $userWorkoutSetId;
                    $workout['sets'][$userWorkoutSetId]['is_superset'] = boolval($isSuperset);
                    $workout['sets'][$userWorkoutSetId]['lap_count'] = $lapCount;
                    $workout['sets'][$userWorkoutSetId]['exercise_id_numbers'][] = $exerciseId;
                    $workout['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['user_workout_set_exercise_id'] = $userWorkoutSetExerciseId;
                    $workout['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['weight'] = $weight;
                    $workout['sets'][$userWorkoutSetId]['exercises'][$userWorkoutSetExerciseId]['repetitions'] = $repetitions;
                }
                $query->close();

                foreach ($workout['sets'] as $userWorkoutSetId => $setDetails)
                {
                    $workout['sets'][$userWorkoutSetId]['exercises'] = array_values($workout['sets'][$userWorkoutSetId]['exercises']);
                    $workout['sets'][$userWorkoutSetId]['exercise_id_numbers'] = array_values(array_unique($workout['sets'][$userWorkoutSetId]['exercise_id_numbers']));
                }
                $workout['sets'] = array_values($workout['sets']);
            }
        return $workout;
    }
}
