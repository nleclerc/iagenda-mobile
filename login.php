<?php



?>
<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8"/>
<meta name='HandheldFriendly' content='True' />
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="stylesheet" href="css/main.css" type="text/css" />

</head>
<body>

<div id="loginForm">
<div id="loginHeader">
<img class="headerLogo" alt="iAgenda" src="images/calendar.png">
iAgenda Mobile
</div>

<div class="inputLabel">Identifiant</div>
<input type="text">

<div class="inputLabel">Mot de passe</div>
<input type="password">

<button id="loginSubmit" type="submit" onclick="window.location.href='list.php'">Valider</button>
</div>

</body>
</html>