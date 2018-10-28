<?php
    include_once 'includes.php';


    $loginNotRequired = [
        "logIn"
    ];
    // if (!in_array($_REQUEST['function'], $loginNotRequired) && !$controller->isUserLoggedIn())
    //     unset($_REQUEST['function']);

    $message = '';
    if (isset($_REQUEST['function']))
    {
        switch($_REQUEST['function'])
        {
            case ('login'):
                $success = $controller->login($_POST['email'], $_POST['password']);
                if ($success === true)
                {
                    header('Location: ../exercise.php');
                    return;
                }
                else
                    $message = 'Error logging in: '.$success;
                break;
            case ('logout'):
                $success = $controller->logout();
                if ($success === true)
                {
                    header("Location: ../index.php");
                    return;
                }
                else
                    $message = 'Error logging out. Please contact system administrator.';
                break;
            default:
                break;
        }
        // send user back to the page they were on
        header("Location: ".preg_replace("/\?message=[^&]*/", '', $_SERVER['HTTP_REFERER']).($message != '' ? "?message=".$message : ''));
    }
?>
