<?php

/**
 * You can create your own local.php file, similar to this one, to configure your local installation.
 * If you do not create a local.php file, the scripts will run with default settings
 */

$xmlg["site_base_url"] = "127.0.0.1/phase3" ;

$xmlg["docbook"] = array (
	"command_pdf" => "C:/docbook/bat/docbook_pdf.bat %1" ,
	"temp_dir" => "C:/docbook/repository" ,
	"out_dir" => "C:/docbook/output" ,
	"dtd" => "file:/c:/docbook/dtd/docbookx.dtd"
) ;

?>
