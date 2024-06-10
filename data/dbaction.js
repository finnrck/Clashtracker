function sendRequest(action, data) {
  data.action = action;
  return fetch("/data/dbConnection.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .catch((error) => console.error("Fehler bei der Fetch-Operation:", error));
}
async function updateUserData(user_id, newDisplayname, newUsername){
  const data = {
    user_id: user_id,
    newUsername: newUsername,
    newDisplayname: newDisplayname
  }
  return await sendRequest("updateUserData", data).then((response) => {
    if (response.status === "success") {
      return response;
    } else {
      console.log(response);
      console.error("Fehler beim Einfügen des Accounts:", response.message);
      return response;
    }
  });
}

function insertNewAccount(ingameschlüssel, display_name) {
  const data = {
    ingameschlüssel: ingameschlüssel,
    display_name: display_name,
  };

  sendRequest("insertNewAccount", data).then((response) => {
    if (response.status === "success") {
      console.log("Account erfolgreich eingefügt");
    } else {
      console.error("Fehler beim Einfügen des Accounts:", response.message);
    }
  });
}

function insertApiRequest(playerData) {
  sendRequest("insertApiRequest", playerData).then((response) => {
    if (response.status === "success") {
      console.log("daten eingefügt");
    } else {
      console.error("Fehler beim speicher der Daten: ", response.message);
    }
  });
}

function displayAccData(playerData) {
  insertApiRequest(playerData);

  var playerHtml = `<div class="display-single-data">`;
  playerHtml += createHtmlforDisplay(playerData);
  playerHtml += "</div>";

  return playerHtml;
}

function getApiDates(playerID) {
  const data = {
    ingameschlüssel: playerID,
  };

  return sendRequest("getApiDates", data)
    .then((response) => {
      if (response.status === "success") {
        return response;
      } else {
        console.error("Fehler beim Abrufen der Daten:", response.message);
        return Promise.reject(
          "Fehler beim Abrufen der Daten: " + response.message
        );
      }
    })
    .catch((error) => {
      console.error("Fehler beim Abrufen der Daten:", error);
      return Promise.reject(error);
    });
}

async function displayComparisonToOldData(playerData, input) {
  playerData.input = input;

  try {
    const response = await sendRequest("getOldData", playerData);
    const alternativData = response.data;

    if (response.status === "success") {
      console.log("daten eingefügt");
    } else {
      console.error("Fehler beim speicher der Daten: ", response.message);
    }

    const html = createHtmlforComparsion(playerData, alternativData);
    return html;
  } catch (error) {
    console.error("Fehler bei der Anfrage: ", error);
    return null;
  }
}

async function displayComparisonToAltAcc(playerData, input) {
  const jsonObject = { input: input };

  try {
    const response = await sendRequest("getDataFromAcc", jsonObject);
    const alternativData = response.data;

    if (response.status === "success") {
      console.log("daten eingefügt");
    } else {
      console.error("Fehler beim Speichern der Daten: ", response.message);
    }

    const html = createHtmlforComparsion(playerData, alternativData);
    return html;
  } catch (error) {
    console.error("Fehler bei der Anfrage: ", error);
    return null;
  }
}
function formatNumberWithDots(number) {
  let numStr = number.toString();
  return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function processHeroes(element) {
  // Liste der möglichen Helden
  const possibleHeroes = [
    "Barbarian King",
    "Archer Queen",
    "Grand Warden",
    "Royal Champion",
    "Battle Machine",
    "Battle Copter",
  ];

  element.existingHeroes = [];

  for (const heroName of possibleHeroes) {
    const hero = element.heroes.find((h) => h.name === heroName);

    if (hero) {
      element.existingHeroes.push({
        name: hero.name,
        level: hero.level,
        maxLevel: hero.maxLevel,
        equipment: hero.equipment || [],
      });
    } else {
      element.existingHeroes.push({
        name: heroName,
        level: "-",
        maxLevel: "-",
        equipment: [
          {
            name: "",
            level: "-",
            maxLevel: "-",
          },
          {
            name: "",
            level: "-",
            maxLevel: "-",
          },
        ],
      });
    }
  }
}

function splitArray(array) {
  const middleIndex = Math.ceil(array.length / 2);
  const firstArray = array.slice(0, middleIndex);
  const secondArray = array.slice(middleIndex);
  return [firstArray, secondArray];
}

function splitByVillage(array) {
  const homeArray = [];
  const builderBaseArray = [];

  array.forEach((item) => {
    if (item.village === "home") {
      homeArray.push(item);
    } else if (item.village === "builderBase") {
      builderBaseArray.push(item);
    }
  });

  return {
    home: splitArray(homeArray),
    builderBase: splitArray(builderBaseArray),
  };
}

function createHtmlforComparsion(playerData, comparsionData) {
  const dataArray = [playerData, comparsionData];

  var html = `<div class="split-data">`;
  dataArray.forEach((element) => {
    html += createHtmlforDisplay(element, "split");
  });

  html += "</div>";
  return html;
}

function formatYAxis(number) {
  if (Math.abs(number) >= 1.0e9) {
    return (number / 1.0e9).toFixed(1) + " Mrd";
  } else if (Math.abs(number) >= 1.0e6) {
    return (number / 1.0e6).toFixed(1) + " Mio";
  } else if (Math.abs(number) >= 1.0e3) {
    return (number / 1.0e3).toFixed(1) + " Tsd";
  } else {
    return number.toFixed(1);
  }
}

async function createLineGraph(playerTag, goldContainer, elexierContainer, darkelexierContainer) {
  var jsonObject = {
    playerTag: playerTag,
  };

  var result = await sendRequest("getAllPlayerStats", jsonObject);
  console.log(result);

  var margin = { top: 30, right: 60, bottom: 60, left: 90 },
    width = 1200 - margin.left - margin.right, // Breite der x-Achse erhöht
    height = 300 - margin.top - margin.bottom;

  var x = d3.scaleLinear().range([0, width]);

  // Separate Skalierungen für jede Y-Achse
  var yGold = d3.scaleLinear().range([height, 0]);
  var yElexier = d3.scaleLinear().range([height, 0]);
  var yDarkelexier = d3.scaleLinear().range([height, 0]);

  var lineGold = d3.line()
    .x(function(d) { return x(d.x); })
    .y(function(d) { return yGold(d.y); });

  var lineElexier = d3.line()
    .x(function(d) { return x(d.x); })
    .y(function(d) { return yElexier(d.y); });

  var lineDarkelexier = d3.line()
    .x(function(d) { return x(d.x); })
    .y(function(d) { return yDarkelexier(d.y); });

  var svgGold = createSvg(goldContainer);
  var svgElexier = createSvg(elexierContainer);
  var svgDarkelexier = createSvg(darkelexierContainer);

  function createSvg(container) {
    return d3.select(container)
      .append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
  }

  function processDataset(datasetString) {
    dataset = JSON.parse(datasetString.data);

    var dateParts = datasetString.erstelldatum.split(' ')[0].split('-');
    var date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

    return {
      date: date.getTime(),
      gold: dataset.achievements[5].value,
      elexier: dataset.achievements[6].value,
      darkelexier: dataset.achievements[16].value
    };
  }

  var processedData = result.data.map(processDataset);

  // Extrahiere die Daten für Gold, Elexier und Dunklelexier
  var goldData = processedData.map(d => ({ x: d.date, y: d.gold }));
  var elexierData = processedData.map(d => ({ x: d.date, y: d.elexier }));
  var darkelexierData = processedData.map(d => ({ x: d.date, y: d.darkelexier }));

  x.domain([processedData[0].date, processedData[processedData.length - 1].date]);

  // Berechne die Domänen für jede Y-Achse separat
  yGold.domain([0, d3.max(goldData, function(d) { return d.y; })]);
  yElexier.domain([0, d3.max(elexierData, function(d) { return d.y; })]);
  yDarkelexier.domain([0, d3.max(darkelexierData, function(d) { return d.y; })]);

  svgGold.append("g")
    .attr("transform", "translate(0," + height + ")")
    .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%d.%m.%y"))); // Zeitformatierung hinzugefügt

  svgGold.append("g")
    .call(d3.axisLeft(yGold).tickFormat(formatYAxis)); // formatYAxis-Funktion verwenden

  svgGold.append("path")
    .datum(goldData)
    .attr("fill", "none")
    .attr("stroke", "gold")
    .attr("stroke-width", 1.5)
    .attr("d", lineGold);

  svgElexier.append("g")
    .attr("transform", "translate(0," + height + ")")
    .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%d.%m.%y"))); // Zeitformatierung hinzugefügt

  svgElexier.append("g")
    .call(d3.axisLeft(yElexier).tickFormat(formatYAxis)); // format
    svgElexier.append("path")
    .datum(elexierData)
    .attr("fill", "none")
    .attr("stroke", "rgb(165, 31, 165)")
    .attr("stroke-width", 1.5)
    .attr("d", lineElexier);

svgDarkelexier.append("g")
    .attr("transform", "translate(0," + height + ")")
    .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%d.%m.%y"))); // Zeitformatierung hinzugefügt

svgDarkelexier.append("g")
    .call(d3.axisLeft(yDarkelexier).tickFormat(formatYAxis)); // formatYAxis-Funktion verwenden

svgDarkelexier.append("path")
    .datum(darkelexierData)
    .attr("fill", "none")
    .attr("stroke", "rgb(10, 10, 56)")
    .attr("stroke-width", 1.5)
    .attr("d", lineDarkelexier);
}

function createHtmlforDisplay(element, view) {
  var view = view;
  var html = "";
  let gold = element.achievements[5].value;
  let elexier = element.achievements[6].value;
  let darkelex = element.achievements[16].value;
  let warPreference = element.warPreference;
  let townHallWeaponLevel = element.townHallWeaponLevel;
  let spells = splitArray(element.spells);
  let achievements = splitArray(element.achievements);
  if (townHallWeaponLevel == undefined) {
    townHallWeaponLevel = "-";
  }
  if (warPreference == "in") {
    warPreference = "nimmt teil";
  } else {
    warPreference = "keine Teilnahme";
  }

  processHeroes(element);
  console.log(element.troops);
  element.troops = splitByVillage(element.troops);
  console.log(element.troops);

  html += `
    <div class="centered-data border">
      <div class="centered-data-header">
        <h1>Ingamename: ${element.name} </h1>
        <h2>(${element.tag})</h2>
      </div>
      <div class="split"></div> `;
  if (view == "split") {
    html += `
      <div class="centered-data-statusdata">
        <h3 class="subheading">Stats</h3>
        <p class="split-data-p"><i id="level" class="fa-solid fa-splotch"></i> Level: ${
          element.expLevel
        }</p>
        <p class="split-data-p"><i id="gold" class="fa-solid fa-coins"></i> Erbeutetes Gold: ${formatNumberWithDots(
          gold
        )}</p>
        <p class="split-data-p"><i id="elexier" class="fa-solid fa-droplet"></i> Erbeutetes Elexier: ${formatNumberWithDots(
          elexier
        )}</p>
        <p class="split-data-p"><i id="dukleselexier" class="fa-solid fa-droplet"></i> Erbeutetes Dunkleselexier: ${formatNumberWithDots(
          darkelex
        )}</p>
      </div>`;
  } else {
    createLineGraph(
      element.tag,
      "#gold-graph",
      "#elexier-graph",
      "#darkelexier-graph"
    );
    html += `
      <div class="centered-data-statusdata">
        <h3 class="subheading">Stats</h3>
        <div class="status-data-section">
          <p class="split-data-p"><i id="level" class="fa-solid fa-splotch"></i> Level: ${
            element.expLevel
          }</p>
          <p class="split-data-p"><i id="gold" class="fa-solid fa-coins"></i> Erbeutetes Gold: ${formatNumberWithDots(
            gold
          )}</p>
          <p class="split-data-p"><i id="elexier" class="fa-solid fa-droplet"></i> Erbeutetes Elexier: ${formatNumberWithDots(
            elexier
          )}</p>
          <p class="split-data-p"><i id="dukleselexier" class="fa-solid fa-droplet"></i> Erbeutetes Dunkleselexier: ${formatNumberWithDots(
            darkelex
          )}</p>
        </div>
        <div class="graph-data-section">
          <div class="graph-container">
            <div id="gold-graph"></div>
          </div>
          <div class="graph-container">
            <div id="elexier-graph"></div>
          </div>
          <div class="graph-container">
            <div id="darkelexier-graph"></div>
          </div>
        </div>
      </div>`;
  }
  html += `
      <div class="split"></div>
      <div class="mainvillage">
        <h3 class="subheading">Hauptdorf</h3>
        <p class="split-data-p text-center underlined">Rathauslevel: ${element.townHallLevel}</p>
        <p class="split-data-p text-center">Rathausverteidigung: ${townHallWeaponLevel}</p>
        <div class="mainviliage-overview">
            <div class="display-overview">
                <p class="split-data-p">gewonnene Verteidigungen: ${element.defenseWins}</p>
                <p class="split-data-p">gewonnene Angriffe: ${element.attackWins}</p>
            </div>
            <div class="display-overview">
                <p class="split-data-p"><i class="fa-solid fa-trophy"></i>  Trophäen: ${element.trophies}</p>
                <p class="split-data-p"><i class="fa-solid fa-trophy"></i>  Trophäenrekord: ${element.bestTrophies}</p>
            </div>
        </div> `;
  if (view == "split") {
    html += `<div class="hero-list">`;
  }

  html += `<div class="split-heroes">
            <div class="hero-overview">
                <p class="split-data-p underlined">Barbarenkönig</p>
                <p class="split-data-p">Level: ${element.existingHeroes[0].level}</p>
                <p class="split-data-p">maxLevel: ${element.existingHeroes[0].maxLevel}</p>
                <div class="hero-equip">
                    <p class="split-data-p text-center">Equipment</p>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 1: ${element.existingHeroes[0].equipment[0].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[0].equipment[0].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[0].equipment[0].maxLevel}</p>
                    </div>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 2: ${element.existingHeroes[0].equipment[1].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[0].equipment[1].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[0].equipment[1].maxLevel}</p>
                    </div>
                </div>
            </div>
            <div class="hero-overview">
                <p class="split-data-p underlined">Bogenschützenkönigin</p>
                <p class="split-data-p">Level: ${element.existingHeroes[1].level}</p>
                <p class="split-data-p">maxLevel: ${element.existingHeroes[1].maxLevel}</p>
                <div class="hero-equip">
                    <p class="split-data-p text-center">Equipment</p>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 1: ${element.existingHeroes[1].equipment[0].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[1].equipment[0].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[1].equipment[0].maxLevel}</p>
                    </div>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 2: ${element.existingHeroes[1].equipment[1].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[1].equipment[1].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[1].equipment[1].maxLevel}</p>
                    </div>
                </div>
            </div>`;
  if (view == "split") {
    html += `</div>
            <div class="split-heroes">`;
  }
  html += `
            <div class="hero-overview">
                <p class="split-data-p underlined">Großer Wächter</p>
                <p class="split-data-p">Level: ${element.existingHeroes[2].level}</p>
                <p class="split-data-p">maxLevel: ${element.existingHeroes[2].maxLevel}</p>
                <div class="hero-equip">
                    <p class="split-data-p text-center">Equipment</p>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 1: ${element.existingHeroes[2].equipment[0].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[2].equipment[0].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[2].equipment[0].maxLevel}</p>
                    </div>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 2: ${element.existingHeroes[2].equipment[1].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[2].equipment[1].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[2].equipment[1].maxLevel}</p>
                    </div>
                </div>
            </div>
            <div class="hero-overview">
                <p class="split-data-p underlined">Königliche Gladiatorin</p>
                <p class="split-data-p">Level: ${element.existingHeroes[3].level}</p>
                <p class="split-data-p">maxLevel: ${element.existingHeroes[3].maxLevel}</p>
                <div class="hero-equip">
                    <p class="split-data-p text-center">Equipment</p>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 1: ${element.existingHeroes[3].equipment[0].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[3].equipment[0].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[3].equipment[0].maxLevel}</p>
                    </div>
                    <div class="equip-div">
                        <p class="split-data-p">Slot 2: ${element.existingHeroes[3].equipment[1].name}</p>
                        <p class="split-data-p">Level: ${element.existingHeroes[3].equipment[1].level}</p>
                        <p class="split-data-p">maxLevel: ${element.existingHeroes[3].equipment[1].maxLevel}</p>
                    </div>
                </div>`;
  if (view == "split") {
    html += `</div>`;
  }
  html += `
            </div>
        </div>
    </div>
    <div class="split"></div>
    <div class="builderbase">
        <h3 class="subheading">Nachttdorf</h3>
        <p class="split-data-p text-center underlined">Meisterhütte: </p>
        <div class="builderbase-stats">
            <div>
                <p class="split-data-p text-center">Tropähen</p>
                <p class="split-data-p text-center"><i class="fa-solid fa-trophy"></i>  ${
                  element.builderBaseTrophies
                }</p>
            </div>
            <div>
                <p class="split-data-p text-center">Liga</p>
                <p class="split-data-p text-center">${
                  element.builderBaseLeague.name
                }</p>
            </div>
            <div>
                <p class="split-data-p text-center">Trophäenrekord</p>
                <p class="split-data-p text-center"><i class="fa-solid fa-trophy"></i>  ${
                  element.bestBuilderBaseTrophies
                }</p>
            </div>
        </div>
        <div class="builder-heros">
            <p class="split-data-p underlined">Helden</p>
            <div class="builder-hero-data">
                <div>
                    <p class="split-data-p underlined">Kampfmaschine: </p>
                    <p class="split-data-p">Level: ${
                      element.existingHeroes[4].level
                    }</p>
                    <p class="split-data-p">maxLevel: ${
                      element.existingHeroes[4].maxLevel
                    }</p>
                </div>
                <div>
                    <p class="split-data-p underlined">Kampfschrauber: </p>
                    <p class="split-data-p">Level: ${
                      element.existingHeroes[5].level
                    }</p>
                    <p class="split-data-p">maxLevel: ${
                      element.existingHeroes[5].maxLevel
                    }</p>
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
                  element.clan.badgeUrls.medium
                }" alt="Clan Badge">
            </div>
            <div>
                <p class="split-data-p">Name: ${element.clan.name}</p>
                <p class="split-data-p">Clanschlüssel: ${element.clan.tag}</p>
                <p class="split-data-p">Level: ${element.clanLevel}</p>
            </div>
        </div>
        <div>
            <div class="clan-splitdata-overview">
                <p class="split-data-p">Rolle: ${element.role}</p>
                <p class="split-data-p">Clanstadtbeitrag: ${formatNumberWithDots(
                  element.clanCapitalContributions
                )}</p>
            </div>
            <div class="clan-splitdata-overview">
                <div>
                    <p class="split-data-p">Spenden: ${element.donations}</p>
                    <p class="split-data-p">erhaltene Spenden: ${
                      element.donationsReceived
                    }</p>
                </div>
                <div>
                    <p class="split-data-p"><i id="star" class="fa-solid fa-star"></i>  Klankriege: ${warPreference}</p>
                    <p class="split-data-p"><i id="star" class="fa-solid fa-star"></i>  Kriegssterne: ${
                      element.warStars
                    }</p>
                </div>
            </div>
        </div>
    </div>
    <div class="split"></div>
    <div class="split-data-mainvillage-troops-container">
        <h3 class="subheading">Truppen</h3>
        <div class="split-data-mainvillage-troops height-160">
            <div class="mainvillage-troops">
              <ul class="split-data-list">
                ${element.troops.home[0]
                  .map(
                    (troop) => `
                      <li class="split-data-list-item">
                        <p>${troop.name}</p>
                        <p>Level: ${troop.level} / ${troop.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
            <div class="mainvillage-troops">
                <ul class="split-data-list">
                ${element.troops.home[1]
                  .map(
                    (troop) => `
                      <li class="split-data-list-item">
                        <p>${troop.name}</p>
                        <p>Level: ${troop.level} / ${troop.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
        </div>
    </div>
    <div class="split"></div>
    <div class="split-data-mainvillage-troops-container">
        <h3 class="subheading">Zauber</h3>
        <div class="split-data-mainvillage-troops height-80">
            <div class="mainvillage-troops">
              <ul class="split-data-list">
                ${spells[0]
                  .map(
                    (spell) => `
                      <li class="split-data-list-item">
                        <p>${spell.name}</p>
                        <p>Level: ${spell.level} / ${spell.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
            <div class="mainvillage-troops">
                <ul class="split-data-list">
                ${spells[1]
                  .map(
                    (spell) => `
                      <li class="split-data-list-item">
                        <p>${spell.name}</p>
                        <p>Level: ${spell.level} / ${spell.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
        </div>
    </div>
    <div class="split"></div>
    <div class="split-data-mainvillage-troops-container">
        <h3 class="subheading">Truppen Nachtdorf</h3>
        <div class="split-data-mainvillage-troops height-80">
            <div class="mainvillage-troops">
              <ul class="split-data-list">
                ${element.troops.builderBase[0]
                  .map(
                    (troop) => `
                      <li class="split-data-list-item">
                        <p>${troop.name}</p>
                        <p>Level: ${troop.level} / ${troop.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
            <div class="mainvillage-troops">
                <ul class="split-data-list">
                ${element.troops.builderBase[1]
                  .map(
                    (troop) => `
                      <li class="split-data-list-item">
                        <p>${troop.name}</p>
                        <p>Level: ${troop.level} / ${troop.maxLevel}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
        </div>
    </div>
    <div class="split"></div>
    <div class="split-data-mainvillage-troops-container">
        <h3 class="subheading">Achievements</h3>
        <div class="split-data-mainvillage-troops height-320">
            <div class="mainvillage-troops">
              <ul class="split-data-list">
                ${achievements[0]
                  .map(
                    (achievement) => `
                      <li class="split-data-list-item">
                        <p>${achievement.name}</p>
                        <p>Sterne: ${achievement.stars} / 3</p>
                        <p>Info: ${achievement.info} </p>
                        <p>${achievement.value} / ${achievement.target}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
            <div class="mainvillage-troops">
                <ul class="split-data-list">
                ${achievements[1]
                  .map(
                    (achievement) => `
                      <li class="split-data-list-item">
                        <p>${achievement.name}</p>
                        <p>Sterne: ${achievement.stars} / 3</p>
                        <p>Info: ${achievement.info} </p>
                        <p>${achievement.value} / ${achievement.target}</p>
                    `
                  )
                  .join("")}
              </ul>
            </div>
        </div>
    </div>
</div>`;
  return html;
}
