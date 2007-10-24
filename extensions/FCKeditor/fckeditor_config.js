/*
 * FCKeditor Extension for MediaWiki specific settings.
 */

// When using the modified image dialog you must set this variable. It must
// correspond to $wgScriptPath in LocalSettings.php.
FCKConfig.mwScriptPath = '' ;     

// Setup the editor toolbar.
FCKConfig.ToolbarSets['Wiki'] = [
	['Source'],
	['Cut','Copy','Paste',/*'PasteText','PasteWord',*/'-','Print'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['SpecialChar','Table','Image','Rule'],
	['MW_Template','MW_Ref','MW_Math'],
	'/',
	['FontFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Blockquote'],
//	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
//	['TextColor','BGColor'],
	['FitWindow','-','About']
] ;

// Load the extension plugins.
FCKConfig.PluginsPath = FCKConfig.EditorPath + '../plugins/' ;
FCKConfig.Plugins.Add( 'mediawiki' ) ;

FCKConfig.ForcePasteAsPlainText = true ;
FCKConfig.FontFormats	= 'p;h1;h2;h3;h4;h5;h6;pre' ;

FCKConfig.AutoDetectLanguage	= false ;
FCKConfig.DefaultLanguage		= 'en' ;

// FCKConfig.DisableObjectResizing = true ;

FCKConfig.EditorAreaStyles = '\
.FCK__MWTemplate \
{ \
	border: 1px dotted #00F; \
	background-position: center center; \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_template.gif); \
	background-repeat: no-repeat; \
	width: 20px; \
	height: 15px; \
	vertical-align: middle; \
} \
.FCK__MWRef \
{ \
	border: 1px dotted #00F; \
	background-position: center center; \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_ref.gif); \
	background-repeat: no-repeat; \
	width: 18px; \
	height: 15px; \
	vertical-align: middle; \
} \
.FCK__MWReferences \
{ \
	border: 1px dotted #00F; \
	background-position: center center; \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_references.gif); \
	background-repeat: no-repeat; \
	width: 66px; \
	height: 15px; \
	vertical-align: middle; \
} \
' ;
