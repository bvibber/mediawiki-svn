<?php

//set this
$wgExperimentalSkinPath = 'ExperimentalSkins/skin-name-here.css';

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

UsabilityInitiativeHooks::addStyle( $wgExperimentalSkinPath );

