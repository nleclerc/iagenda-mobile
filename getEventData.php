<?php
include_once 'inc/ioUtil.php';
include_once 'inc/eventUtil.php';
session_start();

header('Content-type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8'); // used for debugging.

$errorMessage = '';
$result = array();
	
if (!isset($_GET['eventId']))
	$errorMessage = "Identifiant d'événement manquant.";
else {
	$eventId = $_GET['eventId'];
	$eventDetailsContent = getEventDetailPage($eventId);
	
	$isLoggedIn = isLoggedIn($eventDetailsContent);
	
	if (!$isLoggedIn)
		$errorMessage = "Vous n'êtes pas identifié.";
	else {
		$userId = getUserIdFromContent($eventDetailsContent);
		
		$username = getUserNameFromContent($eventDetailsContent);
		$eventDetails = new EventDetails($eventId, $eventDetailsContent);
		
		$result = (array)$eventDetails;
		$result["username"] = $username;
		$result["userid"] = $userId;
		$result["isParticipating"] = $eventDetails->isParticipating($userId);
	}
	
	$result["loggedIn"] = $isLoggedIn;
}

$result["errorMessage"] = escapeQuotes($errorMessage);
echo json_encode($result);
?>
