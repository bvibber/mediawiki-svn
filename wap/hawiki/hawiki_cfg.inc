<?php
/*
 * hawiki config file for hawpedia
 * $Date$
 */

//define("HAWIKI_TITLE", "HawPedia");            // to be defined on hawpedia config layer
define("HAWIKI_MAXINPUTCHARS", 100000);          // maximum wiki size
define("HAWIKI_DISP_WIDTH",  128);               // HAWHAW display width (pixel)
define("HAWIKI_DISP_HEIGHT", 220);               //                height
define("HAWIKI_DISP_BGCOLOR", "#FFFFFF");        //                background color
define("HAWIKI_DATETIME_SHORT", "d/m/Y [H:i]");  // for modifications see PHP function date()
define("HAWIKI_DATETIME_LONG",  "l, F jS Y, g:i A");
define("HAWIKI_EXTLINK_ICON", "images/external_link"); // must exist as .gif and .wbmp version
define("HAWIKI_EXTLINK_ICON_WIDTH",  7);         // icon width
define("HAWIKI_EXTLINK_ICON_HEIGHT", 8);         // icon height
define("HAWIKI_SKIN", HAW_SIM_NONE);             // do NOT use any simulator

?>
