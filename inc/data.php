<?php

define('DATEFORMAT', 'd/m/Y');

function createEventEntry($id, $title, $date){
	return array(
		'id' => intval($id),
		'title' => decodeEntities($title),
		'date' => $date
	);
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
		'title' => decodeEntities($data[4]),
		'date' => reformatDate($data[1]),
		'author' => decodeEntities($data[2]),
		'authorEmail' => $data[3],
		'description' => decodeEntities($data[6]),
		'participants' => $participants,
		'maxParticipants' => parseMaxCount($data[5])
	);
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

function createMemberDetails($definition){
	$data = rmatch('#Fiche Membre (\d+).*?<td.*?>\s*(.*?)\.?\s(.*?)\s*<.*?<td.*?>(.*?)<.*?<form#', $definition);
	
	// if no match then member does not exist.
	if (!$data)
		return null;
	
	$result = array(
		'id' => intval($data[1]),
		'title' => $data[2],
		'name' => $data[3],
		'region' => $data[4]
	);
	
	$addressMatch = rmatch('#>Adresse<.*?<span.*?>\s*(.*?)\s*</span#', $data[0]);
	
	if ($addressMatch)
		$result['address'] = decodeEntities(preg_replace('#<br ?/>#', '\n', $addressMatch[1]));
	
	// Checks for an invisible special char (\x{00a0}).
	$deviseMatch = rmatch('#>Devise<.*?&quot;\s*\x{00a0}?(.*?)\x{00a0}?\s*&quot;#u', $data[0]);
	
	if ($deviseMatch)
		$result['motto'] = decodeEntities($deviseMatch[1]);

	$contacts = parseDataGroup(
		'#>Contact</th></tr>\s*(<tr.*?<span>\s*(.*?)\s*</span>.*?<td>\s*(.*?)\s*</td>.*?</tr>)\s*<tr><th#',
		'#<tr>.*?<span>(.*?)</span>.*?<td>\s*(.*?)\s*</td>.*?</tr>#',
		'createContact', // name of the object creating function to be called.
		$data[0]
	);
	
	if ($contacts)
		$result['contacts'] = $contacts;

	$interests = parseDataGroup(
		'#>Intérêts</th></tr>\s*(<tr>\s*<td.*?>\s*<table.*?/table>\s*</td>\s*</tr>)+\s*<tr><th#',
		'#<table.*?<td.*?>(.*?)</td>.*?>(.*?)</td>.*?>(.*?)</td>.*?</table>#',
		'createInterest', // name of the object creating function to be called.
		$data[0]
	);
	
	if ($interests)
		$result['interests'] = $interests;

	$languages = parseDataGroup(
		'#>Langue</th></tr>\s*(<tr>\s*<td.*?>\s*.*?\s*</td>\s*</tr>)+\s*<tr><th#',
		'#<tr>\s*<td.*?>\s*(.*?)\s*</td>.*?>\s*(.*?)\s*<br.*?</tr>#',
		'createLanguage', // name of the object creating function to be called.
		$data[0]
	);
	
	if ($languages)
		$result['languages'] = $languages;
		
	return $result;
}

function parseDataGroup ($definitionsEx, $detailsEx, $formatFunction, $source) {
	$defMatches = rmatch($definitionsEx, $source);
	
	if ($defMatches) {
		$values = array();
		$dataMatches = rmatch_all($detailsEx, $defMatches[1]);
		
		for ($i=0; $i<count($dataMatches); $i++)
			array_push($values, $formatFunction($dataMatches[$i]));
		
		return $values;
	}
	
	return null;
}

function createLanguage($matches){
	return array(
		'name' => $matches[1],
		'level' => $matches[2]
	);
}

function createInterest($matches){
	return array(
		'name' => $matches[1],
		'skill' => $matches[2],
		'level' => $matches[3]
	);
}

function createContact($matches){
	$type = 'Unknown';
	$value = $matches[2];
	
	switch ($matches[1]) {
		case 'Courriel':
			$type = 'email';
			$emailMatch = rmatch('#>(.*?)</#', $value);
			$value = $emailMatch[1];
			break;
			
		case 'Tél. fixe':
			$type = 'phone';
			break;
			
		case 'Tél. mobile':
			$type = 'mobile';
			break;
	}
	
	return array(
		'type' => $type,
		'value' => $value
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
	
	if (preg_match($regex, $source, $matches))
		return $matches;
	
	return null;
}

function rmatch_all($regex, $source) {
	$matches = array();
	
	if (preg_match_all($regex, $source, $matches, PREG_SET_ORDER))
		return $matches;
	
	return null;
}

function parseMaxCount($definition) {
	if ($definition == "Nombre de personnes illimit&eacute;")
		return -1;
	
	$countMatch = rmatch("/Nombre de personnes max : (\d+)/", $definition);
	
	if ($countMatch)
		return $countMatch[1];
	
	return 0;
}

function isParticipating($eventDetails, $userId){
	foreach ($eventDetails['participants'] as $p)
		if ($p['id'] == $userId)
			return true;
	
	return false;
}

function reformatDate($originalDate) {
	$result = rmatch('%(\d+)/(\d+)/(\d+)%', $originalDate);
	
	if ($result)
		return formatDigits($result[1]).'/'.formatDigits($result[2]).'/'.$result[3];
	
	throw new Exception("Invalid date: $originalDate");
}

function getUserIdFromContent($content) {
	$result = rmatch('%<input type="hidden" name="membre" value="(\d+)" />%', $content);
	
	if ($result)
		return intval($result[1]);
	
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

?>