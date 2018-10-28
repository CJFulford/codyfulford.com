<?php
class exerciseModel
{
    private $mysqli = null;

    public function __construct()
    {
        require_once('databaseCredentials.php');
        $this->mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);
        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function login(string $email, string $password) : bool
    {
        $success = false;

        // get the users stored password, based on their entered email
        $query = $this->mysqli->prepare('SELECT id, hash FROM users WHERE email = ?');
        if($query)
        {
            $query->bind_param('s', $email);
            $query->execute();
            $query->store_result();
            $query->bind_result($userId, $storedHash);
            $query->fetch();
            $numberOfUsersFound = $query->num_rows;
            $query->close();
        }

        if ($numberOfUsersFound == 1)
        {
            // we were able to retrieve the user from teh database and the entered password matches, user has logged in
            if (password_verify($password, $storedHash))
            {
                $success = true;
                $_SESSION['login_string'] = hash('sha512', $email.$storedHash.$_SERVER['REMOTE_ADDR']);
                $_SESSION['user_id'] = $userId;
            }
        }

        if ($success === false)
            $success = 'Incorrect username or password. Please try again.';

        return $success;
    }

    public function logout() : bool
    {
        $params = session_get_cookie_params();
        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        session_destroy();
        return true;
    }

    public function isUserLoggedIn() : bool
    {
        $isUserLoggedIn = false;

        if (isset($_SESSION['login_string']) && isset($_SESSION['user_id']))
        {
            $query = $this->mysqli->prepare('SELECT email, hash FROM users WHERE id = ?');
            if ($query)
            {
                // get the users stored has from the database to compare against the login string
                $query->bind_param('i', $_SESSION['user_id']);
                $query->execute();
                $query->store_result();
                $query->bind_result($email, $storedHash);
                $query->fetch();
                $numberOfUsersFound = $query->num_rows;
                $query->close();

                // user was found in the databas (needs to still be active), and the re-computed login string matches the stored login string
                if ($numberOfUsersFound == 1 && $_SESSION['login_string'] === hash('sha512', $email.$storedHash.$_SERVER['REMOTE_ADDR']))
                    $isUserLoggedIn = true;
            }
        }
        return $isUserLoggedIn;
    }
}