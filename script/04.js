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

//----------------Öffnen uns schließen der Bearbeiten und Anmerkung/Allergene Overlays-----------------------------//

var old = null;

function open_close_options(options_btn) {
    
  // Kein Pop-up ist geöffnet
    if (old == null) {
        options_btn.src = '../media/cross.svg';
        options_btn.nextElementSibling.style.display = 'flex';
        old = options_btn;

  // Geöffnetes Pop-up wird wieder geschlossen
    } else if (old == options_btn){
        if(options_btn.id == "bubble") {
            options_btn.src = '../media/comment_icon.svg';
            options_btn.nextElementSibling.style.display = 'none';
            old = null;
        } else {
            options_btn.src = '../media/edit_icon.svg';
            options_btn.nextElementSibling.style.display = 'none';
            old = null;
        } 

  // Geöffnetes Pop-up schließt sich, weil ein anderes Pop-up aufgeht    
    } else if (old != options_btn) {
        if(options_btn.id == "bubble" && old.id != "bubble") {
            old.nextElementSibling.style.display = 'none';
            old.src = '../media/edit_icon.svg';
            options_btn.nextElementSibling.style.display = 'flex';
            options_btn.src = '../media/cross.svg';
            old = options_btn;
        } else if(options_btn.id != "bubble" && old.id != "bubble"){
            old.nextElementSibling.style.display = 'none';
            old.src = '../media/edit_icon.svg';
            options_btn.nextElementSibling.style.display = 'flex';
            options_btn.src = '../media/cross.svg';
            old = options_btn;
        } else if(options_btn.id == "bubble" && old.id == "bubble"){
            old.nextElementSibling.style.display = 'none';
            old.src = '../media/comment_icon.svg';
            options_btn.nextElementSibling.style.display = 'flex';
            options_btn.src = '../media/cross.svg';
            old = options_btn;
        } else if(options_btn.id != "bubble" && old.id == "bubble"){
            old.nextElementSibling.style.display = 'none';
            old.src = '../media/comment_icon.svg';
            options_btn.nextElementSibling.style.display = 'flex';
            options_btn.src = '../media/cross.svg';
            old = options_btn;
        }        
    }

  // Geöffnetes Pop-up schließt sich, weil außerhalb des Pop-ups gedrückt wird
  var triggerIcons = document.getElementsByClassName("open_icon");

  window.onclick = function (event) {
    if (event.target.classList[0] != "open_icon") {
      if(options_btn.id == "bubble") {
        options_btn.src = '../media/comment_icon.svg';
        options_btn.nextElementSibling.style.display = 'none';
        old = null;
    } else {
        options_btn.src = '../media/edit_icon.svg';
        options_btn.nextElementSibling.style.display = 'none';
        old = null;
    } 
  }
};
}

//----------------Öffnen des Overlays Lm-löschen-----------------------------//
function openLoeschen(id) {
  document.getElementById("overlayLoeschen:" + id).style.display = "block";
  document.getElementById("grauer-hintergrund").style.display = "block";
}


