<?php

/**
 * You can create your own local.php file, similar to this one, to configure your local installation.
 * If you do not create a local.php file, the scripts will run with default settings
 */

$xmlg["site_base_url"] = "127.0.0.1/phase3" ;
$xmlg["use_special_export"] = 1 ;
$xmlg["temp_dir"] = "C:/windows/temp" ; # Directory for temporary files

$xmlg["docbook"] = array (
	"command_pdf" => "C:/docbook/bat/docbook_pdf.bat %1" ,
	"command_html" => "C:/docbook/bat/docbook_html.bat %1" ,
	"temp_dir" => "C:/docbook/repository" ,
	"out_dir" => "C:/docbook/output" ,
	"dtd" => "file:/c:/docbook/dtd/docbookx.dtd"
) ;

# On Windows, set
# $xmlg['is_windows'] = true ;

### Uncomment the following to use Special:Export and (potentially) automatic authors list; a little slower, though
#$xmlg["use_special_export"] = 1 ;

### Uncomment and localize the following to offer ODT export
#$xmlg["zip_odt_path"] = "E:\\Program Files\\7-Zip" ; # Path to the zip/unzip programs; can be omitted if in default execuatable path
#$xmlg["zip_odt"] = '7z.exe  a -r -tzip $1 $2*' ; # Command to zip directory $1 to file $2; NOTE THE '*' AFTER '$2' FOR WINDOWS ONLY!
#$xmlg["unzip_odt"] = '7z.exe x $1 -o$2' ; # Command to unzip file $1 to directory $2



# If you want to do text-file browsing, run "xmldump2files.php" once (see settings there), then set this:
$base_text_dir = "C:/dewiki-20060327-pages-articles" ;

?>
