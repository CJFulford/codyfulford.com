<?php
class databaseModel
{
    private $mysqli = null;

    public function __construct()
    {
        require_once('/var/www/databaseCredentials.php');
        $this->mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);
        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function getDatabaseConnection()
    {
        return $this->mysqli;
    }

    public function getSexes()
    {
        $sexes = [];
        $query = $this->mysqli->prepare('SELECT id, sex_name FROM sexes');
        if ($query)
        {
            $query->execute();
            $query->bind_result($id, $name);
            $query->store_result();
            while($query->fetch())
                $sexes[$id] = $name;
            $query->close();
        }
        return $sexes;
    }
}
