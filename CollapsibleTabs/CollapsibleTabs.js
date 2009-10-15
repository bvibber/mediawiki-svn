js2AddOnloadHook( function() {
	
	/* ANIMATION NOT READY FOR PRIME TIME 
	$j.collapsibleTabs.moveToCollapsed = function( ele ) {
		$moving = $j(ele);
		$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = true;
		data = $moving.data('collapsibleTabsSettings');
		// Remove the element from where it's at and put it in the dropdown menu
		target = $moving.data('collapsibleTabsSettings').collapsedContainer;
		// $moving.hide(500);
		$moving.css("position", "relative").css('right',0);
		$moving.animate({width: '0px'},"normal",function(){
			//$j(this).remove().prependTo(target).data('collapsibleTabsSettings', data).show();
			$j(this).remove().prependTo(target).data('collapsibleTabsSettings', data);
			$j(this).css("position", "static").css('right','auto').css("width", "auto");
			$j($j(ele).data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = false;
		});
		//$moving.remove().prependTo(target).data('collapsibleTabsSettings', data);
	};
	
	$j.collapsibleTabs.moveToExpanded = function( ele ) {
		$moving = $j(ele);
		$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = true;
		data = $moving.data('collapsibleTabsSettings');
		// remove this element from where it's at and put it in the dropdown menu
		target = $moving.data('collapsibleTabsSettings').prevElement;
		expandedWidth = $moving.data('collapsibleTabsSettings').expandedWidth;
		$moving.css("position", "relative").css('right',0).css('width','0px');
		$moving.remove().insertAfter(target).data('collapsibleTabsSettings', data)
			.animate({width: expandedWidth+"px"}, "normal", function(){
			//$j(this).remove().prependTo(target).data('collapsibleTabsSettings', data).show();
			$j(this).css("position", "static").css('right','auto');
			$j($moving.data('collapsibleTabsSettings').expandedContainer).data('collapsibleTabsSettings').shifting = false;

		});
	};
	*/
	
	$j('#p-views ul').collapsibleTabs().bind("beforeTabCollapse", function(){
		$j("#p-cactions").removeClass("emptyPortlet").addClass("filledPortlet");
	}).bind("beforeTabExpand", function(){
		if($j("#p-cactions li.collapsible").length==1) 
			$j("#p-cactions").removeClass("filledPortlet").addClass("emptyPortlet");
	});
	
});