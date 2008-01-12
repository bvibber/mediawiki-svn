<?php
/*
 * Internationalization file for Call Extension
 *
 * @addGroup Extension
 */

$messages = array();

$messages['en'] = array(
	'call' => 'Call',
	'call-text' => 'The Call extension expects a wiki page and optional parameters for that page as an argument.<br><br>
Example 1: &nbsp; <tt>[[Special:Call/My Template,parm1=value1]]</tt><br/>
Example 2: &nbsp; <tt>[[Special:Call/Talk:My Discussion,parm1=value1]]</tt><br/>
Example 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br/><br/>
Example 4 (Browser URL): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br/><br/>

The <i>Call extension</i> will call the given page and pass the parameters.<br>You will see the contents of the called page and its title but its \'type\' will be that of a special page,<br>i.e. such a page cannot be edited.<br>The contents you see may vary depending on the value of the parameters you passed.<br><br>
The <i>Call extension</i> is useful to build interactive applications with MediaWiki.<br>For an example see <a href=\'http://semeb.com/dpldemo/Template:Catlist\'>the DPL GUI</a> ..<br/>
In case of problems you can try <b>Special:Call/DebuG</b>',
	'call-save' => 'The output of this call would be saved to a page called \'\'$1\'\'.',
	'call-save-success' => 'The following text has been saved to page <big>[[$1]]</big> .',
	'call-save-failed' => 'The following text has NOT been saved to page <big>[[$1]]</big> because that page already exists.',
);

$messages['fr'] = array(
	'call' => 'Appel',
	'call-text' => 'L’extension Appel a besoin d’une page wiki et des paramètres facultatifs pour cette dernière.<br><br>
Example 1: &nbsp; <tt>[[Special:Call/Mon modèle,parm1=value1]]</tt><br/>
Example 2: &nbsp; <tt>[[Special:Call/Discussion:Ma discussion,parm1=value1]]</tt><br/>
Example 3: &nbsp; <tt>[[Special:Call/:Ma page,parm1=value1,parm2=value2]]</tt><br/><br/>
Example 4 (Adresse pour navigateur) : &nbsp; <tt>http://mondomaine/monwiki/index.php?Special:Call/:Ma_Page,parm1=value1</tt><br/><br/>

L’extension <i>Appel</i> appellera la page indiquée en y passant les paramètres.<br>Vous verrez les informations de cette page, son titre, mais son « type » sera celui d’une page spéciale mais ne pourra pas être éditée.<br>Les informations que vous verrez varierons en fonction des paramètres que vous aurez indiqués.<br>Cette extension est très pratique pour créer des applications interactives avec MediaWiki.<br>À titre d’exemple, voyez <a href=\'http://semeb.com/dpldemo/Template:Catlist\'>the DPL GUI</a> ..<br/>En cas de problèmes, vous pouvez essayer <b>Special:Call/DebuG</b>',
	'call-save' => 'Ce qui est indiqué par cet appel pourrait être sauvé vers une page intitulée \'\'$1\'\'.',
	'call-save-success' => 'Le texte suivant a été sauvegardé vers la page <big>[[$1]]</big> .',
	'call-save-failed' => 'Le texte suivant n’a pu être sauvergardé vers la page <big>[[$1]]</big> du fait qu’elle existe déjà.',
);
