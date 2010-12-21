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

$thisMonthAgendaContent = getAgendaPage(12, 2010);
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

<script type="text/javascript">
function openEvent(eventId) {
	window.location.href = "event.php?eventId="+eventId;
}
</script>

<script type="text/javascript" src="/ga.js"></script>
</head>
<body>

<div class="header">
<a href="."><img class="headerLogo" alt="iAgenda" src="images/calendar.png"></a>
<?= $username ?>
<a href="logout.php"><img class="quitButton" alt="Quit" src="images/close-gray.png"></a>
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
	<div class="$eventStyle" onclick="openEvent('$eventId')">
		<div class="eventTitle">$eventTitle</div>
		<div class="eventTime">$evt</div>
	</div>
EOD;
}
?>

</body>
</html>