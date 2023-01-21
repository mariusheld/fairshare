//Pop-Up: "Nicht genießbar"---------------------------------------------------------------

// document.getElementById('open_nicht_genießbar').addEventListener('click', open_NichtGenießbar);
// document.getElementById('close_nicht_genießbar').addEventListener('click', close_NichtGenießbar);


function open_NichtGenießbar() {
    document.getElementById('popup_nicht_genießbar').style.display = 'flex';
}

function close_NichtGenießbar() {
    document.getElementById('popup_nicht_genießbar').style.display = 'none';
}


// Pop-Up: "Keine Boxen"-------------------------------------------------------------------

function open_KeineBoxen() {
    document.getElementById('popup_keine_boxen').style.display = 'flex';
}

function close_KeineBoxen() {
    document.getElementById('popup_keine_boxen').style.display = 'none';
}


// Anmerkungen -------------------------------------------------------------------

// function open_anmerkung(btn) {
//     btn.nextElementSibling.style.display = 'flex';
//     btn.src = '../media/cross.svg';
// }

// Kontextmenü

var old = null;

function open_close_options(options_btn) {
    
    if (old == null) {
        options_btn.src = '../media/cross.svg';
        options_btn.nextElementSibling.style.display = 'flex';
        old = options_btn;

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

// Pop-Up: "Lebensmittel fairteilen"-------------------------------------------------------------------

function open_lebensmittel_fairteilen(fairteilen_btn) {
    document.getElementById('popup_lebensmittel_fairteilen-' + fairteilen_btn.id).style.display = 'flex';
    open_close_options(fairteilen_btn.parentElement.previousElementSibling);
}

function fairteilen_abbrechen(abbrechen_btn) {
    console.log(abbrechen_btn.id)
    document.getElementById("popup_lebensmittel_fairteilen-" + abbrechen_btn.id).style.display = 'none';
}

// document.getElementById('fairteilen').addEventListener('click', () => {
//     window.location.href='admin.php?fairteilen=1';
// });


// Pop-Up: "Lebensmittel entsorgen"-------------------------------------------------------------------

function open_lebensmittel_entsorgen(entsorgen_btn) {
    document.getElementById("popup_lebensmittel_entsorgen-" + entsorgen_btn.id).style.display = 'flex';
    open_close_options(entsorgen_btn.parentElement.previousElementSibling);
}

function entsorgen_abbrechen(abbrechen_btn) {
    document.getElementById("popup_lebensmittel_entsorgen-" + abbrechen_btn.id).style.display = 'none';
}

// Pop-Up: "Lebensmittel ansehen"-------------------------------------------------------------------

function open_lebensmittel_ansehen(ansehen_btn) {
    document.getElementById("popup_lebensmittel_ansehen-" + ansehen_btn.id).style.display = 'flex';
    open_close_options(ansehen_btn.parentElement.previousElementSibling);
}

function close_lebensmittel_ansehen(close_btn) {
    document.getElementById("popup_lebensmittel_ansehen-" + close_btn.id).style.display = 'none'; 
}

// function entsorgen_abbrechen(abbrechen_btn) {
//     document.getElementById("popup_lebensmittel_entsorgen-" + abbrechen_btn.id).style.display = 'none';
// }


