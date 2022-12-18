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

// Schließen wenn außerhalb des Pop-ups gedrückt wird
//window.onclick = function (event) {
//if (event.target == fsHilfe) {
//fsHilfe.style.display = "none";
//}
//};

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

// Schließen wenn außerhalb des Pop-ups gedrückt wird
//window.onclick = function (event) {
//if (event.target == fsUebersichtAbbr) {
//fsUebersichtAbbr.style.display = "none";
//}
//};

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

// Schließen wenn außerhalb des Pop-ups gedrückt wird
// window.onclick = function (event) {
//   if (event.target == fsLmVerstauen) {
//     fsLmVerstauen.style.display = "none";
//   }
// };

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


//----------------JavaScript für fs-anmerkungen-allergene Overlay, genutzt in 04 -----------------------------//
//Overlay auswählen

      
//Das öffnet das Overlay
//var openAnmerkungAllergene = document.getElementById("openAnmerkungAllergene");
      
//Das schließt das Overlay
//var exitAnmerkungAllergene = document.getElementsByClassName("close")[0];
      
//Öffnen wenn icon geklickt wird
//openAnmerkungAllergene.onclick = function() {
//fsAnmerkungAllergene.style.display = "block";
//}
      
//Schließen nach Button drücken
//exitAnmerkungAllergene.onclick = function() {
//fsAnmerkungAllergene.style.display = "none";
//}

var fsLmLoeschen = document.getElementById("fsLmLoeschen");

let anmerkungen = false
let bearbeiten = false

// console.log(array[1].id);

function changeAnmerkung(id){
  if (anmerkungen == false){
    anmerkungen = true;
    document.getElementById(id).setAttribute("src", "../media/cross.svg");
    document.getElementById("overlay:" + id).style.display = "block";
  } else {
    anmerkungen = false;
    document.getElementById(id).setAttribute("src", "../media/comment_icon.svg");
    document.getElementById("overlay:" + id).style.display = "none";
  }
}

function changeBearbeiten(id){
  if (bearbeiten == false){
    bearbeiten = true;
    document.getElementById("editButton:" + id).setAttribute("src", "../media/cross.svg");
    document.getElementById("overlayLoeschen:" + id).style.display = "block";
  } else {
    bearbeiten = false;
    document.getElementById("editButton:" + id).setAttribute("src", "../media/edit_icon.svg");
    document.getElementById("overlayLoeschen:" + id).style.display = "none";
  }
}