/* JavaScript for Toolbar extension */

/**
 * Prototype for global toolbar object
 * @param {String} toolbarSelector jQuery compatible selector of toolbar div
 */
function EditingToolbar( toolbarSelector ) {
	
	/* Private Members */
	
	// Reference to object's self
	var self = this;
	// Reference to DIV container object (set on initialize)
	var toolbarDiv = null;
	// Sections (main and subs), groups and buttons
	var tools = { main: {}, subs: {} };
	// Path to images (THIS WILL HAVE TO CHANGE WHEN YOU MOVE THIS INTO CORE)
	var path = wgScriptPath + '/extensions/UsabilityInitiative/Toolbar/images/';
	
	/* Functions */
	
	/**
	 * Initializes the toolbar user interface
	 */
	this.initialize = function() {
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
					// Creates tool button
					var toolDiv = $( '<div class="tool"></div>' );
					// Appends button to group
					toolDiv.appendTo( groupDiv );
					// Customizes button image
					toolDiv.css(
						'background-image',
						'url(' + path + tools.main[group][tool].icon + ')'
					);
					// Sets button action
					toolDiv.click( tools.main[group][tool].action );
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
var editingToolbar = new EditingToolbar( '#editing-toolbar' );
// Executes function when document is ready
$( document ).ready( function() {
	// Initializes editing toolbar
	editingToolbar.initialize();
});

/**
 * This is a problem for internationalization - so clearly this will be moved
 * or restructured at some point.
 */
// Adds tools to toolbar
editingToolbar.addTool(
	'main', 'format', 'bold',
	{
		icon: 'format-bold.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				"'''", null, 'Bold text'
			);
			return false;
		}
	}
);
editingToolbar.addTool(
	'main', 'format', 'italic',
	{
		icon: 'format-italic.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				"''", null, 'Italic text'
			);
			return false;
		}
	}
);
editingToolbar.addTool(
	'main', 'insert', 'link',
	{
		icon: 'insert-link.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'[[', ']]', 'Internal link'
			);
			return false;
		}
	}
);
editingToolbar.addTool(
	'main', 'insert', 'image',
	{
		icon: 'insert-image.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'[[File:', ']]', 'Image name'
			);
			return false;
		}
	}
);
editingToolbar.addTool(
	'main', 'insert', 'reference',
	{
		icon: 'insert-reference.png',
		action: function() {
			$( '#wpTextbox1' ).encapsulateSelection(
				'<ref>', '</ref>', 'Reference content'
			);
			return false;
		}
	}
);