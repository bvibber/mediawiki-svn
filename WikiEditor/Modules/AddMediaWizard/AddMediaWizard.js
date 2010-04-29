
/* JavaScript for AddMediaWizard gadget */

if( typeof mwAddMediaConfig == 'undefined'){
    mwAddMediaConfig = {};
}
mwAddMediaConfig['enabled_providers'] = [ 'wiki_commons', 'upload' ];

// Transclude mwEmbed.js
importScriptURI('http://prototype.wikimedia.org/s-2/js/mwEmbed/remotes/mediaWiki.js?&uselang=' +  wgUserLanguage );
