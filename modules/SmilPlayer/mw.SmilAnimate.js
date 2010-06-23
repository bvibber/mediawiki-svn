
/**
* Handles the smil animate class
*/
mw.SmilAnimate = function( smilObject ){
	return this.init( smilObject );
}
mw.SmilAnimate.prototype = {

	// Constructor: 
	init: function( smilObject ){		
		this.smil = smilObject;
	},
	
	/** 
	* Transform a smil element for a requested time.
	*
	* @param {Element} smilElement Element to be transformed
	* @param {float} animateTime The relative time to be transfored. 
	*/
	transformElement: function( smilElement, animateTime ) {
		var nodeName = $j( smilElement ).get(0).nodeName ;
		mw.log("transformForTime: " + nodeName );
		switch( nodeName.toLowerCase() ){
			case 'smiltext':
				return this.transformTextForTime( smilElement, animateTime);
			break;
			case 'img': 
				return this.transformImageForTime( smilElement, animateTime);
			break;			
		}
	},
	
	transformTextForTime: function( textElement, animateTime ) {
		if( $j( textElement ).children().length == 0 ){
			// no text transform children
			return ;
		}
		
		// xxx Note: we could have text transforms in the future: 
		var textCss = this.smil.getLayout().transformSmilCss( textElement );
		
		var bucketText = '';
		var textBuckets = [];
		var clearInx = 0;
		var el = $j( textElement ).get(0);
		for ( var i=0; i < el.childNodes.length; i++ ) {	
			var node = el.childNodes[i];
			// Check for text Node type: 
			if( node.nodeType == 3 ) {					
				bucketText += node.nodeValue;
			} else if( node.nodeName == 'clear'){
				var clearTime = this.smil.parseTime(  $j( node ).attr( 'begin') );
				// append bucket
				textBuckets.push( {
					'text' : bucketText,
					'clearTime' : clearTime 
				} );
				// Clear the bucket text collection
				bucketText ='';
			}
		}			
		
		var textValue ='';
		// Get the text node in range given time:
		for( var i =0; i < textBuckets.length ; i++){
			var bucket = textBuckets[i];
			if( animateTime < bucket.clearTime ){
				textValue = bucket.text;
				break;
			}
		}
		// Update the text value target
		// xxx need to profile update vs check value
		$j( '#' + this.smil.getAssetId( textElement )  )
			.html( 
				$j('<span />')
				// Add the text value
				.text( textValue )
				.css( textCss	)
			)
	},
	
	transformImageForTime: function( smilImgElement, animateTime ) {
		var _this = this;
		if( $j( smilImgElement ).children().length == 0 ){
			// no image transform children
			return ;
		}
		// Get transform elements in range
		$j( smilImgElement ).find( 'animate' ).each( function( inx, animateElement ){
			var begin = _this.smil.parseTime(  $j( animateElement ).attr( 'begin') );
			var duration = _this.smil.parseTime(  $j( animateElement ).attr( 'dur') );
			//mw.log( "b:" + begin +" < " + animateTime + " && b+d: " + ( begin + duration ) + " > " + animateTime );
			
			// Check if the animate element is in range
			var cssTransform = {};			
			if( begin <= animateTime && ( begin + duration ) >= animateTime ) {
				// Get the transform type:
				switch( $j( animateElement ).attr('attributeName') ){
					case 'panZoom':						
						// Get the pan zoom css for "this" time 
						_this.transformPanZoom ( smilImgElement, animateElement, animateTime );
					break;
					default: 
						mw.log("Error unrecognized Annimation attributName: " +
							 $j( animateElement ).attr('attributeName') ); 
					
				}
				//mw.log("b:transformImageForTime: " +  $j( animateElement ).attr( 'values' ) );
				//$j( smilImgElement ).css( cssTransform );
			}
			
		});
	},
	/**
	* get the css layout transforms for a panzoom transform type
	* 
	* http://www.w3.org/TR/SMIL/smil-extended-media-object.html#q32
	* 
	*/
	transformPanZoom: function( smilImgElement, animateElement, animateTime ){
		var begin = this.smil.parseTime(  $j( animateElement ).attr( 'begin') );
		var duration = this.smil.parseTime(  $j( animateElement ).attr( 'dur') );
		
		// internal offset
		var relativeAnimationTime = animateTime - begin;
		
		// Get target panZoom for given animateTime 
		var animatePoints = $j( animateElement ).attr('values').split( ';' );
		
		// Get the target interpreted value
		var targetValue = this.getInterpolatePointsValue( animatePoints, relativeAnimationTime, duration );
								
		// Let Top Width Height
		// translate values into % values
		// NOTE this is dependent on the media being "loaded" and having natural width and height
		var namedValueOrder = ['left', 'top', 'width', 'height' ];
		var htmlAsset = $j( '#' + this.smil.getAssetId( smilImgElement ) ).get(0);
		
		var percentValues = {};
		for( var i =0 ;i < targetValue.length ; i++ ){
			if( targetValue[i].indexOf('%') == -1 ){
				switch( namedValueOrder[i] ){
					case 'left':
					case 'width':
						percentValues[ namedValueOrder[i] ] = parseFloat( targetValue[i] ) / htmlAsset.naturalWidth;
					break;
					case 'height':
					case 'top':
						percentValues[ namedValueOrder[i] ] =  parseFloat( targetValue[i] ) / htmlAsset.naturalHeight 
					break;
				}				
			} else {
				percentValues[ namedValueOrder[i] ] = parseFloat( targetValue[i] ) / 100;
			} 
		}		
		
		// Now we have "hard" layout info try and render it. 
		this.updateElementLayout( smilImgElement, percentValues );		
		
		// Now set the target value
		
		//~first get image scale~
		
		// check for a "viewWindow" ( if not wrap the image in a view window ) 
		
		/*
		
		"scale mode"?
		
		fit: 
		"width or height dominate"? 
		
		width X percentage "virtualPixles" 
		height relative to width
		
		layout: 
			viewWindow ( defined in "real" pixles. 
		*/
	},
	
	// xxx need to refactor move to "smilLayout"
	updateElementLayout: function( smilElement, percentValues ){
		
		mw.log("updateElementLayout::" + percentValues.top + ' ' + percentValues.left + ' ' + percentValues.width + ' ' + percentValues.height );
		// get a pointer to the html target:
		var $target = $j( '#' + this.smil.getAssetId( smilElement ));
		
		var htmlAsset = $j( '#' + this.smil.getAssetId( smilElement ) ).get(0);
		
		// find if we are height or width bound
		
		// Setup target height width based target region size	
		var fullWidth = $target.parents('.smilRegion').width() ;
		var fullHeight =  $target.parents('.smilRegion').height() ;
		var targetWidth = fullWidth;
		var targetHeight = targetWidth * ( 
			( percentValues['height'] * htmlAsset.naturalHeight )				
			/ 
			( percentValues['width'] * htmlAsset.naturalWidth ) 
		)		
		// Check if it exceeds the height constraint: 	
		var sourceScale = ( targetHeight <  fullHeight ) 
			? (1 / percentValues['width'] )
			: (1 / percentValues['height'] )
		
		
		// Wrap the target and absolute the image layout ( if not already ) 
		if( $target.parent('.refTransformWrap').length === 0 ){
			$target		
			.wrap( 
				$j( '<div />' )
				.css( {
					'position' : 'relative',
					'overflow' : 'hidden',
					'width'	: '100%',
					'height' : '100%'
				} )
				.addClass('refTransformWrap') 
			)
		}	
		// run the css transform
		$target.css({ 
			'position' : 'absolute', 
			'width' : sourceScale *100 + '%',
			'height': sourceScale *100 + '%',
			'top' : (-1 * percentValues['top'])*100 + '%',
			'left' : (-1 * percentValues['left'])*100 + '%',
		})		
			
		// set up the offsets for the percentage wrap. 
		
		// scale the 
	},
	
	/**
	* getInterpolatePointsValue
	* @param animatePoints Set of points to be interpolated 
	*/ 
	getInterpolatePointsValue: function( animatePoints, relativeAnimationTime,  duration ){
		// For now only support "linear" transforms 
		// What two points are we animating between: 
		var timeInx = ( relativeAnimationTime / duration ) * animatePoints.length ;
		// if timeInx is zero just return the first point: 
		if( timeInx == 0 ){
			return animatePoints[0].split(',');
		}
		// make sure we are in bounds: 
		var startInx = ( Math.floor( timeInx ) -1 ); 
		startInx = ( startInx < 0 ) ? 0 : startInx; 		
		var startPointSet = animatePoints[ startInx ].split( ',' );					
		var endPointSet = animatePoints[ Math.ceil( timeInx) -1 ].split( ',' );
		
		var interptPercent = ( relativeAnimationTime / duration ) / ( animatePoints.length -1 );
		// Interpolate between start and end points to get target "value"
		var targetValue = []; 
		for( var i = 0 ; i < startPointSet.length ; i++ ){			
			targetValue[ i ] = parseFloat( startPointSet[i] ) + ( parseFloat( endPointSet[i] ) - parseFloat( startPointSet[i] ) ) *  interptPercent;
			// Retain percent measurement			
			targetValue[ i ] += ( startPointSet[i].indexOf('%') != -1 ) ? '%' : ''; 
		}
		return targetValue;
	}
	
}