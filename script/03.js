//----------------JavaScript für fs-hilfe Overlay, genutzt in 02, 03, 04, 05-----------------------------//
//Overlay auswählen
var fsHilfe = document.getElementById("fsHilfe");

//Das öffnet das Overlay
var openHelp = document.getElementById("openHelp");

//Das schließt das Overlay
var exitHilfe = document.getElementsByClassName("allesklarButton")[0];

//Öffnen wenn icon geklickt wird
openHelp.onclick = function () {
  console.log("Button geklickt");
  fsHilfe.style.display = "block";
};

//Schließen nach Button drücken
exitHilfe.onclick = function () {
  console.log("Button geklickt");
  fsHilfe.style.display = "none";
};

// Schließen wenn außerhalb des Pop-ups gedrückt wird
//window.onclick = function (event) {
  //if (event.target == fsHilfe) {
    //fsHilfe.style.display = "none";
  //}
//};

//----------------JavaScript für fs-nicht-erlaubte-lm Overlay, genutzt in 03-----------------------------//
//Overlay auswählen
var fsNichtErlaubteLm = document.getElementById("fsNichtErlaubteLm");

//Das öffnet das Overlay
var openNichtErlaubteLm = document.getElementById("openNichtErlaubteLm");

//Das schließt das Overlay
var exitNichtErlaubteLm = document.getElementsByClassName("allesklarButton")[0];

//Öffnen wenn icon geklickt wird
openNichtErlaubteLm.onclick = function () {
  fsNichtErlaubteLm.style.display = "block";
};

//Schließen nach Button drücken
exitNichtErlaubteLm.onclick = function () {
  fsNichtErlaubteLm.style.display = "none";
};

// Schließen wenn außerhalb des Pop-ups gedrückt wird
//window.onclick = function (event) {
  //if (event.target == fsNichtErlaubteLm) {
    //fsNichtErlaubteLm.style.display = "none";
  //}
//};


//----------------JavaScript für fs-hinzufuegen-abbr Overlay, genutzt in 03-----------------------------//
//Overlay auswählen
var fsHinzufuegenAbbr = document.getElementById("fsHinzufuegenAbbr");
      
//Das öffnet das Overlay
var openHinzufuegenAbbr = document.getElementById("openHinzufuegenAbbr");
      
//Das schließt das Overlay
var exitHinzufuegenAbbr = document.getElementsByClassName("exitButton")[0];

//Leitet auf Startseite weiter
var nextHinzufuegenAbbr = document.getElementsByClassName("nextButton")[0];
      
//Öffnen wenn icon geklickt wird
openHinzufuegenAbbr.onclick = function() {
fsHinzufuegenAbbr.style.display = "block";
}
      
//Schließen nach Button drücken
exitHinzufuegenAbbr.onclick = function() {
fsHinzufuegenAbbr.style.display = "none";
}

//Weiterleitung zur Startseite
nextHinzufuegenAbbr.onclick = function() {
fsHinzufuegenAbbr.style.display = "none";
window.open('https://forum.selfhtml.org/');
}

//----------------JavaScript für Formular-InfoPopups, genutzt in 03-----------------------------//
//List of OpenIcon HTML Elements
var openIcon = document.getElementsByClassName("open_icon");
//Overlay SchließenIcon
var closeIcon = document.getElementsByClassName("close_icon");
//Overlay auswählen
var hilfeKategorien = document.getElementById("hilfeKategorien");
var hilfeMenge = document.getElementById("hilfeMenge");
var hilfeAllergene = document.getElementById("hilfeAllergene");

//Öffnen wenn icon geklickt wird
for (let item of openIcon) {
  item.onclick = function (event) {
    var element = document.getElementById(event.target.classList[1]);
    element.style.display = "block";
    item.style.display = "none";
  };
}
// Schließen wenn außerhalb des Pop-ups gedrückt wird
window.onclick = function (event) {
  if (event.target.classList[0] != "open_icon") {
    hilfeKategorien.style.display = "none";
    hilfeMenge.style.display = "none";
    hilfeAllergene.style.display = "none";
    openIcon[0].style.display = "block";
    openIcon[1].style.display = "block";
    openIcon[2].style.display = "block";
  }
};
