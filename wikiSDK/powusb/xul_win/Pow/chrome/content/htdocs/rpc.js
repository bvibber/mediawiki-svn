
var result = "";

function pow_Serv() {
  this.REQUEST = new Array();
  this.GET =  new Array();
  this.POST =  new Array();
  this.REWRITE_RULES =  new Array();
  this.REQUEST_HEADERS =  new Array();
  this.RAW_POST = "";
  this.RAW_REQUEST = "";
  this.POST_FILENAME = "";
	var rewrite_obj = talktoServer('get_rewrite_rules');
	rewrite_obj = rewrite_obj.replace(/__DOLLAR__/mg, "\$");
	eval("var tmp_obj = "+rewrite_obj);
	this.REWRITE_RULES = tmp_obj;
	var json_obj = talktoServer('new_pow_serv');
	eval("var tmp_obj = "+json_obj);
	this.REMOTE_HOST = tmp_obj.REMOTE_HOST;
	this.get_uploaded_file = function() {
		return this.POST_FILE;
	}
	this.log_access = function(message) {
		talktoServer('pow_server_log_access', message);
	}
}

// var pow_server = new pow_Serv();

function pow_DB(db_name) {
	talktoServer('new_pow_db', db_name);
	this.name = db_name;
	this.exec = function(sql) {
		var result = new Object();
		var json_result = talktoServer('pow_db_exec', this.name, sql);
		eval("whole_result = "+json_result);
		if(whole_result['column_values']) {
			result = whole_result['column_values'];
		}
		result['column_names'] = whole_result['column_names'];
		result['column_widths'] = whole_result['column_widths'];
		return result;
	};
	this.pretty_print = function(result) {
    var table;
    table = "<pre>";
    var separator = "";
    var this_row = new Array();
    for(var j=0; j < results["column_names"].length; j++) {
     for(var k=0;k < results["column_widths"][j]; k++) {
       if(j || k) {
         separator += "-";
       } else {
         separator += " ";
       }
     }
     separator += "---";
   // }
    }
    separator += "\n";

    table += "\n";
    table += separator;
    for(var i=-1; i < results.length; i++) {
     table += "| ";
     if(i==-1) {
       this_row = results["column_names"];
     } else {
       this_row = results[i];
     }
     var column_row_length = 0;
     for(var j=0; j < this_row.length; j++) {
       if(this_row[j] == null ) {
          column_row_length = 4;
       } else {
          column_row_length = this_row[j].length;
       }
       for(var k=0;k < results["column_widths"][j]-column_row_length; k++) {
        table += " ";
       }
       table += this_row[j];
       table += " | ";
     }
     table += "\n";
     table += separator;
    }
    table += "</pre>";
		document.writeln(table);
	};
	this.drop_database = function(db_name) {
		talktoServer('pow_db_drop_database', db_name);
	};
}

function pow_file(filename) {
	return talktoServer('pow_file', filename);
}

function pow_exit() {
	throw("Pow normal exit");
}

function pow_include(filename) {
	var inc = talktoServer('pow_include', filename);
	eval(inc);
}

function talktoServer(function_name, arg1, arg2) {
	var return_val = "";
	var req = newXMLHttpRequest();
	if(arg1 && arg2) {
 		req.open("GET", "http://localhost:6673/system/rpc.sjs?function="+function_name+"&arg1="+arg1+"&arg2="+arg2, false);
	} else if(arg1) {
 		req.open("GET", "http://localhost:6673/system/rpc.sjs?function="+function_name+"&arg1="+arg1, false);
	} else {
 		req.open("GET", "http://localhost:6673/system/rpc.sjs?function="+function_name, false);
	}
 	req.send("");
	if (req.readyState == 4) {
		if (req.status == 200) {
			return_val = req.responseText;
		} else {
			// Error;
 		}
 	}
	return return_val;
}

function newXMLHttpRequest() {
	var xmlreq = false;
	if (window.XMLHttpRequest) {
		xmlreq = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try { 
			xmlreq = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e1) { 
			try {
				xmlreq = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				 // both methods failed 
			} 
		}
 	}
  return xmlreq;
} 

function pow_file_put_contents(filename, contents, rwa_flags) {
	return talktoServer('pow_file_put_contents', filename, contents, rwa_flags);
}

function pow_file_delete(filename, contents, rwa_flags) {
	return talktoServer('pow_file_delete', filename);
}

function pow_download_file(filename) {
	return talktoServer('pow_download_file', filename);
}

function pow_file_exists(filename) {
	return talktoServer('pow_file_exists', filename);
}

function pow_exec(command, arg_array) {
	document.writeln("POW_EXEC IS NOT IMPLEMENTED");
	/*
	// TURNED off until security is assured
	if(arg_array[0]) {
		return talktoServer('pow_exec', arg_array[0]);
	} else {
		return talktoServer('pow_exec', "");
	}
	*/
}

function pow_file_exists(filename) {
	return talktoServer('pow_file_exists', filename);
}

function pow_info() {
  try {
    var info = "";
    info += "<table border='1' >";
    var request_text = pow_array_tostring(pow_server.REQUEST);
    info += "<tr><td>pow_server.REQUEST</td><td>"+
        request_text+"</td></tr>";
    var post_text = pow_array_tostring(pow_server.POST);
    info += "<tr><td>pow_server.POST</td><td>"+post_text+"</td></tr>";
    var get_text = pow_array_tostring(pow_server.GET);
    info += "<tr><td>pow_server.GET</td><td>"+get_text+"</td></tr>";
    info += "<tr><td>pow_server.REMOTE_HOST</td><td>"+
        pow_server.REMOTE_HOST+"</td></tr>";
    info += "<tr><td>pow_server.POST_FILENAME</td><td>"+
        pow_server.POST_FILENAME+"</td></tr>";
    info += "</table>";
    //pow_response.script_out += info;
    document.writeln(info);
  } catch (e) {
    log_error(e); return "";
  }
}

function pow_array_tostring(arr) {
  var joined = new Array();
  for (var i in arr) {
    joined.push(i+": "+arr[i]);
  }
  return joined.join(", ");
}
