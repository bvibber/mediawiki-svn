<?php
/**
 * Modern skin, derived from monobook template.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/** */
require_once( dirname(__FILE__) . '/MonoBook.php' );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinModern extends SkinTemplate {
	/*
	 * We don't like the default getPoweredBy, the icon clashes with the 
	 * skin L&F.
	 */
	function getPoweredBy() {
	global	$wgVersion;
		return "<div class='mw_poweredby'>Powered by MediaWiki $wgVersion</div>";
	}

	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'modern';
		$this->stylename = 'modern';
		$this->template  = 'ModernTemplate';
	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class ModernTemplate extends MonoBookTemplate {

	function executeSidebarPortlets() {
		$logo = '<a style="background-image: url(' . htmlspecialchars( $this->data['logopath'] ) . ');" ' .
			'href="' . htmlspecialchars($this->data['nav_urls']['mainpage']['href']).'" ' .
			$this->skin->tooltipAndAccesskey('n-mainpage') . '></a>';

		$hints = array( 'portletClass' => 'generated-sidebar portlet' );

		foreach ($this->data['sidebar'] as $bar => $cont) {
			$this->executePortlet( $bar, $cont, $hints );
		}

		$this->executePortlet( 'search', $this->skin,
					array( 'noTitle' => true, 'noBody' => true, #XXX: title and body are a bit special. This is somewhat nasty.
						'contentGenerator' => array( &$this, 'executeSearchForm' ) ) );

		$this->executePortlet( 'tb', 
					$this->data['toolbox_urls'],
					array( 'titleMessage' => 'toolbox',
						'idPrefix' => 't-',
						'endOfListHooks' => array( 'MonoBookTemplateToolboxEnd', 'SkinTemplateToolboxEnd' ) ) );

		$this->executePortlet( 'lang', 
					$this->data['language_urls'],
					array( 'titleMessage' => 'otherlanguages' ) );
	}


	function executeFooter() { /////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Generate additional footer links
		?>
			<ul id="f-list">
		<?php
		$footer = wfMsg( 'footertext' );
		if ( !wfEmptyMsg('footertext', $footer) ) {
			echo $footer;
		}
		else {
			foreach( $this->data['footer_links'] as $lnk ) {
		?>		<li id="<?php echo $lnk['id']; ?>"><?php echo $lnk['html']; ?></li>
		<?php		
			}
		}
		?>
			</ul>
		<?php echo $this->html("poweredbyico"); ?>
		<?php
	}

	/**
	 * Template filter callback for Modern skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgUser;
		$skin = $wgUser->getSkin();

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php 
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php $this->html('headlinks') ?>
		<title><?php $this->text('pagetitle') ?></title>
		<?php if(empty($this->data['printable']) ) { ?>
		<style type="text/css" media="screen, projection">/*<![CDATA[*/
			@import "<?php $this->text('stylepath') ?>/common/shared.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
			@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
		/*]]>*/</style>
		<?php } ?>
		<link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/print.css?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
		<!--[if lt IE 7]><meta http-equiv="imagetoolbar" content="no" /><![endif]-->
		
	<?php
		$this->executeHeadScripts();
	?>
	</head>
<body<?php if($this->data['body_ondblclick']) { ?> ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload'    ]) { ?> onload="<?php     $this->text('body_onload')     ?>"<?php } ?>
 class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">

	<!-- heading -->
	<div id="mw_header">
		<h1 id="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?></h1>
	</div>

	<div id="mw_main">
	<div id="mw_contentwrapper">
	<!-- navigation portlet -->
	<?php
	$this->executePortlet( 'cactions', 
				$this->data['content_actions'],
				array( 'titleMessage' => 'views',
					'idPrefix' => 'ca-') );
	?>

	<!-- content -->
	<div id="mw_content">
	<!-- contentholder does nothing by default, but it allows users to style the text inside
	     the content area without affecting the meaning of 'em' in #mw_content, which is used
	     for the margins -->
	<div id="mw_contentholder">
		<div class='mw-topboxes'>
			<div class="mw-topbox" id="siteSub"><?php $this->msg('tagline') ?></div>
			<?php if($this->data['newtalk'] ) {
				?><div class="usermessage mw-topbox"><?php $this->html('newtalk')  ?></div>
			<?php } ?>
			<?php if($this->data['sitenotice']) {
				?><div class="mw-topbox" id="siteNotice"><?php $this->html('sitenotice') ?></div>
			<?php } ?>
		</div>

		<div id="contentSub"><?php $this->html('subtitle') ?></div>

		<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
		<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#mw_portlets"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>

		<?php $this->html('bodytext') ?>
		<div class='mw_clear'></div>
		<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
	</div><!-- mw_contentholder -->
	</div><!-- mw_content -->
	</div><!-- mw_contentwrapper -->

	<div id="mw_portlets">
	<?php
		$this->executeSidebarPortlets();
	?>
	</div><!-- mw_portlets -->

	</div><!-- main -->

	<div class="mw_clear"></div>

	<!-- personal portlet -->
	<?php
	$this->executePortlet( 'personal',
				$this->data['personal_urls'],
				array( 'titleMessage' => 'personaltools',
					'idPrefix' => 'pt-',
					'useClassForLinks' => true ) );
	?>

	<!-- footer --> 
	<div id="footer">
	<?php 
		$this->executeFooter();
	?>
	</div>

	<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>
-->
<?php endif; ?>
</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method
} // end of class
?>
