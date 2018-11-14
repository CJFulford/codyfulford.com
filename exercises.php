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
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/exercise.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>