<?php
if(session_status()!=PHP_SESSION_ACTIVE)
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'global-header.php'; ?>
        <title>Cody Fulford - Login</title>
    </head>
    <body>
        <?php include 'navbar.php'; ?>
        <div class="container">
            <div class="row">
                <div class="col">
                    <form action="includes/api.php" method="post" class="clearfix">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" name="email" class="form-control w-100" type="email" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" name="password" class="form-control w-100" type="password" />
                        </div>
                        <input type="hidden" name="function" value="login" />
                        <input class="btn btn-secondary float-right" type="submit" value="Login" />
                    </form>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script>$(document).ready(function () { $('#navbar-login').addClass('active rounded'); });</script>
    </body>
</html>
