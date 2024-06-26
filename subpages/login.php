<?php
session_start();
include "../templates.php";

$response = [
    "success" => false,
    "message" => "",
    "redirect" => ""
];

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once("../config.php");

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        $response["message"] = "Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error();
        echo json_encode($response);
        exit();
    }

    $action = $_POST["action"];
    $benutzername = $_POST["BenutzerName"];
    $password = $_POST["pw"];
    $redirect_url = $_SESSION["redirect_url"] ?? "/index.php"; //gesetzte url nach erfolgreichem login / registrierung oder startseite

    if ($action == "login") {
        if (filter_var($benutzername, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email = ?";
        } else {
            $sql = "SELECT * FROM users WHERE username = ?";
        }

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $benutzername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["displayname"] = $row["displayname"];

                $response["success"] = true;
                $response["message"] = "Login erfolgreich";
                $response["redirect"] = $redirect_url;
            } else {
                $response["message"] = "Login fehlgeschlagen: Falsches Passwort";
            }
        } else {
            $response["message"] = "Login fehlgeschlagen: Benutzer nicht gefunden";
        }
    } elseif ($action == "register") {
        $email = $_POST["Email"];
        $passwordCheck = $_POST["pwCheck"];

        if (strlen($benutzername) < 4) {
            $response["message"] = "Registrierung fehlgeschlagen! Benutzername muss mindestens 4 Buchstaben haben!";
            echo json_encode($response);
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["message"] = "Registrierung fehlgeschlagen! Ungültige Email-Adresse!";
            echo json_encode($response);
            exit();
        }
        if ($password !== $passwordCheck) {
            $response["message"] = "Registrierung fehlgeschlagen! Pw nicht gleich!";
            echo json_encode($response);
            exit();
        }
        if (
            strlen($password) < 6 ||
            !preg_match("/[A-Z]/", $password) ||
            !preg_match("/[a-z]/", $password) ||
            !preg_match("/\d/", $password) ||
            !preg_match("/[^a-zA-Z\d]/", $password)
        ) {
            $response["message"] = "Registrierung fehlgeschlagen! Passwort muss 6 Zeichen lang sein (min.: 1x( G ; g ; 0 ; ! )";
            echo json_encode($response);
            exit();
        }


        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $benutzername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $response["message"] = "Registrierung fehlgeschlagen! Benutzername vergeben!";
            echo json_encode($response);
            exit();
        }

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $response["message"] = "Registrierung fehlgeschlagen! Email bereits registriert!";
            echo json_encode($response);
            exit();
        }

        $hasedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(username, email, password, displayname) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $benutzername, $email, $hasedPassword, $benutzername);

        if (mysqli_stmt_execute($stmt)) {
            $user_id = mysqli_insert_id($conn);
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $benutzername;
            $_SESSION["displayname"] = $benutzername;

            $response["success"] = true;
            $response["message"] = "Registrierung erfolgreich";

            $response["redirect"] = $redirect_url;
        } else {
            $response["message"] = "Fehler";
        }
    }
    mysqli_close($conn);
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login</title>
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
        <?php
        if (isset($_SESSION["failmessage"]) && $_SESSION["failmessage"] === "not_logged_in") {
            $warning = "Um diese Seite zu öffnen musst du eingeloggt sein.";
        } else {
            $warning = "";
        }
        ?>
        <section class="quicksearch">
            <h1 class="padding-block-700">ClashTracker Log in</h1>
            <div class="searchbox padding-block-700">
                <div class="searchfield loginfield">
                    <div class="button-group">
                        <button id="loginBtn" class="active">Log in</button>
                        <button id="registerBtn" class="inactive">Registrieren</button>
                    </div>
                    <div class="infoinput">
                        <div id="login" class="submit-box submit-active">
                            <form id="login-form" action="" method="POST">
                                <input name="action" type="hidden" value="login">
                                <input name="BenutzerName" type="text" placeholder="Benutzername" required>
                                <input name="pw" type="password" placeholder="Passwort" required>
                                <div class="submit-button">
                                    <button type="submit">Bestätigen</button>
                                </div>
                            </form>
                        </div>
                        <div id="register" class="submit-box submit-inactive">
                            <form id="register-form" action="" method="POST">
                                <input name="action" type="hidden" value="register">
                                <input name="BenutzerName" type="text" placeholder="Benutzername" required>
                                <input name="Email" type="text" placeholder="E-Mail" required>
                                <input name="pw" type="password" placeholder="Passwort" required>
                                <input name="pwCheck" type="password" placeholder="Passwort bestätigen" required>
                                <div class="submit-button">
                                    <button type="submit">Bestätigen</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="failmassage-box">
                        <p id="failmassage"></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    echo $footer;
    ?>
    <script>
        const warning = "<?php echo $warning ?>";
        if (warning === "") {
            document.getElementById("failmassage").innerText = warning;
            document.getElementById("failmassage").style.backgroundColor = "none";
        } else {
            document.getElementById("failmassage").innerText = warning;
            document.getElementById("failmassage").style.backgroundColor = "hsl(225, 6.3%, 12.5%)";
        }

        document.addEventListener("DOMContentLoaded", function() {
            const loginForm = document.getElementById("login-form");
            const registerForm = document.getElementById("register-form");

            loginForm.addEventListener("submit", function(event) {
                event.preventDefault();
                const formData = new FormData(loginForm);
                sendForm(formData);
            });

            registerForm.addEventListener("submit", function(event) {
                event.preventDefault();
                const formData = new FormData(registerForm);
                sendForm(formData);
            });

            function sendForm(formData) {
                fetch("login.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            //fehlermeldung als display unter button

                            document.getElementById("failmassage").innerText = data.message;
                            document.getElementById("failmassage").style.backgroundColor = "hsl(225, 6.3%, 12.5%)";
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        });
    </script>
</body>

</html>