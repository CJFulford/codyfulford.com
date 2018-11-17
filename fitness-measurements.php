<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: ./');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
    $userMeasurements = $controller->getUserMeasurements($_SESSION['user_id']);
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
                        Measurements
                    </h1>
                    <div class="card">
                        <div class="card-header bg-red">
                            Measure
                        </div>
                        <div class="card-body">
                            <form method="post" action="includes/api.php">
                                <input type="hidden" name="function" value="saveMeasurement"/>
                                <div class="row">
                                    <div class="col-12 col-md form-group">
                                        <select class="form-control" name="measurement-id">
                                            <?php foreach ($measurements as $measurementId => $measurement): ?>
                                                <option value="<?=$measurementId;?>"><?=$measurement['measurement_name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md form-group">
                                        <input class="form-control" name="measurement-value" type="number" step="0.01" placeholder="Measured As..." required/>
                                    </div>
                                    <div class="col-auto float-right form-group mt-auto">
                                        <input class="btn btn-secondary" type="submit" step="Save"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr />
                    <div class="card-columns">
                        <?php foreach ($userMeasurements as $measurementId => $userMeasurements): ?>
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <?=$measurements[$measurementId]['measurement_name'];?>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($userMeasurements as $measurement): ?>
                                        <div class="row">
                                            <div class="col text-muted text-truncate">
                                                <small>
                                                    <?php
                                                        // measurement date
                                                        echo formatDate($measurement['created']);
                                                        echo ' - ';

                                                        // days since that measurement
                                                        $daysSinceMeasurement = getDaysBetweenDates(date('Y-m-d'), $measurement['created']);
                                                        echo $daysSinceMeasurement > 0 ? $daysSinceMeasurement.' Days' : 'Today';
                                                    ?>
                                                </small>
                                            </div>
                                            <div class="col-auto text-right">
                                                <?=number_format($measurement['measurement_value'], 2);?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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
