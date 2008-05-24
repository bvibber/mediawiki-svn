<?sjs
	var head = get_pow_header("Welcome to POW");
	document.write(head);
?>
<center><h2>Welcome to POW</h2></center>
<p>
Your webserver is now working. See the help page for more information.
</p>

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
document.writeln("In Mac OSX and Windows, <b>drag the 'POW Files' link to your desktop</b>.<br>");
document.writeln("In Linux, bookmark this link or create a shortcut. <br>");
document.writeln("Drag this: <a href='file://"+dir+"/'>POW Files</a>");

?>

</body>
</html>
