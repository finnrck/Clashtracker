<?php
session_start();
include "../../templates.php";
if (isset($_SESSION["user_id"]) && isset($_SESSION["displayname"])){
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        document.getElementById(\"loggedin\").classList.add(\"visible\");
        document.getElementById(\"loggedin\").classList.remove(\"invisible\");
        document.getElementById(\"login-button\").classList.add(\"invisible\");
        document.getElementById(\"login-button\").classList.remove(\"visible\");
    });
    </script>";
}   //TODO erstellen spielersuche
?>
<head>
    <meta charset="UTF-8" />
    <title>Spielerinfo</title>
    <link rel="icon" href="/images/cocTracker-favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="https://kit.fontawesome.com/b2c4ecfb6d.js" crossorigin="anonymous"></script>
    <script src="/script.js" defer></script>
    <script src="/data/request.js"></script>
</head>

<body>
<?php
    echo $header;
    ?>
    <main class="padding-block-1200">
        <div class="padding-block-1200 text-center">
            WIP
        </div>
    </main>
    <?php
    echo $footer;
    ?>
</body>