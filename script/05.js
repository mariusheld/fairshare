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
