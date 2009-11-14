<?php
// General variables
$wikiportrait_version = "1.1beta-3";

if ( !@include 'config.php' ) {
	die( "Could not find config.php. Maybe you haven't made a configuration file yet?" );
}

require 'lib.php'; // General library
require 'lib-db.php'; // Global database Class
require 'lib-utils.php'; // Utility stuff
require 'lib-admin.php'; // stuff for admin/index.php

// connect to db
$db = new db( DB_HOST, DB_USER, DB_PASS, DB_DATABASE );

// start session
session_start();

// select language
$_SESSION['language'] = GE_LANGUAGE; // default

// Maybe overwrite with a custom setting?
if ( isset( $_GET['newLanguage'] ) && is_language( $_GET['newLanguage'] ) ) {
	$_SESSION['language'] = $_GET['newLanguage'];
}

// fill a global var with the language
$GLOBALS['settings']['language'] = $_SESSION['language'];

// fill all global variables with data from the db
fill_globals();
$GLOBALS['question'] = $_GET['question'];
