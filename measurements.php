<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: ./');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
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
                        <div class="card-header">Measure</div>
                        <div class="card-body">
                            <form method="post" action="includes/api.php">
                                <input type="hidden" name="function" value="saveMeasurement"/>
                                <div class="row">
                                    <div class="col-12 col-md form-group">
                                        <label for="measurement-id">Type</label>
                                        <select class="form-control" name="measurement-id">
                                            <?php foreach ($measurements as $measurementId => $measurement): ?>
                                                <option value="<?=$measurementId;?>"><?=$measurement['measurement_name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md form-group">
                                        <label for="measuerment-value">Measuerment</label>
                                        <input class="form-control" name="measurement-value" type="number" step="0.01" required/>
                                    </div>
                                    <div class="col-auto float-right form-group mt-auto">
                                        <input class="btn btn-secondary" type="submit" step="Save"/>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Measurement</th>
                                                <th scope="col" class="text-right">Value</th>
                                                <th scope="col" class="text-right">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($controller->getUserMeasurements($_SESSION['user_id']) as $measurement): ?>
                                                <tr>
                                                    <td scope="col"><?=$measurements[$measurement['measurement_id']]['measurement_name'];?></td>
                                                    <td scope="col" class="text-right"><?=number_format($measurement['measurement_value'], 2);?></td>
                                                    <td scope="col" class="text-right"><?=date('d/m/Y', strtotime($measurement['created']));?></td>
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