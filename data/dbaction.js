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

  //TODO vollüberarbeitung
  var playerHtml = `
<div class="player-container">
  <div class="player-header">
    <h1 class="player-name">${playerData.name}</h1>
  </div>
  <div class="player-details">
    <div class="player-info">
      <p class="exp-level">Experience Level: ${playerData.expLevel}</p>
      <p class="war-stars">War Stars: ${playerData.warStars}</p>
      <p class="role">Role: ${playerData.role}</p>
      <p class="clan-capital-contributions">Clan Capital Contributions: ${
        playerData.clanCapitalContributions
      }</p>
    </div>
    <div class="town-hall">
      <p>Town Hall Level: ${playerData.townHallLevel}</p>
      <p>Town Hall Weapon Level: ${playerData.townHallWeaponLevel}</p>
      <p class="trophies">Trophies: ${playerData.trophies}</p>
      <p class="best-trophies">Best Trophies: ${playerData.bestTrophies}</p>
    </div>
    <div class="builder-base">
      <p>Builder Hall Level: ${playerData.builderHallLevel}</p>
      <p>Builder Base Trophies: ${playerData.builderBaseTrophies}</p>
      <p>Best Builder Base Trophies: ${playerData.bestBuilderBaseTrophies}</p>
      <p>Builder Base League: ${playerData.builderBaseLeague.name}</p>
    </div>
  </div>
  <div class="clan-info">
  <img class="clan-badge" src="${
    playerData.clan.badgeUrls.medium
  }" alt="Clan Badge">
    <div class="clan-data">
    <h2 class="clan-name">${playerData.clan.name}</h2>
    <p class="clan-tag">${playerData.clan.tag}</p>
    <p class="clan-level">Clan Level: ${playerData.clan.clanLevel}</p>
    <p class="donations">Donations: ${playerData.donations}</p>
    <p class="donations-received">Donations Received: ${
      playerData.donationsReceived
    }</p>
    </div>
  </div>
  
  <div class="troops">
    <h3>Troops:</h3>
    <div class="troop-container">
    <ul class="troop-list">
      ${playerData.troops
        .map(
          (troop) => `
        <li class="troop">
          <p class="troop-name">${troop.name} (${troop.village})</p>
          <p class="troop-level">Level: ${troop.level} / ${troop.maxLevel}</p>
        </li>
      `
        )
        .join("")}
    </ul>
    </div>
  </div>
  <div class="achievements">
    <h3>Achievements:</h3>
    <ul>
      ${playerData.achievements
        .map(
          (achievement) => `
        <li class="achievement">
          <p class="achievement-name">${achievement.name} (${
            achievement.village
          })</p>
          <p class="achievement-stars">Stars: ${achievement.stars}</p>
          <p class="achievement-info">${achievement.info}</p>
          <p class="achievement-value">Value: ${achievement.value} / ${
            achievement.target
          }</p>
          ${
            achievement.completionInfo
              ? `<p class="achievement-completion">${achievement.completionInfo}</p>`
              : ""
          }
        </li>
      `
        )
        .join("")}
    </ul>
  </div>
</div>`;

  return playerHtml;
}

function getApiDates(playerID) {
  const data = {
    ingameschlüssel: playerID
  };

  return sendRequest("getApiDates", data).then((response) => {
    if (response.status === "success") {
        return response;
    } else {
        console.error("Fehler beim Abrufen der Daten:", response.message);
        return Promise.reject("Fehler beim Abrufen der Daten: " + response.message);
    }
}).catch(error => {
    console.error("Fehler beim Abrufen der Daten:", error);
    return Promise.reject(error);
});
}
