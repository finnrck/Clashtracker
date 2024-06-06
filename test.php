<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Home</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css" />
    <script src="https://kit.fontawesome.com/b2c4ecfb6d.js" crossorigin="anonymous"></script>
    <script src="/script.js" defer></script>
    <script src="/data/request.js"></script>
    <script src="/data/dbaction.js"></script>
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
        <aside class="sidebar">
            <h1 class="sidebar-header">Verbundene Ingame-Konten</h1>
            <div id="overflow" class="overflow">
                <!-- wird von scripts gefüllt -->
            </div>
            <div class="button-center">
                <button id="add-acc-btn" class="button">Account hinzufügen</button>
            </div>
        </aside>
        <div id="manager-main" class="manager-content">
            <div class="stats-container ">
                <h1 id="current-ingame-key-header" class="sidebar-header managerstat-header">Kein Account zur Datenabfrage gefunden</h1>

                <div class="checkbox-section">
                    <div class="checkbox-container">
                        <label>
                            <input class="checkbox-input" type="checkbox" id="checkbox1"> Vergleich mit älterem Sielstand
                        </label>
                        <input class="checkbox-input" type="text" id="input1" placeholder="YYYY-MM-DD" disabled>
                        <div id="dropdown1" class="dropdown-accmanager invisible"></div>
                    </div>
                    <div class="checkbox-container">
                        <label>
                            <input class="checkbox-input" type="checkbox" id="checkbox2"> Vergleich mit einem anderem Account
                        </label>
                        <input class="checkbox-input" type="text" id="input2" placeholder="ID oder Anzeigename" disabled>
                        <div id="dropdown2" class="dropdown-accmanager invisible"></div>
                    </div>
                </div>


                <!--Hier werden daten automatisch geladen-->
                <div id="current-ingame-key" class="split-data overflow">
                    <div class="centered-data border">
                        <div class="centered-data-header">
                            <h1>Ingamename</h1>
                            <h2>ingametag</h2>
                        </div>
                        <div class="split"></div>
                        <div class="centered-data-statusdata">
                            <h3 class="subheading">Stats</h3>
                            <p>explevel: </p>
                            <p>goldfarmed: </p>
                            <p>elexfarmed: </p>
                            <p>darkelexfarmed: </p>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Hauptdorf</h3>
                            <p>Townhalllevel</p>
                            <div class="mainviliage-overview">
                                <div class="display-overview">
                                    <p>defensewins</p>
                                    <p>attackwins</p>
                                </div>
                                <div class="display-overview">
                                    <p>trophäen</p>
                                    <p>bestentrophäen</p>
                                </div>
                            </div>
                            <div class="hero-list">
                                <div class="hero-overview">
                                    <p>King</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                </div>
                                <div class="hero-overview">
                                    <p>Queen</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                </div>
                                <div class="hero-overview">
                                    <p>Warden</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                    <div>
                                        <p>Equipment</p>
                                        <div>
                                            <p>1: </p>
                                            <p>lvl: </p>
                                            <p>maxlvl:</p>
                                        </div>
                                        <div>
                                            <p>2: </p>
                                            <p>lvl: </p>
                                            <p>maxlvl: </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Nachttdorf</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Clan</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Truppen</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Zauber</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Truppen Nachtdorf</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Achievements</h3>
                        </div>
                    </div>
                    <div class="centered-data">
                        <div class="centered-data-header">
                            <h1>Ingamename</h1>
                            <h2>ingametag</h2>
                        </div>
                        <div class="split"></div>
                        <div class="centered-data-statusdata">
                            <h3 class="subheading">Stats</h3>
                            <p>explevel: </p>
                            <p>goldfarmed: </p>
                            <p>elexfarmed: </p>
                            <p>darkelexfarmed: </p>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Hauptdorf</h3>
                            <p>Townhalllevel</p>
                            <div class="mainviliage-overview">
                                <div class="display-overview">
                                    <p>defensewins</p>
                                    <p>attackwins</p>
                                </div>
                                <div class="display-overview">
                                    <p>trophäen</p>
                                    <p>bestentrophäen</p>
                                </div>
                            </div>
                            <div class="hero-list">
                                <div class="hero-overview">
                                    <p>King</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                </div>
                                <div class="hero-overview">
                                    <p>Queen</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                </div>
                                <div class="hero-overview">
                                    <p>Warden</p>
                                    <p>lvl</p>
                                    <p>maxlvl</p>
                                    <div>
                                        <p>Equipment</p>
                                        <div>
                                            <p>1: </p>
                                            <p>lvl: </p>
                                            <p>maxlvl:</p>
                                        </div>
                                        <div>
                                            <p>2: </p>
                                            <p>lvl: </p>
                                            <p>maxlvl: </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Nachttdorf</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Clan</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Truppen</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Zauber</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Truppen Nachtdorf</h3>
                        </div>
                        <div class="split"></div>
                        <div>
                            <h3 class="subheading">Achievements</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="add-acc-box" class="add-acc invisible">
            <div class="add-acc-header">
                <h1>Hinzufügen eines neuen Accounts</h1> <!--TODO fragezeichen symbol für erklärung -->
                <button id="closeing-btn"><i id="closing-acc" class="fa-solid fa-xmark fa-4x"></i></button>
            </div>
            <div class="acc-inputbox">
                <form class="acc-inputform" action="" method="POST" id="registerForm">
                    <input name="register-IngameKey" type="text" placeholder="IngameID" oninput="convertToUpperCase(this)">
                    <input id="display_name" name="display-name" type="text" placeholder="Anzeigename">
                    <input id="token" name="verify-token" type="text" placeholder="VerifizierungsToken">
                    <button type="submit" class="button">
                        <p>Link Account</p>
                    </button>
                </form>
                <div id="failmassage">

                </div>
            </div>
        </div>
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