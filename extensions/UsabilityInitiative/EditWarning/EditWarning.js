/* JavaScript for EditWarning extension */

function editWarningSetWarning() {
	$(window).bind('beforeunload', function(event) {
		if(!confirm(gM('editwarning-warning')))
			event.preventDefault();
		event.stopImmediatePropagation();
	});
}

$(document).ready(function() {
	$('#wpTextbox1').change(editWarningSetWarning)
		.bind('paste', editWarningSetWarning);
	$('#wpSummary').change(editWarningSetWarning)
		.bind('paste', editWarningSetWarning);
});
