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
    <title>Home</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="/script.js" defer></script>
</head>

<body>
<?php
    echo $header;
    ?>

    <main>
        <section class="quicksearch">
            <h1 class="padding-block-700">Willkommen bei ClashTracker</h1>
            <div class="searchbox">
                <div class="searchfield">
                    <h2>Suchen Sie nach einem...</h2>
                    <div class="button-group">
                        <button id="clanBtn" class="active">Clan</button>
                        <button id="playerBtn" class="inactive">Spieler</button>
                    </div>
                    <div class="input-group">
                        <input type="text" placeholder="Zum Suchen tippen">
                        <button>Suchen</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    echo $footer;
    ?>
</body>

</html>