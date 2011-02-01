
function handleLoginSubmit(eventObject){
	$.getJSON("services/login.php?login="+$('#login').val()+"&pwd="+$('#pwd').val(), null, handleLoginresult);
}

function handleLoginresult(data){
	if (data.errorMessage)
		setErrorMessage(data.errorMessage);
	else if (!data.loggedIn)
		setErrorMessage("Erreur de login.");
	else {
//		$('#headerTitle').html(data.username);
		jumpTo(".");
	}
}

function toggleQRCode(){
	var code = $('#qrcode');
	if (code.html() == '')
		code.html('<img class="qrcode" src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&choe=UTF-8&chld=chld=L|1&chl='+window.location.href+'">');
	else
		code.html('');
}
