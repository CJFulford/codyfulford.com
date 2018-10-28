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
                <div class="col-auto mx-auto">
                    <form>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input id="username" name="username" class="form-control" type="text" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" name="password" class="form-control" type="password" />
                        </div>
                        <input class="btn btn-secondary float-right" type="submit" value="login" />
                    </form>
                </div>
            </div>
        </div>
        <?php include 'global-footer.php'; ?>
        <script>$(document).ready(function () { $('#navbar-volunteering').addClass('active rounded'); });</script>
    </body>
</html>