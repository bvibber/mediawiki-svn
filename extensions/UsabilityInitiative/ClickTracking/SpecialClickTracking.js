(function($) {
	
	//functions
	$.colorizeTable = function (){
		
			//expert
			
			//get totals
			var expert_total = 0;
			
			$(".expert_data").each(function(){
				expert_total +=  parseInt($(this).attr( "value"));
			});
			
			//set proper red shade
			$(".expert_data").each(function(){
				var rval = 255;
				var gval = (expert_total == 0 ? 255 : 255 - (255 * $(this).attr("value") / expert_total));
				var bval = gval;
				rgbString = "rgb(" + parseInt(rval) + "," + parseInt(gval) + "," + parseInt(bval) + ")";
				$(this).css("color", rgbString);
				$(this).css("background-color", rgbString);
			});
					

			//intermediate
			
			//total
			var intermediate_total = 0;
			$(".intermediate_data").each(function(){
				intermediate_total +=  parseInt($(this).attr( "value"));
			});
			
			
			//blue shade
			$(".intermediate_data").each(function(){
				var rval = (intermediate_total == 0 ? 255 : 255 - (255 *  $(this).attr("value") / intermediate_total));
				var gval = rval;
				var bval = 255;
				rgbString = "rgb(" + parseInt(rval) + "," + parseInt(gval) + "," + parseInt(bval) + ")";
				$(this).css("color", rgbString);
				$(this).css("background-color", rgbString);
			});
			
			//total
			var basic_total = 0;
			$(".basic_data").each(function(){
				basic_total +=  parseInt($(this).attr( "value"));
			});
			
			//green shade
			$(".basic_data").each(function(){
				var rval = (basic_total == 0 ? 255 : 255 - (255 * $(this).attr("value") / basic_total));
				var gval = 255;
				var bval = rval;
				rgbString = "rgb(" + parseInt(rval) + "," + parseInt(gval) + "," + parseInt(bval) + ")";
				$(this).css("color", rgbString);
				$(this).css("background-color", rgbString);
			});
		
	};
	
	
	

	$.changeDataLinks = function (){
		$("#change_graph").click(function(){
			console.log($("#increment_date").val());
		});
		
		$(".event_name").each(function(){
			$(this).click(function(){
			//$j.changeData( $(this).attr( "value" ));
			
			console.log($(this).attr( "value" ));
			
			event_name = $(this).text();
			console.log(event_name);
			
			var processChartJSON = function(data, status){
				
				var getMax = function(findMax){
					var retval = Number.MIN_VALUE;
					for(var i in findMax){
						if(findMax[i] > retval) {
							retval = findMax[i];
						}
					}
					return retval;
				};
				
				max1 = getMax(data['datapoints']['expert']);
				max2 = getMax(data['datapoints']['intermediate']);
				max3 = getMax(data['datapoints']['basic']);
				max = Math.max(max3, Math.max(max1,max2));
				chartURL = 'http://chart.apis.google.com/chart?' +
							'chs=400x400&' +
							'cht=lc&' +
							'chco=FF0000,0000FF,00FF00&' +
							'chtt=' + event_name + ' from ' + $("#start_date").val() +' to ' +$("#end_date").val() + "&" +
							'chdl=' + 'Expert|Intermediate|Beginner' + "&"+
							'chxt=x,y&' +
							'chd=t:' + data['datapoints']['expert'].join(',') + "|" + 
								data['datapoints']['intermediate'].join(',') + "|" + data['datapoints']['basic'].join(',') + "&" +
							'chds=0,'+ max +',0,'+ max +',0,'+ max
				;
				
				console.log(chartURL);
				$("#chart_img").attr( "src",chartURL);
			};
			
			//post relevant info
			$j.post( wgScriptPath + '/api.php', { 'action': 'specialclicktracking', 'format': 'json', 'eventid': $(this).attr( "value" ), 'increment': $("#increment_date").val(), 'startdate':$("#start_date").val(), 'enddate':$("#end_date").val() } , processChartJSON, "json");
			
			
			
			});//click
		});//each
	};//adlink
	

return $(this);
})(jQuery);

//colorize the table on document.ready
js2AddOnloadHook($j.colorizeTable);
js2AddOnloadHook($j.changeDataLinks);
