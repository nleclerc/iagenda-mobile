
function createListItem(title, details, icon, link, isSubseq, isHighlighted, listName, itemId){
	var item = $('<a class="listItem"></a>');
	
	if (listName && itemId)
		item.attr('id', 'item-'+listName+'-'+itemId);
	
	if (isHighlighted)
		item.addClass('highlightedItem');
	
	if (isSubseq)
		item.addClass('subseqListItem');
	
	if (link) {
		if (link.indexOf(':') > 0)
			item.attr('href', link);
		else
			item.click(function(){jumpTo(link);});
	}
	
	if (icon)
		$('<img src="images/'+icon+'.png" class="listItemIcon">').appendTo(item);
	
	var title = $('<div class="listItemTitle lineBlock">'+title+'</div>').appendTo(item);
	var details = $('<div class="listItemDetails lineBlock">'+details+'</div>').appendTo(item);
	
	return item;
}

function jumpTo(url){
	window.location.href=url;
}

function getCurrentDate(){
	return formatDate(new Date())
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
	loadAndRefresh("services/logout.php");
}

function formatMaxParticipants(count){
	if (count > 0)
		return ''+count;
	else if (count < 0)
		return "illimité";
	else
		return "inconnu (ERREUR)";
}

function formatDate(date) {
	return getDoubleDigit(date.getDate())+'/'+getDoubleDigit(date.getMonth()+1)+'/'+date.getFullYear();
}

var daysOfWeek = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];

function getWeekDay(datestr){
	var date = new Date(datestr.match(/\d+$/), datestr.match(/\/\d+\//).toString().match(/\d+/)-1, datestr.match(/^\d+/));
	return daysOfWeek[date.getDay()];
}

function beautifyDate(date, referenceDate){
	var result = date+' : '+getWeekDay(date)
	
	if (referenceDate == date)
		result = "Aujourd'hui, "+result;;
	
	return result;
}

function setErrorMessage(message){
	var errorZone = $("#errorMessage");
	errorZone.addClass("errorMessage");
	errorZone.html(message);
}

function addMonth(date){
	return new Date(date.getFullYear(), date.getMonth()+1, 1);
}

function isBefore(testedDate, referenceDate){
	return parseDate(testedDate) < parseDate(referenceDate);
}

function parseDate(dateString){
	var dateParts = dateString.split("/");
	return new Date(dateParts[2], dateParts[1]-1, dateParts[0]);
}

function enable(element){
	element.removeAttr('disabled');
}

function disable(element){
	element.attr('disabled', 'true');
}