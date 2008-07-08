<?php
/**
 * MonoBook nouveau
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

/**
NEW:
msg: footertext
hook: SkinTemplatePortlet
**/

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinMonoBook extends SkinTemplate {
	/** Using monobook. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'monobook';
		$this->stylename = 'monobook';
		$this->template  = 'MonoBookTemplate';
		# Bug 14520: skins that just include this file shouldn't load nonexis-
		# tent CSS fix files.
		$this->cssfiles = array( 'IE', 'IE50', 'IE55', 'IE60', 'IE70', 'rtl' );
	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class MonoBookTemplate extends QuickTemplate {

	function __construct() {
		parent::__construct();

		global $wgUser;
		$this->skin = $wgUser->getSkin();
	}

	function executeFixalpha() { /////////////////////////////////////////////////////////////////////////////////////////////////////////
		?>
		<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
		<?php
	}

	function executeSearchForm( ) { /////////////////////////////////////////////////////////////////////////////////////////////////////////
		?>
		<h5><label for="searchInput"><?php $this->msg('search') ?></label></h5>
		<div id="searchBody" class="pBody">
			<form action="<?php $this->text('searchaction') ?>" id="searchform"><div>
				<input id="searchInput" name="search" type="text"<?php echo $this->skin->tooltipAndAccesskey('search');
					if( isset( $this->data['search'] ) ) {
						?> value="<?php $this->text('search') ?>"<?php } ?> />
				<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('searcharticle') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-go' ); ?> />&nbsp;
				<input type='submit' name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?> />
			</div></form>
		</div>
		<?php
	}

	/**
	 * Method for generating a portlet. For use during template execution. 
	 *
	 * This method triggers the SkinTemplatePortlet hook, called with the parameters ( &$this, $bar, &$cont, &$hints )
	 * Implementors are free to modify the given parameters as appropriate.
	 *
	 * This method calls $this->printPortlet( $bar, $cont, $hints ), unless a  SkinTemplatePortlet hook returned false.
	 * Implementors of SkinTemplatePortlet hooks may call printPortlet directly, to execute "around", "before" or "after"
	 * the default code.
	 * 
	 * @param string $id the portlet's ID
	 * @param mixed $content the portlets content. Usually an array of items, or a string containing HTML
	 * @param array $hints an associative array of hints that determin how the portlet is generated. The following
	 *              hints are supported:
	 *              * portletClass: CSS class to be assigned to the portlet. Defaults to "portlet".
	 *              * idPrefix : prefix to be used with item-ids
	 *              * useClassForLinks: assign the item's CSS calls to the &lt;a&gt; element instead of the &lt;li&gt; element
	 *              * noTitle: suppresses the title element of the portlet
	 *              * titleMessage: id of the system message cotaining the title of this portlet. Defaults to $id
	 *              * noBody: suppress the pBody div
	 *              * bodyId: id to assign to the pBody div
	 *              * contentGenerator: callable to be invoked with the parameters ($cont, $hints). May print or return the HTML
	 *                                  code that should serve as the content of the portlet.
	 *              * itemGenerator: callable to be invoked with the parameters ($key, $val, $hints). May print or return the HTML
	 *                               code that for an item. This must be a &lt;li&gt; element.
	 *              * endOfListHooks: a list of hooks to be called at the end of the &lt;ul&gt; element, with the parameters ( &amp;$this )
	 */
	function executePortlet( $bar, &$cont, $hints = NULL ) { /////////////////////////////////////////////////////////////////////////////////////////////////////////
		wfProfileIn( __METHOD__ );

		if ( wfRunHooks( 'SkinTemplatePortlet', array( &$this, $bar, &$cont, &$hints ) ) ) {
			$this->printPortlet( $bar, $cont, $hints );
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Internal method for generating a portlet. Should be called only from executePortlet
	 * or from SkinTemplatePortlet hooks. Use executePortlet instead.
	 * @param string $id the portlet's ID
	 * @param mixed $content the portlets content. Usually an array of items, or a string containing HTML
	 * @param array $hints an associative array of hints that determin how the portlet is generated. The following
	 *              hints are supported:
	 *              * portletClass: CSS class to be assigned to the portlet. Defaults to "portlet".
	 *              * idPrefix : prefix to be used with item-ids
	 *              * useClassForLinks: assign the item's CSS calls to the &lt;a&gt; element instead of the &lt;li&gt; element
	 *              * noTitle: suppresses the title element of the portlet
	 *              * titleMessage: id of the system message cotaining the title of this portlet. Defaults to $id
	 *              * noBody: suppress the pBody div
	 *              * bodyId: id to assign to the pBody div
	 *              * contentGenerator: callable to be invoked with the parameters ($cont, $hints). May print or return the HTML
	 *                                  code that should serve as the content of the portlet.
	 *              * itemGenerator: callable to be invoked with the parameters ($key, $val, $hints). May print or return the HTML
	 *                               code that for an item. This must be a &lt;li&gt; element.
	 *              * endOfListHooks: a list of hooks to be called at the end of the &lt;ul&gt; element, with the parameters ( &amp;$this )
	 */
	function printPortlet( $id, &$cont, $hints ) { /////////////////////////////////////////////////////////////////////////////////////////////////////////
		wfProfileIn( __METHOD__ . '-prepare' );

		if ( $cont === false || $cont === NULL || ( is_array($cont) && !$cont) ) {
			wfProfileOut( __METHOD__ . '-prepare' );
			return;
		}

		wfProfileOut( __METHOD__ . '-prepare' );
		wfProfileIn( __METHOD__ . '-execute');

		$portletClass = isset( $hints['portletClass'] ) ? $hints['portletClass'] : 'portlet'; 
		$idPrefix = isset( $hints['idPrefix'] ) ? $hints['idPrefix'] : '';
		$useClassForLinks = isset( $hints['useClassForLinks'] ) ? $hints['useClassForLinks'] : false;
	?>
		<div class="<?php echo $portletClass?>" id='p-<?php echo Sanitizer::escapeId($id) ?>'<?php echo $this->skin->tooltip('p-'.$id) ?>>
		<?php
			if ( !isset( $hints['noTitle'] ) || !$hints['noTitle'] ) {
				$titleId = isset( $hints['titleMessage'] ) ? $hints['titleMessage'] : $id;
				$title = wfMsg( $titleId );
				if (wfEmptyMsg($titleId, $title)) $title = $id;
				echo "\t\t\t<h5>$title</h5>\n";
			}

			if ( !isset( $hints['noBody'] ) || !$hints['noBody'] ) {
				$attr = '';
				if ( isset( $hints['bodyId'] ) ) $attr = 'id="' . Sanitizer::escapeId($hints['bodyId']) . '"';
				echo "\t\t\t<div $attr class='pBody'>\n";
			}
		?>
	<?php   if ( isset( $hints['contentGenerator'] ) ) {
			echo call_user_func( $hints['contentGenerator'], $cont, $hints );
		} else if ( is_array( $cont ) ) { ?>
				<ul>
	<?php 			foreach($cont as $key => $val) {
					if ( isset( $hints['itemGenerator'] ) ) {
						echo call_user_func( $hints['itemGenerator'], $key, $val, $hints );
					} else if ( is_array($val) ) { 
						$cls = '';
						if ( !$useClassForLinks && isset( $val['class'] ) ) $cls .= $val['class'];
						if ( isset( $val['active'] ) && $val['active'] ) $cls .= 'active';
						
						$linkcls = ( $useClassForLinks && isset( $val['class'] ) ) ? $val['class'] : '';

						$id = isset( $val['id'] ) ? $val['id'] : $key;
						
						echo "\t\t\t<li ";
						if ( $id !== false ) echo ' id="' . $idPrefix . Sanitizer::escapeId( $id ) . '"';
						if ( $cls ) echo ' class="' . htmlspecialchars($cls) . '"';
						echo ">";

						if ( isset( $val['href'] ) && $val['href'] ) {
							echo '<a ';
							echo ' href="' . htmlspecialchars($val['href']) . '"';
							echo $this->skin->tooltipAndAccesskey($idPrefix . $id);
							if( $linkcls ) echo ' class="' . htmlspecialchars($linkcls) . '"';
							echo '>';
						}

						if ( isset( $val['html'] ) ) echo $val['html'];
						else if ( isset( $val['text'] ) ) echo htmlspecialchars( $val['text'] );
						else if ( isset( $val['msg'] ) ) $this->msg( $val['msg'] );
						else $this->msg( $id ); #XXX: with or without $idPrefix?

						if ( isset( $val['href'] ) && $val['href'] ) {
							echo '</a>';
						}
						echo "</li>\n";
					} else {
						if ( is_int($id) ) echo "<li>$val</li>";
						else echo "<li id=\"" . Sanitizer::escapeId( $key ) . "\">$val</li>";
					}
				} 
				
				if ( isset( $hints['endOfListHooks'] ) ) {
					foreach( $hints['endOfListHooks'] as $hook ) {
						wfRunHooks( $hook, array( &$this ) );
					}
				}
				?>
				</ul>
	<?php   } else {
				# allow raw HTML block to be defined by extensions
				print $cont;
		} 
	?>
	<?php   
		if ( !isset( $hints['noBody'] ) || !$hints['noBody'] ) {
			echo "\t\t\t</div>\n";
		}
	?>
		</div>
	<?php
		wfProfileOut( __METHOD__ . '-execute');
	}


	function executeContentPrelude() { /////////////////////////////////////////////////////////////////////////////////////////////////////////
	?>
			<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
			<div id="contentSub"><?php $this->html('subtitle') ?></div>
			<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
			<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
			<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
	<?php
	}

	function executeHeadScripts( ) { /////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
		
		<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>
                
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
		<!-- Head Scripts -->
		<?php $this->html('headscripts') ?>
		<?php	if($this->data['jsvarurl']) { ?>
				<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"><!-- site js --></script>
		<?php	} ?>
		<?php	if($this->data['pagecss']) { ?>
				<style type="text/css"><?php $this->html('pagecss') ?></style>
		<?php	}
				if($this->data['usercss']) { ?>
				<style type="text/css"><?php $this->html('usercss') ?></style>
		<?php	}
				if($this->data['userjs']) { ?>
				<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
		<?php	}
				if($this->data['userjsprev']) { ?>
				<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
		<?php	}
				if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
		<?php
	}

	function executeSidebarPortlets() {
		$this->executePortlet( 'cactions', 
					$this->data['content_actions'],
					array( 'titleMessage' => 'views',
						'idPrefix' => 'ca-') );

		$this->executePortlet( 'personal',
					$this->data['personal_urls'],
					array( 'titleMessage' => 'personaltools',
						'idPrefix' => 'pt-',
						'useClassForLinks' => true ) );

		$logo = '<a style="background-image: url(' . htmlspecialchars( $this->data['logopath'] ) . ');" ' .
			'href="' . htmlspecialchars($this->data['nav_urls']['mainpage']['href']).'" ' .
			$this->skin->tooltipAndAccesskey('n-mainpage') . '></a>';

		$this->executePortlet( 'logo',
					$logo,
					array( 'noTitle' => true,
						'noBody' => true ) );

		$this->executeFixalpha();

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
		if($this->data['poweredbyico']) { ?>
				<div id="f-poweredbyico"><?php $this->html('poweredbyico') ?></div>
		<?php 	}
		if($this->data['copyrightico']) { ?>
				<div id="f-copyrightico"><?php $this->html('copyrightico') ?></div>
		<?php	}

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
		<?php
	}


	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() { /////////////////////////////////////////////////////////////////////////////////////////////////////////

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

		<style type="text/css" media="screen, projection">/*<![CDATA[*/
			@import "<?php $this->text('stylepath') ?>/common/shared.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
			@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
		/*]]>*/</style>
		<link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('printcss') ?>?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
		<?php if( in_array( 'IE50', $this->skin->cssfiles ) ) { ?><!--[if lt IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE50Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<?php } if( in_array( 'IE55', $this->skin->cssfiles ) ) { ?><!--[if IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE55Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<?php } if( in_array( 'IE60', $this->skin->cssfiles ) ) { ?><!--[if IE 6]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE60Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<?php } if( in_array( 'IE70', $this->skin->cssfiles ) ) { ?><!--[if IE 7]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE70Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<?php } ?><!--[if lt IE 7]><?php if( in_array( 'IE', $this->skin->cssfiles ) ) { ?><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/IEFixes.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"></script>
		<?php } ?><meta http-equiv="imagetoolbar" content="no" /><![endif]-->
	<?php
		$this->executeHeadScripts();
	?>
	</head>
<body<?php if($this->data['body_ondblclick']) { ?> ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload']) { ?> onload="<?php $this->text('body_onload') ?>"<?php } ?>
 class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">
	<div id="globalWrapper">
	<div id="column-content">
	<div id="content">
		<a name="top" id="top"></a>
		<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
		<h1 class="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?></h1>
		<div id="bodyContent">
			<?php $this->executeContentPrelude() ?>
			<!-- start content -->
			<?php $this->html('bodytext') ?>
			<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
			<!-- end content -->
			<div class="visualClear"></div>
		</div>
	</div>
	</div>
	<div id="column-one">
	<?php
		$this->executeSidebarPortlets();
	?>
	</div><!-- end of the left (by default at least) column -->
	<div class="visualClear"></div>

	<div id="footer">
	<?php 
		$this->executeFooter();
	?>
	</div>
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
