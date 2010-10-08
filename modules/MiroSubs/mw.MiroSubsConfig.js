
/** 
 * Generates a miro subs config also see:
 * http://dev.universalsubtitles.org/widget/api_demo.html
 */
mw.MiroSubsConfig = {
	getConfig : function( embedPlayer, callback ){
	
		// Setup the config object: 
		var config = {};		
		
		// Check for page context variables
	
		// Get the latest subtitles * hard code example  
		var existingSubs = [
		      {
                   'subtitle_id': 'a',
                   'text': 'First sub',
                   'start_time': 3.4,
                   'end_time': 6.9,
                   'sub_order': 1
               },
               {
                   'subtitle_id': 'b',
                   'text': 'Second sub',
                   'start_time': 7.4,
                   'end_time': 10.9,
                   'sub_order': 2
               }
           ];

		// Covert into the mirosubs format
		var config ={
				'subtitles': existingSubs,
				'closeListener': function(){
					// close event refresh page? 
					alert('closed editor');
				},
				'videoURL': 'http://videos.mozilla.org/firefox/3.5/switch/switch.ogv',
				'username': wgUserName, // or use actual username
				'save': function(jsonSubs, callback, cancelCallback) {
					mw.log('save text:')
				},
				'permalink': 'http://myvideopermalink.com',
				// not sure if this is needed
				'login': function( ){
					 mirosubs.api.loggedIn( wgUserName )
				},
				'embedCode' : 'some code to embed'
		}
		callback( config );		
	}
};