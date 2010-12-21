<?php
include_once 'inc/ioUtil.php';
include_once 'inc/eventUtil.php';
session_start();

if (!isLoggedIn()) {
	header('Location: login.php');
	exit;
}

$errorMessage = '';
$username = '';

if (!isset($_GET['eventId']))
	$errorMessage = "Identifiant d'événement manquant.";
else {
	$eventId = $_GET['eventId'];
	$eventDetailsContent = getEventDetailPage($eventId);
	$userId = getUserIdFromContent($eventDetailsContent);
	
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		
		echo "<pre>$action</pre>";
		
		if ($action == 'insc')
			setEventParticipation($eventId, $userId);
		else
			removeEventParticipation($eventId, $userId);
		
		$currentUri = $_SERVER["SCRIPT_NAME"];
		
		header("Location: $currentUri?eventId=$eventId");
		exit;
	}
	
	$username = getUserNameFromContent($eventDetailsContent);
	$eventDetails = new EventDetails($eventId, $eventDetailsContent);
	$isParticipating = $eventDetails->isParticipating($userId);
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

<title>iAgenda Mobile</title>

<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/main.css" type="text/css" />

<script type="text/javascript" src="/ga.js"></script>
</head>

<body>

<div class="header">
<a href="."><img class="headerLogo" alt="iAgenda" src="images/calendar.png"></a>
<?= $username ?>
<a href="logout.php"><img class="quitButton" alt="Quit" src="images/close-gray.png"></a>
</div>

<?php
	$detailsStyle = 'eventDetails';
	if ($isParticipating)
		$detailsStyle = 'eventDetailsParticipating';
?>
<div class="<?=$detailsStyle?>">
<?php if ($errorMessage != '') echo "<div class=\"errorMessage\">$errorMessage</div>" ?>

<div class="eventDetailsTitle"><?= $eventDetails->getTitle() ?></div>
<div class="eventTime"><?= $eventDetails->getDate() ?> - <a class="organizerMailto" href="mailto:<?= $eventDetails->getAuthorEmail() ?>?subject=<?= $eventDetails->getTitle() ?>"><?= $eventDetails->getAuthor() ?></a></div>

<div class="eventDetailsDesc"><?= parseLinks($eventDetails->getDescription()) ?></div>
<div>

<div id="controlBar">
<button type="button" id="subscribeButton"
<?php if ($isParticipating) echo 'disabled="true" '?>
onclick="window.location.href = window.location.href+'&amp;action=insc'">S'inscrire</button>

<button type="button" id="unsubscribeButton"
<?php if (!$isParticipating) echo 'disabled="true" '?>
onclick="window.location.href = window.location.href+'&amp;action=desinsc'">Se désinscrire</button>
</div>

</div>
</div>

<div class="participantMaxCount">Participants: <?= $eventDetails->getParticipantCount() ?> / <?= $eventDetails->getMaxParticipantCount() ?></div>

<div class="list">
<?php 
	$firstP = true;
	
	foreach ($eventDetails->getParticipants() as $participant) {
		$pId = $participant->getId();
		$pName = $participant->getName();
		$pEmail = $participant->getEmail();
		
		$isSelf = $pId == $userId;
		
		$itemStyle = 'subseqListItem';
		
		if ($isSelf)
			$itemStyle = 'subseqHighlightedListItem';
		
		if ($firstP) {
			$firstP = false;
			
			if ($isSelf)
				$itemStyle = 'highlightedListItem';
			else
				$itemStyle = 'listItem';
		}
		
		echo <<<EOD
	<div class="$itemStyle">
		<div class="participantName">$pName</div>
		<div class="participantDetails">$pId - <a class="participantMailto" href="mailto:$pEmail">$pEmail</a></div>
	</div>
EOD;
	}
?>
</div>

</body>
</html>