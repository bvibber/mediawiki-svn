
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
			mw.log( "b:" + begin +" < " + animateTime + " && b+d: " + ( begin + duration ) + " > " + animateTime );
			
			// Check if the animate element is in range
			var cssTransform = {};
			if( begin < animateTime && ( begin + duration ) > animateTime ) {
				// Get the tranform type: 
				switch( $j( animateElement ).attr('attributeName') ){
					case 'panZoom':						
						// Get the pan zoom css for "this" time 
						_this.transformPanZoom ( smilImgElement, animateElement, animateTime );
					break;
					default: 
						mw.log("Error unrecognized Annimation attributName: " +
							 $j( animateElement ).attr('attributeName') ); 
					
				}
				mw.log("b:transformImageForTime: " +  $j( animateElement ).attr( 'values' ) );
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
		
		// Get the target intepreted value
		var targetValue = this.getInterpetedPointsValue( animatePoints, relativeAnimationTime, duration );
								
		// Let Top Width Height
		// translate values into % values
		// NOTE this is depenent on the media being "loaded" and having natural width and height
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
		debugger;
		
		// Now set the target value
		
		//~first get image scale~
		
		// check for a "viewWindow" ( if not wrap the image in a view window ) 
		
		/*
		
		"scale mode"?
		
		fit: 
		"width or height dominiate"? 
		
		width X percetnage "virtualPixles" 
		height relative to width
		
		layout: 
			viewWindow ( defined in "real" pixles. 
		*/
	},
	
	// xxx need to refactor
	updateElementLayout: function( smilEmelent, percentValues ){
		// get a pointer to the hmtl target:
		var $target = $j( '#' + this.smil.getAssetId( smilEmelent ));
		
		// get the scale via width ( need to think about this might need to support either )
		// width is 20% of orginal means we have to scale up 1/ .2 

	
		// Wrap the target and absolute the image layout ( if not already ) 
		if( $target.parent('.refTransformWrap').length === 0 ){
			$target
			.css({ 
				'position' : 'abolute', 
				'width' : (1 / percentValues['width'])*100 + '%',
				'height' : (1 / percentValues['height'])*100 + '%',
				'top' : (-1 * percentValues['top'])*100 + '%',
				'left' : (-1 * percentValues['left'])*100 + '%',
			})
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
		debugger;
		// set up the offsets for the percentage wrap. 
		
		// scale the 
	},
	
	/**
	* getInterpetedPointsValue
	*/ 
	getInterpetedPointsValue: function( animatePoints, relativeAnimationTime,  duration){
		// For now only support "linear" transforms 
		// What two points are we animating between: 
		var timeInx = ( relativeAnimationTime / duration ) * animatePoints.length ;
		var startPointSet =  animatePoints[ Math.floor( timeInx ) -1 ].split( ',' );
		var endPointSet = animatePoints[ Math.ceil( timeInx) - 1 ].split( ',' );
		
		var interptPercent = ( relativeAnimationTime / duration ) / ( animatePoints.length -1 );
		// Interpolate between start and end points to get target "value"
		var targetValue = []; 
		for( var i = 0 ; i < startPointSet.length ; i++ ){			
			targetValue[ i ] = parseFloat( startPointSet[i] ) + ( parseFloat( endPointSet[i] ) - parseFloat( startPointSet[i] ) ) *  interptPercent;
			// Retain percent messurment			
			targetValue[ i ] += ( startPointSet[i].indexOf('%') != -1 ) ? '%' : ''; 
		}
		return targetValue;
	}
	
}