<?php
include_once 'inc/ioUtil.php';
include_once 'inc/eventUtil.php';
session_start();

if (isset($_GET['eventId']) && isset($_GET['userId'])) {
	$userId = $_GET['userId'];
	$eventId = $_GET['eventId'];
	
	removeEventParticipation($eventId, $userId);
}
?>