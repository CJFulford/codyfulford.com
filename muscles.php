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
        <title>Cody Fulford - Fitness - Muscles</title>
    </head>
    <body>
        <?php include "navbar.php"; ?>

        <div class="container">
            <div class="row">
                <div class="col-12">
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
        <?php include 'global-footer.php'; ?>
        <script src="<?=auto_version_file('/js/exercise.js');?>"></script>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>