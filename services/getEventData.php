<?php
include_once '../inc/io.php';
include_once '../inc/data.php';
session_start();

header('Content-type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8'); // used for debugging.

$errorMessage = '';
$result = array();
	
if (!isset($_GET['eventId']))
	$errorMessage = "Identifiant d'événement manquant (eventId).";
else {
	try {
		$eventId = $_GET['eventId'];
		$eventDetailsContent = getEventDetailPage($eventId);
		
		$isLoggedIn = isLoggedIn($eventDetailsContent);
		
		if (!$isLoggedIn)
			$errorMessage = "Vous n'êtes pas identifié.";
		else {
			$userId = getUserIdFromContent($eventDetailsContent);
			$username = getUserNameFromContent($eventDetailsContent);
			
			$result = createEventDetails($eventId, $eventDetailsContent);
			$result["username"] = $username;
			$result["userid"] = $userId;
			$result["isParticipating"] = isParticipating($result, $userId);
		}
		
		$result["loggedIn"] = $isLoggedIn;
	} catch (Exception $e) {
		$errorMessage = $e->getMessage();
	}
}

$result["errorMessage"] = $errorMessage;
echo json_encode($result);


//echo "\n\n\n";
//echo $result['description'];

?>
