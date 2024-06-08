<?php
include 'templates.php';
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
    <?php
    echo $header;
    ?>

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
                            <h1>Ingamename: </h1>
                            <h2>(ingametag)</h2>
                        </div>
                        <div class="split"></div>
                        <div class="centered-data-statusdata">
                            <h3 class="subheading">Stats</h3>
                            <p class="split-data-p"><i id="level" class="fa-solid fa-splotch"></i> Level: </p>
                            <p class="split-data-p"><i id="gold" class="fa-solid fa-coins"></i> Erbeutetes Gold: </p>
                            <p class="split-data-p"><i id="elexier" class="fa-solid fa-droplet"></i> Erbeutetes Elexier: </p>
                            <p class="split-data-p"><i id="dukleselexier" class="fa-solid fa-droplet"></i> Erbeutetes Dunkleselexier: </p>
                        </div>
                        <div class="split"></div>
                        <div class="mainvillage">
                            <h3 class="subheading">Hauptdorf</h3>
                            <p class="split-data-p text-center underlined">Rathauslevel: </p>
                            <p class="split-data-p text-center">Rathausverteidigung: </p>
                            <div class="mainviliage-overview">
                                <div class="display-overview">
                                    <p class="split-data-p">gewonnene Verteidigungen: </p>
                                    <p class="split-data-p">gewonnene Angriffe: </p>
                                </div>
                                <div class="display-overview">
                                    <p class="split-data-p">Trophäen: </p>
                                    <p class="split-data-p">Trophäenrekord: </p>
                                </div>
                            </div>
                            <div class="hero-list">
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">Barbarenkönig</p>
                                    <p class="split-data-p">Level: </p>
                                    <p class="split-data-p">maxLevel</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p text-center">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 1: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 2: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel: </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">Bogenschützenkönigin</p>
                                    <p class="split-data-p">Level</p>
                                    <p class="split-data-p">maxLevel</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p text-center">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 1: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 2: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel: </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">Großer Wächter</p>
                                    <p class="split-data-p">Level</p>
                                    <p class="split-data-p">maxLevel</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p text-center">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 1: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">Slot 2: </p>
                                            <p class="split-data-p">Level: </p>
                                            <p class="split-data-p">maxLevel: </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="builderbase">
                            <h3 class="subheading">Nachttdorf</h3>
                            <p class="split-data-p text-center underlined">Meisterhütte: </p>
                            <div class="builderbase-stats">
                                <div>
                                    <p class="split-data-p text-center"><i class="fa-solid fa-trophy"></i> Tropähen</p>
                                    <p class="split-data-p text-center"></p>
                                </div>
                                <div>
                                    <p class="split-data-p text-center">Liga</p>
                                    <p class="split-data-p text-center"></p>
                                </div>
                                <div>
                                    <p class="split-data-p text-center"><i class="fa-solid fa-trophy"></i> Trophäenrekord</p>
                                    <p class="split-data-p text-center"></p>
                                </div>
                            </div>
                            <div class="builder-heros">
                                <p class="split-data-p underlined">Helden</p>
                                <div class="builder-hero-data">
                                    <div>
                                        <p class="split-data-p underlined">Kampfmaschine:</p>
                                        <p class="split-data-p">Level: </p>
                                        <p class="split-data-p">maxLevel: </p>
                                    </div>
                                    <div>
                                        <p class="split-data-p underlined">Kampfschrauber: </p>
                                        <p class="split-data-p">Level: </p>
                                        <p class="split-data-p">maxLevel: </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="clan-splitdata">
                            <h3 class="subheading">Clan</h3>
                            <div class="clan-splitdata-overview">
                                <div>
                                    <img class="clan-badge" src="${
    playerData.clan.badgeUrls.medium
  }" alt="Clan Badge">
                                </div>
                                <div>
                                    <p class="split-data-p">Name: </p>
                                    <p class="split-data-p">Clanschlüssel: </p>
                                    <p class="split-data-p">Level: </p>
                                </div>
                            </div>
                            <div>
                                <div class="clan-splitdata-overview">
                                    <p class="split-data-p">Rolle: </p>
                                    <p class="split-data-p">Clanstadtbeitrag: </p>
                                </div>
                                <div class="clan-splitdata-overview">
                                    <div>
                                        <p class="split-data-p">Spenden: </p>
                                        <p class="split-data-p">erhaltene Spenden: </p>
                                    </div>
                                    <div>
                                        <p class="split-data-p"><i id="star" class="fa-solid fa-star"></i> Klankriege: </p>
                                        <p class="split-data-p"><i id="star" class="fa-solid fa-star"></i> Kriegssterne: </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="split-data-mainvillage-troops-container">
                            <h3 class="subheading">Truppen</h3>
                            <div class="split-data-mainvillage-troops">
                                <div class="mainvillage-troops">
                                    test1
                                </div>
                                <div class="mainvillage-troops">
                                    test23
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="split-data-mainvillage-troops-container">
                            <h3 class="subheading">Zauber</h3>
                            <div class="split-data-mainvillage-troops">
                                <div class="mainvillage-troops">
                                    test1
                                </div>
                                <div class="mainvillage-troops">
                                    test23
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="split-data-mainvillage-troops-container">
                            <h3 class="subheading">Truppen Nachtdorf</h3>
                            <div class="split-data-mainvillage-troops">
                                <div class="mainvillage-troops">
                                    test1
                                </div>
                                <div class="mainvillage-troops">
                                    test23
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="split-data-mainvillage-troops-container">
                            <h3 class="subheading">Achievements</h3>
                            <div class="split-data-mainvillage-troops">
                                <div class="mainvillage-troops">
                                    test1
                                </div>
                                <div class="mainvillage-troops">
                                    test23
                                </div>
                            </div>
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
                            <p class="split-data-p">explevel: </p>
                            <p class="split-data-p">goldfarmed: </p>
                            <p class="split-data-p">elexfarmed: </p>
                            <p class="split-data-p">darkelexfarmed: </p>
                        </div>
                        <div class="split"></div>
                        <div class="mainvillage">
                            <h3 class="subheading">Hauptdorf</h3>
                            <p class="split-data-p text-center underlined">Townhalllevel</p>
                            <p class="split-data-p text-center">TownhallWeponlevel</p>
                            <div class="mainviliage-overview">
                                <div class="display-overview">
                                    <p class="split-data-p">defensewins</p>
                                    <p class="split-data-p">attackwins</p>
                                </div>
                                <div class="display-overview">
                                    <p class="split-data-p">trophäen</p>
                                    <p class="split-data-p">bestentrophäen</p>
                                </div>
                            </div>
                            <div class="hero-list">
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">King</p>
                                    <p class="split-data-p">lvl</p>
                                    <p class="split-data-p">maxlvl</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">1: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">2: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl: </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">Queen</p>
                                    <p class="split-data-p">lvl</p>
                                    <p class="split-data-p">maxlvl</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">1: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">2: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl: </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-overview">
                                    <p class="split-data-p underlined">Warden</p>
                                    <p class="split-data-p">lvl</p>
                                    <p class="split-data-p">maxlvl</p>
                                    <div class="hero-equip">
                                        <p class="split-data-p">Equipment</p>
                                        <div class="equip-div">
                                            <p class="split-data-p">1: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl:</p>
                                        </div>
                                        <div class="equip-div">
                                            <p class="split-data-p">2: </p>
                                            <p class="split-data-p">lvl: </p>
                                            <p class="split-data-p">maxlvl: </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="builderbase">
                            <h3 class="subheading">Nachttdorf</h3>
                            <p class="split-data-p text-center underlined">builderhalllevel</p>
                            <div class="builderbase-stats">
                                <div>
                                    <p>Tropähen</p>
                                    <p></p>
                                </div>
                                <div>
                                    <p>bestentrophäen</p>
                                    <p></p>
                                </div>
                                <div>
                                    <p>Liga</p>
                                    <p></p>
                                </div>
                            </div>
                            <div class="builder-heros">
                                <p class="underlined">Helden</p>
                                <div class="builder-hero-data">
                                    <div>
                                        <p>Kampfmaschine</p>
                                        <p>level</p>
                                        <p>maxlvl</p>
                                    </div>
                                    <div>
                                        <p>Kampfschrauber</p>
                                        <p>level</p>
                                        <p>maxlevel</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="split"></div>
                        <div class="clan-splitdata">
                            <h3 class="subheading">Clan</h3>
                            <div class="clan-splitdata-overview">
                                <div>
                                    <img class="clan-badge" src="${
    playerData.clan.badgeUrls.medium
  }" alt="Clan Badge">
                                </div>
                                <div>
                                    <p>clan name</p>
                                    <p>clan tag</p>
                                    <p>clan level</p>
                                </div>
                            </div>
                            <div>
                                <div class="clan-splitdata-overview">
                                    <p>role</p>
                                    <p>clancontributions</p>
                                </div>
                                <div class="clan-splitdata-overview">
                                    <div>
                                        <p>donations</p>
                                        <p>donationsrecieved</p>
                                    </div>
                                    <div>
                                        <p>war pref</p>
                                        <p>warstars</p>
                                    </div>
                                </div>
                            </div>
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
    <?php
    echo $footer;
    ?>