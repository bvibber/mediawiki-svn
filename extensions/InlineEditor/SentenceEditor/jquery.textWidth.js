/**
 * Function to determine the actual usable width of a span.
 * This means that the span has to start at the left of the containing element,
 * and the text inside the span may wrap if it gets too long.
 */
( function( $ ) {
	$.fn.textWidth = function(){
		var element = $j(this);
		
		// build an outer element that stretches to the maximum width, so the span will
		// be located to the leftmost position
		var outer = $('<div style="width: 100%"></div>');
		
		// build a span inside the outer div
		var inner = $('<span></span>');
		inner.html(element.html());
		outer.append(inner);
		
		// place the outer div after the original element and hide the original element so it'll
		// be in exactly the same place
		element.after(outer);
		element.hide();
		
		// calculate the div of the span (which will wrap when it meets the maximum width)
		var width = inner.width();
		
		// remove the test elements and show the original element again
		outer.remove();
		element.show();
		
		return width;
	};
} ) ( jQuery );