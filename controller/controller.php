<?php
include_once 'model/exerciseModel.php';

class controller
{
    private $exerciseModel = null;

    public function __construct()
    {
        $this->sec_session_start();
        $this->exerciseModel = new exerciseModel();
    }

    public function sec_session_start()
    {
        $session_name = 'sec_session_id'; // Set a custom session name
        //$secure = true; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id.
        $lifetime = 60 * 60 * 8;

        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        ini_set("session.gc_maxlifetime","64800"); // 18 hours
        ini_set("session.save_path", "/var/tmp/");
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($lifetime, $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
    }

    public function login(string $email, string $password) : bool
    {
        return $this->exerciseModel->login($email, $password);
    }

    public function logout() : bool
    {
        return $this->exerciseModel->logout();
    }

    public function isUserLoggedIn() : bool
    {
        return $this->exerciseModel->isUserLoggedIn();
    }
}
