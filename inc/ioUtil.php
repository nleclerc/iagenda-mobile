<?php

$MAIN_URL = "http://www.mensa-idf.org/index.php";
$AGENDA_URL = "$MAIN_URL?action=iAgenda_iagenda";
$EVENT_URL = "$MAIN_URL?action=iAgenda_iactivite";
$DISCONNECT_URL = "$MAIN_URL?action=deconnection";
$LOGIN_URL = "$MAIN_URL?action=connection";

$SET_PARTICIPATION_URL = "$MAIN_URL?action=iAgenda_iactivite";
$REMOVE_PARTICIPATION_URL = "$MAIN_URL?action=iAgenda_iactivite&d=1";


function parseLinks($source) {
	$result = $source;
	$result = preg_replace('/https?:\/\/\S+/', '<a href="$0">$0</a>', $result);
	$result = preg_replace('/ftp:\/\/\S+/', '<a href="$0">$0</a>', $result);
	$result = preg_replace('/mailto:\S+/', '<a href="$0">$0</a>', $result);
	$result = preg_replace('/[A-Za-z0-9.\+]+@[A-Za-z0-9.]+\.[A-Za-z]+/', '<a href="mailto:$0">$0</a>', $result);
	$result = preg_replace('/[^:\/](www.\S+)/', '<a href="http://$1">$1</a>', $result);
	
	// phone links
	$result = preg_replace('/(0\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d)/', '<a href="tel:$1$2$3$4$5">$0</a>', $result);
//	$result = preg_replace('/([+0][\d.\- ]{7,})/', '<a href="tel:$0">$0</a>', $result); // causes pb with google maps links.
	
	return $result;
}

function getUserNameFromContent($content) {
	$matches = array();
	if (preg_match("%>Bonjour&nbsp;(.*?)</div>%", $content, $matches))
		return $matches[1];
	
	throw new Exception("Username not found.");
}

function getSessionCookieFilePath($sessionId) {
	// use an ugly hack to retrieve system tmp folder...
	$tmpfile = tempnam("____dummy","");
	$tmpDir = dirname($tmpfile);
	unlink($tmpfile);
	
	return "$tmpDir/iagenda.$sessionId.tmp";
}

function readFileContent($fileUrl) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $fileUrl); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$cookieFile = getSessionCookieFilePath(session_id());
	
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
	
	$content = curl_exec($ch);
	$content = preg_replace('/\s+/', ' ', $content); // Normalize spaces for regex convenience.
	
	// TODO: fix some chars encoding (like euro sign or 'oe').
	
	return utf8_encode($content);
}

function getErrorMessage($pageContent) {
	$matches = array();
	
	if (preg_match('%<center.*?<span.*?>(.*?)<\/span><\/center>%', $pageContent, $matches))
		return $matches[1];
	
	return null;
}

function logout() {
	global $DISCONNECT_URL;
	readFileContent($DISCONNECT_URL);
	unlink(getSessionCookieFilePath(session_id()));
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

function getAgendaPage($month, $year) {
	global $AGENDA_URL;
	return readFileContent("$AGENDA_URL&mois=$month&annee=$year");
}

function getEventDetailPage($eventId) {
	global $EVENT_URL;
	return readFileContent("$EVENT_URL&id=$eventId");
}

function setEventParticipation($eventId, $userId) {
	global $SET_PARTICIPATION_URL;
	return readFileContent("$SET_PARTICIPATION_URL&id=$eventId&membre=$userId");
}

function removeEventParticipation($eventId, $userId) {
	global $REMOVE_PARTICIPATION_URL;
	return readFileContent("$REMOVE_PARTICIPATION_URL&id=$eventId&membre=$userId");
}

?>