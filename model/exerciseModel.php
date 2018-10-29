<?php
class exerciseModel
{
    private $mysqli = null;

    public function __construct(mysqli $databaseConnection)
    {
        $this->mysqli = $databaseConnection;
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