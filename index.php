<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'global-header.html'; ?>
    <title>Cody Fulford</title>
</head>

<body>
    <?php include "navbar.html"; ?>

    <div class="container">
        <p>
            Hello.
        </p>
        <p class="main-greeting">
            I am Cody Fulford.
        </p>

        <p>Welcome to my personal website.</p>

        <p>I hope you enjoy your time here and learn something new about me.</p>

        <br />

        <p>I am a computer programmer, graduated from the University of Calgary in 2017 with a BSC in computer science and a specialization in computer graphics.</p>

        <br />

        <p>I work with a small team at Integrity Post Structures Ltd. as a full stack developer. Together we have developed a web based ERP service that tracks everything related to the constrction of our post frame buildings from th first time the custoer contacts us until the building is complete.</p>

    </div>
    <?php include 'global-footer.html'; ?>
    <script>
        $(document).ready(function() {
            $('#navbar-index').addClass('active rounded');
        });
    </script>
</body>

</html>