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
				updateEnd: null,
				busy: false
			},
			opt
		);
		
		return this.each( function() {
			var ele = this;
			var $me = jQuery( this );
			var $sp;
			var fnEnd;
			var offset = 0;
			var lsp = -1;
			
			_css();
			
			opt.boxTemplate = ( opt.boxTemplate || "<span class='" + opt.boxClass + "'>&nbsp</span>" );
			opt.batchTemplate = ( opt.batchTemplate || "<span></span>" );
			
			$sp = jQuery( "<div></div>" ).addClass( opt.scrollPaneClass );
			$me.append( $sp );
			offset = batch( $sp, offset, opt );
			$me.scrollTop(0).scrollLeft(0);
			
			_ab();
			
			fnEnd = vEnd;
			
			setTimeout( monEnd, opt.endDelay );
			
			if( typeof opt.updateBatch == 'function' ){
				setTimeout( handleScrolling, opt.scrollDelay );
			}
			
			function _css(){
				$me.css( {
					"overflow-x": "hidden",
					"overflow-y": "auto"
				} );
			}
			
			function _ab(){
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
			}
			
			function batch( $s, offset, opt ) {
				var $b;
				var i;
				var rp = opt.batchNum;
				
				while( rp-- ) {
					$b=jQuery( opt.batchTemplate )
						.attr({
							offset: offset,
							len: opt.batchSiz,
							storymodified: 0,
							storyid: 0
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
			
			function vScroll() {
				// If a batcnh is currently being loaded, we can't start another one yet.
				if ( opt.busy ) {
					return;
				}
				
				var so = $me.scrollTop();
				
				if( lsp != so) {
					lsp = so;
					var co = $me.offset().top;
					
					$sp.find( '> .' + opt.emptyBatchClass ).each( function( i, obj ) {
						var $b = jQuery( obj );
						var p = $b.position().top - co;
						
						if ( opt.lBound > p || p > opt.uBound ) { 
							return;
						}
						
						opt.busy = true;
						opt.updateBatch( $b.removeClass( opt.emptyBatchClass ), opt );
					});
				}
			};
			
			function vEnd() {
				if ( ele.scrollTop > 0 && ele.scrollHeight - ele.scrollTop < opt.eBound ) {
					offset = batch( $sp, offset, opt );
					return 1;
				}
				
				return opt.endDelay;
			};
			
			/**
			 * This function emulates a scroll event handler by firing itself every so many ms, and
			 * then calling a function checking if the user has scrolled, and if any batches should be loaded. 
			 */
			function handleScrolling() {
				vScroll();
				setTimeout( handleScrolling, opt.scrollDelay );
			};
			
			function monEnd() {
				if ( offset < opt.maxOffset ) {
					setTimeout( monEnd, fnEnd() );
				}
			}
			
		});
	}; 
})(jQuery);
