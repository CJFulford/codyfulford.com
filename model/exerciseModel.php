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
        $query = $this->mysqli->prepare('SELECT id, muscle_name FROM exercise__muscles ORDER BY muscle_name ASC');
        if($query)
        {
            $query->execute();
            $query->bind_result($id, $muscleName);
            $query->store_result();
            while ($query->fetch())
                $muscles[$id] = $muscleName;
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
            $query->close();
        }
        return $success;
    }
}