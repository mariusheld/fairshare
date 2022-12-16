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


