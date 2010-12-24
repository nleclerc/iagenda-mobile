<?php

define('DATEFORMAT', 'd/m/Y');

class EventEntry {
	public $id;
	public $title;
	public $year;
	public $month;
	public $day;
	public $date;
	
	public function __construct($id, $title, $year, $month, $day) {
		$this->id = $id;
		$this->title = decodeEntities($title);
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
		$this->date = $this->getdate();
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitle() {
		return $this->title; 
	}
		
	public function getYear() {
		return $this->year;
	}
	
	public function getMonth() {
		return $this->month;
	}
	
	public function getDay() {
		return $this->day;
	}
	
	public function getdate() {
		return formatDigits($this->day)."/".formatDigits($this->month)."/".$this->year;
	}
	
	public function __toString() {
		return $this->id.":".$this->getdate().":".$this->title;
	}
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

class EventDetails {
	public $id;
	public $title;
	public $date;
	public $author;
	public $authorEmail;
	public $description;
	public $participants;
	public $maxParticipants;
	private $maxParticipantCount;
	
	public function __construct($id, $definition) {
		$this->id = $id;
		
		$data = rmatch('%<th.*?>(.*?)</th>.*?Activit&eacute; propos&eacute;e par (.+?)</td>.*?mailto:(.+?)".*?<b>(.*?)</b>.*?<b>(.*?)</b>.*?<td>(.*?)</td>%', $definition);
		
		$this->date = reformatDate($data[0]);
		
		$this->author = decodeEntities($data[1]);
		$this->authorEmail = $data[2];
		
		$this->title = decodeEntities($data[3]);
		$this->maxParticipantCount = parseMaxCount($data[4]);
		$this->maxParticipants = $this->maxParticipantCount;
		
		$this->description = decodeEntities($data[5]);
		
		$this->participants = array();
		
		$participantMatches = array();
		
		if (preg_match_all('%<td>(\d+?) - <a href="mailto:(.+?)">(.+?)</a>%', $definition, $participantMatches))
			for ($i=0; $i<count($participantMatches[0]); $i++)
				array_push($this->participants, new EventParticipant($participantMatches[1][$i], $participantMatches[3][$i], $participantMatches[2][$i]));
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getDate() {
		return $this->date;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getAuthorEmail() {
		return $this->authorEmail;
	}
		
	public function getDescription() {
		return $this->description;
	}
	
	public function getParticipants() {
		return $this->participants;
	}
	
	public function getParticipantCount() {
		return count($this->participants);
	}
	
	public function getMaxParticipantCount() {
		if ($this->maxParticipantCount < 0)
			return "illimité";
		
		if ($this->maxParticipantCount == 0)
			return "inconnu (erreur)";
		
		return $this->maxParticipantCount;
	}
	
	public function isParticipating($userId) {
		foreach ($this->participants as $participant)
			if ($participant->getId() == $userId)
				return true;
		
		return false;
	}
}

class EventParticipant {
	public $id;
	public $name;
	public $email;
	
	public function __construct($id, $name, $email) {
		$this->id = intval($id);
		$this->name = decodeEntities($name);
		$this->email = $email;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getEmail() {
		return $this->email;
	}
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
			array_push($events, new EventEntry($matches[1][$i], $matches[2][$i], $year, $month, $day));
	
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