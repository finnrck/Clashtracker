<?php
session_start();
include "../templates.php";
if (isset($_SESSION["user_id"]) && isset($_SESSION["username"])) {
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        document.getElementById(\"loggedin\").classList.add(\"visible\");
        document.getElementById(\"loggedin\").classList.remove(\"invisible\");
        document.getElementById(\"login-button\").classList.add(\"invisible\");
        document.getElementById(\"login-button\").classList.remove(\"visible\");
    });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Information</title>
    <link rel="icon" href="/images/cocTracker-favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="/script.js" defer></script>
</head>

<body>
<?php
    echo $header;
    ?>

    <main>
        <section class="padding-block-700" id="über-uns">
            <div class="container">
                <div class="padding-block-500">
                    <h1 class="information-heading">Über uns</h1>
                </div>
                <div class="information-svg-box">
                    <img class="information-svg" src="/images/Team.svg" alt="">
                </div>
                <div class="text-center | padding-block-500">
                    <p data-width="wide">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
                </div>
            </div>
        </section>
        <section class="padding-block-700" id="Impressum">
            <div class="container">
                <div class="padding-block-500">
                    <h1 class="information-heading">Impressum</h1>
                </div>
            </div>
        </section>
    </main>
    <?php
    echo $footer;
    ?>
</body>

</html>