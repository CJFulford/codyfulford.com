<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: index.php');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
    $workouts = $controller->getWorkouts();
    $exercises = $controller->getExercises();
    $muscles = $controller->getMuscles();
    $measurements = $controller->getMeasurements();
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
                        <?php foreach($workouts as $workoutId => $workout): ?>
                            <div class="card my-2">
                                <div class="card-header bg-dark text-white clearfix">
                                    <small class="float-right">
                                        <?=formatDate($workout['created']);?>
                                    </small>
                                    <?=$workout['name'];?>
                                </div>
                                <div class="card-body py-1">
                                    <div class="row">
                                        <?php $setIndex = 0; ?>
                                        <?php foreach ($workout['sets'] as $setId => $set): ?>
                                            <div class="col-12 py-1 my-0 <?=($setIndex > 0 ? 'border-top border-dark' : '');?>">
                                                <?php if ($set['is_superset']): ?>
                                                    <div class="text-center">
                                                        <small>
                                                            Superset
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                                <?php foreach ($set['exercise_id_numbers'] as $exerciseId): ?>
                                                    <p class="text-truncate mb-0">
                                                        <?=$exercises[$exerciseId]['name'];?>
                                                    </p>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php $setIndex++; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="card-footer clearfix">
                                    <a class="btn btn-secondary btn-sm float-right" href="workout/<?=$workoutId;?>">
                                        Start Workout
                                    </a>
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
