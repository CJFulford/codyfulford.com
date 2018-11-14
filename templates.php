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
                    <div class="card border-dark">
                        <div class="card-header">Create Workout</div>
                        <div class="card-body">
                            <form method="post" id="workout-form" action="includes/api.php" onsubmit="return gatherWorkoutSets();">
                                <?php if($_SESSION['user_id'] === 1): ?>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="all-user-workout" name="all-user-workout">
                                        <label class="form-check-label" for="all-user-workout">All User Workout</label>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="exercise-name">Workout Name</label>
                                    <input class="form-control" id="workout-name" name="workout-name" type="text" required/>
                                </div>
                                <div id="workout-sets" class="card-columns">
                                    <div class="card border-dark">
                                        <div class="card-body">
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="superset-0">
                                                <label class="form-check-label" for="superset-0">SuperSet</label>
                                            </div>
                                            <small>
                                                <a href="#" onclick="removeSetFromWorkout(this);">
                                                    <i class="far fa-minus-square"></i>
                                                    Remove Set
                                                </a>
                                            </small>
                                            <br />
                                            <div class="form-group exercise-select">
                                                <select class="form-control">
                                                    <option selected></option>
                                                    <?php foreach ($exercises as $exerciseId => $exercise): ?>
                                                        <option value="<?=$exerciseId;?>"><?=$exercise['name'];?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <small>
                                                <a href="#" onclick="addExerciseToSet(this);">
                                                    <i class="far fa-plus-square"></i>
                                                    Add Exercise
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <small>
                                    <a href="#" onclick="addSetToWorkout();">
                                        <i class="far fa-plus-square"></i>
                                        Add Set
                                    </a>
                                </small>
                                <input type="hidden" id="sets" name="sets" />
                                <input type="hidden" name="function" value="saveWorkout"/>
                                <input class="btn btn-secondary float-right" type="submit" value="Save" />
                            </form>
                        </div>
                    </div>
                    <div class="card-columns">
                    <?php $workoutCounter = count($workouts); ?>
                    <?php foreach($workouts as $workoutId => $workout): ?>
                        <div class="card border-dark my-2">
                            <div class="card-header py-1">
                                <div class="row">
                                    <div class="col">
                                        <?=$workoutCounter;?> - <?=$workout['name'];?>
                                    </div>
                                    <div class="col-auto">
                                        <small>
                                            <?=date('d/m/Y', strtotime($workout['created']))?>
                                        </small>
                                    </div>
                                </div>
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
                                <a class="btn btn-secondary btn-sm float-right" href="workout.php?workout-id=<?=$workoutId;?>">
                                    Start Workout
                                </a>
                            </div>
                        </div>
                        <?php $workoutCounter--; ?>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/exercise.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>