<?php

/**
 * You can create your own local.php file, similar to this one, to configure your local installation.
 * If you do not create a local.php file, the scripts will run with default settings
 */

$xmlg["site_base_url"] = "127.0.0.1/phase3" ;
$xmlg["use_special_export"] = 1 ;

$xmlg["docbook"] = array (
	"command_pdf" => "C:/docbook/bat/docbook_pdf.bat %1" ,
	"command_html" => "C:/docbook/bat/docbook_html.bat %1" ,
	"temp_dir" => "C:/docbook/repository" ,
	"out_dir" => "C:/docbook/output" ,
	"dtd" => "file:/c:/docbook/dtd/docbookx.dtd"
) ;

# Uncomment the following to use Special:Export and (potentially) automatic authors list
#$xmlg["use_special_export"] = 1 ;

# Uncomment and localize the following to offer ODT export
#$xmlg["zip_odt_path"] = "C:\\Program Files\\7-Zip" ;
#$xmlg["zip_odt"] = '7z.exe  a -r -tzip $1 $2' ;
#$xmlg["odt_template_dir"] = 'E:\\Program Files\\Apache Group\\Apache2\\htdocs\\wiki2xml\\php\\temp' ; # An unzipped blank ODT file

?>
