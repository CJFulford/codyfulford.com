<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: index.php');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
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
                    <ul class="nav nav-tabs" id="exercise-tables" role="tablist">
                        <?php
                        $exersizeTab = 0;
                        switch($_GET['exercise-tabs'])
                        {
                            case ('workouts-tab'):
                                $exersizeTab = 0;
                                break;
                            case ('sets-tab'):
                                $exersizeTab = 1;
                                break;
                            case ('exercise-tab'):
                                $exersizeTab = 2;
                                break;
                            case ('muscles-tab'):
                                $exersizeTab = 3;
                                break;
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?=($exersizeTab === 0 ? 'active' : '');?>" id="workouts-tab" data-toggle="tab" href="#workouts" role="tab" aria-controls="workouts" aria-selected="true">Workouts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?=($exersizeTab === 1 ? 'active' : '');?>" id="sets-tab" data-toggle="tab" href="#sets" role="tab" aria-controls="sets" aria-selected="false">Sets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?=($exersizeTab === 2 ? 'active' : '');?>"id="exercises-tab" data-toggle="tab" href="#exercises" role="tab" aria-controls="exercises" aria-selected="false">Exercises</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?=($exersizeTab === 3 ? 'active' : '');?>" id="muscles-tab" data-toggle="tab" href="#muscles" role="tab" aria-controls="muscles" aria-selected="false">Muscles</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <!--Workouts-->
                        <div class="tab-pane fade <?=($exersizeTab === 0 ? 'show active' : '');?>" id="workouts" role="tabpanel" aria-labelledby="workouts-tab">
                            Workouts
                        </div>
                        <!--Sets-->
                        <div class="tab-pane fade <?=($exersizeTab === 1 ? 'show active' : '');?>" id="sets" role="tabpanel" aria-labelledby="sets-tab">
                            Sets
                        </div>
                        <!--Exercises-->
                        <div class="tab-pane fade <?=($exersizeTab === 2 ? 'show active' : '');?>" id="exercises" role="tabpanel" aria-labelledby="exercises-tab">
                            <div class="card">
                                <div class="card-header">Create Muscle</div>
                                <div class="card-body">
                                    <form method="post" action="includes/api.php" onsubmit="return gatherExerciseMuscles();">
                                        <div class="form-group">
                                            <label for="exercise-name">Exercise Name</label>
                                            <input class="form-control" id="exercise-name" name="exercise-name" type="text" />
                                        </div>
                                        <div class="form-group">
                                            <label for="exercise-name">Exercise Description</label>
                                            <textarea class="form-control" id="exercise-description" name="exercise-description"></textarea>
                                        </div>
                                        <div id="exercise-muscles">
                                            <label for="exercise-name">Related Muscles</label>
                                            <div class="form-group">
                                                <select class="form-control">
                                                    <option selected></option>
                                                    <?php foreach ($muscles as $muscleId => $muscleName): ?>
                                                        <option value="<?=$muscleId;?>"><?=$muscleName?></option>
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
                                            <?php foreach ($controller->getExercises() as $exerciseId => $exercise): ?>
                                                <tr>
                                                    <td scope="col"><?=$exerciseId;?></td>
                                                    <td scope="col"><?=$exercise['name'];?></td>
                                                    <td scope="col">
                                                        <?php foreach ($exercise['muscles'] as $muscleId): ?>
                                                            <?=$muscles[$muscleId];?><br />
                                                        <?php endforeach;?>
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
                        <div class="tab-pane fade <?=($exersizeTab === 3 ? 'show active' : '');?>" id="muscles" role="tabpanel" aria-labelledby="muscles-tab">
                            <div class="card">
                                <div class="card-header">Create Muscle</div>
                                <div class="card-body">
                                    <form method="post" action="includes/api.php">
                                        <div class="form-group">
                                            <label for="muscle-name">Muscle Name</label>
                                            <input class="form-control" id="muscle-name" name="muscle-name" type="text" />
                                        </div>
                                        <input type="hidden" id="muscle-id" name="muscle-id" />
                                        <input type="hidden" name="function" value="saveMuscle"/>
                                        <div class="btn btn-secondary d-none float-right mx-2" id="cancel-muscle-edit-button" onclick="cancelMuscleEdit();">
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
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($muscles as $muscleId => $muscleName): ?>
                                                <tr>
                                                    <td scope="col"><?=$muscleId;?></td>
                                                    <td scope="col"><?=$muscleName;?></td>
                                                    <td scope="col">
                                                        <a href="#" onclick="editMuscle(<?=$muscleId;?>, '<?=rawurlencode($muscleName);?>');">
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
        <script src="js/exercise.js"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>