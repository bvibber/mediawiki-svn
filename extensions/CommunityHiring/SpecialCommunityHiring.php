<?php

class SpecialCommunityHiring extends SpecialPage {
	function __construct() {
		parent::__construct( 'CommunityHiring' );
		wfLoadExtensionMessages( 'CommunityHiring' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut;
		
		wfLoadExtensionMessages( 'CommunityHiring' );
		
		$wgOut->setPageTitle( 'Job Openings/Community Department' );
		
		$formDescriptor = array(
			'about-intro' => array(
				'type' => 'info',
				'default' => wfMsgExt( 'communityhiring-about-intro', 'parse' ),
				'raw' => 1,
				'section' => 'aboutyou',
			),
			'given-name' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-given',
				'section' => 'aboutyou',
			),
			'family-name' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-family',
				'section' => 'aboutyou',
			),
			'address-line1' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-address',
				'section' => 'aboutyou',
				'rows' => '3',
				'cols' => '20',
			),
			'address-city' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-address-city',
				'section' => 'aboutyou',
			),
			'address-postal' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-address-postal',
				'section' => 'aboutyou',
			),
			'address-country' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-address-country',
				'section' => 'aboutyou',
			),
			'phone' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-phone',
				'section' => 'aboutyou',
			),
			'email' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-email',
				'section' => 'aboutyou',
			),
			
			// Pararaph answers
			'paragraph-intro' => array(
				'type' => 'info',
				'default' => wfMsgExt( 'communityhiring-paragraphs-intro', 'parse' ),
				'raw' => 1,
				'section' => 'paragraphs',
				'vertical-label' => 1,
			),
			'significance' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-significance',
				'section' => 'paragraphs',
				'rows' => 10,
				'vertical-label' => 1,
			),
			'excitement' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-excitement',
				'section' => 'paragraphs',
				'rows' => 10,
				'vertical-label' => 1,
			),
			'experiences' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-experiences',
				'section' => 'paragraphs',
				'rows' => 10,
				'vertical-label' => 1,
			),
			'other' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-other',
				'section' => 'paragraphs',
				'rows' => 10,
				'vertical-label' => 1,
			),
			
			// Demonstrative info
			'languages' => array(
				'type' => 'textarea',
				'options' => array_flip( Language::getLanguageNames() ),
				'section' => 'demonstrative/languages',
				'rows' => '3',
				'label-message' => 'communityhiring-languages-label',
				'vertical-label' => 1,
			),
			
			'contributor' => array(
				'type' => 'radio',
				'label-message' => 'communityhiring-contributor',
				'section' => 'demonstrative/involvement',
				'options' => array(
					'Yes' => 1,
					'No' => 0,
				),
			),
			'usernames' => array(
				'type' => 'textarea',
				'rows' => '3',
				'cols' => '20',
				'label-message' => 'communityhiring-usernames',
				'section' => 'demonstrative/involvement',
				'vertical-label' => 1,
			),
			'wikimedia-links' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-links',
				'section' => 'demonstrative/involvement',
				'rows' => '3',
				'cols' => '20',
				'vertical-label' => 1,
			),
			'other-links' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-links-other',
				'section' => 'demonstrative',
				'rows' => '3',
				'cols' => '20',
				'vertical-label' => 1,
			),
			
			// Availability
			'availability-time' => array(
				'type' => 'text',
				'label-message' => 'communityhiring-availability-intro',
				'section' => 'availability',
				'vertical-label' => 1,
			),
			'availability-info' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-availability-info',
				'section' => 'availability',
				'rows' => '5',
				'cols' => '20',
				'vertical-label' => 1,
			),
			'relocation' => array(
				'type' => 'radio',
				'label-message' => 'communityhiring-relocation-ok',
				'section' => 'availability',
				'vertical-label' => 1,
				'options' => array(
						'Yes' => 'yes',
						'No' => 'no',
						'It would be hard, but maybe I would' => 'maybe'
						),
			),
			
			// Quick research question
			'research' => array(
				'type' => 'textarea',
				'label-message' => 'communityhiring-research',
				'vertical-label' => 1,
			),
		);
		
		$form = new HTMLForm( $formDescriptor, 'communityhiring' );
		
		$form->setIntro( wfMsgExt( 'communityhiring-intro', 'parse' ) );
		$form->setSubmitCallback( array( $this, 'submit' ) );
		$form->setTitle( $this->getTitle() );
		
		$form->show();
	}
	
	function submit( $info ) {
		global $wgOut;
		
		$dbw = wfGetDB( DB_MASTER );
		
		$dbw->insert( 'community_hiring_application', array( 'ch_data' => json_encode($info) ),
				__METHOD__ );
				
		$wgOut->addWikiMsg( 'communityhiring-done' );
		
		return true;
	}
}
