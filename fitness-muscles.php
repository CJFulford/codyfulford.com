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
                    <h1 class="text-center border-bottom border-red border-2">
                        Muscles
                    </h1>
                    <div class="card"id="save-muscle-card">
                        <div class="card-header bg-red">Create Muscle</div>
                        <div class="card-body">
                            <form method="post" action="includes/api.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control" id="muscle-name" name="muscle-name" type="text"  placeholder="Muscle Name"/>
                                        <div class="input-group-append">
                                            <input class="btn btn-secondary" type="submit" value="Save Muscle" />
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="muscle-id" name="muscle-id" />
                                <input type="hidden" name="function" value="saveMuscle"/>
                            </form>
                        </div>
                    </div>
                    <hr />
                    <div class="card-columns">
                        <?php foreach ($muscles as $muscleId => $muscle): ?>
                            <div class="card">
                                <div class="card-header bg-dark text-white clearfix">
                                    <?=$muscle['name'];?>
                                    <a href="#" class="float-right" onclick="editMuscle(<?=$muscleId;?>, '<?=rawurlencode($muscle['name']);?>', event);">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($muscle['exercise_id_numbers'])): ?>
                                        <ul class="my-0">
                                            <?php foreach($muscle['exercise_id_numbers'] as $exerciseId): ?>
                                                <li>
                                                    <?=$exercises[$exerciseId]['name'];?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="mb-0 text-center">
                                            --
                                            <small>
                                                No Workouts
                                            </small>
                                            --
                                        </p>
                                    <?php endif; ?>
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
