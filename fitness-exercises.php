<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: ./');

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
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center border-bottom border-red border-2">
                        Exercises
                    </h1>
                    <div class="card" id="save-exercise-card">
                        <div class="card-header bg-red">
                            Create Exercise
                        </div>
                        <form method="post" action="includes/api.php" onsubmit="return gatherExerciseMuscles();">
                            <input type="hidden" id="related-muscles" name="related-muscles" />
                            <input type="hidden" id="exercise-id" name="exercise-id" />
                            <input type="hidden" name="function" value="saveExercise"/>
                            <div class="card-body">
                                <div class="form-group">
                                    <input class="form-control" id="exercise-name" name="exercise-name" type="text" placeholder="Name" />
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="exercise-description" name="exercise-description" placeholder="Description"></textarea>
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
                            </div>
                            <div class="card-footer clearfix">
                                <div class="btn btn-secondary d-none float-right mx-2" id="cancel-exercise-edit-button" onclick="cancelExerciseEdit();">
                                    Cancel Edit
                                </div>
                                <input class="btn btn-secondary float-right" type="submit" value="Save Exercise" />
                            </div>
                        </form>
                    </div>
                    <hr />
                    <div class="card-columns">
                        <?php foreach ($exercises as $exerciseId => $exercise): ?>
                            <div class="card exercise-card">
                                <div class="card-header clearfix bg-dark text-white">
                                    <?=$exercise['name'];?>
                                    <a href="#" onclick="editExercise(<?=$exerciseId;?>, '<?=rawurlencode($exercise['name']);?>', '<?=rawurlencode($exercise['description']);?>', '<?=rawurlencode(json_encode($exercise['muscles']));?>', event);" class="float-right">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <div>
                                    <div class="card-body">
                                        <?=preg_replace('/[\n]/', '<br>', $exercise['description']);?>
                                    </div>
                                    <div class="card-footer border-top border-dark">
                                        <small>
                                            <ul class="my-0">
                                            <?php foreach ($exercise['muscles'] as $muscleId): ?>
                                                <li><?=$muscles[$muscleId]['name'];?></li>
                                            <?php endforeach;?>
                                            </ul>
                                        </small>
                                    </div>
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
