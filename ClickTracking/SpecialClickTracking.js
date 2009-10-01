(function($) {
	/* Very limited JSON encoder */
	$.json_encode = function (js_obj){
		var returnstr = "{ ";
		
		//trailing commas and json don't mix
		var propertynum = 0;
		for(property in js_obj){
			if(propertynum > 0){
				returnstr +=", ";
			}
			returnstr += "\"" + property + "\"" + " : ";
			if(typeof js_obj[property] == 'object'){
				returnstr += $.json_encode(js_obj[property]);
			}
			else{
				returnstr += "\"" + js_obj[property] + "\" ";
			}
			propertynum++;
		}
		
		returnstr+= " }";
		return returnstr;
	};
	
	
	
	$.renderUserDefDialogWith = function (userDef, defName){
		//change name
		$("#user_def_alter_legend").text($("#user_def_alter_legend").data("defaultChangeText") + " " + defName);
		$("#user_def_alter_legend").data("currentlyEditing", defName);
		console.dir(userDef);
		
		var setContribs = function(conditionArray, contribName){
			
			initialDiv = $("<div>").attr("id", contribName +"_div");
			initialDiv.addClass("checkbox_div");
			initialDiv.addClass("control_div");
			
			textDiv = $("<div>").attr("id", contribName +"_text_div");
			mainCheckbox = $("<input>").attr("id", contribName +"_checkbox");
			mainCheckbox.attr("type", "checkbox");
			mainCheckbox.addClass("user_def_checkbox");
			
			if(conditionArray.length > 0){
				mainCheckbox.attr("checked", true);
			}
			
			textDiv.append(mainCheckbox);
			textDiv.text(""); //i18n txt here
			initialDiv.append(textDiv);
			
			var i=0;
			console.dir(conditionArray);
			for( var condition in conditionArray){
				i++;
				conditionDiv = $("<div>").attr("id", contribName + "_range_" + i + "_div");
				conditionDiv.addClass("checkbox_div");
				conditionDiv.addClass("sub_option_div");

				//initialDiv.append(conditionDiv);
				cCheckbox = $("<input>").attr("id", contribName+"_"+i+"_checkbox");
				cCheckbox.attr("type", "checkbox");
				cCheckbox.attr("checked", true);
				cCheckbox.addClass("number_select_checkbox");
				conditionDiv.append(cCheckbox);
				
				cSelect = $("<select>").attr("id", contribName+"_"+i+"_ltgt");
				cSelect.addClass("number_select_ltgt");
				
				cOpt1 = $("<option>").attr("id", contribName+"_"+i+"_lt");
				cOpt1.addClass("number_select_ltgt_opt");
				cOpt1.attr("value", "lt");
				cOpt1.text("<");
				if(condition["operation"] == "<"){
					cOpt1.attr("selected", true);
				}
				
				
				cOpt2 = $("<option>").attr("id", contribName+"_"+i+"_gt");
				cOpt2.addClass("number_select_ltgt_opt");
				cOpt2.attr("value", "gt");
				cOpt2.text(">");
				if(condition["operation"] == ">"){
					cOpt2.attr("selected", true);
				}
				
				cOpt3 = $("<option>").attr("id", contribName+"_"+i+"_lteq");
				cOpt3.addClass("number_select_ltgt_opt");
				cOpt3.attr("value", "lteq");
				cOpt3.text("<=");
				if(condition["operation"] == "<="){
					cOpt3.attr("selected", true);
				}
				
				cOpt4 = $("<option>").attr("id", contribName+"_"+i+"_gteq");
				cOpt4.addClass("number_select_ltgt_opt");
				cOpt4.attr("value", "gteq");
				cOpt4.text(">=");
				if(condition["operation"] == ">="){
					cOpt4.attr("selected", true);
				}
				
				cSelect.append(cOpt1);
				cSelect.append(cOpt2);
				cSelect.append(cOpt3);
				cSelect.append(cOpt4);
				conditionDiv.append(cSelect);
				
				cTextInput = $("<input>").attr("id", contribName+"_"+i+"_text");
				cTextInput.addClass("number_select_text");
				cTextInput.text(condition["value"]);				
				conditionDiv.append(cTextInput);
				initialDiv.append(conditionDiv);
			} //forloop
			initialDiv.data("totalConditions", i);
			return initialDiv;
		}; //setcontribs
		
		
		//check anonymous
		var anon = false;
		if(parseInt(userDef["anonymous"]) == 1){
			anon = true;
		}
		$("#anon_users_checkbox").attr('checked', anon);
		
		//clear out old contents
		$("#contrib_opts_container").empty();
		
		var setup_set_contribs = function(contribName){
			var current_contribs = userDef[contribName];
			if(current_contribs == undefined){
				current_contribs = new Array();
			}
			$("#contrib_opts_container").append(setContribs(current_contribs, contribName));
		};
		
		
		//total contribs
		setup_set_contribs("total_contribs");
		setup_set_contribs("contribs_span_1");
		setup_set_contribs("contribs_span_2");
		setup_set_contribs("contribs_span_3");
		
	}; //renderUserDefDialogWith
	
	
	
	$.getUserDefsFromDialog = function(){
		var currUserDefs = new Array();
		if($("#anon_users_checkbox").is(":checked")){
			currUserDefs["anonymous"] = 1;
		}
		else{
			currUserDefs["anonymous"] = 0;
		}
		
		var getCheckBoxData = function(contribName){
			if($("#"+ contribName +"_checkbox").is(":checked")){
				currUserDefs[contribName] = new Array();
			}
			else{ return;}
			var totalConds = $("#" + contribName +"_div").data("totalConditions");
			var i;
			for(i = 0; i <= totalConds; i++){
				if($(contribName+"_"+i+"_checkbox").is(":checked")){
					$(contribName+"_"+i+"_ltgt").children().each(function(){
						if($(this).is(":selected")){
							var currentCond = new Array();
							switch($(this).attr("value")){
								case 'lt': currentCond["operation"] = "<"; break;
								case 'lteq': currentCond["operation"] = "<="; break;
								case 'gt': currentCond["operation"] = ">"; break;
								case 'gteq' : currentCond["operation"] = ">="; break;
								default: currentCond["operation"] = "<"; break;
							}
							currentCond["value"] = $("#"+contribName+"_"+i+"_text").val();
							currUserDefs[contribName].push(currentCond);
						}
					});
				} //ifchecked
			}//forloop
		};
		
		
		getCheckBoxData("total_contribs");
		getCheckBoxData("contribs_span_1");
		getCheckBoxData("contribs_span_2");
		getCheckBoxData("contribs_span_3");
		wgClickTrackUserDefs[$("#user_def_alter_legend").data("currentlyEditing")] = currUserDefs;
	};
	
	//functions
	$.updateChart = function(){
		
		event_name = $("#chart_img").data("event_name");
		
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
			$("#chart_img").attr( "src",chartURL);
		};
		
		
		start_date = $("#start_date").val();
		if($("#start_date").hasClass("hidden")){
			start_date = '0';
		}
		
		end_date = $("#end_date").val();
		if($("#end_date").hasClass("hidden")){
			end_date = '0';
		}
		
		//post relevant info
		$j.post( wgScriptPath + '/api.php', 
				{ 'action': 'specialclicktracking', 'format': 'json', 
				'eventid': $("#chart_img").data( "eventid" ), 'increment': $("#chart_increment").val(), 
				'startdate': start_date, 'enddate':end_date, 'userdefs': $.json_encode(wgClickTrackUserDefs) } , processChartJSON, "json");
	};
	
	
	
	
	//pretty colors for the table
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
				$(this).data("rgb", rgbString);
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
				$(this).data("rgb", rgbString);
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
				$(this).data("rgb", rgbString);
				$(this).css("color", rgbString);
				$(this).css("background-color", rgbString);
			});
			
			// I wanted to do this with classes, but the element's style rule wins over class rule
			// and each element has its own alternative color
			$(".event_data").mouseover(function(){
				$(this).css("color", "#000000");
				$(this).css("background-color", "#FFFFFF");
			});
			
			$(".event_data").mouseout(function(){
				rgbString = $(this).data("rgb");
				$(this).css("color", rgbString);
				$(this).css("background-color", rgbString);
			});
			
	};
	
	
	$.setUIControls = function(){
		
		//SET UP DATE RANGES
		
		
		//date-pickers for start and end dates
		$('.date_range_input').each(function(){
			$(this).datepicker();
			$(this).datepicker('option', 'dateFormat', 'yymmdd');
		});
		$('#start_date').datepicker('option', 'defaultDate', '-3m');
		
		var toggleDateInput = function(tableRow){
			var checked= false;
			tableRow.children().each(function(){
				if(checked == false){	
				checked = $(this).children("input:checkbox").eq(0).is(":checked");}
			});
			
			if(checked){
				tableRow.removeClass("disabled_option");
				tableRow.children("td").each(function(){
					$(this).children(".date_range_input").removeClass("hidden");
				});
			}
			else{
				tableRow.children("td").each(function(){
					$(this).children(".date_range_input").addClass("hidden");
				});
				tableRow.addClass("disabled_option");
			}
		};
		
		$('.date_range_checkbox').click(function(){
			toggleDateInput($(this).closest("tr"));
		});
		
		//CHART DIALOG
		$("#chart_dialog").dialog({ autoOpen: false, width: 400 });
		$("#chart_img").css("cursor","pointer");
		$("#chart_img").click(function(){
			$("#chart_dialog").dialog('open');
		});
		
		$("#chart_increment").data("value", $("#chart_increment").val());
		
		$("#change_graph").click(function(){
			$("#chart_dialog").dialog('close');

			//check if the value actually changed, if so, update and increment things accordingly
			if(  $("#chart_increment").data("value") !=  $("#chart_increment").val() ){
				$("#chart_increment").data("value", $("#chart_increment").val());
				$.updateChart();
			}
			
		});
		
		//CHANGE USER INFO DIALOG
		$("#user_def_dialog").dialog({ autoOpen: true, width: 400 });
		$("#user_def_alter_legend").data("defaultChangeText", $("#user_def_alter_legend").text());
		
		
		//CHANGE USER/INTERMEDIATE/EXPERT DIALOGS
		var loadHeaderInfo = function(headerName){
			$("#" + headerName + "_header").css("cursor", "pointer");
			$("#" + headerName + "_header").click(function(){
				$.renderUserDefDialogWith (wgClickTrackUserDefs[headerName], headerName);
				$("#user_def_dialog").dialog('open');
			});
		}; //headername
		
		
		loadHeaderInfo("beginner");
		loadHeaderInfo("intermediate");
		loadHeaderInfo("expert");
		
	};
	
		
	$.changeDataLinks = function (){
		
		$(".event_name").each(function(){
			$(this).css("cursor", "pointer");
			
			$(this).click(function(){
				$("#chart_img").data("eventid", $(this).attr( "value" ));
				$("#chart_img").data("event_name", $(this).text());
				$.updateChart();
			});//click
		});//each
	};//addlink
	
return $(this);
})(jQuery);

//colorize the table on document.ready
js2AddOnloadHook($j.colorizeTable);
js2AddOnloadHook($j.changeDataLinks);
js2AddOnloadHook($j.setUIControls);

