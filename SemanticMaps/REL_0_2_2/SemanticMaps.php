<?php
  
/**
 * Initialization file for the Semantic Maps extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Semantic_Maps
 *
 * @file SemanticMaps.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define('SM_VERSION', '0.3');

$smgScriptPath = $wgScriptPath . '/extensions/SemanticMaps';
$smgIP = $IP . '/extensions/SemanticMaps';

$wgExtensionFunctions[] = 'smfSetup';

$wgHooks['AdminLinks'][] = 'smfAddToAdminLinks';

$wgExtensionMessagesFiles['SemanticMaps'] = $smgIP . '/SemanticMaps.i18n.php';

// Autoload the general classes
$wgAutoloadClasses['SMMapPrinter'] = $smgIP . '/SM_MapPrinter.php';
$wgAutoloadClasses['SMMapper'] = $smgIP . '/SM_Mapper.php';
$wgAutoloadClasses['SMFormInput'] = $smgIP . '/SM_FormInput.php';

function smfSetup() {
	global $wgExtensionCredits, $egMapsMainServices, $wgExtensionCredits;

	$services_list = implode(', ', $egMapsMainServices);
	
	wfLoadExtensionMessages( 'SemanticMaps' );
	
	$wgExtensionCredits['other'][]= array(
		'path' => __FILE__,
		'name' => wfMsg('semanticmaps_name'),
		'version' => SM_VERSION,
		'author' => array("[http://bn2vs.com Jeroen De Dauw]", "Yaron Koren", "Robert Buzink"),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Maps',
		'description' => wfMsg( 'semanticmaps_desc', $services_list ),
		'descriptionmsg' => array( 'semanticmaps_desc', $services_list ),
	);

	smfInitFormat('map');
	smfInitializeService('map');
	
	foreach($egMapsMainServices as $service) smfInitFormat($service);
	
	smfInitializeServiceAliases('googlemaps');
	smfInitializeServiceAliases('yahoomaps');
	smfInitializeServiceAliases('openlayers');
}

/**
 * Apply smfInitializeService() to a service and all it's aliases.
 *
 * @param unknown_type $mainServiceName
 * @param unknown_type $queryPrinter
 */
function smfInitializeServiceAliases($mainServiceName) {
	global $egMapsServices;
	
	smfInitializeService($mainServiceName, $mainServiceName);
	foreach ($egMapsServices[$mainServiceName] as $alias) smfInitializeService($alias, $mainServiceName);
}

/**
 * Add the service name as result format for the provided query printer, 
 * and set a hook for it's form input.
 *
 * @param unknown_type $service
 * @param unknown_type $queryPrinter
 * @param unknown_type $mainName
 */
function smfInitializeService($service, $mainName = '') {
	global $sfgFormPrinter;

	// Add the form input hook for the service
	$field_args = array();
	if (strlen($mainName) > 0) $field_args['service_name'] = $mainName;
	$sfgFormPrinter->setInputTypeHook($service, 'smfSelectFormInputHTML', $field_args);
}

/**
 * Initialize the result format depending on the map service
 */
function smfInitFormat( $format ) {
	global $smwgResultFormats, $wgAutoloadClasses, $smgIP;
	
	switch ($format) {
		case 'map':
			$class = 'SMMapper';
			$file = $smgIP . '/SM_Mapper';
		break;				
		case 'googlemaps':
			$class = 'SMGoogleMaps';
			$file = $smgIP . '/GoogleMaps/SM_GoogleMaps';
		break;
		case 'openlayers':
			$class = 'SMOpenLayers';
			$file = $smgIP . '/OpenLayers/SM_OpenLayers';
		break;
		case 'yahoomaps':
			$class = 'SMYahooMaps';
			$file = $smgIP . '/YahooMaps/SM_YahooMaps';
		break;
	}

	if (isset($class) && isset($file)) {
		// Check if $smwgResultFormats, a global variable introduced in SMW 1.2.2, 
		// is set and add the query printer to the result format.
		if (isset($smwgResultFormats)) {
			$smwgResultFormats[$format] = $class;
		}
		else {
			SMWQueryProcessor::$formats[$format] = $class;
		}
		
		if ($format != 'map') {
			$wgAutoloadClasses[$class] = $file . ".php";
			$wgAutoloadClasses[$class . "FormInput"] = $file . "FormInput.php";
		}
	}
	


}

/**
 * Class for the form input type 'map'. The relevant form input class is called depending on the provided service.
 *
 * @param unknown_type $coordinates
 * @param unknown_type $input_name
 * @param unknown_type $is_mandatory
 * @param unknown_type $is_disabled
 * @param unknown_type $field_args
 * @return unknown
 */
function smfSelectFormInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
	global $egMapsAvailableServices, $egMapsDefaultService;
	
	// If service_name is set, use this value, and ignore any given service parameters
	// This will prevent ..input type=googlemaps|service=yahoo.. from shwoing up a Yahoo! Maps map
	if (array_key_exists('service_name', $field_args)) $field_args['service'] = $field_args['service_name'];
		
	$field_args['service'] = MapsMapper::getValidService($field_args['service']);
	
	// Create an instace of 
	switch ($field_args['service']) {
		case 'googlemaps' :
			$formInput = new SMGoogleMapsFormInput();
			break;			
		case 'openlayers' :
			$formInput = new SMOpenLayersFormInput();
			break;
		case 'yahoomaps' :
			$formInput = new SMYahooMapsFormInput();
			break;				
	}
	
	// Get and return the form input HTML from the hook corresponding with the provided service
	return $formInput->formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args);		
}

function smfGetDynamicInput($id, $value, $args='') {
	// By De Dauw Jeroen - November 2008 - http://code.bn2vs.com/viewtopic.php?t=120
	return '<input id="'.$id.'" '.$args.' value="'.$value.'" onfocus="if (this.value==\''.$value.'\') {this.value=\'\';}" onblur="if (this.value==\'\') {this.value=\''.$value.'\';}" />';
}

/**
 * Adds a link to Admin Links page
 */
function smfAddToAdminLinks(&$admin_links_tree) {
    $displaying_data_section = $admin_links_tree->getSection(wfMsg('smw_adminlinks_displayingdata'));
    // Escape if SMW hasn't added links
    if (is_null($displaying_data_section))
        return true;
    $smw_docu_row = $displaying_data_section->getRow('smw');
    wfLoadExtensionMessages('SemanticMaps');
    $sm_docu_label = wfMsg('adminlinks_documentation', wfMsg('semanticmaps_name'));
    $smw_docu_row->addItem(AlItem::newFromExternalLink("http://www.mediawiki.org/wiki/Extension:Semantic_Maps", $sm_docu_label));
    return true;
}

