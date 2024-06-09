<?php
ob_start();
?>
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
                        <a class="dropdown-item" href="/subpages/accmanager.php">Account Manager</a>
                        <a class="dropdown-item" href="/subpages/accountsettings.php">Settings</a>
                        <div class="dropdown-item-button">
                            <button class="button" id="Logout-button">log out</button>
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
<?php
$header = ob_get_clean();

ob_start();
?>
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
<?php
$footer = ob_get_clean();
?>