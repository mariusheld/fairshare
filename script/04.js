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