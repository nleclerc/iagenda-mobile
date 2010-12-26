var queue = new Array();

var currentDate = getCurrentDate();
var listingDate = new Date();

var blockIndex = new Object();
var eventCount = 0;

var stopLoading = false;
var eventLoading = false;
var queueInProcess = false;

var init = true;

function setLoadStatus(data){
	$('#loadStatus').html(data);
}

function resetLoadStatus(){
	setLoadStatus('<a href="javascript:loadNextEvents()">Charger plus d\'évènements...</a>');
}

function isBottom(){
	return (window.pageYOffset+$(window).height()) >= ($(document).height());
}

function checkScrollToBottom(){
	if (isBottom()) {
		loadNextEvents();
		return true;
	}
	
	init = false;
	return false;
}

function loadNextEvents(){
	if (!eventLoading) {
		if (!stopLoading) {
			eventLoading = true;
			setLoadStatus('Chargement en cours...');
			$.getJSON("services/listEvents.php", {month:listingDate.getMonth()+1, year:listingDate.getFullYear()}, handleNewEvents);
		} else {
			processQueue(); // in case max loading happens because screen is too tall.
		}
	}
}

function handleNewEvents(data){
	if (!data.loggedIn) {
		window.location.href = "login.php";
		return;
	}
	
	if (data.errorMessage)
		setErrorMessage(data.errorMessage);
	else {
		if (data.events.length == 0) {
			// found empty month, stop loading
			stopLoading = true;
			init = false;
			
			setLoadStatus("Plus d'autres évènements ensuite.");
			processQueue();
		} else {
			listingDate = addMonth(listingDate);
			
			$('#headerTitle').html(data.username);
			
			for (var i=0; i<data.events.length; i++)
				if (!isBefore(data.events[i].date, currentDate))
					addEvent(data.events[i]);
			
			eventLoading = false;
			resetLoadStatus();
		}
		
		// process queue only if not at bottom ie. not reloading events.
		if (!init || !checkScrollToBottom())
			processQueue();
	}
}

function addEvent(eventData){
	var dateBlock = blockIndex[eventData.date];
	var first = false;
	
	if (!dateBlock) {
		dateBlock = createDateBlock(eventData.date);
		first = true;		
	}
	
	var eventDiv = $('<div class="listItem" onclick="openEvent('+eventData.id+')"></div>');
	eventDiv.append('<div class="eventTitle" id="evtTitle-'+eventData.id+'">'+eventData.title+'</div>');
	eventDiv.append('<div class="eventSummary" id="evtDetails-'+eventData.id+'">&nbsp;</div>');
	
	if (!first)
		eventDiv.addClass('subseqListItem');
	
	eventDiv.hide().appendTo(dateBlock).fadeIn(500);
	queue.push(eventData.id);
	eventCount++;
}

function createDateBlock(date){
	var events = $('#eventList');
	var dateBlock = $('<div id="block-'+date+'" class="list"></div>\n');
	blockIndex[date] = dateBlock;
	
	events.append('<div class="listDate">'+beautifyDate(date, currentDate)+'</div>\n');
	dateBlock.hide().appendTo(events).fadeIn(500);
	
	return dateBlock
}

function processQueue(){
	if (queue.length == 0 || queueInProcess)
		return;
	
	queueInProcess = true;
	loadEventData(queue.shift(), processQueue);
}

function openEvent(eventId) {
	window.location.href = "event.html?eventId="+eventId;
}

function loadEventData(eventId, callback) {
	$("#evtDetails-"+eventId).html('<img src="images/loading.gif">');
	$.getJSON("services/getEventData.php", {"eventId":eventId}, function(data){
		var details = "";
		details += data.participants.length+" / ";
		
		if (data.maxParticipants > 0)
			details += data.maxParticipants;
		else if (data.maxParticipants < 0)
			details += "illimité";
		else
			details += "inconnu (ERREUR)";
		
		details += " - ";
		details += data.author;
		
		$("#evtDetails-"+data.id).html(details);

		if (data.isParticipating)
			$("#evtTitle-"+data.id).addClass("highlightedItem");
		
		queueInProcess = false;

		if (callback)
			callback();
	});
}
