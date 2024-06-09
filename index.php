<?php
session_start();
include "templates.php";
if (isset($_SESSION["user_id"]) && isset($_SESSION["displayname"])) {
    //prüft ob user eingeloggt ist und ändert je nach dem den Log in Button zum Username mit dropdown menu
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        document.getElementById(\"loggedin\").classList.add(\"visible\");
        document.getElementById(\"loggedin\").classList.remove(\"invisible\");
        document.getElementById(\"login-button\").classList.add(\"invisible\");
        document.getElementById(\"login-button\").classList.remove(\"visible\");
    });
    </script>";
}
unset($_SESSION["redirect_url"]);
unset($_SESSION["failmessage"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Home</title>
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

    <main>
        <section class="quicksearch">
            <h1 class="padding-block-700">Willkommen bei ClashTracker</h1>
            <div class="searchbox ">
                <div class="searchfield">
                    <h2>Suchen Sie nach einem...</h2>
                    <div class="button-group">
                        <button id="clanBtn" class="active">Clan</button>
                        <button id="playerBtn" class="inactive">Spieler</button>
                    </div>
                    <div class="input-group">
                        <input id="search-input" type="text" placeholder="Zum Suchen nach einem Clan tippen">
                        <button id="quicksearch">Suchen</button>
                    </div>
                </div>
            </div>
        </section>
        <section class="padding-block-900">
            <h1 class="content-hadding">Deutschland Rankings</h1>
            <div id="content" class="container rankingbox">
                <div class="spinner-box">
                    <div class="spinner">
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    echo $footer;
    ?>

    <script>
        window.addEventListener("load", async function() {
            var contentElement = document.getElementById("content");
            if (contentElement) {
                contentElement.innerHTML = await getRanking();
            }
        });

        const clanButton = document.getElementById("clanBtn");
        const playerButton = document.getElementById("playerBtn");
        const input = document.getElementById("search-input");
        const button = document.getElementById("quicksearch");
        button.addEventListener("click", (event) => {
            let inputvalue = input.value;
            if (inputvalue.length > 5) {
                if (clanButton.classList.contains("active")) {
                    const url = "/subpages/searchinformation/clan.php/?id=" + encodeURIComponent(inputvalue);
                    window.location.href = url;
                } else if (playerButton.classList.contains("active")) {
                    const url = "/subpages/searchinformation/player.php/?id=" + encodeURIComponent(inputvalue);
                    window.location.href = url;
                } else {
                    console.error("Unerwarteter Fehler: Kein Button Aktiv");
                }
            } else {
                console.log("loser");
            }
        });
    </script>
</body>

</html>