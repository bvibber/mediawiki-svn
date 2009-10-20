js2AddOnloadHook( function() {
	
	//Overloading the moveToCollapsed function to animate the transition 
	$j.collapsibleTabs.moveToCollapsed = function( ele ) {
		var $moving = $j(ele);
		$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = true;
		var data = $moving.data('collapsibleTabsSettings');
		// Remove the element from where it's at and put it in the dropdown menu
		var target = $moving.data('collapsibleTabsSettings').collapsedContainer;
		// $moving.hide(500);
		$moving.css("position", "relative").css('right',0);
		$moving.animate({width: '1px'},"normal",function(){
			//$j(this).remove().prependTo(target).data('collapsibleTabsSettings', data).show();
			$j(this).hide();
			$j(this).remove().prependTo(target).data('collapsibleTabsSettings', data);
			$j(this).attr('style', '');
			$j($j(ele).data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = false;
			$j.collapsibleTabs.handleResize();
		});
	};
	
	// Overloading the moveToExpanded function to animate the transition
	$j.collapsibleTabs.moveToExpanded = function( ele ) {
		var $moving = $j(ele);
		$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = true;
		var data = $moving.data('collapsibleTabsSettings');
		// remove this element from where it's at and put it in the dropdown menu
		var target = $moving.data('collapsibleTabsSettings').prevElement;
		var expandedWidth = $moving.data('collapsibleTabsSettings').expandedWidth;
		$moving.css("position", "relative").css('left',0).css('width','1px');
		$moving.remove().css('width','1px').insertAfter(target).data('collapsibleTabsSettings', data)
			.animate({width: expandedWidth+"px"}, "normal", function(){
			$j(this).attr('style', '');
			$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = false;
			$j.collapsibleTabs.handleResize();
		});
	};
	
	// Bind callback functions to animate our drop down menu in and out
	// and then call the collapsibleTabs function on the menu 
	$j('#p-views ul').bind("beforeTabCollapse", function(){
		if($j('#p-cactions').css('display')=='none')
		$j("#p-cactions").addClass("filledPortlet").removeClass("emptyPortlet")
			.find('h5').css('width','1px').animate({'width':'26px'}, 390);
	}).bind("beforeTabExpand", function(){
		if($j('#p-cactions li').length==1)
		$j("#p-cactions h5").animate({'width':'1px'},370, function(){
			$j(this).attr('style','').parent().addClass("emptyPortlet").removeClass("filledPortlet");
		});
	}).collapsibleTabs();
	
});