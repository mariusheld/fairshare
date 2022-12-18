//Pop-Up: "Nicht genießbar"---------------------------------------------------------------

document.getElementById('open_nicht_genießbar').addEventListener('click', open_NichtGenießbar);
document.getElementById('close_nicht_genießbar').addEventListener('click', close_NichtGenießbar);


function open_NichtGenießbar() {
    document.getElementById('popup_nicht_genießbar').style.display = 'flex';
}

function close_NichtGenießbar() {
    document.getElementById('popup_nicht_genießbar').style.display = 'none';
}


// Pop-Up: "Keine Boxen"-------------------------------------------------------------------

document.getElementById('open_keine_boxen').addEventListener('click', open_KeineBoxen);
document.getElementById('close_keine_boxen').addEventListener('click', close_KeineBoxen);

function open_KeineBoxen() {
    document.getElementById('popup_keine_boxen').style.display = 'flex';
}

function close_KeineBoxen() {
    document.getElementById('popup_keine_boxen').style.display = 'none';
}


// Kontextmenü

let visible = false;



function open_close_options(options_btn) {
    if (visible == false) {
        options_btn.src = '../media/cross.svg';
        options_btn.nextElementSibling.style.display = 'block';
        visible = true;
    } else {
        options_btn.src = '../media/edit_icon.svg';
        options_btn.nextElementSibling.style.display = 'none';
        visible = false;
    }
}

// Pop-Up: "Lebensmittel fairteilen"-------------------------------------------------------------------

function open_lebensmittel_fairteilen(fairteilen_btn) {
    let bezeichnung = document.getElementById('bezeichnung-' + fairteilen_btn.parentElement.id).innerHTML;
    let gewicht = document.getElementById('gewicht-' + fairteilen_btn.parentElement.id).innerHTML;
    
    $('#popup_lebensmittel_fairteilen h5').html(bezeichnung);
    $('#popup_lebensmittel_fairteilen #bestand').html("/ " + gewicht);

    document.getElementById('popup_lebensmittel_fairteilen').style.display = 'flex';
    open_close_options(fairteilen_btn.parentElement.previousElementSibling);
}

document.getElementById('fairteilen-abbrechen').addEventListener('click', () => {
    document.getElementById('popup_lebensmittel_fairteilen').style.display = 'none';
});

document.getElementById('fairteilen').addEventListener('click', () => {
    document.getElementById('popup_lebensmittel_fairteilen').style.display = 'none';
    document.getElementById('popup_lebensmittel_fairteilt').style.display = 'flex';
});

