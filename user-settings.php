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
        <title>Cody Fulford - User</title>
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
                    <div class="card-columns">
                        <div class="card border-dark">
                            <div class="card-header">
                                Personal Settings
                            </div>
                            <div class="card-body">
                                <form method="post" action="includes/api.php" class="clearfix">
                                    <div class="form-group">
                                        <label for="first-name">First Name</label>
                                        <input class="form-control" type="text" id="first-name" name="first-name" value="<?=$userDetails['first_name'];?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="last-name">Last Name</label>
                                        <input class="form-control" type="text" id="last-name" name="last-name" value="<?=$userDetails['last_name'];?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input class="form-control" type="email" id="email" name="email" value="<?=$userDetails['email'];?>"/>
                                        <small>* Changing your email will log you out.</small>
                                    </div>
                                    <input type="hidden" name="function" value="saveUserDetails" />
                                    <input class="btn btn-secondary float-right" type="submit" value="Save"/>
                                </form>
                            </div>
                        </div>
                        <div class="card border-dark">
                            <div class="card-header">
                                Change Password
                            </div>
                            <div class="card-body">
                                <form method="post" action="includes/api.php" class="clearfix">
                                    <div class="form-group">
                                        <label for="password-0">Current Password</label>
                                        <input class="form-control" type="password" id="password-0" name="password-0" />
                                    </div>
                                    <div class="form-group">
                                        <label for="password-1">New Password</label>
                                        <input class="form-control" type="password" id="password-1" name="password-1" />
                                    </div>
                                    <div class="form-group">
                                        <label for="password-2">New Password Again</label>
                                        <input class="form-control" type="password" id="password-2" name="password-2" />
                                        <small>* Changing your password will log you out.</small>
                                    </div>
                                    <input type="hidden" name="function" value="changeUserPassword" />
                                    <input class="btn btn-secondary float-right" type="submit" value="Save"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script>$(document).ready(function () { $('#navbar-user-settings').addClass('active rounded'); });</script>
    </body>
</html>