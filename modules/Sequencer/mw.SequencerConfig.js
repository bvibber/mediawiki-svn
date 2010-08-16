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
	
	// Default timeline "video / image" clip thumb size
	"Sequencer.TimelineVideoThumbSize" : 100,
	
	// Default timeline "audio / collapsed" size 
	"Sequencer.TimelineVideoThumbSize" : 30
})
	