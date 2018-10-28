<?php
    include_once 'includes/includes.php';

    if (!$controller->isUserLoggedIn())
        header('Location: index.php');

    $userDetails = $controller->getUserDetails($_SESSION['user_id']);
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
                <div class="col">
                    <h2><?=$userDetails['first_name']?> <?=$userDetails['last_name'];?></h2>
                </div>
            </div>
            <hr />
            <div class="col">
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script>$(document).ready(function () { $('#navbar-exercise').addClass('active rounded'); });</script>
    </body>
</html>