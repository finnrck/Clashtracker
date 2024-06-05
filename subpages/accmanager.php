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
    $_SESSION["redirect_url"] = "";
} else {
    //weiterleitung an login + Fehlermeldung (Diese Seite nur als loggedin User nutzbar)
    $_SESSION["redirect_url"] = "/subpages/accmanager.php";
    $_SESSION["failmessage"] = "not_logged_in";
    header("Location: /subpages/login.php");
    exit();
}
$ingameKey = isset($_SESSION["ingameKey"]) ? $_SESSION["ingameKey"] : "Noch kein Account ausgewählt";
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
                <div id="current-ingame-key" class="overflow">

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

    <script>
        function convertToUpperCase(input) {
            input.value = input.value.toUpperCase();
        }

        let accKeys = [];
        let accoutput = "";
        let fE = "";
        let displayNames = [];
        let apiDates = [];

        //TODO einbindung des Datenabrufs in dbconnection.php
        function loadAccounts() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "/data/userAccounts.php", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        accKeys = data.accKeys;
                        accoutput = data.accoutput;
                        fE = data.fE;
                        displayNames = data.displayNames;
                        document.getElementById("overflow").innerHTML = accoutput;
                    } catch (error) {
                        document.getElementById("overflow").innerHTML = '<div class="button-center"><p> Keine Accounts gefunden <p><div>';
                    }
                    addEventListenersToButtons();
                } else {
                    console.error("Ein Fehler ist aufgetreten:", xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error("Request fehlgeschlagen");
            };
            xhr.send();
        }

        //wenn seite geladen wird werden alle accounts geladen
        window.onload = loadAccounts();

        function addEventListenersToButtons() {
            const accSwitchButtons = [];

            //laden der Daten für den aktiven Account
            function updateActiveButton(button) {
                accSwitchButtons.forEach(button => {
                    button.classList.add("inactive");
                    button.classList.remove("active");
                });
                button.classList.add("active");
                button.classList.remove("inactive");

                const dropdown1 = document.getElementById("dropdown1");
                const dropdown2 = document.getElementById("dropdown2");
                const checkbox1 = document.getElementById("checkbox1");
                const checkbox2 = document.getElementById("checkbox2");
                const input1 = document.getElementById("input1");
                const input2 = document.getElementById("input2");

                try {
                    dropdown1.classList.add("invisible");
                    dropdown2.classList.add("invisible");
                    checkbox1.checked = false;
                    input1.disabled = true;
                    input1.value = "";
                    checkbox2.checked = false;
                    input2.disabled = true;
                    input2.value = "";
                } catch (error) {}

                const clickedButtonId = button.id;
                const numberAtEnd = clickedButtonId.split("acc-switch-btn").pop(); //splitet die id des buttons ab
                const ingameKey = accKeys[numberAtEnd];
                const display_name = displayNames[numberAtEnd];

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "/data/processKey.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = async function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.status === "success") {

                                    const playerData = await getPlayerApiData(response.ingameKey);
                                    fetchApiDates(response.ingameKey, response.display_name);
                                    const display = displayAccData(playerData);


                                    document.getElementById("current-ingame-key").innerHTML = display;
                                    document.getElementById("current-ingame-key-header").innerText = "Stats für Ingame Konto: " + response.display_name + " (#" + response.ingameKey + ")"; // header update
                                } else {
                                    console.error("Fehler beim Aktualisieren des ingameKey: " + response.message);
                                }
                            } catch (e) {
                                console.error("Fehler beim Parsen der JSON-Antwort: ", e);
                            }
                        } else {
                            console.error("Fehler beim Server: ", xhr.status);
                        }
                    }
                };
                xhr.send("ingameKey=" + encodeURIComponent(ingameKey) + "&display_name=" + encodeURIComponent(display_name));
            }

            //erstellen der Eventlistener und speichern der buttons im accSwitchButtons array
            for (let i = 1; i <= fE; i++) {
                const x = "acc-switch-btn" + i;
                let button = document.getElementById(x);
                if (button) {
                    accSwitchButtons.push(button);
                    button.addEventListener("click", (event) => {
                        updateActiveButton(event.currentTarget);
                    });
                } else {
                    console.error(`Button mit ID ${x} wurde nicht gefunden.`);
                }
            }

            //beim laden der seite und mehr als einem verbundenen Account der erste aus dem Array (auch in der Sidebar) als aktiv dargestellt
            if (accSwitchButtons.length > 0) {
                updateActiveButton(accSwitchButtons[0]);
            }
        }

        async function fetchApiDates(ingameKey, displayname) {
            try {
                const apiDatesResponse = await getApiDates(ingameKey);
                if (apiDatesResponse) {
                    apiDates = apiDatesResponse.dates;
                    createInputSearch(ingameKey, displayname);
                }
            } catch (error) {
                console.error("Error fetching API dates:", error);
            }
        }

        function createInputSearch(ingamekey, displayname) {
            const checkbox1 = document.getElementById("checkbox1");
            const checkbox2 = document.getElementById("checkbox2");
            const input1 = document.getElementById("input1");
            const input2 = document.getElementById("input2");
            const dropdown1 = document.getElementById("dropdown1");
            const dropdown2 = document.getElementById("dropdown2");

            let data1 = [];
            let data2 = [];
            for (const key in apiDates) {
                if (Object.prototype.hasOwnProperty.call(apiDates, key)) {
                    data1.push(apiDates[key]);
                }
            }
            for (const key in displayNames) {
                if (Object.prototype.hasOwnProperty.call(displayNames, key)) {
                    data2.push(displayNames[key]);
                }
            }
            for (const key in accKeys) {
                if (Object.prototype.hasOwnProperty.call(accKeys, key)) {
                    data2.push(accKeys[key]);
                }
            }


            function filterData(data, query) {
                return data.filter(item => item.toLowerCase().includes(query.toLowerCase()));
            }

            function createDropdown(dropdown, data, input) {
                dropdown.innerHTML = "";
                data.forEach(item => {
                    const div = document.createElement("div");
                    div.innerText = item;
                    div.addEventListener("click", () => {
                        input.value = item;
                        dropdown.classList.add("invisible"); // Menü nach Auswahl wieder unsichtbar
                    });
                    dropdown.appendChild(div);
                });
                if (data.length > 5) {
                    dropdown.classList.add("dropdown-overflow");
                }
            }

            let ingameKey = ingamekey;
            let initialValue = "";

            async function processInputChange(input, displayFunction) {
                if (input !== initialValue) {
                    initialValue = input;
                    playerData = await getPlayerApiData(ingameKey);
                    const display = displayFunction(playerData, input);
                    document.getElementById("current-ingame-key").innerHTML = display;
                    document.getElementById("current-ingame-key-header").innerText = `Stats für Ingame Konto: ${displayname} (#${ingameKey}) im Vergleich zu(m) ${input}`;
                }
            }

            function addInputEventListeners(inputElement, displayFunction) {
                inputElement.addEventListener("blur", () => {
                    // Verzögern des Event-Listeners um 200 Millisekunden damit value vom dropdown ankommt
                    timeoutId = setTimeout(() => {
                        processInputChange(inputElement.value, displayFunction);
                    }, 200);
                });
                inputElement.addEventListener("keydown", (event) => {
                    if (event.key === "Enter") {
                        processInputChange(inputElement.value, displayFunction);
                    }
                });
            }
            addInputEventListeners(input1, displayComparisonToOldData);
            addInputEventListeners(input2, displayComparisonToAltAcc);

            checkbox1.addEventListener("change", async function() {
                if (this.checked) {
                    checkbox2.checked = false;
                    input2.disabled = true;
                    input2.value = "";
                    input1.disabled = false;
                } else {
                    input1.disabled = true;
                    input1.value = "";
                    dropdown1.classList.add("invisible"); // Menü verstecken Checkbox deaktiviert
                    if (initialValue != "") {
                        const playerData = await getPlayerApiData(ingameKey);
                        fetchApiDates(ingameKey, displayname);
                        const display = displayAccData(playerData);
                        document.getElementById("current-ingame-key").innerHTML = display;
                        document.getElementById("current-ingame-key-header").innerText = "Stats für Ingame Konto: " + displayname + " (#" + ingameKey + ")";
                    }
                }
            });

            checkbox2.addEventListener("change", async function() {
                if (this.checked) {
                    checkbox1.checked = false;
                    input1.disabled = true;
                    input1.value = "";
                    input2.disabled = false;
                } else {
                    input2.disabled = true;
                    input2.value = "";
                    dropdown2.classList.add("invisible"); // Menü verstecken Checkbox deaktiviert
                    if (initialValue != "") {
                        const playerData = await getPlayerApiData(ingameKey);
                        fetchApiDates(ingameKey, displayname);
                        const display = displayAccData(playerData);
                        document.getElementById("current-ingame-key").innerHTML = display;
                        document.getElementById("current-ingame-key-header").innerText = "Stats für Ingame Konto: " + displayname + " (#" + ingameKey + ")";
                    }
                }
            });

            function showDropdown(input, dropdown, data) {
                const filteredData = filterData(data, input.value);
                if (filteredData.length > 0) {
                    createDropdown(dropdown, filteredData, input);
                    dropdown.classList.remove("invisible"); // Menü anzeigen
                    const rect = input.getBoundingClientRect();
                    dropdown.style.left = `${rect.left}px`;
                    dropdown.style.top = `${rect.bottom}px`;
                } else {
                    dropdown.classList.add("invisible"); // Menü verstecken keine Ergebnisse gefunden
                }
            }
            input1.addEventListener("click", () => {
                showDropdown(input1, dropdown1, data1);
            });
            input1.addEventListener("input", () => {
                showDropdown(input1, dropdown1, data1);
            });

            input2.addEventListener("click", () => {
                showDropdown(input2, dropdown2, data2);
            });
            input2.addEventListener("input", () => {
                showDropdown(input2, dropdown2, data2);
            });

            document.addEventListener("click", (event) => {
                if (!event.target.matches("#input1")) {
                    dropdown1.classList.add("invisible"); // Menü verstecken außerhalb des Inputfeldes geklickt 
                }
                if (!event.target.matches("#input2")) {
                    dropdown2.classList.add("invisible"); // Menü verstecken außerhalb des Inputfeldes geklickt 
                }
            });
        }

        document.getElementById("registerForm").addEventListener("submit", function(event) {
            event.preventDefault(); //verhindert neuladen der seite beim absenden

            // sicherstellen das accKeys ein Array ist (um später include zu benutzten)
            // bei mehreren Accounts normalerweise bereits in Array form als einzelner Account als Object
            let accKeyArray = [];
            if (Array.isArray(accKeys)) {
                accKeyArray = accKeys;
            } else if (typeof accKeys === "object") {
                if (Object.keys(accKeys).length === 1) {
                    accKeyArray = Object.values(accKeys)[0];
                }
            } else {
                console.warn("Unerwartete Datenstruktur für accKeys:", accKeys);
            }

            const formData = new FormData(this); //daten des froms
            const data = {}; // umwandeln der fromData in eine Json struktur zur leichteren weitergabe und verarbeitung
            formData.forEach((value, key) => {
                data[key] = value;
                if (key === "register-IngameKey") {
                    ingameKey = value;
                }
                if (key === "display-name") {
                    display_name = value;
                }
            });

            if (ingameKey.startsWith("#")) {
                ingameKey = ingameKey.substring(1); // Entferne das "#" am Anfang (ingame mit # jedoch System ohne # aufgebaut)
            }

            if (accKeyArray.indexOf(ingameKey) !== -1) {
                //resetting die eingabe Value + darstellung des Fehlers
                document.getElementById("failmassage").innerText = "Dieser Account ist bereits an dein Konto verknüpft!";
                document.querySelectorAll("#registerForm input").forEach(input => {
                    input.value = "";
                });
            } else if (display_name.length < 4) {
                //restten vom displayname da dieser fehlerhaft ist + Fehlermeldung
                document.getElementById("failmassage").innerText = "Der Anzeigename muss mindestens aus 4 Zeichen bestehen";
                document.querySelectorAll("#display_name").forEach(input => {
                    input.value = "";

                });
            } else {
                fetch("/data/processVerifyToken.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "ok") { // antwort der Api nach prüfung des Token 
                            document.getElementById("failmassage").innerText = "Der Account wurde erfolgreich Verbunden";
                            document.getElementById("failmassage").style.color = "green";
                            document.querySelectorAll("#registerForm input").forEach(input => {
                                input.value = "";
                            });
                            insertNewAccount(data.ingameKey, display_name);
                            loadAccounts();
                        } else {
                            console.error("Token-Verifizierung fehlgeschlagen:", data.message);
                            //entfernen der token value da dieser sich ändert bei Fehlerhafem versuch sich zu einem Account zu linken
                            document.getElementById("failmassage").innerText = "Der Account konnte nicht gefunden werden oder der dazugehörige Token ist nicht gültig!";
                            document.querySelectorAll("#token").forEach(input => {
                                input.value = "";
                            });
                        }
                    })
                    .catch((error) => {
                        console.error("Fehler:", error);
                    });
            }
        });
    </script>
</body>

</html>