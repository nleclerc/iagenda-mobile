
function jumpTo(url){
	window.location.href=url;
}

function getCurrentDate(){
	var now = new Date();
	return getDoubleDigit(now.getDate())+'/'+getDoubleDigit(now.getMonth()+1)+'/'+now.getFullYear();
}

function getDoubleDigit(number){
	var result = ''+number;
	
	while (result.length < 2)
		result = '0'+result;
	
	return result;
}

function loadAndRefresh(url, args, callback){
	$.ajax({
		url: url,
		dataType: 'json',
		data: args,
		success: function(data, textStatus, xhr){
			if (callback)
				callback(data, textStatus, xhr);
			
			window.location.reload();
		}
	});
}

function logout(){
	loadAndRefresh("logout.php");
}

function formatMaxParticipants(count){
	if (count > 0)
		return ''+count;
	else if (count < 0)
		return "illimité";
	else
		return "inconnu (ERREUR)";
}

function formatDate(source) {
	if (getCurrentDate() == source)
		return "Aujourd'hui, "+source;
	
	return source;
}
