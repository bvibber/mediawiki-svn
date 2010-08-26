/**
 * Master default configuration for sequencer
 * 
 * Do not modify this file rather after including mwEmbed 
 * set any of these configuration values via
 * the mw.setConfig() method 
 * 
 */

// Define the class name 
mw.SequencerConfig = true;

mw.setDefaultConfig({
	// If the sequencer should attribute kaltura
	"Sequencer.KalturaAttribution" : true,

	// The size of the undo stack 
	"Sequencer.numberOfUndos" : 100,
	
	// Default image duration
	"Sequencer.AddMediaImageDuration" : 2,
	
	// Default image source width
	"Sequencer.AddMediaImageWidth" : 640,
	
	// Default timeline clip timeline track height
	"Sequencer.TimelineTrackHeight" : 100,
	
	// Default timeline audio or collapsed timeline height 
	"Sequencer.TimelineColapsedTrackSize" : 35,

	// Asset domain restriction array of domains or keyword 'none'
	// Before any asset is displayed its domain is checked against this array of wildcard domains
	// Additionally best effort is made to check any text/html asset references  
	// for example [ '*.wikimedia.org', 'en.wikipeida.org']
	"Sequencer.DomainRestriction" : 'none'
})
	