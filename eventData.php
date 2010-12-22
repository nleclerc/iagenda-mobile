<?php
include_once 'inc/ioUtil.php';
include_once 'inc/eventUtil.php';
session_start();

if (!isLoggedIn()) {
	exit;
}

header('Content-type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8'); // used for debugging.

$errorMessage = '';
$username = '';

if (!isset($_GET['eventId']))
	$errorMessage = "Identifiant d'événement manquant.";
else {
	$eventId = $_GET['eventId'];
	$eventDetailsContent = getEventDetailPage($eventId);
	$userId = getUserIdFromContent($eventDetailsContent);
	
	$username = getUserNameFromContent($eventDetailsContent);
	$eventDetails = new EventDetails($eventId, $eventDetailsContent);
	
	$isParticipating = "false";
	
	if ($eventDetails->isParticipating($userId))
		$isParticipating = "true";
}
?>
{
"errorMessage":"<?=escapeQuotes($errorMessage)?>",
"id":<?=$eventId?>,
"title":"<?=escapeQuotes($eventDetails->getTitle())?>",
"date":"<?=$eventDetails->getDate()?>",
"description":"<?=escapeQuotes($eventDetails->getDescription())?>",
"author":"<?=$eventDetails->getAuthor()?>",
"authorEmail":"<?=$eventDetails->getAuthorEmail()?>",
"maxParticipants":"<?=$eventDetails->getMaxParticipantCount()?>",
"participantCount":<?=$eventDetails->getParticipantCount()?>,
"participants":[<?php 
	$isFirst = true;
	foreach ($eventDetails->getParticipants() as $participant) {
		if ($isFirst)
			$isFirst = false;
		else
			echo ',';
		
		$pId = $participant->getId();
		$pName = $participant->getName();
		$pEmail = $participant->getEmail();
		echo "{\"id\":$pId,\"name\":\"$pName\",\"email\":\"$pEmail\"}";
	}
?>],
"isParticipating":<?=$isParticipating?>

}
