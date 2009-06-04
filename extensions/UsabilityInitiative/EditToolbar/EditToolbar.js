/* JavaScript for EditToolbar extension */

/**
 * Prototype for global editToolbar object
 * @param {String} toolbarSelector jQuery compatible selector of toolbar div
 */
function EditToolbar( toolbarSelector ) {
	
	/* Private Members */
	
	// Reference to object's self
	var self = this;
	// Reference to DIV container object (set on initialize)
	var toolbarDiv = null;
	// Sections (main and subs), groups and buttons
	var tools = { main: {}, subs: {} };
	// Internationalized user interface messages
	var messages = {};
	
	/* Functions */
	
	/**
	 * Initializes the toolbar user interface
	 */
	this.initialize = function() {
		// Path to images (THIS WILL HAVE TO CHANGE IF YOU MOVE THIS INTO CORE)
		var imagePath = wgScriptPath +
			'/extensions/UsabilityInitiative/EditToolbar/images/';
		// Gets object handle for container
		toolbarDiv = $( toolbarSelector );
		// Checks if the toolbar div existed
		if ( toolbarDiv ) {
			// Loops over each main group
			for ( group in tools.main ) {
				// Creates tool group
				var groupDiv = $( '<div class="group"></div>' );
				// Appends group to toolbar
				groupDiv.appendTo( toolbarDiv );
				for ( tool in tools.main[group] ) {
					// Creates tool
					var toolImg = $( '<img />' );
					// Appends tool to group
					toolImg.appendTo( groupDiv );
					// Customizes the tool
					toolImg.attr({
						src: imagePath + tools.main[group][tool].icon,
						alt: messages[group + '-' + tool],
						title: messages[group + '-' + tool]
					});
					// Sets button action
					toolImg.click( tools.main[group][tool].action );
				}
			}
		}
	}
	
	/**
	 * Adds a tool to the toolbar
	 * @param {String} section ID of section to add tool to
	 * @param {String} group ID of group to add tool to
	 * @param {String} tool ID of tool to add
	 * @param {Object} configuration Object of configuration for tool
	 */
	this.addTool = function( section, group, tool, configuration ) {
		// Checks if the section is valid
		if ( section in tools ) {
			// Checks if the group doesn't exist in the section
			if ( !( group in tools[section] ) ) {
				// Adds the group to the section 
				tools[section][group] = {};
			}
			// Checks if the tool doesn't exist in the group
			if ( !( tool in tools[section][group] ) ) {
				// Adds tool and configuration to group
				tools[section][group][tool] = configuration;
			}
		}
	}
	
	/**
	 * Sets several user interface messages
	 * @param {Object} messageList List of key/value pairs of messages
	 */
	this.setMessages = function( messageList ) {
		for ( messageItem in messageList ) {
			messages[messageItem] = messageList[messageItem];
		}
	}
	
	/**
	 * Sets a user interface message
	 * @param {String} key Key of message
	 * @param {String} value Value of message
	 */
	this.setMessage = function( key, value ) {
		messages[key] = value;
	}
	
	/**
	 * Gets a user interface message
	 * @param {String} key Key of message
	 */
	this.getMessage = function( key ) {
		if ( key in messages ) {
			return messages[key];
		} else {
			return key;
		}
	}
	
	/**
	 * Performs the action associated with a tool
	 * @param {String} section ID of section of tool to use
	 * @param {String} group ID of group of tool to use
	 * @param {String} tool ID of tool to use
	 */
	this.useTool = function( section, group, tool ) {
		// Checks if the tool exists
		if ( tool in tools[section][group] ) {
			// Adds tool and configuration to group
			tools[section][group][tool].action();
		}
	}
}

// Creates global toolbar object
var editToolbar = new EditToolbar( '#edittoolbar' );
// Executes function when document is ready
$( document ).ready( function() {
	// Initializes edit toolbar
	editToolbar.initialize();
});

/**
 * This is a problem for internationalization - so clearly this will be moved
 * or restructured at some point.
 */
// Adds tools to toolbar
editToolbar.addTool(
	'main', 'format', 'bold',
	{
		icon: 'format-bold.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				"'''", null, editToolbar.getMessage( 'format-bold-example' )
			);
			return false;
		}
	}
);
editToolbar.addTool(
	'main', 'format', 'italic',
	{
		icon: 'format-italic.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				"''", null, editToolbar.getMessage( 'format-italic-example' )
			);
			return false;
		}
	}
);
editToolbar.addTool(
	'main', 'insert', 'ilink',
	{
		icon: 'insert-ilink.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'[[', ']]', editToolbar.getMessage( 'insert-ilink-example' )
			);
			return false;
		}
	}
);
editToolbar.addTool(
	'main', 'insert', 'xlink',
	{
		icon: 'insert-xlink.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'[', ']', editToolbar.getMessage( 'insert-xlink-example' )
			);
			return false;
		}
	}
);
editToolbar.addTool(
	'main', 'insert', 'image',
	{
		icon: 'insert-image.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'[[File:', ']]', editToolbar.getMessage( 'insert-image-example' )
			);
			return false;
		}
	}
);
editToolbar.addTool(
	'main', 'insert', 'reference',
	{
		icon: 'insert-reference.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'<ref>', '</ref>', editToolbar.getMessage( 'insert-reference-example' )
			);
			return false;
		}
	}
);