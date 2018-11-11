<?php include_once 'includes/includes.php'; ?>
<nav class="navbar navbar-expand-md navbar-dark bg-black mb-3">
    <a class="navbar-brand" href="#">Cody Fulford</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item" id="navbar-index">
                <a class="nav-link text-white" href="index.php">
                    <i class="fas fa-home"></i>
                    Home
                </a>
            </li>
            <li class="nav-item" id="navbar-career">
                <a class="nav-link text-white" href="career.php">
                    <i class="fas fa-building"></i>
                    Career
                </a>
            </li>
            <li class="nav-item" id="navbar-volunteering">
                <a class="nav-link text-white" href="volunteering.php">
                    <i class="fas fa-hands-helping"></i>
                    Volunteering
                </a>
            </li>
            <li class="nav-item" id="navbar-development">
                <a class="nav-link text-white" href="development.php">
                    <i class="fas fa-dumbbell"></i>
                    code
                </a>
            </li>
            <li class="nav-item" id="navbar-doodles">
                <a class="nav-link text-white" href="doodles.php">
                    <i class="fas fa-dragon"></i>
                    Doodles
                </a>
            </li>
            <?php if ($controller->isUserLoggedIn()) : ?>
                <li class="nav-item" id="navbar-exercise">
                    <a class="nav-link text-white" href="exercise.php">
                        <i class="fas fa-dumbbell"></i>
                        Exercise
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if ($controller->isUserLoggedIn()) : ?>
                <li class="nav-item" id="navbar-user-settings">
                    <a class="nav-link text-white" href="user-settings.php">
                        <i class="fas fa-user"></i>
                        User Settings
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="https://www.linkedin.com/in/codyfulford/">
                    <i class="fab fa-linkedin"></i>
                    LinkedIn
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="https://github.com/CJFulford">
                    <i class="fab fa-github"></i>
                    Github
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="https://www.youtube.com/channel/UCNFsx1Nd9wa-0s_ny_ry_Hg">
                    <i class="fab fa-youtube"></i>
                    YouTube
                </a>
            </li>
            <?php if ($controller->isUserLoggedIn()) : ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="includes/api.php?function=logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item" id="navbar-login">
                    <a class="nav-link text-white" href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<?php if (!empty($_GET['message'])): ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="border border-dark bg-warning py-2 rounded text-center text-capitalize h4">
                    <?=$_GET['message'];?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>