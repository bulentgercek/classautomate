/**
 * Ajax Kontrol Fonksiyonlari
 *
 * @author Haydar Tuna <haydartuna@hotmail.com>
 * @edited Bulent Gercek <bulentgercek@gmail.com>
 */
function ajaxCreate() {
	var httpAjax = null;
	var webBrowser = navigator.appName;
	
	if (webBrowser == "Microsoft Internet Explorer") {
		httpNesne = new ActiveObject ("Microsoft.XMLHTTP");
		
	} else {
		httpAjax = new XMLHttpRequest();
	}
	
	return httpAjax;
}

function ajaxRequest(object,method,file,variable,jsFunction) {
	ajaxObject = ajaxCreate();
	
	if (method == 'POST') {
		if ( ajaxObject != NULL ) {
			ajaxObject.onreadystatechange = jsFunction;
			ajaxObject.open ('POST', file, true);
			header = "application/x-www-form-urlencoded";
			ajaxObject.setRequestHeader("Content-Type", header);
			ajaxObject.send(variable);
			
		} else {
			alert ('Ajax creation error.');
		}
		
	} else {
		if ( ajaxObject != NULL) {
			ajaxObject.onreadystatechange = jsFunction;
			ajaxObject.open ('GET', file + '?' + variable, true);
			date = "06: Oct 1977";
			ajaxObject.setRequestHeader("If-Modified-Since",date);
			ajaxObject.send(null);
			
		} else {
			alert('Ajax creation error.');
		}
	}
}