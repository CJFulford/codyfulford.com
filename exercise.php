<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: index.php');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
    $workouts = $controller->getWorkouts();
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
            <div class="row">
                <div class="col-12">
                    <h2>
                        <?=$userDetails['first_name']?> <?=$userDetails['last_name'];?>
                    </h2>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col"></div>
                        <div class="col-auto">
                            <ul class="nav nav-tabs" id="exercise-tables" role="tablist">
                                <?php
                                $exersizeTab = 0;
                                switch($_GET['exercise-tabs'])
                                {
                                    case ('workouts-tab'):
                                        $exersizeTab = 0;
                                        break;
                                    case ('exercise-tab'):
                                        $exersizeTab = 1;
                                        break;
                                    case ('muscles-tab'):
                                        $exersizeTab = 2;
                                        break;
                                }
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?=($exersizeTab === 0 ? 'active' : '');?>" id="workouts-tab" data-toggle="tab" href="#workouts" role="tab" aria-controls="workouts" aria-selected="true">
                                        Workouts
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?=($exersizeTab === 1 ? 'active' : '');?>"id="exercises-tab" data-toggle="tab" href="#exercises" role="tab" aria-controls="exercises" aria-selected="false">
                                        Exercises
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?=($exersizeTab === 2 ? 'active' : '');?>" id="muscles-tab" data-toggle="tab" href="#muscles" role="tab" aria-controls="muscles" aria-selected="false">
                                        Muscles
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <!--Workouts-->
                        <div class="tab-pane fade <?=($exersizeTab === 0 ? 'show active' : '');?>" id="workouts" role="tabpanel" aria-labelledby="workouts-tab">
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
                                            <input class="form-control" id="workout-name" name="workout-name" type="text" />
                                        </div>
                                        <div id="workout-sets" class="card-columns">
                                            <div class="card border-dark">
                                                <div class="card-body">
                                                    <div class="form-group form-check">
                                                        <input type="checkbox" class="form-check-input" id="superset">
                                                        <label class="form-check-label">SuperSet</label>
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
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col">
                                                <?=$workoutCounter;?> - <?=$workout['name'];?>
                                            </div>
                                            <?php if ($workout['is_default_workout']): ?>
                                                <div class="col-auto">
                                                    <sup>
                                                        * Default
                                                    </sup>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php $setCounter = count($workout['sets']); ?>
                                            <?php foreach ($workout['sets'] as $setId => $set): ?>
                                                <div class="col-12 <?=($setCounter != count($workout['sets']) ? 'border-top border-dark' : '');?>">
                                                    <div class="modal-title">
                                                        <?=$setCounter;?>
                                                        <?=($set['is_superset'] ? '<small> - Superset</small>' : '');?>
                                                        <div class="row">
                                                            <div class="col">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <ol>
                                                        <?php foreach ($set['exercise_id_numbers'] as $exerciseId): ?>
                                                            <li class="text-truncate">
                                                                <?=$exercises[$exerciseId]['name'];?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ol>
                                                </div>
                                                <?php $setCounter--; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $workoutCounter--; ?>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <!--Exercises-->
                        <div class="tab-pane fade <?=($exersizeTab === 1 ? 'show active' : '');?>" id="exercises" role="tabpanel" aria-labelledby="exercises-tab">
                            <div class="card border-dark">
                                <div class="card-header">Create Muscle</div>
                                <div class="card-body">
                                    <form method="post" action="includes/api.php" onsubmit="return gatherExerciseMuscles();">
                                        <div class="form-group">
                                            <label for="exercise-name">Exercise Name</label>
                                            <input class="form-control" id="exercise-name" name="exercise-name" type="text" />
                                        </div>
                                        <div class="form-group">
                                            <label for="exercise-description">Exercise Description</label>
                                            <textarea class="form-control" id="exercise-description" name="exercise-description"></textarea>
                                        </div>
                                        <div id="exercise-muscles">
                                            <label>Related Muscles</label>
                                            <div class="form-group">
                                                <select class="form-control">
                                                    <option selected></option>
                                                    <?php foreach ($muscles as $muscleId => $muscle): ?>
                                                        <option value="<?=$muscleId;?>"><?=$muscle['name'];?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <small>
                                            <a href="#" onclick="addMuscleToExercise();">
                                                <i class="far fa-plus-square"></i>
                                                Add Muscle
                                            </a>
                                        </small>
                                        <input type="hidden" id="related-muscles" name="related-muscles" />
                                        <input type="hidden" id="exercise-id" name="exercise-id" />
                                        <input type="hidden" name="function" value="saveExercise"/>
                                        <div class="btn btn-secondary d-none float-right mx-2" id="cancel-exercise-edit-button" onclick="cancelExerciseEdit();">
                                            Cancel Edit
                                        </div>
                                        <input class="btn btn-secondary float-right" type="submit" value="Save" />
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Muscles</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($exercises as $exerciseId => $exercise): ?>
                                                <tr>
                                                    <td scope="col"><?=$exerciseId;?></td>
                                                    <td scope="col"><?=$exercise['name'];?></td>
                                                    <td scope="col">
                                                        <ul class="my-0">
                                                        <?php foreach ($exercise['muscles'] as $muscleId): ?>
                                                            <li><?=$muscles[$muscleId]['name'];?></li>
                                                        <?php endforeach;?>
                                                        </ul>
                                                    </td>
                                                    <td scope="col"><?=preg_replace('/[\n]/', '<br>', $exercise['description']);?></td>
                                                    <td scope="col">
                                                        <a href="#" onclick="editExercise(<?=$exerciseId;?>, '<?=rawurlencode($exercise['name']);?>', '<?=rawurlencode($exercise['description']);?>', '<?=rawurlencode(json_encode($exercise['muscles']));?>');">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--Muscles-->
                        <div class="tab-pane fade <?=($exersizeTab === 2 ? 'show active' : '');?>" id="muscles" role="tabpanel" aria-labelledby="muscles-tab">
                            <div class="card border-dark">
                                <div class="card-header">Create Muscle</div>
                                <div class="card-body">
                                    <form method="post" action="includes/api.php">
                                        <div class="form-group">
                                            <label for="muscle-name">Muscle Name</label>
                                            <div class="input-group">
                                                <input class="form-control" id="muscle-name" name="muscle-name" type="text" />
                                                <div class="input-group-append">
                                                    <div class="btn btn-secondary border border-dark d-none" id="cancel-muscle-edit-button" onclick="cancelMuscleEdit();">
                                                        Cancel
                                                    </div>
                                                    <input class="btn btn-secondary border border-dark" type="submit" value="Save" />
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="muscle-id" name="muscle-id" />
                                        <input type="hidden" name="function" value="saveMuscle"/>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Exercises</th>
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($muscles as $muscleId => $muscle): ?>
                                                <tr>
                                                    <td scope="col"><?=$muscleId;?></td>
                                                    <td scope="col"><?=$muscle['name'];?></td>
                                                    <td scope="col">
                                                        <ul class="my-0">
                                                        <?php foreach($muscle['exercise_id_numbers'] as $exerciseId): ?>
                                                           <li><?=$exercises[$exerciseId]['name'];?></li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </td>
                                                    <td scope="col">
                                                        <a href="#" onclick="editMuscle(<?=$muscleId;?>, '<?=rawurlencode($muscle['name']);?>');">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/exercise.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>