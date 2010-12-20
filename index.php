<?php
include_once 'inc/ioUtil.php';
session_start();

if (!isLoggedIn())
	header('Location: login.php');

?>
<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8"/>
<meta name='HandheldFriendly' content='True' />
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="stylesheet" href="css/main.css" type="text/css" />

<script type="text/javascript">
function openEvent(eventId) {
	window.location.href = "event.php?id="+eventId;
}
</script>

</head>
<body>

<div class="header">
<a href="."><img class="headerLogo" alt="iAgenda" src="images/calendar.png"></a>
Nicolas Leclerc
<a href="logout.php"><img class="quitButton" alt="Quit" src="images/close-gray.png"></a>
</div>

<div class="listDate">Aujourd'hui, 16/12/2010</div>
<div class="list">
	<div class="listItem" onclick="openEvent('toto')">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="subseqListItem">
		<div class="eventTitle">Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="subseqListItem">
		<div class="eventTitle">Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="subseqListItem">
		<div class="eventTitle">Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="subseqListItem">
		<div class="eventTitle">Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>

<div class="listDate">17/12/2010</div>
<div class="list">
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>

<div class="listDate">17/12/2010</div>
<div class="list">
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>

<div class="listDate">17/12/2010</div>
<div class="list">
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>

<div class="listDate">17/12/2010</div>
<div class="list">
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>

<div class="listDate">17/12/2010</div>
<div class="list">
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
	<div class="listItem">
		<div class="eventTitle">Repas noel</div>
		<div class="eventTime">12:00 - 14:00</div>
	</div>
</div>
</body>
</html>