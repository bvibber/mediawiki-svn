<?php
$messages = array();
$messages['en'] = array( 
	//About the Extension
	'emergencydesysop' => 'Emergency DeSysop',
	'emergencydesysop-desc' => 'Allows a Sysop to sacrifice their own bit, in order to desysop another',

	//Extension Messages
	'emergencydesysop-title' => 'Remove sysop access from both current user and another sysop',
	'emergencydesysop-otheradmin' => 'Other Sysop to degroup',
	'emergencydesysop-reason' => 'Reason for removal',
	'emergencydesysop-submit' => 'Submit',
	'emergencydesysop-incomplete' => 'All form fields are required, please try again.',
	'emergencydesysop-notasysop' => 'The target user is not in the sysop group.',
	'emergencydesysop-nogroups' => 'None',
	'emergencydesysop-done' => 'Action complete, both you and [[$1]] have been desysopped.',
	'emergencydesysop-invalidtarget' => 'The target user does not exist.',
	'emergencydesysop-blocked' => 'You cannot access this page while blocked',
	'emergencydesysop-noright' => 'You do not have sufficient permissions to access this page',


	//Rights Messages
	'right-emergencydesysop' => 'Able to desysop another user, mutually.',

);
