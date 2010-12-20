<?php
include_once 'inc/ioUtil.php';
session_start();

if (!isLoggedIn()) {
	header('Location: login.php');
	exit;
}


?>
<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8"/>
<meta name='HandheldFriendly' content='True' />
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/main.css" type="text/css" />

</head>
<body>

<div class="header">
<a href="."><img class="headerLogo" alt="iAgenda" src="images/calendar.png"></a>
Nicolas Leclerc
<a href="logout.php"><img class="quitButton" alt="Quit" src="images/close-gray.png"></a>
</div>

<div class="eventDetails">
	<div class="eventDetailsTitle">Soirée "Funky Town"</div>
	<div class="eventTime">Aujourd'hui, 16/12/2010 - <a class="organizerMailto" href="mailto:albert@telechat.fr?subject=Soirée &quot;Funky Town&quot;">Albert Le Dictionnaire</a></div>
	
	<div class="eventDetailsDesc">Bonjour,<br>
<br>
Une soirée à "orientation dansante" vous est proposée à partir de 21h dans un bar/pub pour celles et ceux qui veulent se réunir autour d'un verre et/ou se "déchirer" sur la piste de danse.<br>
<br>
Il s'agit du bar/pub "Le merle moqueur" (11, rue de la Butte aux Cailles, 75013 Paris), un bar à cocktail / rhumerie dont l'ambiance est festive et la musique variée.<br>
<br>
Ce genre de soirées pourra être proposé à nouveau, soit dans le même cadre, soit dans des cadres différents.</div>

<div>
<div id="controlBar">
<button type="button" id="subscribeButton" disabled="disabled">S'inscrire</button>
<button type="button" id="unsubscribeButton">Se désinscrire</button>
</div>
</div>
</div>

<div class="participantMaxCount">Participants: 5 / illimité</div>

<div class="list">
<div class="listItem">
	<div class="participantName">Lola L'AUTRUCHE</div>
	<div class="participantDetails">3575 - <a class="participantMailto" href="mailto:lola@telechat.fr">lola@telechat.fr</a></div>
</div>
<div class="subseqListItem">
	<div class="participantName">Nicolas LECLERC</div>
	<div class="participantDetails">4521 - nl@spirotron.fr</div>
</div>
<div class="subseqListItem">
	<div class="participantName">Gluon DU-TROU</div>
	<div class="participantDetails">4565 - gluondutrou@telechat.fr</div>
</div>
<div class="subseqListItem">
	<div class="participantName">Pub PUB</div>
	<div class="participantDetails">6532 - pubpub@telechat.fr</div>
</div>
<div class="subseqListItem">
	<div class="participantName">Legu MAN</div>
	<div class="participantDetails">8543 - leguman@telechat.fr</div>
</div>
</div>

</body>
</html>