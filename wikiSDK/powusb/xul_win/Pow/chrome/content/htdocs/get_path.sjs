<?sjs

function get_directory() {
	try {
		var dirservice = 
			Components.classes["@mozilla.org/file/directory_service;1"].
			getService(Components.interfaces.nsIProperties);
		var file = dirservice.get('ProfD', Components.interfaces.nsILocalFile);
		file = pow_append_path(file, "/pow/htdocs");
		return file.path;
	} catch (e) {
		log_error(e);
	}
}
var dir = get_directory();
dir = dir.replace(/ /g, "%20");
document.writeln("The webserver files are located in <br>\n");
document.writeln(dir+"<br><br>");
document.writeln("In Max OSX and Windows, <b>drag this to your desktop</b>.<br>");
document.writeln("In Linux, bookmark this link or create a shortcut.<br>");
document.writeln("<a href='file://"+dir+"/'>POW Files</a>");

?>
