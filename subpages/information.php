<?php
session_start();
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
                                    <img class="data-icon-header" src="/images/SVG/Icon 62.svg" alt="">Account Manager</a></li>
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
                        <!--drei punkte :hover // on klick-->
                    </nav>
                </div>
                <div class="account-button">
                    <div id="loggedin" class="profile-box invisible"> <!-- link liste etc-->
                        <span class="profile-data">
                            <?php
                            echo $_SESSION["username"];
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
</body>

</html>