<?php
session_start();
include "../templates.php";
include "../data/dbConnection.php";
if (isset($_SESSION["user_id"]) && isset($_SESSION["displayname"])) {
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        document.getElementById(\"loggedin\").classList.add(\"visible\");
        document.getElementById(\"loggedin\").classList.remove(\"invisible\");
        document.getElementById(\"login-button\").classList.add(\"invisible\");
        document.getElementById(\"login-button\").classList.remove(\"visible\");
        
    });
    </script>";
    unset($_SESSION["redirect_url"]);
    unset($_SESSION["failmessage"]);
} else {
    //weiterleitung an login + Fehlermeldung (Diese Seite nur als loggedin User nutzbar)
    $_SESSION["redirect_url"] = "/subpages/accountsettings.php";
    $_SESSION["failmessage"] = "not_logged_in";
    header("Location: /subpages/login.php");
    exit();
}

$user_data_json = getUserData($_SESSION["user_id"]);
$user_data_array = json_decode($user_data_json, true);
if ($user_data_array["status"] == "success") {
    $user_data = $user_data_array["data"];
} else {
    echo "Fehler: " . $user_data_array["message"];
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
    <script src="https://kit.fontawesome.com/b2c4ecfb6d.js" crossorigin="anonymous"></script>
    <script src="/script.js" defer></script>
    <script src="/data/dbaction.js"></script>
</head>

<body>
    <?php
    echo $header;
    ?>

    <main>
        <h1 class="settings-header">Account Settings</h1>
        <div class="setting-container">
            <section class="setting-block user-data-container">
                <h2>Account Daten</h2>
                <div>
                    <form class="userSetting-form" id="userSettings">
                        <!-- TODO UserSettings -->
                        <!-- über js aus db holen und manuel laden $_SESSION["id"] nutzen um dafür alle daten abzurufen-->
                        <div class="user-data-block">
                            <p>Anzeigename:</p>
                            <input type="text" id="displayname" value="">
                            <span>
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                        </div>
                        <div class="user-data-block">
                            <p>Benutzername:</p>
                            <input type="text" id="username" value="">
                            <span>
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                        </div>
                        <div class="user-data-block">
                            <p>Email:</p>
                            <input type="email" id="email" value="" disabled>
                            <span>
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                        </div>
                        <div class="user-data-block">
                            <p>Passwort:</p>
                            <div class="user-data-password">
                                <button class="button" type="button" onclick="openPasswordModal()">Ändern</button>
                            </div>
                            <span>
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                        </div>
                        <button class="button setting-button" type="submit">Speichern</button>
                    </form>
                </div>
                <div class="settings-failmassage-box">
                    <p id="setting-failmassage"></p>
                </div>
            </section>
            <section class="setting-block ingame-user-data-container">
                <h2>Verbundene Ingamekonten</h2>
            </section>
        </div>
    </main>
    <?php
    echo $footer;
    ?>
    <script>
        const displaynameHeader = document.getElementById("displayname-header");
        const failmassage = document.getElementById("setting-failmassage");
        const displaynameValue = "<?php echo $user_data["displayname"]; ?>";
        const displayname = document.getElementById("displayname");
        const usernameValue = "<?php echo $user_data["username"]; ?>";
        const username = document.getElementById("username");
        const emailValue = "<?php echo $user_data["email"]; ?>";
        const email = document.getElementById("email");
        const userID = <?php echo $_SESSION["user_id"] ?>

        function loadUserData() {
            displayname.value = "<?php echo $user_data["displayname"]; ?>";
            username.value = "<?php echo $user_data["username"]; ?>";
            email.value = "<?php echo $user_data["email"]; ?>";
        }
        window.onload = loadUserData();

        document.getElementById("userSettings").addEventListener("submit", async function(event) {
            event.preventDefault();

            var newUsername = username.value;
            var newDisplayname = displayname.value;

            if (newDisplayname != displaynameValue || newUsername != usernameValue) {
                if (newDisplayname.length > 3 && newUsername.length > 3) {
                    let response = await updateUserData(userID, newDisplayname, newUsername);
                    if (response.status === "success") {
                        console.log("erfolgreich");
                        failmassage.innerText = "Account Daten erfolgreich geupdated";
                        failmassage.style.color = "green";
                        displaynameHeader.innerText = newDisplayname;
                        displaynameValue = newDisplayname;
                        usernameValue = newUsername;
                    } else {
                        console.log("fehlgeschlagen");
                        failmassage.innerText = response.message;
                        failmassage.style.color = "hsl(12, 88%, 59%)";
                        displayname.value = displaynameValue;
                        username.value = usernameValue;
                    }
                } else if (newDisplayname.length < 4) {
                    console.log("fehler");
                } else if (newUsername.length < 4) {
                    console.log("fehler zu kurz");
                }
            } else {

            }
        });
    </script>
</body>

</html>