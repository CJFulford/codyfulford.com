<?php
    include_once 'includes/includes.php';

    if (isset($_GET['redirect-page']))
        header('Location: ../'.$_GET['redirect-page']);

    if (!$controller->isUserLoggedIn())
        header('Location: ./');

    $workoutId = is_numeric($_GET['workout-id']) ? $_GET['workout-id'] : null;

    $workout = is_numeric($workoutId) ? $controller->getWorkout($workoutId) : [];

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

        <div class="container">
            <div class="clearfix">
                    <small class="float-right">
                        Created: <?=date('d/m/Y', strtotime($workout['created']));?>
                    </small>
                    <h1>
                        <?=$workout['name'];?>
                    </h1>
            </div>
            <div class="row">
                <div class="col">
                    <strong>
                        <form method="post" action="includes/api.php">
                            <input type="hidden" name="function" value="completeWorkout" />
                            <input type="hidden" name="workout-id" value="<?=$workoutId;?>" />
                            <?php $exerciseCounter = 0; ?>
                            <?php foreach ($workout['sets'] as $setIndex => $set): ?>
                                <div class="card mb-5 border-dark">
                                    <div class="card-header py-1 <?=($set['is_superset'] ? 'bg-warning' : 'bg-dark text-white');?>">
                                        <div class="row">
                                            <div class="col text-truncate my-auto">
                                                <?=$setIndex+1;?>
                                                <?=($set['is_superset'] ? ' - SUPERSET' : '');?>
                                            </div>
                                            <div class="col input-group my-auto">
                                                <input class="form-control text-center set-round-count" value="1" type="number" min="0" onkeyup="changeNumberOfSets(<?=$setIndex;?>, this);" data-current-set-rounds="0"/>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        #
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body py-1">
                                        <?php foreach ($set['exercise_id_numbers'] as $setExerciseIndex => $exerciseId): ?>
                                            <div class="row <?=($setExerciseIndex > 0 ? 'border-top border-dark' : '');?>">
                                                <div class="col my-auto">
                                                    <?=$exercises[$exerciseId]['name'];?>
                                                </div>
                                                <div class="col-12 col-md py-2">
                                                    <div class="set-round d-none">
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
                                            <?php $exerciseCounter++; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="row">
                                <div class="col"></div>
                                <div class="col-auto">
                                    <input class="btn btn-secondary" type="submit" value="Complete Workout"/>
                                </div>
                            </div>
                        </form>
                    </strong>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/exercise.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>