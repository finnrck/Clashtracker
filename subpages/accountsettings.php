<?php
session_start();
include "../templates.php";
include "../data/dbConnection.php";

$user_data_json = getUserData($_SESSION["user_id"]);
$user_data_array = json_decode($user_data_json, true);
if ($user_data_array["status"] == "success") {
    $user_data = $user_data_array["data"];
} else {
    echo "Fehler: " . $user_data_array["message"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $passwordAuth = $_POST["passwordAuth"];
    $newPW = $_POST["newpassword"];
    if (password_verify($passwordAuth, $user_data["password"])) {
        if (password_verify($newPW, $user_data["password"])) {
            echo "samePassword";
        } else {
            echo "success";
        }
    } else {
        echo "falsePassword";
    }
    exit;
}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/script.js" defer></script>
    <script src="/data/dbaction.js"></script>
</head>

<body>
    <?php
    echo $header;
    ?>

    <main id="settings">
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
                                <button class="button" type="button" id="password-openbutton">Ändern</button>
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
                <div class="ingameaccount-settings">
                    <div class="ingameaccount-box overflow" id="ingame-setting-datablock">
                        <div class="spinner-box">
                            <div class="spinner">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </main>
    <!-- von Accmanager acc hinzufügen importiert -->
    <div id="change-password" class="add-acc invisible">
        <div class="add-acc-header">
            <h1>Passwort ändern</h1> <!--TODO fragezeichen symbol für erklärung -->
            <button id="closeing-btn"><i id="closing-acc" class="fa-solid fa-xmark fa-4x"></i></button>
        </div>
        <div class="acc-inputbox">
            <form class="acc-inputform" action="" method="POST" id="passwordForm">
                <input class="passwordchange-input" name="newPassword" id="newPassword" type="password" placeholder="Neues Passwort" required>
                <input class="passwordchange-input" name="newPassword-verify" id="newPassword-verify" type="password" placeholder="Neues Passwort bestätigen" required>
                <input class="passwordchange-input" name="password-auth" id="password-auth" type="password" placeholder="Aktuelles Passwort" required>
                <button id="passwordchange-button" type="submit" class="button">
                    <p>Passwort aktualisieren</p>
                </button>
            </form>
            <div id="failmassage">

            </div>
        </div>
    </div>
    <div id="delete-ingame" class="add-acc invisible">
        <div class="add-acc-header">
            <h1>Accountverbindung Löschen</h1>
            <button id="delete-closeing-btn"><i id="closing-acc" class="fa-solid fa-xmark fa-4x"></i></button>
        </div>
        <div class="acc-inputbox delete-ingame-box">
            <p>Zum Löschen der Konto-Verbindung mit Passwort bestätigen</p>
            <p data-ingame-id="" id="delete-ingameID"></p>
            <input id="delete-pwcheck" type="password" placeholder="Passwort" required>
            <button id="delete-ingameBtn" class="button">Bestätigen</button>
            <div id="delete-failmessage">
            </div>
        </div>
    </div>

    <?php
    echo $footer;
    ?>
    <script>
        const displaynameHeader = document.getElementById("displayname-header");
        const failmassage = document.getElementById("setting-failmassage");
        let displaynameValue = "<?php echo $user_data["displayname"]; ?>";
        const displayname = document.getElementById("displayname");
        let usernameValue = "<?php echo $user_data["username"]; ?>";
        const username = document.getElementById("username");
        let emailValue = "<?php echo $user_data["email"]; ?>";
        const email = document.getElementById("email");
        let userID = <?php echo $_SESSION["user_id"] ?>;
        const passwordField = document.getElementById("change-password");
        const newpassword = document.getElementById("newPassword");
        const newpasswordVerify = document.getElementById("newPassword-verify");
        const passwordAuth = document.getElementById("password-auth");
        const pwfailmassage = document.getElementById("failmassage");

        async function loadIngameAccounts() {

            let response = await displayAllIngameAccounts(userID);
            document.getElementById("ingame-setting-datablock").innerHTML = response;

            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let container = button.closest(".ingame-settingcontainer");
                    container.querySelector("input").disabled = false;
                    container.querySelector(".edit-btn-box").classList.add("invisible");
                    container.querySelector(".save-cancel-buttons").classList.remove("invisible");
                });
            });

            document.querySelectorAll(".newdisplayname_input").forEach(input => {
                input.addEventListener("keydown", function(event) {
                    if (event.key === "Enter") {
                        let container = input.closest(".ingame-settingcontainer");
                        saveChanges(container.querySelector(".save-btn"));
                    }
                });
            });

            document.querySelectorAll(".save-btn").forEach(button => {
                button.addEventListener("click", function() {
                    saveChanges(button);
                });
            });

            document.querySelectorAll(".cancel-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let container = button.closest(".ingame-settingcontainer");
                    let input = container.querySelector("input");
                    input.value = input.getAttribute("data-original-value");
                    input.disabled = true;
                    container.querySelector(".save-cancel-buttons").classList.add("invisible");
                    container.querySelector(".edit-btn-box").classList.remove("invisible");
                });
            });

            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function() {
                    console.log("test");
                    let container = button.closest(".ingame-settingcontainer");
                    let ingame_id = container.getAttribute("data-ingame-id");
                    let ingameKeyfield = container.querySelector(".ingameKey");
                    let ingameKey = ingameKeyfield.getAttribute("data-ingameKey");
                    let input = container.querySelector("input");
                    let displayname = input.getAttribute("data-original-value");
                    document.getElementById("delete-ingameID").innerText = "Verbindung zu " + displayname + " ( #" + ingameKey + " ) " + " wird gelöscht!";
                    document.getElementById("delete-ingameID").setAttribute("data-ingame-id", ingame_id);

                    document.getElementById("delete-ingame").classList.remove("invisible");
                    document.getElementById("settings").classList.add("blur");
                })
            });
        }

        async function saveChanges(button) {
            let container = button.closest(".ingame-settingcontainer");
            let ingame_id = container.getAttribute("data-ingame-id");
            let input = container.querySelector("input");
            input.disabled = true;
            container.querySelector(".save-cancel-buttons").classList.add("invisible");
            container.querySelector(".edit-btn-box").classList.remove("invisible");

            let newDisplayname = input.value;
            if (newDisplayname.length < 4) {
                input.value = input.getAttribute("data-original-value");
            } else {
                var response = await updateDisplayname(newDisplayname, ingame_id, userID);
                if (response.status != "success") {
                    input.value = input.getAttribute("data-original-value");
                    console.log(response.message);
                } else {
                    input.setAttribute("data-original-value", input.value);
                }
            }
        }

        function loadUserData() {
            displayname.value = displaynameValue;
            username.value = usernameValue;
            email.value = emailValue;

            loadIngameAccounts();
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
                    displayname.value = displaynameValue;
                    username.value = usernameValue;
                    failmassage.innerText = "Neuer Displayname zu Kurz (min. 4)";
                    failmassage.style.color = "hsl(12, 88%, 59%)";
                    console.log("fehler");
                } else if (newUsername.length < 4) {
                    displayname.value = displaynameValue;
                    username.value = usernameValue;
                    failmassage.innerText = "Neuer Benutzername zu Kurz (min. 4)";
                    failmassage.style.color = "hsl(12, 88%, 59%)";
                    console.log("fehler zu kurz");
                }
            } else {

            }
        });
        document.getElementById("passwordForm").addEventListener("submit", async function(event) {
            event.preventDefault();
            const hashedpw = "<?php echo $user_data["password"]; ?>";
            const newpasswordvalue = newpassword.value;

            if (newpasswordvalue != newpasswordVerify.value) {
                pwfailmassage.innerText = "Fehlerhafte wiederholung des neuen Passworts";
                pwfailmassage.style.color = "hsl(12, 88%, 59%)";
                document.querySelectorAll("#passwordForm input").forEach(input => {
                    input.value = "";

                });
                return
            }

            if (newpasswordvalue.length < 6 ||
                !/[A-Z]/.test(newpasswordvalue) ||
                !/[a-z]/.test(newpasswordvalue) ||
                !/\d/.test(newpasswordvalue) ||
                !/[^a-zA-Z\d]/.test(newpasswordvalue)) {
                pwfailmassage.innerText =
                    "Das Passwort muss mindestens 6 Zeichen lang sein, einen Großbuchstaben, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten";
                pwfailmassage.style.color = "hsl(12, 88%, 59%)";
                document.querySelectorAll("#passwordForm input").forEach(input => {
                    input.value = "";

                });
                return;
            }

            $.ajax({
                url: window.location.href,
                type: "POST",
                data: {
                    passwordAuth: passwordAuth.value,
                    newpassword: newpasswordvalue
                },
                success: async function(response) {
                    if (response == "success") {
                        let insertResponse = await updatePassword(newpasswordvalue, userID);
                        if (insertResponse.status === "success") {
                            document.querySelectorAll("#passwordForm input").forEach(input => {
                                input.value = "";
                            });
                            document.getElementById("closeing-btn").click();
                            failmassage.innerText = "Passwort erfolgreich geändert";
                            failmassage.style.color = "green";
                        } else {
                            document.getElementById("closeing-btn").click();
                            failmassage.innerText = "Fehler beim Speichern des neuen Passworts";
                            failmassage.style.color = "hsl(12, 88%, 59%)";
                        }
                    } else if (response == "samePassword") {
                        document.querySelectorAll("#passwordForm input").forEach(input => {
                            input.value = "";
                        });
                        pwfailmassage.innerText = "Password darf nicht das gleiche sein wie zuvor";
                        pwfailmassage.style.color = "hsl(12, 88%, 59%)";
                    } else {

                        document.querySelectorAll("#password-auth").forEach(input => {
                            input.value = "";
                        });
                        pwfailmassage.innerText = "Falsches Passwort";
                        pwfailmassage.style.color = "hsl(12, 88%, 59%)";
                    }
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error("Fehler:", error);
                }
            })





            console.log("schauen wie mal was wird");
        });

        document.getElementById("delete-ingameBtn").addEventListener("click", async function (event) {

            var ingame_id = document.getElementById("delete-ingameID").getAttribute("data-ingame-id");
            var pwcheck = document.getElementById("delete-pwcheck").value;
            var failmessage = document.getElementById("delete-failmessage");

            if (pwcheck.length < 1){
                failmessage.innerText = "Ungültiges Passwort";
                failmessage.style.color = "hsl(12, 88%, 59%)";
                return;
            }
            var response = await deleteIngameConnection(pwcheck, userID, ingame_id);

            if (response.status == "success"){
                failmessage.innerText = "Erfolgreich gelöscht!"
                failmessage.style.color = "green";
                loadUserData();
            }else{
                failmessage.innerText = response.message;
                failmessage.style.color = "hsl(12, 88%, 59%)";
            }
        });

    </script>
</body>

</html>