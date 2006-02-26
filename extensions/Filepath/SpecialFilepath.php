<?php
if (!defined('MEDIAWIKI')) die();
/**
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfSpecialFilepath';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Filepath',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => '[[Special:Filepath|a special page]] to get the full path of a file from its name',
	'url' => 'http://meta.wikimedia.org/wiki/Filepath'
);

function wfSpecialFilepath() {
	global $IP, $wgMessageCache;

	$wgMessageCache->addMessages(
		array(
			'filepath' => 'File path',
			'filepath_instructions' => "Enter the name of an image to be redirected to the actual location of the image file",
			'filepath_page' => 'File: ',
			'filepath_submit' => 'Path',
		)
	);

	require_once "$IP/includes/SpecialPage.php";
	class SpecialFilepath extends SpecialPage {
		function SpecialFilepath() {
			SpecialPage::SpecialPage( 'Filepath' );
		}
		
		function execute( $par ) {
			global $wgRequest, $wgOut;

			$file = isset( $par ) ? $par : $wgRequest->getText( 'file' );
			
			$title = Title::makeTitleSafe( NS_IMAGE, $file );
			$article = new Article( $title );
			
			if ( ! is_null( $title ) && ! $article->exists() ) {
				$wgOut->setStatusCode( 404 );
				$this->setHeaders();
				$this->outputHeader();
				$cform = new FilepathForm( $title );
				$cform->execute();
			} else if ( is_null( $title ) ) {
				$this->setHeaders();
				$this->outputHeader();
				$cform = new FilepathForm( $title );
				$cform->execute();
			} else {
				$file = new Image( $title ); 
				$wgOut->redirect( $file->getURL() );
			}
		}
	}

	class FilepathForm {
		var $mTitle;
		
		function FilepathForm( &$title ) {
			$this->mTitle =& $title;
		}
		
		function execute() {
			global $wgOut, $wgTitle;

			$wgOut->addHTML(
				wfElement( 'form',
					array(
						'id' => 'specialfilepath',
						'method' => 'get',
						'action' => $wgTitle->escapeLocalUrl()
					),
					null
				) .
					wfOpenElement( 'label' ) .
						wfMsgHtml( 'filepath_page' ) .
						wfElement( 'input',
							array(
								'type' => 'text',
								'size' => 25,
								'name' => 'file',
								'value' => is_object( $this->mTitle ) ? $this->mTitle->getText() : ''
							),
							''
						) .
						' ' .
						wfElement( 'input',
							array(
								'type' => 'submit',
								'value' => wfMsgHtml( 'filepath_submit' )
							),
							''
						) .
					wfCloseElement( 'label' ) .
				wfCloseElement( 'form' )
			);
		}

	}

	SpecialPage::addPage( new SpecialFilepath );
}
