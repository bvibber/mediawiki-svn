
runOnloadHook = function(){
	sendReq("cc-reporting.php?load=1&utm_src=" + document.payment.utm_source.value);
}

//replace "validate_form" call with this
submitForm = function(){
	sendReq("cc-reporting.php?submit=1&utm_src="+ document.payment.utm_source.value);
	return validate_form(document.payment);
}

sendReq = function(sendRequest){
	if (window.XMLHttpRequest)
	{
	  xhttp=new XMLHttpRequest();
    }
	else // Internet Explorer 5/6
	{
      xhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	  xhttp.open("GET",sendRequest ,false);
	  xhttp.send("");
	  dummyVar=xhttp.responseXML;
}

