<?php
/** 
 * UploadWizardMessages
 *
 * Simple class to take messages from a modules' i18n.php and dump them into Javascript.
 * 
 * @file
 * @ingroup Upload
 */

/* This class is temporary.
  
   With the rejection of JS2 and reassignment of Michael Dale, there seems to be no currently supported 
   resource loader (July 2010), but UploadWizard already relies on things like 
   pluralization in messages which were only available in JS2. Also we have problems with how JS2 loads 
   things in IE6; there are bugs which neither I nor Trevor Parscal can figure out. So, we are temporarily 
   falling back to a simpler method of loading messages, which will hopefully 
   be superseded by the new loader Roan Kattouw & Trevor Parscal are writing. 

*/

class UploadWizardMessages {

	/**
	 * getMessagesJs generates a javascript addMessages() calls for a given module and language
	 *
	 * @param String $moduleName the name of the module
	 * @param String $langCode Name of scriptText module ( that hosts messages )
	 * @return string
	 */


	public static function getMessagesJs( $moduleName, $language ) {
		global $wgOut;

	 	// TODO this should be cached. Perhaps with Localisation Cache.
		global $wgExtensionMessagesFiles;

		// Empty out messages in the current scope
		$messages = array();
		require( $wgExtensionMessagesFiles[ $moduleName ] );
		
		// iterate over the default messages, and get this wiki's current messages
		// presumably this will include local overrides in MediaWiki: space	
		$messagesForJs = array();
		
		// 'en' is the default language, so it will be the most complete
		foreach ( array_keys( $messages['en'] ) as $key ) {
			$messagesForJs[ $key ] = wfMsgGetKey( $key, /*DB*/true, $language, /*Transform*/false ); 
		}

		$messagesJson = FormatJson::encode( $messagesForJs );
		return 'mw.addMessages(' . $messagesJson . ');';
	}


	static function getNormalizedLangCode( $langCode ) {
		global $wgLang;
		// Check the langCode
		if( !$langCode) {
			if ( $wgLang ) {
				$langCode = $wgLang->getCode();
			} else {
				$langCode = 'en'; // desperation
			}
		} 
		
	}

}
