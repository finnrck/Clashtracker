<?php
session_start();

$response = [
    "success" => false,
    "message" => "",
    "redirect" => ""
];

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
            $response["message"] = "Registrierung fehlgeschlagen! Das Passwort muss mindestens 6 Zeichen lang sein (mindestens: 1 Großbuchstabe, 1 Kleinbuchstabe, 1 Zahl & 1 Sonderzeichen";
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
        $sql = "INSERT INTO users(username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $benutzername, $email, $hasedPassword);

        if (mysqli_stmt_execute($stmt)) {
            $user_id = mysqli_insert_id($conn);
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $benutzername;

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
    <title>Home</title>


    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="/script.js" defer></script>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="/index.php">
                        <img src="/images/logo.jpg" alt="CoCTracker" class="nav-img">
                    </a>
                </div>
                <div>
                    <nav class="nav-wide">
                        <ul aria-label="Main" role="list" class="nav-list">
                            <li><a href="#">Neuigkeiten</a></li>
                            <li><a href="/subpages/information.php">Über uns</a></li>
                            <li><a href="#">Community</a></li>
                            <li><a class="closed-icon" href="/subpages/accmanager.php">
                                    <img class="data-icon-header" src="/images/SVG/Icon 62.svg" alt="">Account
                                    Manager</a></li>
                        </ul>
                    </nav>
                    <nav class="nav-mobile">
                        <span></span>
                        <ul aria-label="Main" role="list" class="nav-list">
                            <li><a href="#">Neuigkeiten</a></li>
                            <li><a href="/subpages/information.php">Über uns</a></li>
                            <li><a href="#">Community</a></li>
                            <li><a href="/subpages/accmanager.php">Account Manager</a></li>
                        </ul>
                        <!--TODO responsivesearch drei punkte :hover // on klick-->
                    </nav>
                </div>
                <div class="account-button">
                    <div id="loggedin" class="profile-box invisible"> <!-- link liste etc-->
                        <span class="profile-data">
                            <?php
                            echo $_SESSION["username"]; //TODO dropdown schöner
                            ?>
                            <img class="data-icons" src="/images/SVG/Icon 29.svg" alt="">
                        </span>
                        <div class="dropdown-content">
                            <div class="dropdown-item">
                                <a href="/subpages/accmanager.php">Account Manager</a>
                            </div>
                            <div class="dropdown-item">
                                <a href="/subpages/accountsettings.php">Settings</a>
                            </div>
                            <div class="dropdown-item">
                                <button class="dropdown-logout" id="Logout-button">log out</button>
                            </div>
                        </div>
                    </div>
                    <div id="login-button" class="login-box visible">
                        <a href="/subpages/login.php">
                            <button class="button login-button">
                                Log in
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                        </div> <!--TODO login nach registrierung-->
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

    <footer class="main-footer | padding-block-700 bg-neutral-900 text-neutral-100">
        <div class="container">
            <div class="main-footer-wrapper">
                <div class="main-footer-logo-social">
                    <ul class="social-list" role="list" aria-label="Social links">
                        <li>
                            <a aria-label="facebook" href="#">
                                <svg class="social-icon">
                                    <use xlink:href="/images/social-icons.svg#icon-facebook"></use>
                                </svg></a>
                        </li>
                        <li>
                            <a aria-label="youtube" href="#">
                                <svg class="social-icon">
                                    <use xlink:href="/images/social-icons.svg#icon-youtube"></use>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a aria-label="twitter" href="#">
                                <svg class="social-icon">
                                    <use xlink:href="/images/social-icons.svg#icon-twitter"></use>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a aria-label="pinterest" href="#">
                                <svg class="social-icon">
                                    <use xlink:href="/images/social-icons.svg#icon-pinterest"></use>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a aria-label="instragram" href="#">
                                <svg class="social-icon">
                                    <use xlink:href="/images/social-icons.svg#icon-instagram"></use>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="main-footer-nav">
                    <nav class="footer-nav">
                        <ul class="flow" style="--flow-spacer: 1em" aria-label="Footer" role="list">
                            <li><a href="/index.php">Home</a></li>
                            <li><a href="#">Neuigkeiten</a></li>
                            <li><a href="/subpages/information.php">Über uns</a></li>
                            <li><a href="#">Community</a></li>
                            <li><a href="/subpages/information.php#Impressum">Impressum</a></li>
                            <li><a href="#">Richtlinien</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="main-footer-form">
                    <div class="container newsletter">
                        <p>Konstenloser Newsletter</p>
                    </div>
                    <form action="">
                        <input type="email" />
                        <button class="button" data-shadow="none">Abo!</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer-impressum padding-block-700 container">
            <div class="footer-impressum-data">
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 29.svg" alt="">
                    <p>Finn Reinecke</p>
                </div>
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 58.svg" alt="">
                    <p>f.reinecke@schueler.lg-ks.de</p>
                </div>
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 2.svg" alt="">
                    <p>0561 940840</p>
                </div>
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 27.svg" alt="">
                    <a href="https://lg-ks.de/">Schüler des Lichtenberg Gymnasiums</a>
                </div>
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 61.svg" alt="">
                    <p>Hessen, 34132 Kassel</p>
                </div>
                <div class="impressum-data">
                    <img class="data-icons" src="/images/SVG/Icon 63.svg" alt="">
                    <p>Brückenhofstraße 88</p>
                </div>
            </div>
        </div>
    </footer>
    <script>
        const warning = "<?php echo $warning ?>";
        if (warning === "") {
            document.getElementById("failmassage").innerText = warning;
            document.getElementById("failmassage").style.backgroundColor = "hsl(225, 6.3%, 12.5%)";
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