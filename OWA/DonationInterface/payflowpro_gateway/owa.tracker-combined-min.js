// OWA Tracker Min file created 1286236498 

//// Start of json2 //// 

//// End of owa.tracker //// 

///hard-coded///
//<![CDATA[
//OWA.setSetting('debug', true);
// Set base URL
OWA.setSetting('baseUrl', 'http://analytics.tesla.usability.wikimedia.org/wiki/d/extensions/owa/');
OWA.setApiEndpoint('http://analytics.tesla.usability.wikimedia.org/wiki/d/index.php?action=owa&owa_specialAction');
// Create a tracker
OWATracker = new OWA.tracker();
OWATracker.setEndpoint('http://analytics.tesla.usability.wikimedia.org/wiki/d/index.php?action=owa&owa_logAction=1');
OWATracker.setSiteId('e0ae80323e1a995598038d4f3dd913f8');
OWATracker.trackPageView();
OWATracker.trackClicks();
OWATracker.trackDomStream();
//]]>
