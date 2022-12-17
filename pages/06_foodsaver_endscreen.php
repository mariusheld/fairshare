<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <title>
        FAIRSHARE
    </title>
    <link rel="stylesheet" href="../css/startendstyle.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&family=Londrina+Solid&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body id="endscreen">

    <div id="endscreen-header">
        <img id="logo" src="../media/logo.svg" alt="fairshare" height="50px">
    </div>

    <div id="endscreen-content">
        <div>
            <h1 id="endscreen-title">50 Kilogramm</h1>
            <p id="endscreen-text">Lebensmittel hast du bisher für die Raupe gerettet!</p>
            <p id="endscreen-text-danke">Danke für deinen Beitrag!</p>
            <button id="endscreen-button">Beenden</button>
        </div>
    </div>

    <div id="endscreen-footer">
        <p id="endscreen-footer-text">Raupe Immersatt, Stuttgart 2023</p>
    </div>
    <script>
        //Weiterleitung auf Startscreen
        document.getElementById('endscreen-button').onclick = function () {
            window.location.href = '../index.php'
        }
    </script>
</body>

</html>