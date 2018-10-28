<?php
    include_once 'includes.php';


    $loginNotRequired = [
        "login"
    ];
    if (!in_array($_REQUEST['function'], $loginNotRequired) && !$controller->isUserLoggedIn())
        unset($_REQUEST['function']);

    $message = '';
    if (isset($_REQUEST['function']))
    {
        switch($_REQUEST['function'])
        {
            case ('login'):
                if ($controller->login($_POST['email'], $_POST['password']) === true)
                {
                    header('Location: ../user-settings.php');
                    return;
                }
                else
                    $message = 'Error logging in: '.$success;
                break;
            case ('logout'):
                if ($controller->logout() === true)
                {
                    header("Location: ../index.php");
                    return;
                }
                else
                    $message = 'Error logging out. Please contact system administrator.';
                break;
            case ('saveUserDetails'):
                $message = !$controller->saveUserDetails($_POST['first-name'], $_POST['last-name'], $_POST['email']) ? 'Save Failed' : '';
                break;
            case ('changeUserPassword'):
                $message = !$controller->changeUserPassword($_POST['password-0'], $_POST['password-1'], $_POST['password-2']) ? 'Save Failed' : '';
                break;
            default:
                break;
        }
        // send user back to the page they were on
        header("Location: ".preg_replace("/\?message=[^&]*/", '', $_SERVER['HTTP_REFERER']).($message != '' ? "?message=".$message : ''));
    }
?>
