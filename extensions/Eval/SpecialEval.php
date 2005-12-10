<?php
if ( ! defined( 'MEDIAWIKI' ) ) die();
/**
 * An special page extension that provides a public interface to PHP's eval()
 * function, supersecure, install it on your production servers, no really!
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfSpecialEval';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Eval',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'adds [[Special:Eval|an interface]] to the <code>eval()</code> function',
);

function wfSpecialEval() {
	global $IP, $wgMessageCache, $wgContLang, $wgContLanguageCode;

	$wgMessageCache->addMessages(
		array(
			'eval' => 'Eval',
			'eval_submit' => 'Evaluate',
			'eval_out' => 'Output',
			'eval_code' => 'Code'
		)
	);

	require_once "$IP/includes/SpecialPage.php";
	class Evaluate extends SpecialPage {
		function Evaluate() {
			SpecialPage::SpecialPage( 'Eval' );
		}
		
		function execute( $par ) {
			global $wgOut, $wgRequest, $wgUseTidy;

			$this->setHeaders();

			$code = isset( $par ) ? $par : $wgRequest->getText( 'code' );
			
			$eform = new EvaluateForm( $code );

			if ( trim( $code ) === '' )
				$eform->execute();
			else {
				$eform->execute();
				
				$eout = new EvaluateOutput( $code );
				$eout->execute();
			}
		}
	}

	class EvaluateForm {
		var $mCode;
		
		function EvaluateForm( $code ) {
			$this->mCode =& $code;
		}
		
		function execute() {
			global $wgOut, $wgTitle;

			$wgOut->addHTML(
				wfElement( 'form',
					array(
						'id' => 'specialeval',
						'method' => 'get',
						'action' => $wgTitle->escapeLocalUrl()
					),
					null
				) .
					wfOpenElement( 'label' ) .
						wfElement( 'textarea',
							array(
								'cols' => 40,
								'rows' => 10,
								'name' => 'code',
							),
							$this->mCode
						) .
						' ' .
						wfElement( 'br', null, '' ) .
						wfElement( 'input',
							array(
								'type' => 'submit',
								'value' => wfMsgHtml( 'eval_submit' )
							),
							''
						) .
					wfCloseElement( 'label' ) .
				wfCloseElement( 'form' )
			);
		}

	}

	class EvaluateOutput {
		var $mCode, $mErr;
		
		function EvaluateOutput( &$code ) {
			$this->mCode =& $code;
		}
		
		function execute() {
			$this->start();
			ob_start();
			eval( $this->mCode );

			$this->mErr = ob_get_clean();
			$this->err();
		}

		function start() {
			global $wgOut;

			$wgOut->addHTML(
				wfElement( 'h2', null, wfMsg( 'eval_out' ) )
			);
		}

		function err() {
			global $wgOut;

			if ( $this->mErr !== '' ) {
				$wgOut->addHTML(
					preg_replace( '/^<br \/>/', '', $this->mErr )
				);
				$this->code();
			}
		}

		function code() {
			global $wgOut;

			$lines = explode( "\n", $this->mCode );

			if ( count( $lines ) ) {
				$wgOut->addHTML(
					wfElement( 'h2', null, wfMsg( 'eval_code' ) ) .
					wfOpenElement( 'ol' )
				);
				
				foreach ( $lines as $i => $line )
					$wgOut->addHTML(
						wfElement( 'li', null, $line )
					);
				
				$wgOut->addHtml( wfCloseElement( 'ol' ) );
			}
		}
	}
	
	SpecialPage::addPage( new Evaluate );
}
