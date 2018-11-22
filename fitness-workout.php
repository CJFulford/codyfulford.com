<?php
    include_once 'includes/includes.php';

    if (isset($_GET['redirect-page']))
        header('Location: ../'.$_GET['redirect-page']);

    if (!$controller->isUserLoggedIn())
        header('Location: ./');

    $workoutId = is_numeric($_GET['workout-id']) ? $_GET['workout-id'] : null;

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
    $exercises = $controller->getExercises();
    $muscles = $controller->getMuscles();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'global-header.php'; ?>
        <title>Cody Fulford - Exercise</title>
    </head>
    <body>
        <?php include "navbar.php"; ?>

        <?php if (!is_null($workoutId)): ?>
            <div class="d-none" id="loaded-workout-details">
                <?=rawurlencode(json_encode($controller->getUserWorkout($workoutId)));?>
            </div>
        <?php endif; ?>

        <div class="container">
            <h1 class="text-center border-bottom border-red border-2">
                Workout
            </h1>

            <form method="post" action="/codyfulford.com/includes/api.php">
                <input type="hidden" name="function" value="completeUserWorkout" />
                <input type="hidden" name="start-time" />
                <input type="hidden" name="end-time" />
                <div class="card mb-1 border-dark">
                    <div class="card-header bg-dark text-white py-1">
                        <div class="row text-center">
                            <div class="col my-auto set-index">
                                1
                            </div>
                            <div class="col-auto my-auto superset stopToggle">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input superset-input" />
                                    <label class="custom-control-label">
                                        SUPERSET
                                    </label>
                                </div>
                            </div>
                            <div class="col form-group my-auto">
                                <input class="form-control text-center set-lap-count" value="1" type="number" min="0" onkeyup="changeNumberOfSetLaps(this)"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-1">
                        <div class="row">
                            <div class="col my-auto">
                                <div class="form-group">
                                    <select class="form-control" data-name="exercise-id">
                                        <option></option>
                                        <?php foreach ($exercises as $exerciseId => $exerciseDetails): ?>
                                            <option value="<?=$exerciseId?>">
                                                <?=$exerciseDetails['name'];?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md py-2">
                                <div class="set-lap d-none">
                                    <div class="row no-gutters">
                                        <div class="col-6 form-group my-auto">
                                            <input class="form-control text-center" type="number" step="0.01" placeholder="Weight" data-name="weight"/>
                                        </div>
                                        <div class="col-6 form-group my-auto">
                                            <input class="form-control text-center" type="number" step="0.01" placeholder="Repetitions" min="0" data-name="repetitions"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="btn btn-secondary float-right add-set-exercise-button" onclick="addExerciseToSet(this)">
                            Add Exercise To Set
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col"></div>
                    <div class="col-auto">
                        <div class="btn btn-secondary" onclick="addSetToWorkout();" id="add-workout-set-button">
                            Add Set To Workout
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col"></div>
                    <div class="col-12 col-md-auto">
                        <div class="form-group">
                            <label for="date">
                                Date
                            </label>
                            <input type="date" class="form-control" name="date" value="<?=date('Y-m-d');?>"/>
                        </div>
                        <div class="form-group">
                            <label for="start-time">
                                Time Started
                            </label>
                            <input type="time" class="form-control" name="start-time" value="<?=date('H:i');?>"/>
                        </div>
                        <div class="form-group">
                            <label for="end-time">
                                Time Finished
                            </label>
                            <input type="time" class="form-control" name="finish-time" value="<?=date('H:i', strtotime('+1 hour'));?>"/>
                        </div>
                    <div>
                </div>

                <div class="row mt-5">
                    <div class="col"></div>
                    <div class="col-auto form-group">
                        <input class="btn btn-secondary" type="submit" value="Finish Workout"/>
                    <div>
                </div>
            </form>
        </div>

        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/fitness.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>
