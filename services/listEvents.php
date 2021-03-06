<?php
include_once '../inc/io.php';
include_once '../inc/data.php';
session_start();

header('Content-type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8'); // used for debugging.
	
$errorMessage = '';
$result = array();
	
if (!isset($_GET['month']))
	$errorMessage = "Le mois n'a pas été spécifié.";
else if (!isset($_GET['year']))
	$errorMessage = "L'année n'a pas été spécifié.";
else {
	$month = $_GET['month'];
	$year = $_GET['year'];
	
	$currentDate = getdate();
	
	try {
		$thisMonthAgendaContent = getAgendaPage($month, $year);
	
		$isLoggedIn = isLoggedIn($thisMonthAgendaContent);
		
		if (!$isLoggedIn)
			$errorMessage = "Vous n'êtes pas identifié.";
		else {
			$username = getUserNameFromContent($thisMonthAgendaContent);
			
			$events = parseEventist($thisMonthAgendaContent);
			
			$result = array(
				"username" => $username,
				"month" => $month,
				"year" => $year,
				"events" => $events
			);
		}
		$result["loggedIn"] = $isLoggedIn;
	} catch (Exception $e) {
		$errorMessage = $e->getMessage();
	}
}

$result["errorMessage"] = $errorMessage;
echo json_encode($result);
?>