<?php
// displays the explanation that goes with an action or question if applicable
function explanation($constant) {
	$explanation = $GLOBALS['explanations'][$constant];
	if ($explanation) {
		echo '<div id="expwrap">';
		echo '&raquo; <a href="#" id="toggleexp">'.disp('EXPLANATION').'</a>'."\n";
		echo '<div id="explanation" class="jshide"><p>'.$explanation.'</p></div>'."\n";
		echo '</div>';
	}
}

function question($question, $yes, $no, $constant) {
	$phpself  = $_SERVER['PHP_SELF'];
	echo "<p>$question</p>";
	explanation($constant);
	echo '<a href="'.$phpself.'?question='.$yes.'" class="question">'.disp('YES').'</a><br />';
	echo '<a href="'.$phpself.'?question='.$no.'" class="question">'.disp('NO').'</a></br />';
}

function action($action, $constant) {
	// display an action
	$phpself  = $_SERVER['PHP_SELF'];
	if ($constant == DO_NOT_UPLOAD) {
		$class = 'caution';
	}
	else {
		$class = 'action';
	}

	echo "<p>".disp('ADVICE_WIZARD')."</p>";
	echo '<div class="'.$class.'">'.$action.'</div>';

	explanation($constant);

	// if the action is not 'DO_NOT_UPLOAD' present a link to the upload form
	if ($constant != DO_NOT_UPLOAD) {
		echo '<a href="'.$phpself.'?question=uploadform&action='.$constant.'" class="question">'.disp('UPLOAD_LINK').'</a><br />';
	}

	echo '<a href="'.GE_HOME.'" class="question">'.disp('START_AGAIN').'</a>';
}

function fill_globals() {
	global $db;

	// get all values from the db and puts it into a new array called $constants
	$GLOBALS['constants'] = array();
	$GLOBALS['explanations'] = array();
	$messages = $db->select(TB_MESSAGES, array("language" => $GLOBALS['settings']['language']));

	foreach($messages as $message) {
		$GLOBALS['constants'][$message['constant']] = $message['message'];

		// add to the explanations array if applicable
		if ($message['explanation']) {
			$GLOBALS['explanations'][$message['constant']] = $message['explanation'];
		}
	}

	// transfer data to global array
	$const = $GLOBALS['constants'];
	$GLOBALS['questions']  = array(
		"SUBJECT_PROTECTED" 	=> array($const['SUBJECT_PROTECTED'], "DO_NOT_UPLOAD", "OWN_WORK"),
		"OWN_WORK" 				=> array($const['OWN_WORK'], "WORK_IN_EMPLOYMENT", "CREATOR_PD"),
		"WORK_IN_EMPLOYMENT" 	=> array($const['WORK_IN_EMPLOYMENT'], "EMPLOYER_PERMISSION", "OWN_PERMISSION"),
		"CREATOR_PERMISSION" 	=> array($const['CREATOR_PERMISSION'], "UPLOAD_LICENSE_EMAIL", "DO_NOT_UPLOAD"),
		"CREATOR_PD" 			=> array($const['CREATOR_PD'], "UPLOAD_PD", "DO_NOT_UPLOAD"),
		"OWN_PERMISSION"		=> array($const['OWN_PERMISSION'], "UPLOAD_LICENSE", "DO_NOT_UPLOAD"),
		"EMPLOYER_PERMISSION" 	=> array($const['EMPLOYER_PERMISSION'], "UPLOAD_LICENSE_EMAIL", "DO_NOT_UPLOAD")
	);

	$GLOBALS['actions'] =  array(
		"DO_NOT_UPLOAD" 		=> $const['DO_NOT_UPLOAD'],
		"UPLOAD_PD" 			=> $const['UPLOAD_PD'],
		"UPLOAD_LICENSE" 		=> $const['UPLOAD_LICENSE'],
		"UPLOAD_LICENSE_EMAIL" 	=> $const['UPLOAD_LICENSE_EMAIL']
	);
}

function show_language_chooser() {
	// gives a dropdown box of all available languages and changes $GLOBALS['settings']['language'] to that language
	$languages = array(
		"en" => "English",
		"nl" => "Nederlands"
	);

	echo '<br /><form method="GET" action="" name="language">';
	echo disp('CHOOSE_LANGUAGE').': ';
	echo '<select name="newLanguage">'."\n";
	foreach ($languages as $code=>$language) {
		// if this is the current selected language give a selected attribute
		if ($code == $GLOBALS['settings']['language']) {
			$selected = " selected";
		}
		else {
			$selected = "";
		}
		echo '<option value="'.$code.'"'.$selected.'>'.$language.'</option>';
	}
	echo '</select>';
	echo '<input type="submit" action="" value="OK" />';
	echo '</form>';
}

function return_file_upload_errorcode($code) {
	$errors = array(
		UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
		UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
		UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
		UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
		UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
		UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
	);
	return $errors[$code];
}

function upload_file($file, $filename) {
	$destination = GE_IMAGES_PATH.$filename;
	$moveresult = @move_uploaded_file($_FILES[$file]['tmp_name'], $destination);

	$return = array();
	if ($moveresult) {
		$return['key']   = "ok";
		$return['value'] = $destination;
	}
	else {
		$return['key']   = "error";
		$return['value'] = return_file_upload_errorcode($_FILES[$file]['error']);
	}
	return $return;
}

function add_image($array) {
	global $db, $settings;

	// set up return value
	$return = array();

	// We assume that $array contains all the values we need
	$img_array = build_assoc_array($array, array("file", "title", "source", "name", "email", "license", "disclaimerAgree", "date", "description"));

	// check if all needed values are inserted
	$check = check_array_required($img_array, array("title", "source", "name", "email", "license", "disclaimerAgree"));
	if ($check != "ok") {
		$return['key']   = "missing_values";
		$return['value'] = $check;
		return $return;
	}

	// Check if email is valid
	$regex = "/^[^@]+@[^@]+(\.[^@]+)+$/";
	if (!preg_match($regex, $array['email'])) {
		$return['key'] = "invalid_email";
		return $return;
	}

	// Check if the extension is supported
	$extension = strtolower(strrchr($_FILES["file"]["name"],"."));

	if (!in_array($extension, $settings['ALLOWED_EXTENSIONS'])) {
		$return['key'] = "unsupported_extension";
		return $return;
	}

	// Check if the resolution is high enough
	$imagesize = getimagesize($_FILES["file"]["tmp_name"]);
	if ( ($imagesize[0] < GE_MIN_RESOLUTION) || ($imagesize[1] < GE_MIN_RESOLUTION) ) {
		$return['key'] = "low_resolution";
		return $return;
	}

	// Okay, upload the image
	// First we make an unique name simply by using a sanitized title + the current UNIX timestamp
	$filename = sanitize_dashed($img_array["title"])."-".time().".jpg";

	$upload_result = upload_file("file", $filename);
	if ($upload_result['key'] == "error") {
		$return['key']	  = "upload_false";
		$return['value']  = $upload_result['value'];
		return $return;
	}
	else if ($upload_result['key'] == "ok") {
		$img_array["filename"] = $filename;
		unset($img_array["file"]);
		unset($img_array["disclaimerAgree"]);
	}

	// Also add a timestamp and IP to the DB entry
	$img_array["timestamp"] = time();
	$img_array["ip"] = $_SERVER['REMOTE_ADDR'];

	// build the query and execute it
	$result = $db->insert(TB_IMAGES, $img_array);
	if ($result) {
		$return['key']   = "db_ok";

		// Also add the id from mysql_insert_id()
		$img_array['id'] = $db->last_insert_id;
		$return['value'] = $img_array;
	}
	else {
		$return['key']   = "db_error";
		$return['value'] = mysql_error();
	}
	return $return;
}

function check_array_required($array, $required) {
	$return = '';
	foreach ($required as $value) {
		if ($array[$value] == "") {
			// we translate the form names as well
			$translate = array(
				'title'           => ___('FORM_TITLE'),
				'source'          => ___('FORM_SOURCE'),
				'name'            => ___('FORM_NAME'),
				'email'           => ___('FORM_EMAIL'),
				'disclaimerAgree' => ___('FORM_DISCLAIMER_AGREE')
			);

			foreach ($translate as $val=>$trans) {
				if ($value == $val) $value = $trans;
			}
			$return .= $value.", ";
		}
	}
	$return = ($return == '') ? "ok" : $return;
	return $return;
}

function build_assoc_array($inputarray, $okvalues) {
	$newarray = array();
	foreach ($inputarray as $key=>$value) {
		if (in_array($key, $okvalues)) {
			$newarray[$key] = $value;
		}
	}
	return $newarray;
}

function is_language($lang) {
	global $settings;
	return in_array($lang, $settings['LANGUAGES']);
}

/*
* Sends an e-mail to OTRS containing the data from this upload
*/
function send_otrs_mail($data) {
	global $settings;

	$id = $data['id'];
	$secret = md5(GE_SECRET.$data['id'].$data['timestamp']);

	$msg =  "Content-Type: text/plain; charset=UTF-8\n";
	$msg .= "
	Beste OTRS vrijwillger,
	zojuist is er op www.wikiportret.nl een nieuwe foto geupload.
	Deze foto heeft als titel '".$data['title']."', gemaakt door '".$data['source']."'
	onder de licentie '".$data['license']."' met als omschrijving '".$data['description']."'
	De uploader heeft het volgende IP-adres: '".$data['ip']."'

	Je kunt de foto bekijken op Wikiportret en de foto daar afwijzen, of een tekst genereren die
	je kan copy-pasten om een e-mail te schrijven.

	Klik op deze link:
	".GE_URL."admin/?id=$id&secret=$secret

	Als je vragen hebt over de uploadwizard kun je terecht bij Hay (Husky)
	via http://nl.wikipedia.org/wiki/Gebruiker:Husky of eventueel via hay@bykr.org.

	Al vast heel erg bedankt voor je medewerking!
	";

	// ok, mail
	$to 		= GE_OTRS_MAIL;
	$subject 	= "[Wikiportret] ".$data['title']." geupload op wikiportret.nl";
	if(GE_DEV_MODE > 0) $subject = "TEST-MODE:".$subject;
	$headers	= 'From: '.$data['email']."\r\n".
	'Reply-to: '.$data['email']."\r\n".
	'X-Mailer: PHP/'.phpversion();

	@$mail = mail($to, $subject, $msg, $headers);

	// Also mail to some extra people
	foreach($settings['EXTRA_MAIL_RECEIVERS'] as $address) {
		@mail($address,  $subject, $msg, $headers);
	}

	if ($mail) {
		return true;
	}
	else {
		return false;
	}
}

function show_page($page) {
	// echoes the contents of a static page from the /pages directory
	// used for the welcome page, form page, license page, etc.
	$file = ABSPATH.'pages/'.$page.'_'.$GLOBALS['settings']['language'].'.php';
	if (is_file($file)) {
		$text = file_get_contents($file);
	}
	else {
		$text = "<p class='error'>ERROR: No page could be found for language code '".$GLOBALS['settings']['language']."'</p>";
	}
	echo $text;
}

function show_wizard() {
	global $settings;

	$question   = $GLOBALS['question'];
	$questions 	= $GLOBALS['questions'];
	$actions 	= $GLOBALS['actions'];
	$phpself	= $_SERVER['PHP_SELF'];

	if (empty($question)) {
		// welcome, no questions yet
		show_language_chooser(); // gives a dropdown box of all available languages and changes $GLOBALS['settings']['language'] to that language
		echo "<p>".disp('WELCOME_MESSAGE')."</p>";
		echo '<a href="'.$phpself.'?question=first" class="question">'.disp('CLICK_TO_BEGIN').'</a>';
	}
	else if ($question == "licenses") {
		show_page('licenses');
		echo '<a href="'.GE_HOME.'" class="question">'.disp('START_AGAIN').'</a>';
	}
	else if ($question == "uploadform") {
		if (isset($_POST['btnUpload'])) {
			$result = add_image($_POST);
			$msg_status = "alert"; // we presume there will be an error :)
			if ($result['key'] == "db_ok") {
				$msg_status = "ok";
			}
			else if ($result['key'] == "upload_false") {
				$msg =  "Ik kon het bestand niet uploaden vanwege ".$result['value'];
			}
			else if ($result['key'] == "db_error") {
				$msg =  "Er was een fout met de database: ".$result['value'];
			}
			else if ($result['key'] == "thumbnail_error") {
				$msg =  "Er is een fout opgetreden bij het maken van de thumbnails: ".$result['value'];
				$msg .=  "<br />De foto is <strong>niet</strong> toegevoegd aan de website!";
			}
			else if ($result['key'] == "invalid_email") {
				$msg =  "Het door u opgegeven e-mail adres is niet correct";
			}
			else if ($result['key'] == "unsupported_extension") {
				$msg = "Het bestand wat u probeert te uploaden is geen afbeelding. De afbeeldingstypes die wij ondersteunen zijn: ";
				foreach ($settings['ALLOWED_EXTENSIONS'] as $ext) {
					$msg .= $ext.", ";
				}
			}
			else if ($result['key'] == "low_resolution") {
				$msg = "De resolutie van uw afbeelding is te laag. De minimale afmetingen van uw afbeelding moet 640x480 pixels zijn.";
			}
			else if ($result['key'] == "missing_values") {
				// missing values
				$msg =  "Deze waardes zijn niet ingevuld maar zijn wel noodzakelijk: ".$result['value'];
			}

			// files uploaded
			if ($msg_status == "ok") {
				// Send an e-mail to OTRS

				if (send_otrs_mail($result['value'])) {
					echo '<h2>'.disp('UPLOAD_SUCCESSFUL').'</h2>';
					echo '<p>'.disp('UPLOAD_SUCCESSFUL_MESSAGE').'</p>';
					echo '<big>'.disp('THANKS_UPLOAD').'</big>';
					echo '<a href="'.$phpself.'?question=uploadform" class="question">'.disp('UPLOAD_ANOTHER_IMAGE').'</a><br />';
					echo '<p style="margin-top:0;"><em>'.disp('UPLOAD_ANOTHER_IMAGE_WARNING').'</em></p>';
				}
				else {
					// OTRS mail failed for some reason
					echo '<h2>'.disp('UPLOAD_FAILED').'</h2>';
					echo '<p>'.disp('TRY_AGAIN_LATER').'</h2>';
				}
			}
			else {
				echo '<h2>'.disp('UPLOAD_FAILED').'</h2>';
				echo '<p>'.$msg.'</p>';
				echo '<p><a href="#" id="goback">'.disp('GO_BACK_CHANGE_VALUES').'</a></p>';
			}
		}
		else {
			// files not uploaded yet, only display upload form
			show_page('uploadform');
		}

		echo '<a href="'.GE_HOME.'" class="question">'.disp('START_AGAIN').'</a>';
	}
	else if ($question == "first") {
		// first time question
		// give 'SUBJECT_PROTECTED' in the function to avoid having to search explanations for 'first'
		question ($questions["SUBJECT_PROTECTED"][0], $questions['SUBJECT_PROTECTED'][1], $questions['SUBJECT_PROTECTED'][2], 'SUBJECT_PROTECTED');
	}
	else {
		// not the first question or welcome
		if (!array_key_exists($question, $actions)) {
			question($questions[$question][0], $questions[$question][1], $questions[$question][2], $question);
		}
		else {
			// not a question but an action
			action($actions[$question], $question);
		}
	}
}
