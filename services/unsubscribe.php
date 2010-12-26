<?php
include_once '../inc/io.php';
include_once '../inc/data.php';
session_start();

if (isset($_GET['eventId']) && isset($_GET['userId'])) {
	$userId = $_GET['userId'];
	$eventId = $_GET['eventId'];
	
	removeEventParticipation($eventId, $userId);
}
?>