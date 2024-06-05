function makeApiRequest(url) {
  return new Promise((resolve, reject) => {
    fetch("/data/requesthandler.php?url=" + encodeURIComponent(url))
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          console.error("Fehler bei der API-Anfrage:", data.error);
          reject(data.error);
        } else {
          resolve(data.result);
          return data.result;
        }
        //datenverarbeitung
      })
      .catch((error) => {
        console.log("Fehler bei der API-Anfrage:", error);
      });
  });
}

async function getPlayerApiData(playerID) {
  try {
    const response = await makeApiRequest(
      "https://api.clashofclans.com/v1/players/%23" + playerID
    );
    return response;
  } catch (error) {
    console.error("Fehler beim Parsen der JSON-Antwort:", error);
  }
}

async function getRanking() {
  const apiUrls = [
    "https://api.clashofclans.com/v1/locations/32000094/rankings/clans",
    "https://api.clashofclans.com/v1/locations/32000094/rankings/players",
    "https://api.clashofclans.com/v1/locations/32000094/rankings/clans-builder-base",
    "https://api.clashofclans.com/v1/locations/32000094/rankings/players-builder-base",
    "https://api.clashofclans.com/v1/locations/32000094/rankings/capitals",
  ];

  const heading = [
    "Clan Ranking",
    "Player Ranking",
    "Clan-Builder Rangking",
    "Player-Builder Ranking",
    "Capital Ranking",
  ];

  try {
    const promises = apiUrls.map(async (url, index) => {
      const response = await makeApiRequest(url + "?limit=10");
      let data;

      if (typeof response === "object") {
        data = response;
      } else {
        data = JSON.parse(response);
      }

      const items = data.items || [];

      return items.map((item) => {
        switch (index) {
          case 0:
            return {
              name: item.name,
              clanPoints: item.clanPoints,
            };
          case 1:
            return {
              name: item.name,
              trophies: item.trophies,
            };
          case 2:
            return {
              name: item.name,
              clanBuilderBasePoints: item.clanBuilderBasePoints,
            };
          case 3:
            return {
              name: item.name,
              builderBaseTrophies: item.builderBaseTrophies,
            };
          case 4:
            return {
              name: item.name,
              clanCapitalPoints: item.clanCapitalPoints,
            };
          default:
            return {};
        }
      });
    });

    const results = await Promise.all(promises);

    const html = results
      .map((names, index) => {
        let dataKey = "";
        switch (index) {
          case 0:
            dataKey = "clanPoints";
            break;
          case 1:
            dataKey = "trophies";
            break;
          case 2:
            dataKey = "clanBuilderBasePoints";
            break;
          case 3:
            dataKey = "builderBaseTrophies";
            break;
          case 4:
            dataKey = "clanCapitalPoints";
            break;
          default:
            break;
        }
        return `<div class="ranking-list">
                          <h2>${heading[index]}</h2>
                          <ul>
                              ${names
                                .map(
                                  (item, i) =>
                                    `<li>${i + 1}: ${item.name} (${
                                      item[dataKey]
                                    } <i class="fa-solid fa-trophy"></i>)</li>`
                                )
                                .join("")}
                          </ul>
                      </div>`;
      })
      .join("");

    return html;
  } catch (error) {
    console.error("Fehler beim Abrufen der Daten:", error);
    return ""; // leer falls Fehler
  }
}

async function safeAndDisplayData(playerID){
  const response = await getPlayerApiData(playerID);

  return response;

  
}