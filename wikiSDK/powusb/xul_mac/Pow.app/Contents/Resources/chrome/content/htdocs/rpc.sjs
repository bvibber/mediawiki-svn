<?sjs
if(pow_server.REMOTE_HOST != "127.0.0.1" || 
	!pow_bc_pass_protected()) { pow_exit(); }

var arg1 = pow_server.GET['arg1'];
arg1 = unescape(arg1);

var arg2 = pow_server.GET['arg2'];
arg2 = unescape(arg2);

var arg3 = pow_server.GET['arg3'];
arg3 = unescape(arg3);

if(pow_server.GET['function'] == 'new_pow_serv') {
	var serv_obj = "{'REMOTE_HOST': '"+pow_server.REMOTE_HOST+"'}";
	document.writeln(serv_obj);
}

if(pow_server.GET['function'] == 'pow_file') {
	if(pow_file_exists(pow_server.GET['arg1'])) {
		var contents = pow_file(pow_server.GET['arg1']);
		document.writeln(contents);
	} else {
		document.writeln("found not file");
	}
}

if(pow_server.GET['function'] == 'pow_file_put_contents') {
	if(arg1 && arg2 && arg3) {
		var contents = pow_file_put_contents(arg1, arg2, arg3);
		document.writeln(contents);
	} else {
		document.writeln("ERROR");
	}
}

if(pow_server.GET['function'] == 'pow_file_delete') {
	if(arg1) {
		pow_file_delete(arg1);
	} else {
		document.writeln("ERROR");
	}
}

if(pow_server.GET['function'] == 'pow_include') {
	if(arg1) {
		var file = pow_file(arg1);
		// it seems sjs in the code ruins things
		file = file.replace(/<.sjs/g, "");
		file = file.replace(/.>/g, "");
		document.writeln(file);
	} else {
		document.writeln("ERROR");
	}
}

if(pow_server.GET['function'] == 'pow_download_file') {
	if(arg1) {
		pow_download_file(arg1);
	} else {
		document.writeln("ERROR");
	}
}

if(pow_server.GET['function'] == 'pow_file_exists') {
	if(arg1) {
		var exists = pow_file_exists(arg1);
		document.writeln(exists);	
	} else {
		document.writeln("ERROR");
	}
}

/*
	Does not allow for more arguments
*/

if(pow_server.GET['function'] == 'pow_exec') {
	if(arg1 && arg2) {
		pow_exec(arg1, new Array(arg2));
		document.writeln(result);	
	} else if(arg1) {
		pow_exec(arg1, new Array());
		document.writeln(result);	
	} else {
		document.writeln("ERROR");
	}
}

if(pow_server.GET['function'] == 'new_pow_db') {
	if(pow_server.GET['arg1']) {
		var pdb = new pow_DB(pow_server.GET['arg1']);
	}
}

if(pow_server.GET['function'] == 'pow_db_exec') {
	var pdb;
	if(pow_server.GET['arg1'] && arg2) {
		pdb = new pow_DB(arg1);
		var result = pdb.exec(arg2);
		var json_result = "{}";
		if(result && result[0] && result["column_names"]) {
			var values = "";
			for(var i=0;i<result.length;i++) {
				if(i != 0) {
					values += ",";
				}
				values += "['";
				values += result[i].join("','");
				values += "']";
			}
			json_result = 
					"{ "+
							"'column_names': ['"+result["column_names"].join("','")+"'],"+
							"'column_widths': ['"+result["column_widths"].join("','")+"'],"+
							" 'column_values': ["+values+"]"+
					" }";
		}
		document.writeln(json_result);
	}
}

if(pow_server.GET['function'] == 'pow_db_drop_database') {
	if(arg1 && arg2) {
		var pdb = new pow_DB(arg1);
		pdb.drop_database(arg2);
	}
}

if(pow_server.GET['function'] == 'pow_db_pretty_print') {
	if(arg1) {
		var pdb = new pow_DB(arg1);
		pdb.drop_database(arg2);
	}
	document.writeln("pretty_print IS NOT IMPLEMENTED");
}

if(pow_server.GET['function'] == 'pow_server_log_access') {
	if(arg1) {
		pow_server.log_access(arg1);
	}
}

if(pow_server.GET['function'] == 'get_rewrite_rules') {
/*
pow_server.REWRITE_RULES =  
   [
     [ "dog/?(.*)$",        "/cat.sjs?q=$1" ],
     [ /^\/Jason\/?(.*)$/i, "/cat.sjs?q=$1" ]
   ];
		*/
	if(!pow_server.REWRITE_RULES) {
		pow_server.REWRITE_RULES =  new Object();
	}
		document.writeln("[");
	for(var i in pow_server.REWRITE_RULES) {
		if(i != 0) { document.writeln(", "); }
		document.write("   [ ");
		for(var j in pow_server.REWRITE_RULES[i]) {
			if(j != 0) { document.writeln(", "); }
			var rule = pow_server.REWRITE_RULES[i][j];
			document.write("\""+rule+"\"");
		}
		document.writeln(" ]");
	}
		document.writeln("]");
}

?>
