<?php

$MAIN_URL = "http://www.mensa-idf.org/index.php";
$AGENDA_URL = "$MAIN_URL?action=iAgenda_iagenda";
$DISCONNECT_URL = "$MAIN_URL?action=deconnection";
$LOGIN_URL = "$MAIN_URL?action=connection";

function readFileContent($fileUrl) {
	$file = fopen($fileUrl, "r");
	
	if (!$file)
		throw new Exception("Unable to open file: $fileUrl");
	
	$content = "";
	
	while($line = fread($file, 1024))
		$content .= $line;
	
	return utf8_encode($content);
}

function getErrorMessage($pageContent) {
	$matches = array();
	
	if (preg_match('%<center.*>(.*?)<\/span><\/center>%', $pageContent, $matches))
		return $matches[1];
	
	return null;
}

function login($login, $pwd) {
	global $LOGIN_URL;
	
	$loginResult = readFileContent("$LOGIN_URL&id=$login&pw=$pwd");
	
	$error = getErrorMessage($loginResult);
	
	if ($error)
		throw new Exception($error);
	
	return isLoggedIn($loginResult);
}

function isLoggedIn($indexContent=null) {
	global $MAIN_URL, $DISCONNECT_URL;
	
	if (!$indexContent)
		$indexContent = readFileContent($MAIN_URL);
	
	return preg_match("%".preg_quote($DISCONNECT_URL)."%", $indexContent);
}

?>