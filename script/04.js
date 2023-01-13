//----------------JavaScript für fs-hilfe Overlay, genutzt in 02, 03, 04, 05-----------------------------//
//Overlay auswählen
var fsHilfe = document.getElementById("fsHilfe");

//Das öffnet das Overlay
var openHelp = document.getElementById("openHelp");

//Das schließt das Overlay
var exitHilfe = document.getElementsByClassName("allesklarButton")[0];

//Öffnen wenn icon geklickt wird
openHelp.onclick = function () {
  fsHilfe.style.display = "block";
};

//Schließen nach Button drücken
exitHilfe.onclick = function () {
  fsHilfe.style.display = "none";
};

//----------------JavaScript für fs-uebersicht-abbr Overlay, genutzt in 02, 04 -----------------------------//
//Overlay auswählen
var fsUebersichtAbbr = document.getElementById("fsUebersichtAbbr");

//Das öffnet das Overlay
var openUebersichtAbbr = document.getElementById("openUebersichtAbbr");

//Das schließt das Overlay
var exitUebersichtAbbr = document.getElementsByClassName("exitButton")[0];

//Leitet auf Startseite weiter
var nextUebersichtAbbr = document.getElementsByClassName("nextButton")[0];

//Öffnen wenn icon geklickt wird
openUebersichtAbbr.onclick = function () {
  fsUebersichtAbbr.style.display = "block";
};

//Schließen nach Button drücken
exitUebersichtAbbr.onclick = function () {
  fsUebersichtAbbr.style.display = "none";
};

//Weiterleitung zur Startseite
nextUebersichtAbbr.onclick = function () {
  fsUebersichtAbbr.style.display = "none";
  // window.open(href = "../index.php");
};


//----------------JavaScript für fs-lm-verstauen Overlay, genutzt in 04 -----------------------------//
//Overlay auswählen
var fsLmVerstauen = document.getElementById("fsLmVerstauen");

//Das öffnet das Overlay
var openLmVerstauen = document.getElementById("openLmVerstauen");

//Das schließt das Overlay
var exitLmVerstauen = document.getElementsByClassName("allesklarButton")[0];

//Öffnen wenn icon geklickt wird
openLmVerstauen.onclick = function () {
  fsLmVerstauen.style.display = "block";
};

//Schließen nach Button drücken
exitLmVerstauen.onclick = function () {
  fsLmVerstauen.style.display = "none";
};

function abbr() {
  session_destroy();
  header("Location: ../index.php");
}

// var editButton = document.getElementById("editButton");
// editButton.onclick = function() {
//   var item = array[key];
//   var itemName = item.Lebensmittel;
//   var itemKistennr = item.Kistennr;
//   console.log(itemName);
// }

var fsLmLoeschen = document.getElementById("fsLmLoeschen");

let anmerkungen = false
let bearbeiten = false

// console.log(array[1].id);

function changeAnmerkung(id){
  if (bearbeiten == false) {
    if (anmerkungen == false){
      anmerkungen = true;
      document.getElementById("anmerkungButton:" + id).setAttribute("src", "../media/cross.svg");
      document.getElementById("overlay:" + id).style.display = "block";
    } else {
      anmerkungen = false;
      document.getElementById("anmerkungButton:" + id).setAttribute("src", "../media/comment_icon.svg");
      document.getElementById("overlay:" + id).style.display = "none";
    }
  }
}

function changeBearbeiten(id){
  if (anmerkungen == false){
    if (bearbeiten == false){
      bearbeiten = true;
      document.getElementById("editButton:" + id).setAttribute("src", "../media/cross.svg");
      document.getElementById("overlayBearbeiten:" + id).style.display = "block";
    } else {
      bearbeiten = false;
      document.getElementById("editButton:" + id).setAttribute("src", "../media/edit_icon.svg");
      document.getElementById("overlayBearbeiten:" + id).style.display = "none";
    }
  }
}


function openLoeschen(id) {
  console.log(document.getElementById("overlayLoeschen:" + id))
  document.getElementById("overlayLoeschen:" + id).style.display = "block";
  document.getElementById("grauer-hintergrund").style.display = "block";
}

function loeschenAbbr(id) { 
  console.log("clickAbbr");
  document.getElementById("overlayLoeschen:" + id).style.display = "none";
}

// Schließen wenn außerhalb des Pop-ups gedrückt wird
var popups = document.getElementsByClassName("popup");
var triggerIcons = document.getElementsByClassName("open_icon");
window.onclick = function (event) {
  if (event.target.classList[0] != "open_icon") {
    console.log("click")
    for (let item of popups) {
      for (let trigger of triggerIcons) {
        if (trigger.id.length < 38) {
          bearbeiten = false;
          document.getElementById(trigger.id).setAttribute("src", "../media/edit_icon.svg");
        } else {
          anmerkungen = false;
          document.getElementById(trigger.id).setAttribute("src", "../media/comment_icon.svg");
        } 
      }
      item.style.display = "none";
    }
  }
};


