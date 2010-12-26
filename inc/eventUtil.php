<?php

define('DATEFORMAT', 'd/m/Y');

function createEventEntry($id, $title, $date){
	return array(
		'id' => intval($id),
		'title' => decodeEntities($title),
		'date' => $date
	);
}

function formatDate($day, $month, $year){
	return formatDigits($day)."/".formatDigits($month)."/".$year;
}
	
function formatDigits($nb, $length=2) {
	$str = "$nb";
	
	while (strlen($str) < $length)
		$str = "0$str";
	
	return $str;
}

function decodeEntities($source) {
	return html_entity_decode ($source, ENT_QUOTES, "utf-8");
}

function rmatch($regex, $source) {
	$matches = array();
	
	if (preg_match($regex, $source, $matches)) {
		array_shift($matches); // remove 1st item because it's the full match.
		return $matches;
	}
	
	return null;
}

function parseMaxCount($definition) {
	if ($definition == "Nombre de personnes illimit&eacute;")
		return -1;
	
	$countMatch = rmatch("/Nombre de personnes max : (\d+)/", $definition);
	
	if ($countMatch)
		return $countMatch[0];
	
	return 0;
}

function createEventDetails($id, $definition){
	$data = rmatch('%<th.*?>(.*?)</th>.*?Activit&eacute; propos&eacute;e par (.+?)</td>.*?mailto:(.+?)".*?<b>(.*?)</b>.*?<b>(.*?)</b>.*?<td>(.*?)</td>%', $definition);
	
	$participants = array();
	$participantMatches = array();
	
	if (preg_match_all('%<td>(\d+?) - <a href="mailto:(.+?)">(.+?)</a>%', $definition, $participantMatches))
		for ($i=0; $i<count($participantMatches[0]); $i++)
			array_push($participants, createParticipant($participantMatches[1][$i], $participantMatches[3][$i], $participantMatches[2][$i]));
	
	return array(
		'id' => intval($id),
		'title' => decodeEntities($data[3]),
		'date' => reformatDate($data[0]),
		'author' => decodeEntities($data[1]),
		'authorEmail' => $data[2],
		'description' => decodeEntities($data[5]),
		'participants' => $participants,
		'maxParticipants' => parseMaxCount($data[4])
	);
}

function isParticipating($eventDetails, $userId){
	foreach ($eventDetails['participants'] as $p)
		if ($p['id'] == $userId)
			return true;
	
	return false;
}

function createParticipant($id, $name, $email){
	$result = array(
		'id' => intval($id),
		'name' => decodeEntities($name)
	);
	
	if ($email)
		$result['email'] = $email;
	
	return $result;
}

function reformatDate($originalDate) {
	$result = rmatch('%(\d+)/(\d+)/(\d+)%', $originalDate);
	
	if ($result)
		return formatDigits($result[0]).'/'.formatDigits($result[1]).'/'.$result[2];
	
	throw new Exception("Invalid date: $originalDate");
}

function getUserIdFromContent($content) {
	$result = rmatch('%<input type="hidden" name="membre" value="(\d+)" />%', $content);
	
	if ($result)
		return intval($result[0]);
	
	return null;
}

function parseEventist($content) {
	$events = array();
	$matches = array();
	
	if (preg_match_all('%<a href="index.php\?action=iAgenda_icreation&amp;mois=(\d+)&amp;annee=(\d+)&amp;iaj=(\d+)">\d+</a>.*?</span>%', $content, $matches))
		for ($i=0; $i<count($matches[0]); $i++) {
			$foundEvents = parseDayEvents($matches[0][$i], $matches[2][$i], $matches[1][$i], $matches[3][$i]);
			$events = array_merge($events, $foundEvents);
		}
	
	return $events;
}

function parseDayEvents($dayDef, $year, $month, $day) {
	$events = array();
	$matches = array();
	
	if (preg_match_all('%<li><a href="index.php\?action=iAgenda_iactivite&mois=\d+&id=(\d+)">(.*?)</a></li>%', $dayDef, $matches))
		for ($i=0; $i<count($matches[0]); $i++)
			array_push($events, createEventEntry($matches[1][$i], $matches[2][$i], formatDate($day, $month, $year)));
	
	return 	$events;
}

function getNextMonth($date) {
	$currentMonth = $date['mon'];
	$currentYear = $date['year'];
	
	if ($currentMonth < 12)
		$currentMonth = $currentMonth+1;
	else {
		$currentMonth = 1;
		$currentYear = $currentYear+1;
	}
	
	return getdate(mktime(12, 30, 30, $currentMonth, 1, $currentYear));
}

function insertHighlights($source) {
	$result = $source;
	$result = preg_replace('/\d?\d[hH]\d{0,2}/', '<span class="hour">$0</span>', $result);
	
	return $result;
}

function escapeQuotes($source) {
	return preg_replace('/"/', '\\"', $source);
}

?>