<?php
include_once 'inc/io.php';
session_start();

$receivedLogin = '';
$receivedPassword = '';
$errorMessage = '';

if(isset($_POST['login'])) $receivedLogin = $_POST['login'];
if(isset($_POST['pwd'])) $receivedPassword = $_POST['pwd'];

if ($receivedLogin) {
	try {
		login($receivedLogin, $receivedPassword);
		header('Location: .'); // redirect to index
		exit;
	} catch (Exception $e) {
		$errorMessage = $e->getMessage();
	}
}

?>
<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8"/>
<meta name='HandheldFriendly' content='True' />
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="apple-touch-icon" href="images/calendar-big.png" />

<title>iAgenda Mobile</title>

<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/main.css" type="text/css" />

<script type="text/javascript" src="scripts/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

$(init)

function init(){
	$('#qrcodelink').click(toggleQRCode);
}

function toggleQRCode(){
	var code = $('#qrcode');
	if (code.html() == '')
		code.html('<img class="qrcode" src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&choe=UTF-8&chld=chld=L|1&chl='+window.location.href+'">');
	else
		code.html('');
}

</script>

<script type="text/javascript" src="/ga.js"></script>
</head>
<body>

<div id="header">
<img id="headerLogo" alt="iAgenda" src="images/calendar.png">
<div id="headerTitle" class="lineBlock">iAgenda Mobile</div>
</div>

<form action="#" method="post">
<div id="loginForm">

<?php if ($errorMessage != '') echo "<div class=\"errorMessage\">$errorMessage</div>" ?>

<div class="inputLabel">Identifiant</div>
<input type="text" name="login" value="<?=$receivedLogin?>">

<div class="inputLabel">Mot de passe</div>
<input type="password" name="pwd" value="<?=$receivedPassword?>">

<input id="loginSubmit" type="submit" value="Valider">

<a class="footerLink" href="https://github.com/nleclerc/iagenda-mobile">Code Source</a>
<a class="footerLink" id="qrcodelink" href="#">QRCode</a>

<div id="qrcode"></div>
</div>
</form>

</body>
</html>