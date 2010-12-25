$(loadEvent);

var eventId = null;
var userId = null;

function loadEvent(){
	$.getJSON("services/getEventData.php"+window.location.search, null, handleData);
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
	
	$('#organizerMailto').html(data.author);
	$('#organizerMailto').attr('href', 'mailto:'+data.authorEmail+'?subject=[iAgenda] '+data.title);

	$('#eventDetailsDesc').html(formatDescription(data.description));
	
	$('#participantCount').html(data.participants.length+' / '+formatMaxParticipants(data.maxParticipants));
	
	for (var i=0; i<data.participants.length; i++) {
		var p = data.participants[i];
		$('#participants').append(getParticipantHtml(p, data.userid==p.id, i>0))
	}
	
	showBody();
}

function formatDescription(source) {
	var result = source;
	
	result = result.replace(/(\d?\d[hH]\d{0,2})/g, '<span class="highlight">$1</span>');
//	result = result.replace(/(ATTENTION)/g, '<span class="highlight">$1</span>');
//	result = result.replace(/(NOTE)/g, '<span class="highlight">$1</span>');
	
	result = result.replace(/((0\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d)[.\- ]?(\d\d))/g, '<a href="tel:$2$3$4$5$6">$1</a>');
	
	result = result.replace(/(https?:\/\/\S+)/g, '<a href="$1">$1</a>');
	result = result.replace(/([^:\/])(www.\S+)/g, '$1<a href="http://$2">$2</a>');
	
	result = result.replace(/(ftp:\/\/\S+)/g, '<a href="$1">$1</a>');
	result = result.replace(/([A-Za-z0-9.\+]+@[A-Za-z0-9.]+\.[A-Za-z]+)/g, '<a href="mailto:$1">$1</a>');


	return result;
}

function getParticipantHtml(data, hightlight, subseq){
	var styles = 'listItem';
	
	if (hightlight)
		styles += ' highlightedItem';
	
	if (subseq)
		styles += ' subseqListItem';
	
	var result = '';
	result += '<div class="'+styles+'">';
	result += '<div class="participantName">'+data.name+'</div>';
	result += '<div class="participantDetails">'+data.id+' - <a class="participantMailto" href="mailto:'+data.email+'">'+data.email+'</a></div>';
	result += '</div>';
	
	return result;
}

function subscribe(){
	loadAndRefresh("services/subscribe.php", {userId: userId, eventId: eventId})
}

function unsubscribe(){
	loadAndRefresh("services/unsubscribe.php", {userId: userId, eventId: eventId})
}
