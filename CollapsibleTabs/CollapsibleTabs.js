js2AddOnloadHook( function() {
	
	// add first and last classes to left and right nav
	var $leftNavPosition = $j('#left-navigation').position().left;
	var $leftNavWidth = $j('#left-navigation').width();
	// add browser resive event
	$j(window).bind('resize', function () {
		if(( $leftNavPosition + $leftNavWidth) > $j('#right-navigation').position().left){
			// Collision! Pop the last item off the right nav and put it in the drop down
			console.log('collision!');
		}
	});
});