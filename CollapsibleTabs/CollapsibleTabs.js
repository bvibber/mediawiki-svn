var CollapsibleTabs = {
	// Variable for storing the left position of the left nav
	leftNavPosition: 0,
	// Variable for storing the width of the left nav
	leftNavWidth: 0,
	// pointer to the containing ul for tabs
	tabContainer: null,
	// pointer to the containing ul for the drop down menu items
	dropDownConatiner: null,
	// Variable for storing the number of items initially in the drop down menu
	minDropDownItems: 0,
	// Variable for storing tabs sizes before they're placed in the drop down menu
	tabSizes: [],
	// action flag to avoid multiple events altering the dom concurrently
	shifting: false,

	init: function() {
		// store the left navigations position and width
		this.leftNavPosition = $j('#left-navigation').position().left;
		this.leftNavWidth = $j('#left-navigation').width();

		// store references to the two containers
		this.tabContainer = $j('#p-views ul');
		this.dropDownContainer = $j('#p-cactions ul');
		
		// store the number of items in the drop down menu on load
		this.minDropDownItems = this.dropDownContainer.children("li").length;
		
		// call the resizeHandler function to place all links in the correct spot for the current window size 
		this.resizeHandler();
		
		// bind the resizeHandler to the windows resize event
		$j(window).bind( 'resize', this.resizeHandler );
	},

	resizeHandler: function() {
		// do nothing if the dom is already moving an element
		if(CollapsibleTabs.shifting) return;
		
		// while their are still tabs available and the two navigations are colliding 
		while( CollapsibleTabs.tabContainer.children("li").length > 0 &&
			( CollapsibleTabs.leftNavPosition + CollapsibleTabs.leftNavWidth + 4) 
				> $j('#right-navigation').position().left ) {
					
			// set our action flag
			CollapsibleTabs.shifting = true;
			// move the element to the dropdown menu
			CollapsibleTabs.moveToDropDown( CollapsibleTabs.tabContainer.children('li:last') );
			// unset our action flag
			CollapsibleTabs.shifting = false;
			
		}
		
		// while there are still moveable items in the dropdown menu, 
		// and there is sufficient space to place them in the tab container
		while(CollapsibleTabs.dropDownContainer.children("li").length 
			> CollapsibleTabs.minDropDownItems && 
				( CollapsibleTabs.leftNavPosition + CollapsibleTabs.leftNavWidth + 4) 
					< ($j('#right-navigation').position().left - 
						CollapsibleTabs.tabSizes[CollapsibleTabs.tabSizes.length-1])) {
			
			// set our action flag
			CollapsibleTabs.shifting = true;
			//move the element from the dropdown to the tab
			CollapsibleTabs.moveToTab(CollapsibleTabs.dropDownContainer.children('li:first'));
			// unset our action flag
			CollapsibleTabs.shifting = false;
		}
	},

	moveToDropDown: function( ele ) {
		// push this elements width onto the tabSizes array so we know how much space
		// it will require to render it as a tab again
		this.tabSizes.push(ele.width());
		// Remove the element from where it's at and put it in the dropdown menu
		ele.remove().prependTo(this.dropDownContainer);
	},

	moveToTab: function( ele ) {
		// remove this elements width value from the tabSizes array
		this.tabSizes.pop();
		// remove this element from where it's at and put it in the dropdown menu
		ele.remove().appendTo(this.tabContainer);
	}
};

js2AddOnloadHook( function() {
	CollapsibleTabs.init();
});