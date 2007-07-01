<?PHP 
if (!defined('MEDIAWIKI')) die();

require_once( 'SignDocumentHelpers.php' );

//TODO: Doc
class CreateSignDocument extends SpecialPage {
	
	/**
     * Constructor
     */
    function __construct() {
		parent::__construct( 'CreateSignDocument', 'createsigndocument' );
		self::loadMessages();
	}

	function execute($par = null) {
		global $wgOut, $wgRequest, $wgUser;

		if (!$wgUser->isAllowed( 'createsigndocument' )) {
			$wgOut->permissionRequired( 'createsigndocument' );
			return;
		}
		
		$this->setHeaders();

		if ( $wgRequest->wasPosted() )
			$this->dealWithPost();
		else
			$this->buildCreateForm();
			
			
	}

	function buildCreateForm() {
		global $wgOut, $wgGroupPermissions;

		$wgOut->addWikiText( wfMsg( 'createsigndoc-head' ) );

		$wgOut->addHTML('
			<form action="'. $_SERVER['PHP_SELF'] .'" method="post">
			<table><tr><td>
			<strong>' . wfMsg( 'createsigndoc-pagename' ) . '&nbsp;</strong>
			</td><td>
			<input id="pagename" type="text" name="pagename" style="width: 400px;" />
			</td></tr><tr><td>
			<strong>' . wfMsg( 'createsigndoc-allowedgroup' ) . '&nbsp;</strong>
			</td><td>
			<select id="allowedgroup" name="group" style="width: 400px;">' 
			. $this->makeComboItems( array_keys($wgGroupPermissions) ) . ' </select>
			' . $this->checkMarks( 'email' ) . 
			$this->checkMarks( 'address' ) . 
			$this->checkMarks( 'extaddress' ) . 
			$this->checkMarks( 'phone' ) . 
			$this->checkMarks( 'bday' ) . '
			</td></tr><tr><td>
			<strong>' . wfMsg( 'createsigndoc-minage' ) . '&nbsp;</strong>
			</td><td><input id="minage" type="text" name="minage" style="width: 400px;"/>
			</td></tr><tr><td valign="top">
			<strong>' .wfMsg( 'createsigndoc-introtext' ) . '&nbsp;</strong>
			</td><td><textarea id="introtext" name="introtext" wrap="soft" style="height: 300px; width: 400px;">' .
			'</textarea>
			</td></tr><tr><td></td><td>
			<input type="submit" id="create" name="create" value="' . 
			wfMsg( 'createsigndoc-create' ) . '" />
			</td></tr>
			</table>
			</form>

		');
	}

	function checkMarks( $id ) {
		$out = '</td></tr><tr><td>';
		$out .= '<strong>' . wfMsg( "createsigndoc-$id" ) . '&nbsp;</strong>';
		$out .= '</td><td>';
		$out .=  Xml::checkLabel(
		            wfMsg( 'createsigndoc-hidden' ),
					"mwCreateSignDocHidden-$id",
					"mwCreateSignDocHidden-$id",
					false);
															
		$out .=  Xml::checkLabel(
		            wfMsg( 'createsigndoc-optional' ),
					"mwCreateSignDocOptional-$id",
					"mwCreateSignDocOptional-$id",
					false);
		return $out;
	}

	function makeComboItems( $arr ) {
		$ret = '';
		$selectedAttr = array( 'selected' => 'selected' );
		foreach ( $arr as $a ) {
			$ret .= wfElement( 'option', array(
				'value' => $a) + $selectedAttr, $a );
			$selectedAttr = array();
		}

		return $ret;
	}

	function dealWithPost() {
		global $wgOut;

		try {
			$bob = SignDocumentForm::newFromPost();
		}
		catch (Exception $e) {
			return $this->showError( 'generic', $e->getMessage() );
		}

		if (!$bob->loadArticleData())
			return $this->showError( 'pagenoexist', $bob->mPagename );
			
		if (!$bob->addToDb())
			return $this->showError( 'alreadycreated', $bob->mPagename );

		$wgOut->addWikiText( wfMsg( 'createsigndoc-success',
				$bob->mPagename, $bob->getId() ) );
	}

	function showError( $type, $args ) {
		global $wgOut;
		$wgOut->addWikiText( wfMsg("createsigndoc-error-$type", $args) );
		return;
	}

	function loadMessages() {
		static $messagesLoaded = false;
		global $wgMessageCache;
		
		if ( $messagesLoaded ) return;
		$messagesLoaded = true;

		require( dirname( __FILE__ ) . '/SpecialCreateSignDocument.i18n.php' );
		foreach ( $allMessages as $lang => $langMessages ) {
			$wgMessageCache->addMessages( $langMessages, $lang );
		}
	}																														            
										  

}
?>
