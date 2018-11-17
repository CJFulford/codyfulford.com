<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: index.php');

    $userWorkouts = $controller->getUserWorkouts();
    $exercises = $controller->getExercises();
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
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center border-bottom border-red border-2">
                        Previous Workouts
                    </h1>
                    <div class="card-columns">
                        <?php foreach($userWorkouts as $workoutIndex => $workoutDetails): ?>
                            <div class="card my-2">
                                <div class="card-header bg-dark text-white clearfix">
                                    <small class="float-right">
                                        <?=formattime($workoutDetails['start_time']);?>
                                        -
                                        <?=formattime($workoutDetails['finish_time']);?>
                                    </small>
                                    <?=formatDate($workoutDetails['date']);?>
                                </div>
                                <div class="card-body py-1">
                                    <?php $isFirstSet = true; ?>
                                    <?php foreach ($workoutDetails['sets'] as $setIndex => $setDetails): ?>
                                        <div class="row">
                                            <div class="col-12 text-center text-uppercase <?=($setDetails['is_superset'] ? 'bg-warning' : 'bg-dark text-white');?>">
                                                <small>
                                                    <?=($setDetails['is_superset'] ? 'SUPERSET' : 'SET');?>
                                                </small>
                                            </div>
                                            <div class="col-12">
                                            <?php foreach ($setDetails['exercise_id_numbers'] as $exerciseSetIndex => $exerciseId): ?>
                                                <div class="row <?=($exerciseSetIndex > 0 ? 'border-top border-dark' : '')?>">
                                                    <div class="col my-auto">
                                                        <?=$exercises[$exerciseId]['name'];?>
                                                    </div>
                                                    <div class="col">
                                                        <?php for ($i = 0; $i < $setDetails['lap_count']; $i++): ?>
                                                            <div class="row">
                                                                <div class="col text-truncate text-right">
                                                                    <?=number_format($setDetails['exercises'][$exerciseSetIndex * $setDetails['lap_count'] + $i]['weight'], 1);?>
                                                                </div>
                                                                <div class="col text-truncate text-right">
                                                                    <?=number_format($setDetails['exercises'][$exerciseSetIndex * $setDetails['lap_count'] + $i]['repetitions']);?>
                                                                </div>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php $isFirstSet = false; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="card-footer clearfix">
                                    <button class="btn btn-secondary float-right">
                                        Load Workout
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/fitness.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>
