<?php
session_start();
include "../templates.php";
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>AccountSettings</title>
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
        <div>
            <h1>Account Settings</h1>
        </div>
        <section>
            <h2>Account Daten</h2>
        </section>
        <section>
            <h2>Verbundene Ingamekonten</h2>
        </section>
    </main>
    <?php
    echo $footer;
    ?>
</body>

</html>