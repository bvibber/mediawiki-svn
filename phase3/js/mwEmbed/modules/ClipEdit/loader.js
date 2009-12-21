/*
* Clip edit loader:
*/

mw.addClassFilePaths( {
	"mw.ClipEdit" : "modules/ClipEdit/mw.ClipEdit.js",
	"$j.fn.ColorPicker"	: "modules/ClipEdit/colorpicker/js/colorpicker.js",
	"$j.Jcrop"			: "modules/ClipEdit/Jcrop/js/jquery.Jcrop.js"
} );

/*
* Adds style sheets to be loaded with particular classes   
*/
mw.addClassStyleSheets( {
	"$j.Jcrop"			: "modules/ClipEdit/Jcrop/css/jquery.Jcrop.css",
	"$j.fn.ColorPicker"	: "modules/ClipEdit/colorpicker/css/colorpicker.css"
} );