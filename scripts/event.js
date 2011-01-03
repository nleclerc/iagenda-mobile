var eventId = null;
var userId = null;

function loadEvent(){
	$.getJSON("services/getEventData.php"+window.location.search, null, handleData);
}

function showEventBody(){
	$('#eventbody').fadeIn(200);
}

function handleData(data) {
	if (!data.loggedIn)
		window.location.href = "login.php";
	
	if (data.errorMessage)
		setErrorMessage(data.errorMessage);
	
	if (data.isParticipating) {
		$('#eventDetails').addClass('participatingEvent');
		enable($('#unsubscribeButton'));
	}
	else
		enable($('#subscribeButton'));
	
	eventId = data.id;
	userId = data.userid;
	
	$('#headerTitle').html(data.username);
	$('#eventDetailsTitle').html(data.title);
	$('#eventDate').html(beautifyDate(data.date, getCurrentDate()));
	
	if (data.authorEmail)
		$('#eventAuthor').html('<a id="organizerMailto" href="mailto:'+data.authorEmail+'?subject=[iAgenda] '+data.title+'">'+data.author+'</a>');
	else
		$('#eventAuthor').html(data.author);
	
	$('#eventDetailsDesc').html(formatDescription(data.description));
	
	$('#participantCount').html(data.participants.length+' / '+formatMaxParticipants(data.maxParticipants));
	
	for (var i=0; i<data.participants.length; i++) {
		var p = data.participants[i];
		$('#participants').append(getParticipantHtml(p, data.userid==p.id, i>0))
	}
	
	showEventBody();
}

function formatDescription(source) {
	var result = source;
	
	result = result.replace(/(\d?\d[hH]\d{0,2})/g, '<span class="highlight">$1</span>');
//	result = result.replace(/(ATTENTION)/g, '<span class="highlight">$1</span>');
//	result = result.replace(/(NOTE)/g, '<span class="highlight">$1</span>');
	
	// replace phone numbers with tel: links.
	result = result.replace(/((0\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d))/g, '<a href="tel:$2$3$4$5$6">$1</a>');
	
	// replace full url (including protocol part) 
	result = result.replace(/(\(\s*)((https?|ftp):\/\/[^\s<"\)]+)(\s*\))/gim, '$1<a href="$2">$2</a>$4'); // url between round brackets
	result = result.replace(/(^|[^>"])((https?|ftp):\/\/[^\s<"]+)/gim, '$1<a href="$2">$2</a>');
	
	// replace url without protocol part.
	result = result.replace(/(\(\s*)(www.[^\s<"\)]+)(\s*\))/gim, '$1<a href="http://$2">$2</a>$3'); // url between round brackets
	result = result.replace(/(^|[^>":\/])(www.[^\s<"]+)/gim, '$1<a href="http://$2">$2</a>');
	
	// replace email address with mailto link.
	result = result.replace(/([a-z0-9.\+\-]+@[a-z0-9.\-]+\.[a-z]+)/gim, '<a href="mailto:$1">$1</a>');
	
	// location hack using custom tag in html.
	result = result.replace(/<lieu>(.+?)<\/lieu>/gim, '<a href="http://maps.google.fr/maps?q=$1">$1</a>');

	return result;
}

function getParticipantHtml(data, highlight, subseq){
	var details = ''+data.id;
	
	if (data.email)
		details += ' - '+data.email;
	
	return createListItem(data.name, details, 'person', 'member.html?memberId='+data.id, subseq, highlight);
}

function subscribe(){
	loadAndRefresh("services/subscribe.php", {userId: userId, eventId: eventId})
}

function unsubscribe(){
	loadAndRefresh("services/unsubscribe.php", {userId: userId, eventId: eventId})
}
