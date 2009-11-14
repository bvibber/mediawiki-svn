// This is the main library used for the website

$(document).ready(function() {
    $(".jshide").hide();
	
	$('#goback').click(function() {
		history.go(-1);
	});

    $("#btnUpload").click(function() {
        var validate = FormValidation(document.upload);
          
		// We validate the form first to see if there is any invalid content		
		if (validate.validated) {
			// Form is validated correctly		
			// hide the submit button and upload stuff
			$(this).fadeOut();
			$('#loading').fadeIn();			
			document.upload.submit(); // and submit the form			
		} else {
			// An error occured, display error message of some kind, if the box already exists put the error in there
			if (document.getElementById('uploadwarning')) {
				// box already exists, change just the warning
				$("#uploadwarning > p").html(validate.error);
			} else {
				// Make a new box
				var errorBox = '<div class="warning" id="uploadwarning"><p>' + validate.error + '</p></div>';
				$("#upload").prepend(errorBox);
			}

			// Do some cool effect
			$("#uploadwarning").fadeIn();
			
			// Scroll the screen to that place
			var warningPosition = $("#uploadwarning").offset()
			window.scrollTo(0, warningPosition.top - 50);
			
			return false; // do not execute the normal execution of the button (upload the form)
		}
	});
	
	$('#toggleexp').click( function() {		
		// Slide the explanation down with extra text
		$('#explanation').slideToggle("medium");
		this.blur();
	});
	
	$('#toggleUpload').click(function() {
		// The upload form is only displayed after clicking the 'I've read the rules' button
		$('#uploadbox').slideDown("medium");
		$(this).fadeOut();
	});
});

function popUp(URL) {
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=640,height=480,left = 320,top = 272');");
}