<?php
session_start();
include "../../templates.php";
if (isset($_SESSION["user_id"]) && isset($_SESSION["displayname"])) {
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        document.getElementById(\"loggedin\").classList.add(\"visible\");
        document.getElementById(\"loggedin\").classList.remove(\"invisible\");
        document.getElementById(\"login-button\").classList.add(\"invisible\");
        document.getElementById(\"login-button\").classList.remove(\"visible\");
    });
    </script>";
}

if (isset($_GET["tag"])) {
    $tag = $_GET["tag"];

    if (strpos($tag, "#") === 0) {
        $tag = substr($tag, 1);
    }
} else {
    header("Location: ../../../index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Claninfo</title>
    <link rel="icon" href="/images/cocTracker-favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="https://kit.fontawesome.com/b2c4ecfb6d.js" crossorigin="anonymous"></script>
    <script src="/script.js" defer></script>
    <script src="/data/request.js"></script>
    <script src="/data/dbaction.js"></script>
</head>

<body>
    <?php
    echo $header;
    ?>

    <main>
        <div id="test" class="displayPlayerData-fpsearch">
            <div class="spinner-box">
                <div class="spinner">
                </div>
            </div>
        </div>
    </main>
    <?php
    echo $footer;
    ?>
    <script>
        var ingameKey = "<?php echo $tag ?>";

        async function loadData() {
            document.getElementById("test").innerHTML = await displayClanSearch(ingameKey);
        }

        window.onload = loadData;
    </script>

</body>

</html>