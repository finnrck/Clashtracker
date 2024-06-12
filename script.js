const clanBtn = document.getElementById("clanBtn");
const playerBtn = document.getElementById("playerBtn");
const loginBtn = document.getElementById("loginBtn");
const login = document.getElementById("login");
const registerBtn = document.getElementById("registerBtn");
const register = document.getElementById("register");
const addAcc = document.getElementById("add-acc-btn");
const addAccbox = document.getElementById("add-acc-box");
const closeing = document.getElementById("closeing-btn");
const mngMain = document.getElementById("manager-main");
const searchinput = document.getElementById("search-input");
const changePassword = document.getElementById("change-password");
const passwordButton = document.getElementById("password-openbutton");
const settings = document.getElementById("settings");
const deleteIngame = document.getElementById("delete-ingame");
const deleteIngamebtn = document.getElementById("delete-closeing-btn");

if (deleteIngame && deleteIngamebtn){
  deleteIngamebtn.addEventListener("click", () => {
    deleteIngame.classList.add("invisible");
    settings.classList.remove("blur");
  });
}

if (changePassword && passwordButton) {
  passwordButton.addEventListener("click", () => {
    changePassword.classList.add("visible");
    changePassword.classList.remove("invisible");
    settings.classList.add("blur");
  });
}

if (closeing && changePassword) {
  closeing.addEventListener("click", () => {
    changePassword.classList.add("invisible");
    changePassword.classList.remove("visible");
    settings.classList.remove("blur");
  });
}

if (addAcc && mngMain) {
  addAcc.addEventListener("click", () => {
    addAccbox.classList.add("visible");
    addAccbox.classList.remove("invisible");
    mngMain.classList.add("blur");
  });
}
if (closeing && mngMain) {
  closeing.addEventListener("click", () => {
    addAccbox.classList.add("invisible");
    addAccbox.classList.remove("visible");
    mngMain.classList.remove("blur");
    document.getElementById("failmassage").innerText = "";
  });
}

if (clanBtn && playerBtn) {
  clanBtn.addEventListener("click", () => {
    clanBtn.classList.add("active");
    clanBtn.classList.remove("inactive");
    playerBtn.classList.add("inactive");
    playerBtn.classList.remove("active");
    searchinput.value = "";
    searchinput.setAttribute(
      "placeholder",
      "Zum Suchen nach einem Clan tippen"
    );
  });

  playerBtn.addEventListener("click", () => {
    playerBtn.classList.add("active");
    playerBtn.classList.remove("inactive");
    clanBtn.classList.add("inactive");
    clanBtn.classList.remove("active");
    searchinput.value = "";
    searchinput.setAttribute(
      "placeholder",
      "Zum Suchen nach einem Spieler tippen"
    );
  });
}

if (login && loginBtn) {
  loginBtn.addEventListener("click", () => {
    loginBtn.classList.add("active");
    loginBtn.classList.remove("inactive");
    login.classList.add("submit-active");
    login.classList.remove("submit-inactive");
    registerBtn.classList.add("inactive");
    registerBtn.classList.remove("active");
    register.classList.add("submit-inactive");
    register.classList.remove("submit-active");
  });
}
if (register && registerBtn) {
  registerBtn.addEventListener("click", () => {
    registerBtn.classList.add("active");
    registerBtn.classList.remove("inactive");
    register.classList.add("submit-active");
    register.classList.remove("submit-inactive");
    loginBtn.classList.add("inactive");
    loginBtn.classList.remove("active");
    login.classList.add("submit-inactive");
    login.classList.remove("submit-active");
  });
}

document.getElementById("Logout-button").addEventListener("click", function () {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "/subpages/logout.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      // Weiterleitung oder andere Aktionen nach dem Beenden der Session
      // Hier können Sie den Benutzer z.B. auf eine andere Seite weiterleiten
      window.location.href = "/index.php";
    }
  };
  xhr.send();
});
