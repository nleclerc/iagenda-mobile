<?php
include_once '../inc/io.php';
session_start();

$receivedLogin = '';
$receivedPassword = '';
$errorMessage = '';
$result = array();

if(isset($_GET['login'])) $receivedLogin = $_GET['login'];
if(isset($_GET['pwd'])) $receivedPassword = $_GET['pwd'];

try {
	global $LOGIN_URL;
	
	$loginResult = readFileContent("$LOGIN_URL&id=$receivedLogin&pw=$receivedPassword");
	$errorMessage = getErrorMessage($loginResult);
	$isLoggedIn = isLoggedIn($loginResult);
	
	if ($isLoggedIn) {
		$username = getUserNameFromContent($loginResult);
		
		$result = array(
			"username" => $username
		);
	}
	$result["loggedIn"] = $isLoggedIn;
} catch (Exception $e) {
	$errorMessage = $e->getMessage();
}

$result["errorMessage"] = $errorMessage;
echo json_encode($result);
?>