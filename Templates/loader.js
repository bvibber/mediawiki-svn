addOnloadHook(function() {
	if (wgFundraiserPortal != '') {
		var logo = document.getElementById('p-logo');
		if (logo) {
			var div = document.createElement('div');
			div.id = "p-donate-button";
			div.className = "portlet";
			div.innerHTML = wgFundraiserPortal;
			if (logo.nextSibling == null) {
				logo.parentNode.appendChild(div);
			} else {
				logo.parentNode.insertBefore(div, logo.nextSibling);
			}
		}
	}
});
