<?php
    include_once 'includes.php';


    $loginNotRequired = [
        "login"
    ];
    if (!in_array($_REQUEST['function'], $loginNotRequired) && !$controller->isUserLoggedIn())
        unset($_REQUEST['function']);

    $message = '';
    $gets = '';
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
            case ('saveMuscle'):
                $muscleId = is_numeric($_POST['muscle-id']) ? $_POST['muscle-id'] : null;
                $message = !$controller->saveMuscle($_POST['muscle-name'], $muscleId) ? 'Save Failed' : '';
                $gets[] = 'exercise-tabs=muscles-tab';
                break;
            case ('saveExercise'):
                $exerciseId = is_numeric($_POST['exercise-id']) ? $_POST['exercise-id'] : null;
                $message = !$controller->saveExercise($_POST['exercise-name'], $_POST['exercise-description'], json_decode($_POST['related-muscles'], true), $exerciseId) ? 'Save Failed' : '';
                $gets[] = 'exercise-tabs=exercise-tab';
                break;
            case ('saveWorkout'):
                $messsage = !$controller->saveWorkout($_POST['workout-name'], json_decode($_POST['sets'], true), intval($_SESSION['user_id']) === 1 && isset($_POST['all-user-workout'])) ? 'Save Failed' : '';
                $gets[] = 'exercise-tabs=workouts-tab';
                break;
            case ('saveMeasurement'):
                $message = !$controller->saveMeasurement($_POST['measurement-id'], $_POST['measurement-value']) ? 'Save Failed' : '';
                $gets[] = 'exercise-tabs=measurements-tab';
                break;
            default:
                break;
        }

        // if a messsage is set, add to the the front of teh gets
        if (!empty($message))
            $gets = array_merge(['message' => $message], $gets);

        // cut the existing gets off the referrer link
        $returnPage = substr($_SERVER['HTTP_REFERER'], 0, strpos($_SERVER['HTTP_REFERER'], '.php')+4);
        // clear the old message
        $returnPage .= !empty($message) ? "message=".$message : '';
        // apply all gets to the URL
        $returnPage .= '?'.implode('&', $gets);

        // send user back to the page they were on
        header("Location: ".$returnPage);
    }
?>
