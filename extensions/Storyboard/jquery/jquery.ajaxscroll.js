/**
 * AjaxScroll (jQuery Plugin)
 * Modified for MediaWiki storyboard extension.
 *
 * @author Timmy Tin - http://project.yctin.com/ajaxscroll
 * @author Jeroen De Dauw
 * @license GPL
 * @version 0.2
 */
(function($) {
	$.fn.ajaxScroll = function( opt ) {
		opt = jQuery.extend(
			{
				batchNum: 5,
				batchSize: 30,
				batchTemplate: null,
				boxTemplate: null,
				batchClass: "storyboard-batch",
				boxClass: "storyboard-box",
				emptyBatchClass: "storyboard-empty",
				scrollPaneClass: "scrollpane",
				lBound: "auto",
				uBound: "auto",
				eBound: "auto",
				maxOffset: 1000,
				scrollDelay: 600, // The interval for checking if the user scrolled, in ms.
				endDelay: 100,
				updateBatch: null,
				updateEnd: null
			},
			opt
		);
		
		return this.each( function() {
			var ele = this;
			var $me = jQuery( this );
			var $sp;
			var offset = 0;
			var lsp = -1;
			
			$me.css( {
				"overflow-x": "hidden",
				"overflow-y": "auto"
			} );
			
			opt.boxTemplate = ( opt.boxTemplate || "<span class='" + opt.boxClass + "'>&nbsp</span>" );
			opt.batchTemplate = ( opt.batchTemplate || "<span></span>" );
			
			$sp = jQuery( "<div></div>" ).addClass( opt.scrollPaneClass );
			$me.append( $sp );
			offset = batch( $sp, offset, opt );
			$me.scrollTop(0).scrollLeft(0);
			
			var os = $me.find( '.batch:first' ).next().offset().top;
			var b = ( $me.height() / os + 1 ) * os;
			
			if ( "auto" == opt.uBound ) {
				opt.uBound = b;
			}
			
			if ( "auto" == opt.lBound ) {
				opt.lBound = -b;
			}
			
			if ( "auto" == opt.eBound ) {
				opt.eBound = b * 2;
			}
			
			setTimeout( monEnd, opt.endDelay );
			
			// Initiate the scroll handling.
			if( typeof opt.updateBatch == 'function' ){
				setTimeout( handleScrolling, opt.scrollDelay );
			}
			
			function batch( $s, offset, opt ) {
				var $b;
				var i;
				var rp = opt.batchNum;
				
				while( rp-- ) {
					$b = jQuery( opt.batchTemplate )
						.attr({
							offset: offset,
							storymodified: window.storyModified,
							storyid: window.storyId,							
							len: opt.batchSize
						})
						.addClass( opt.batchClass + " " + opt.emptyBatchClass );
					
					i = opt.batchSize;
					
					while( i-- && opt.maxOffset > offset++ ){
						$b.append( opt.boxTemplate );
					}
					
					$s.append( $b );
				}
				
				return offset;
			};
			
			/**
			 * This function emulates a scroll event handler by firing itself every so many ms.
			 * It checks if the user has scrolled down far enough, and calls the update batch
			 * function if this is the case.
			 */
			function handleScrolling() {
				var so = $me.scrollTop();
				
				if( !window.storyboardBusy && lsp != so ) {
					lsp = so;
					var co = $me.offset().top;
					
					$sp.find( '> .' + opt.emptyBatchClass ).each( function( i, obj ) {
						var $b = jQuery( obj );
						var p = $b.position().top - co;
						
						if ( opt.lBound > p || p > opt.uBound ) { 
							return;
						} 
						
						window.storyboardBusy = true;
						opt.updateBatch( $b.removeClass( opt.emptyBatchClass ) );
					});
				}
				
				setTimeout( handleScrolling, opt.scrollDelay );
			};

			function monEnd() {
				if ( offset < opt.maxOffset ) {
					setTimeout( monEnd, vEnd() );
				}
			}
			
			function vEnd() {
				if ( ele.scrollTop > 0 && ele.scrollHeight - ele.scrollTop < opt.eBound ) {
					offset = batch( $sp, offset, opt );
					return 1;
				}
				
				return opt.endDelay;
			};
			
		});
	}; 
})(jQuery);
