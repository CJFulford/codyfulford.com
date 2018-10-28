<?php
class exerciseModel
{
    private $mysqli = null;

    public function __construct(mysqli $databaseConnection)
    {
        $this->mysqli = $databaseConnection;
    }
}