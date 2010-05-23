<?php
$messages = array();

/** English)
 * @author swiftlytilting
 */
 
$messages['en'] = array(
   'maintenanceshell'         => 'Maintenance Shell',
   'maintenanceshell-desc'    => 'Wiki interface for maintenance scripts',
   'maintshell-pagename'      => 'Special:MaintenanceShell',
   'right-maintenanceshell'   => 'Access the Maintenance Shell',
   'maintshell-installfail'   => "MaintenanceShell did not detect the user right <b>maintenanceshell</b> assigned to any group.<br /><br /> Please see <a href='http://www.mediawiki.org/wiki/Extension:MaintenanceShell'>the Extension:MaintenanceShell documentation</a> for more details.",
   'maintshell-installfail2'  => "<b>MaintenanceShell is configured incorrectly.</b><br />The user right <b>maintenanceshell</b> must be assigned to a user group before the extension MaintanceShell is called in <b>LocalSettings.php</b>.<br /><br />Please see the <a href='http://www.mediawiki.org/wiki/Extension:MaintenanceShell'>Extension:MaintenanceShell</a> documentation for more details.",
   'maintshell-installfail3'  => 'To install MaintenanceShell, put the following code on the <b>very last line</b> of LocalSettings.php:</br />require_once( "$IP/extensions/MaintenanceShell/MaintenanceShell.php" );',
   'maintshell-return'        => 'Return to the Maintenance Shell',
   'maintshell-noexist'       => "Script '$1' does not exist!",
   'maintshell-warning'       => 'Warning: Use these scripts with care.  They are intended for administrators and other advanced users only.',
   'maintshell-links'         => '<ul style="padding-top:1em">
                                    <li><a class="external" href="http://www.mediawiki.org/wiki/Manual:Maintenance_scripts">Manual:Maintenance scripts</a></li>
                                    <li><a class="external" href="http://www.mediawiki.org/wiki/Extension:MaintenanceShell">MaintenanceShell Homepage</a></li>
                                  </ul>',
   'maintshell-available'     => 'Available maintenance scripts:',
   'maintshell-scriptname'    => 'Script name',
   'maintshell-commandline'   => 'Command line options',
   'maintshell-runscript'     => 'Run script',

);


/** German (Deutsch)
 * @author kghbln
 */
$messages['de'] = array(
   'maintenanceshell'         => 'Wartungs-Shell',
   'maintenanceshell-desc'    => 'Erg&auml;nzt eine [[Special:MaintenanceShell|Spezialseite]] mit hilfreichen Links zu Wartungsskripten f&uuml;r die Systemadministration.',
   'maintshell-pagename'      => 'Special:Wartungs-Shell',
   'right-maintenanceshell'   => 'Wartungsskripte &uuml;ber die Wartungs-Shell ausf&uuml;hren.',
   'maintshell-installfail'   => "Das Benutzergruppenrecht <b>maintenanceshell</b>, das f&uuml;r die Verwendung der Wartungs-Shell ben&ouml;tigt wird, wurde keiner Benutzergruppe zugeordnet.<br /><br />Weitere Hinweise hierzu gibt es in der <a href='http://www.mediawiki.org/wiki/Extension:MaintenanceShell'>Dokumentation zur Wartungs-Shell</a>.",
   'maintshell-installfail2'  => "<b>Die Wartungs-Shell wurde fehlerhaft konfiguriert.</b><br />Das Benutzergruppenrecht <b>maintenanceshell</b> muss in der Datei <b>LocalSettings.php</b> einer Benutzergruppe zugewiesen werden, bevor dort die Softwareerweiterung Wartungs-Shell aufgerufen wird.<br /><br />Weitere Hinweise hierzu gibt es in der <a href='http://www.mediawiki.org/wiki/Extension:MaintenanceShell'>Dokumentation zur Wartungs-Shell</a>.",
   'maintshell-installfail3'  => 'Um die Wartungs-Shell zu aktivieren, muss folgender Code in der <b>allerletzten Zeile</b> der Datei <b>LocalSettings.php</b> eingef&uuml;gt werden:</br />require_once( "$IP/extensions/MaintenanceShell/MaintenanceShell.php" );',
   'maintshell-return'        => 'R&uuml;ckkehr zur Wartungs-Shell',
   'maintshell-noexist'       => "Das Skript '$1' ist nicht vorhanden!",
   'maintshell-warning'       => '<b><u>Achtung:</u> Setze diese Skripte sorgf&auml;ltig ein. Dies wird zudem nur Systemadministratoren und fortgeschrittenen Nutzern empfohlen.</b>',
   'maintshell-links'         => '<ul style="padding-top:1em">
			            <li><a class="external" href="http://www.mediawiki.org/wiki/Manual:Maintenance_scripts">Nutzeranleitung f&uuml;r die Wartungs-Shell (englisch)</a></li>
				    <li><a class="external" href="http://www.mediawiki.org/wiki/Extension:MaintenanceShell">Hompage zur Wartungs-Shell (englisch) </a></li>
				  </ul><br />',
   'maintshell-available'     => '<br /><b>Verf&uuml;gbare Wartungsskripte:</b>',
   'maintshell-scriptname'    => 'Name des Skrips',
   'maintshell-commandline'   => 'Zus&auml;tzliche Kommandos',
   'maintshell-runscript'     => 'Skript ausf&uuml;hren',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author kghbln
 */
$messages['de-formal'] = array(
'maintshell-warning'       => '<b><u>Achtung:</u> Setzen Sie diese Skripte sorgf&auml;ltig ein. Dies wird zudem nur Systemadministratoren und fortgeschrittenen Nutzern empfohlen.</b>',
);
