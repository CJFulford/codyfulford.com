<?php
class userModel
{
    private $mysqli = null;

    public function __construct($databaseConnection)
    {
        $this->mysqli = $databaseConnection;
    }

    public function login($email, $password)
    {
        $success = false;

        // get the users stored password, based on their entered email
        $query = $this->mysqli->prepare('SELECT id, hash FROM users WHERE email = ?');
        if($query)
        {
            $email = strtolower(trim($email));
            $password = trim($password);
            $query->bind_param('s', $email);
            $query->execute();
            $query->store_result();
            $query->bind_result($userId, $storedHash);
            $query->fetch();
            $numberOfUsersFound = $query->num_rows;
            $query->close();
        }

        if (intval($numberOfUsersFound) === 1)
        {
            // we were able to retrieve the user from teh database and the entered password matches, user has logged in
            if (password_verify($password, $storedHash))
            {
                $success = true;
                $_SESSION['login_string'] = hash('sha512', $email.$storedHash.$_SERVER['REMOTE_ADDR']);
                $_SESSION['user_id'] = $userId;
            }
        }

        return $success;
    }

    public function logout()
    {
        $params = session_get_cookie_params();
        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        session_destroy();
        return true;
    }

    public function isUserLoggedIn()
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
    public function getUserDetails($userId)
    {
        $userDetails = [];
        $query = $this->mysqli->prepare('SELECT first_name, last_name, birth_date, sex_id, email FROM users WHERE id = ?');
        if ($query)
        {
            $query->bind_param('i', $userId);
            $query->execute();
            if (!$query->error)
            {
                $query->bind_result($userDetails['first_name'], $userDetails['last_name'], $userDetails['birth_date'], $userDetails['sex_id'], $userDetails['email']);
                $query->store_result();
                $query->fetch();
            }
            else
                echo $query->error;
            $query->close();
        }
        else
            echo $this->mysqli->error;
        return $userDetails;
    }

    public function saveUserDetails($firstName, $lastName, $email)
    {
        echo 'asdf';
        $success = false;
        $query = $this->mysqli->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?');
        if ($query)
        {
            $query->bind_param('sssi', $firstName, $lastName, $email, $_SESSION['user_id']);
            $query->execute();
            if (!$query->error)
                $success = true;
            $query->close();
        }
        return $success;
    }

    public function changeUserPassword($password0, $password1, $password2)
    {
        $success = false;

        $hash = '';
        $query = $this->mysqli->prepare('SELECT hash FROM users WHERE id = ?');
        if ($query)
        {
            $query->bind_param('i', $_SESSION['user_id']);
            $query->execute();
            $query->bind_result($storedHash);
            $query->store_reuslt();
            $query->fetch();
        }

        $query = $this->mysqli->prepare('UPDATE users SET hash = ? WHERE id = ?');
        if (password_verify($password0, $storedHash) && $password1 === $password2 && $query)
        {
            echo 'asdf';
            $hash = password_hash($password1, PASSWORD_DEFAULT);
            $query->bind_param('si', $hash, $_SESSION['user_id']);
            $query->execute();
            if (!$query->error)
                $success = true;
            $query->close();
        }
        return $success;
    }
}
