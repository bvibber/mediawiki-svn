#!/usr/bin/perl
$LANGUAGE="de";

%messages=(

notemppath=>
"No path for temporary files specified. Please edit ____ 
and add an entry like this:

[Settings]
Temp Path=/tmp\n",
notemppath_de=>
"Kein Pfad für temporäre Dateien festgelegt. 
Bitte bearbeiten Sie ____
und fügen Sie einen Eintrag wie folgt ein:

[Settings]
Temp Path=/tmp\n",

nocontrolfile=>
"No control file specified.
Syntax: perl ee.pl <control file>\n",
nocontrolfile_de=>
"Keine Kontrolldatei angegeben.
Syntax: perl ee.pl <Kontrolldatei>\n",

twofordiff=>
"Process is diff, but no second URL contained in control file\n",
twofordiff_de=>
"Dateien sollen verglichen werden, Kontrolldatei enthält aber nur eine URL.",

nodifftool=>
"Process is diff, but ee.ini does not contain a 'Diff=' definition line
in the [Settings] section where the diff tool is defined.\n",
nodifftool_de=>
"Dateien sollen verglichen werden, ee.ini enthält aber keine
'Diff='-Zeile im Abschnitt Settings, in der das Diff-Werkzeug 
definiert wird.\n",

unknownprocess=>
"The process type defined in the input file (Type= in the [Process] section) 
is not known to this implementation of the External Editor interface. Perhaps 
you need to upgrade to a newer version?\n",
unknownprocess_de=>
"Der in der Kontrolldatei definierte Prozesstyp (Type= im Abschnitt [Process])
ist dieser Implementierung des application/x-external-editor-Interface nicht
bekannt. Vielleicht müssen Sie Ihre Version des Skripts aktualisieren.\n",

loginfailed=>
"Could not login to 
____ 
with username '____' and password '____'.

Make sure you have a definition for this website in your ee.ini, and that
the 'URL match=' part of the site definition contains a string that is part
of the URL above.\n",

loginfailed_de=>
"Anmeldung bei 
____ 
gescheitert. Benutzername: ____ Passwort: ____

Stellen Sie sicher, dass Ihre ee.ini eine Definition für diese Website
enthält, und in der 'URL match='-Zeile ein Text steht, der Bestandteil der
obigen URL ist.\n",

summary=>
"Summary",
summary_de=>
"Zusammenfassung",

save=>
"Save",
save_de=>
"Speichern",

savecont=>
"Save and continue",
savecont_de=>
"Speichern und weiter",

preview=>
"Preview",
preview_de=>
"Vorschau",

cancel=>
"Cancel",
cancel_de=>
"Abbruch",

entersummary=>
"Enter edit summary",
entersummary_de=>
"Zusammenfassung eingeben",

);

sub _{
	my $message=shift;
	@subst=@_;	
	my $suffix;
	if($LANGUAGE ne "en") { $suffix = "_".$LANGUAGE; }
	$msg=$messages{$message.$suffix};
	foreach $substi(@subst) {
		$msg=~s/____/$substi/s;	
	}
	return $msg;
}