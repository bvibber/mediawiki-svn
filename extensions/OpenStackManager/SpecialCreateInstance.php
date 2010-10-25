<?php
class OpenStackCreateInstance extends SpecialPage {

	var $adminNova, $userNova;

	function __construct() {
		global $wgOpenStackManagerNovaAdminKeys;
		parent::__construct( 'OpenStackCreateInstance' );
		wfLoadExtensionMessages('OpenStackManager');
		$credentials = $wgOpenStackManagerNovaAdminKeys;
		$this->adminNova = new OpenStackNovaController( $credentials );

	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle;
 
		$this->setHeaders();
		$wgOut->setPagetitle("Create Instance");
 
		$instanceid = $wgRequest->getVal('instanceid');

		# TODO: Add project name field

		$instanceInfo = Array(); 
		$instanceInfo['instanceName'] = array(
			'type' => 'text',
			'label-message' => 'instancename',
			'default' => '',
			'section' => 'instance/info',
		);

		$instanceTypes = $this->adminNova->getInstanceTypes();
		$instanceType_keys = Array();
		foreach ( $instanceTypes as $instanceType ) {
			$instanceType_keys["$instanceType"] = $instanceType;
		}
		$instanceInfo['instanceType'] = array(
			'type' => 'select',
			'section' => 'instance/info',
			'options' => $instanceType_keys,
			'label-message' => 'instancetype',
		);

		# Availability zone names can't be translated. Get the keys, and make an array
		# where the name points to itself as a value
		$availabilityZones = $this->adminNova->getAvailabilityZones();
		$availabilityZone_keys = Array();
		foreach ( array_keys( $availabilityZones ) as $availabilityZone_key ) {
			$availabilityZone_keys["$availabilityZone_key"] = $availabilityZone_key;
		}
		$instanceInfo['availabilityZone'] = array(
			'type' => 'select',
			'section' => 'instance/info',
			'options' => $availabilityZone_keys,
			'label-message' => 'availabilityzone',
		);

		# Image names can't be translated. Get the image, and make an array
		# where the name points to itself as a value
		$images = $this->adminNova->getImages();
		$image_keys = Array();
		foreach ( array_keys($images) as $image_key ) {
			$image_keys["$image_key"] = $image_key;
		}
		$instanceInfo['imageType'] = array(
			'type' => 'select',
			'section' => 'instance/info',
			'options' => $image_keys,
			'label-message' => 'imagetype',
		);

		# Keypair names can't be translated. Get the keys, and make an array
		# where the name points to itself as a value
		# TODO: get keypairs as the user, not the admin
		$keypairs = $this->adminNova->getKeypairs();
		$keypair_keys = Array();
		foreach ( array_keys( $keypairs ) as $keypair_key ) {
			$keypair_keys["$keypair_key"] = $keypair_key;
		}
		$instanceInfo['keypair'] = array(
			'type' => 'select',
			'section' => 'instance/info',
			'options' => $keypair_keys,
			'label-message' => 'keypair',
		);

		#TODO: Add availablity zone field

		$instanceForm = new OpenStackCreateInstanceForm( $instanceInfo, 'openstackmanager-form' );
		$instanceForm->setTitle( SpecialPage::getTitleFor( 'OpenStackCreateInstance' ));
		$instanceForm->setSubmitID( 'openstackmanager-form-createinstancesubmit' );
		$instanceForm->setSubmitCallback( array( 'OpenStackCreateInstance', 'tryCreateSubmit' ) );
		$instanceForm->show();

	}

	function tryCreateSubmit( $formData, $entryPoint = 'internal' ) {
		global $wgOut;
		
		return true;
	}
}

class OpenStackCreateInstanceForm extends HTMLForm {
}
