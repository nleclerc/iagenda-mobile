<?php

define('DATEFORMAT', 'd/m/Y');

class EventEntry {
	private $id;
	private $title;
	private $year;
	private $month;
	private $day;
	
	public function __construct($id, $title, $year, $month, $day) {
		$this->id = $id;
		$this->title = $title;
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
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
		return $this->formatDigits($this->day)."/".$this->formatDigits($this->month)."/".$this->year;
	}
	
	public function __toString() {
		return $this->id.":".$this->getdate().":".$this->title;
	}
	
	private function formatDigits($nb, $length=2) {
		$str = "$nb";
		
		while (strlen($str) < $length)
			$str = "0$str";
		
		return $str;
	}
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

?>