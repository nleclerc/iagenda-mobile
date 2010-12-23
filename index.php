<?php
include_once 'inc/ioUtil.php';
include_once 'inc/eventUtil.php';
session_start();

if (!isLoggedIn()) {
	header('Location: login.php');
	exit;
}

$currentDate = getdate();
$nextMonthDate = getNextMonth($currentDate);

$currentDateStr = date(DATEFORMAT);

$thisMonthAgendaContent = getAgendaPage($currentDate['mon'], $currentDate['year']);
$username = getUserNameFromContent($thisMonthAgendaContent);

$events = parseEventist($thisMonthAgendaContent);

// remove past events
foreach ($events as $evt)
	if ($evt->getDay() < $currentDate['mday'])
		array_shift($events);

$events = array_merge($events, parseEventist(getAgendaPage($nextMonthDate['mon'], $nextMonthDate['year'])));

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

<script type="text/javascript" src="scripts/jquery-1.4.4.min.js"></script>

<script type="text/javascript">
/*
$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
    alert(textStatus);
    alert(errorThrown);
    alert(XMLHttpRequest.responseText);
}});
*/

var queue = new Array();
$(processQueue); // add document load handler

function enqueueEvent(eventId){
	queue.push(eventId);
}

function processQueue(){
	if (queue.length == 0)
		return;

	loadEventData(queue.shift(), processQueue);
}

function openEvent(eventId) {
	window.location.href = "event.php?eventId="+eventId;
}

function loadEventData(eventId, callback) {
	$.getJSON("eventData.php", {"eventId":eventId}, function(data){
		var details = "";
		details += data.participantCount+" / "+data.maxParticipants;
		details += " - ";
		details += data.author;
		
		$("#evtDetails-"+data.id).html(details);

		if (data.isParticipating)
			$("#evtTitle-"+data.id).addClass("eventTitleParticipating");

		if (callback)
			callback();
	});
}
</script>

<script type="text/javascript" src="/ga.js"></script>
</head>
<body>

<div class="header">
<a href="."><img class="headerLogo" alt="iAgenda" src="images/calendar.png"></a>
<?= $username ?>
<a href="logout.php"><img class="quitButton" alt="Quit" src="images/close.png"></a>
</div>

<?php 

$currentCalendarDate = null;

foreach ($events as $evt) {
	$eventDate = $evt->getDate();
	$first = false;
	
	if ($currentCalendarDate != $eventDate) {
		if ($currentCalendarDate)
			echo "</div>\n\n";
		
		$first = true;
		$currentCalendarDate = $eventDate;
		$prefix = "";
		
		if ($currentDateStr == $currentCalendarDate)
			$prefix = "Aujourd'hui, ";
		
		echo "<div class=\"listDate\">$prefix$currentCalendarDate</div>\n";
		echo "<div class=\"list\">\n";
	}
	
	$eventId = $evt->getId();
	$eventTitle = $evt->getTitle();
	
	$eventStyle = "subseqListItem";
	 
	if ($first)
		$eventStyle = "listItem";
	
	echo <<<EOD
	<div class="$eventStyle" onclick="openEvent('$eventId')" on>
		<div class="eventTitle" id="evtTitle-$eventId">$eventTitle</div>
		<div class="eventSummary" id="evtDetails-$eventId">...</div>
	</div>
	<script type="text/javascript">enqueueEvent($eventId)</script>
EOD;
}
?>

</body>
</html>