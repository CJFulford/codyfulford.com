<?php
include_once 'model/databaseModel.php';
include_once 'model/userModel.php';
include_once 'model/exerciseModel.php';

class controller
{
    private $databaseModel = null;
    private $userModel = null;
    private $exerciseModel = null;

    public function __construct()
    {
        $this->sec_session_start();
        $this->databaseModel = new databaseModel();
        $this->userModel = new userModel($this->databaseModel->getDatabaseConnection());
        $this->exerciseModel = new exerciseModel($this->databaseModel->getDatabaseConnection());
    }

    public function sec_session_start()
    {
        $session_name = 'sec_session_id'; // Set a custom session name
        //$secure = true; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id.
        $lifetime = 60 * 60 * 8;

        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        ini_set("session.gc_maxlifetime", $lifetime); // 8 hours
        ini_set("session.save_path", "/var/tmp/");
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($lifetime, $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
    }

    public function getSexes() : array
    {
        return $this->databaseModel->getSexes();
    }

    public function login(string $email, string $password) : bool
    {
        return $this->userModel->login($email, $password);
    }

    public function logout() : bool
    {
        return $this->userModel->logout();
    }

    public function isUserLoggedIn() : bool
    {
        return $this->userModel->isUserLoggedIn();
    }

    public function getUserDetails(int $userId) : array
    {
        return $this->userModel->getUserDetails($userId);
    }

    public function saveUserDetails(string $firstName, string $lastName, string $email) : bool
    {
        return $this->userModel->saveUserDetails($firstName, $lastName, $email);
    }

    public function changeUserPassword(string $password0, string $password1, string $password2) : bool
    {
        return $this->userModel->changeUserPassword($password0, $password1, $password2);
    }

    public function getMuscles() : array
    {
        return $this->exerciseModel->getMuscles();
    }

    public function saveMuscle(string $muscleName, int $muscleId = null) : bool
    {
        return $this->exerciseModel->saveMuscle($muscleName, $muscleId);
    }

    public function getExerciseDetails(int $exerciseId) :array
    {
        return $this->exerciseModel->getExerciseDetails($exerciseId);
    }

    public function getExercises() : array
    {
        return $this->exerciseModel->getExercises();
    }

    public function saveExercise(string $exerciseName, string $exerciseDescription, array $relatedMuscles, int $exerciseId = null) : bool
    {
        return $this->exerciseModel->saveExercise($exerciseName, $exerciseDescription, $relatedMuscles, $exerciseId);
    }
}
