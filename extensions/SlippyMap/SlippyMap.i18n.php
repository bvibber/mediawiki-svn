<?php
/**
 * Internationalisation file for SlippyMap extension.
 *
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'slippymap_desc' => "Allows the use of the <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> tag to display an OpenLayers slippy map. Maps are from [http://openstreetmap.org openstreetmap.org]",
	'slippymap_latmissing' => "Missing lat value (for the latitude).",
	'slippymap_lonmissing' => "Missing lon value (for the longitude).",
	'slippymap_zoommissing' => "Missing z value (for the zoom level).",
	'slippymap_longdepreciated' => "Please use 'lon' instead of 'long' (parameter was renamed).",
	'slippymap_widthnan' => "width (w) value '%1' is not a valid integer",
	'slippymap_heightnan' => "height (h) value '%1' is not a valid integer",
	'slippymap_zoomnan' => "zoom (z) value '%1' is not a valid integer",
	'slippymap_latnan' => "latitude (lat) value '%1' is not a valid number",
	'slippymap_lonnan' => "longitude (lon) value '%1' is not a valid number",
	'slippymap_widthbig' => "width (w) value cannot be greater than 1000",
	'slippymap_widthsmall' => "width (w) value cannot be less than 100",
	'slippymap_heightbig' => "height (h) value cannot be greater than 1000",
	'slippymap_heightsmall' => "height (h) value cannot be less than 100",
	'slippymap_latbig' => "latitude (lat) value cannot be greater than 90",
	'slippymap_latsmall' => "latitude (lat) value cannot be less than -90",
	'slippymap_lonbig' => "longitude (lon) value cannot be greater than 180",
	'slippymap_lonsmall' => "longitude (lon) value cannot be less than -180",
	'slippymap_zoomsmall' => "zoom (z) value cannot be less than zero",
	'slippymap_zoom18' => "zoom (z) value cannot be greater than 17. Note that this mediawiki extension hooks into the OpenStreetMap 'osmarender' layer which does not go beyond zoom level 17. The Mapnik layer available on openstreetmap.org, goes up to zoom level 18",
	'slippymap_zoombig' => "zoom (z) value cannot be greater than 17.",
	'slippymap_invalidlayer' => "Invalid 'layer' value '%1'",
	'slippymap_maperror' => "Map error:",
	'slippymap_osmlink' => 'http://www.openstreetmap.org/?lat=%1&lon=%2&zoom=%3', # do not translate or duplicate this message to other languages
	'slippymap_osmtext' => 'See this map on OpenStreetMap.org',
	'slippymap_code'    => 'Wikicode for this map view:',
	'slippymap_license' => 'OpenStreetMap - CC-BY-SA-2.0', # do not translate or duplicate this message to other languages
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'slippymap_desc'            => "Ermöglicht die Nutzung des <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>-Tags zur Anzeige einer OpenLayer-SlippyMap. Die Karten stammen von [http://openstreetmap.org openstreetmap.org]",
	'slippymap_latmissing'      => "Es wurde kein Wert für die geografische Breite (lat) angegeben.",
	'slippymap_lonmissing'      => "Es wurde kein Wert für die geografische Länge (lon) angegeben.",
	'slippymap_zoommissing'     => "Es wurde kein Zoom-Wert (z) angegeben.",
	'slippymap_longdepreciated' => "Bitte benutze 'lon' an Stelle von 'long' (Parameter wurde umbenannt). ",
	'slippymap_widthnan'        => "Der Wert für die Breite (w) '%1' ist keine gültige Zahl",
	'slippymap_heightnan'       => "Der Wert für die Höhe (h) '%1' ist keine gültige Zahl",
	'slippymap_zoomnan'         => "Der Wert für den Zoom (t) '%1' ist keine gültige Zahl",
	'slippymap_latnan'          => "Der Wert für die geografische Breite (lat) '%1' ist keine gültige Zahl",
	'slippymap_lonnan'          => "Der Wert für die geografische Länge (lon) '%1' ist keine gültige Zahl",
	'slippymap_widthbig'        => "Die Breite (w) darf 1000 nicht überschreiten",
	'slippymap_widthsmall'      => "Die Breite (w) darf 100 nicht unterschreiten",
	'slippymap_heightbig'       => "Die Höhe (h) darf 1000 nicht überschreiten",
	'slippymap_heightsmall'     => "Die Höhe (h) darf 100 nicht unterschreiten",
	'slippymap_latbig'          => "Die geografische Breite darf nicht größer als 90 sein",
	'slippymap_latsmall'        => "Die geografische Breite darf nicht kleiner als -90 sein",
	'slippymap_lonbig'          => "Die geografische Länge darf nicht größer als 180 sein",
	'slippymap_lonsmall'        => "Die geografische Länge darf nicht kleiner als -180 sein",
	'slippymap_zoomsmall'       => "Der Zoomwert darf nicht negativ sein",
	'slippymap_zoom18'          => "Der Zoomwert (z) kann nicht größer als 17 sein. Beachten, dass diese MediaWiki-Erweiterung die OpenStreetMap 'Osmarender'-Karte einbindet, sie nicht höher als Zoom 17 geht. Die Mapnik-Karte ist auf openstreetmap.org verfügbar und geht bis Zoom 18.",
	'slippymap_zoombig'         => "Der Zoomwert (z) kann nicht größer als 17 sein.",
	'slippymap_invalidlayer'    => "Ungültiger 'layer'-Wert „%1“",
    'slippymap_code'		=> 'Wikitext für diese Kartenansicht:',
	'slippymap_maperror'        => "Kartenfehler:",
	'slippymap_osmtext'         => 'Siehe diese Karte auf OpenStreetMap.org',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'slippymap_latmissing'      => 'Falta o valor lat (para a latitude).',
	'slippymap_lonmissing'      => 'Falta o valor lan (para a lonxitude).',
	'slippymap_zoommissing'     => 'Falta o valor z (para o nivel do zoom).',
	'slippymap_longdepreciated' => 'Por favor, use "lon" no canto de "long" (o parámetro foi renomeado).',
	'slippymap_widthnan'        => "o valor '%1' do ancho (w) non é un número enteiro válido",
	'slippymap_heightnan'       => "o valor '%1' da altura (h) non é un número enteiro válido",
	'slippymap_zoomnan'         => "o valor '%1' do zoom (z) non é un número enteiro válido",
	'slippymap_latnan'          => "o valor '%1' da latitude (lat) non é un número enteiro válido",
	'slippymap_lonnan'          => "o valor '%1' da lonxitude (lon) non é un número enteiro válido",
	'slippymap_widthbig'        => 'o valor do ancho (w) non pode ser máis de 1000',
	'slippymap_widthsmall'      => 'o valor do ancho (w) non pode ser menos de 100',
	'slippymap_heightbig'       => 'o valor da altura (h) non pode ser máis de 1000',
	'slippymap_heightsmall'     => 'o valor da altura (h) non pode ser menos de 100',
	'slippymap_latbig'          => 'o valor da latitude (lat) non pode ser máis de 90',
	'slippymap_latsmall'        => 'o valor da latitude (lat) non pode ser menos de -90',
	'slippymap_maperror'        => 'Erro no mapa:',
	'slippymap_code'            => 'Código wiki para o visionado deste mapa:',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'slippymap_desc'            => 'Laat het gebruik van de tag <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> toe om een OpenLayers-kaart weer te geven. Kaarten zijn van [http://openstreetmap.org openstreetmap.org]',
	'slippymap_latmissing'      => 'De "lat"-waarde ontbreekt (voor de breedte).',
	'slippymap_lonmissing'      => 'De "lon"-waarde ontbreekt (voor de lengte).',
	'slippymap_zoommissing'     => 'De "z"-waarde ontbreekt (voor het zoomniveau).',
	'slippymap_longdepreciated' => 'Gebruik "lon" in plaats van "long" (parameter is hernoemd).',
	'slippymap_widthnan'        => "De waarde '%1' voor de breedte (w) is geen geldige integer",
	'slippymap_heightnan'       => "De waarde '%1' voor de hoogte (h) is geen geldige integer",
	'slippymap_zoomnan'         => "De waarde '%1' voor de zoom (z) is geen geldige integer",
	'slippymap_latnan'          => "De waarde '%1' voor de breedte (lat) is geen geldig nummer",
	'slippymap_lonnan'          => "De waarde '%1' voor de lengte (lon) is geen geldig nummer",
	'slippymap_widthbig'        => 'De breedte (w) kan niet groter dan 1000 zijn',
	'slippymap_widthsmall'      => 'De breedte (w) kan niet kleiner dan 100 zijn',
	'slippymap_heightbig'       => 'De hoogte (h) kan niet groter dan 1000 zijn',
	'slippymap_heightsmall'     => 'De hoogte (h) kan niet kleiner dan 100 zijn',
	'slippymap_latbig'          => 'De breedte (lat) kan niet groter dan -90 zijn',
	'slippymap_latsmall'        => 'De breedte (lat) kan niet kleiner dan -90 zijn',
	'slippymap_lonbig'          => 'De lengte (lon) kan niet groter dan 180 zijn',
	'slippymap_lonsmall'        => 'De lengte (lon) kan niet kleiner dan -180 zijn',
	'slippymap_zoomsmall'       => 'De zoom (z) kan niet minder dan nul zijn',
	'slippymap_zoom18'          => 'De zoom (z) kan niet groter zijn dan 17. Merk op dat deze MediaWiki-uitbreiding de "Osmarender"-layer van OpenSteetMap gebruikt die niet dieper dan het niveau 17 gaat. de "Mapnik"-layer, beschikbaar op openstreetmap.org, gaat tot niveau 18.',
	'slippymap_zoombig'         => 'De zoom (z) kan niet groter dan 17 zijn',
	'slippymap_invalidlayer'    => 'Ongeldige \'layer\'-waarde "%1"',
	'slippymap_maperror'        => 'Kaartfout:',
	'slippymap_osmtext'         => 'Deze kaart op OpenStreetMap.org bekijken',
	'slippymap_code'            => 'Wikicode voor deze kaart:',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'slippymap_desc'            => 'Tillater bruk av taggen <tt>&lt;slippymap&gt;</tt> for å vise et «slippy map» fra OpenLayers. Kartene kommer fra [http://openstreetmap.org openstreetmap.org]',
	'slippymap_latmissing'      => 'Mangler «lat»-verdi (for breddegraden).',
	'slippymap_lonmissing'      => 'Mangler «lon»-verdi (for lengdegraden).',
	'slippymap_zoommissing'     => 'Mangler «z»-verdi (for zoom-nivået).',
	'slippymap_longdepreciated' => 'Bruk «lon» i stedet for «long» (parameteret fikk nytt navn).',
	'slippymap_widthnan'        => 'breddeverdien («w») «%1» er ikke et gyldig heltall',
	'slippymap_heightnan'       => 'høydeverdien («h»)',
	'slippymap_zoomnan'         => 'zoomverdien («z») «%1» er ikke et gyldig heltall',
	'slippymap_latnan'          => 'breddegradsverdien («lat») «%1» er ikke et gyldig tall',
	'slippymap_lonnan'          => 'lengdegradsverdien («lon») «%1» er ikke et gyldig tall',
	'slippymap_widthbig'        => 'breddeverdien («w») kan ikke være større enn 1000',
	'slippymap_widthsmall'      => 'breddeverdien («w») kan ikke være mindre enn 100',
	'slippymap_heightbig'       => 'høydeverdien («h») kan ikke være større enn 1000',
	'slippymap_heightsmall'     => 'høydeverdien («h») kan ikke være mindre enn 100',
	'slippymap_latbig'          => 'breddegradsverdien («lat») kan ikke være større enn 90',
	'slippymap_latsmall'        => 'breddegradsverdien («lat») kan ikke være mindre enn –90',
	'slippymap_lonbig'          => 'lengdegradsverdien («lon») kan ikke være større enn 180',
	'slippymap_lonsmall'        => 'lengdegradsverdien («lon») kan ikke være mindre enn –180',
	'slippymap_zoomsmall'       => 'zoomverdien («z») kan ikke være mindre enn null',
	'slippymap_zoom18'          => 'zoomverdien («z») kan ikke være større enn 17. Merk at denne MediaWiki-utvidelsen bruker OpenStreetMap-laget «osmarender», som ikke kan zoome mer enn til nivå 17. «Mapnik»-laget på openstreetmap.org går til zoomnivå 18.',
	'slippymap_zoombig'         => 'zoomverdien («z») kan ikke være større enn 17.',
	'slippymap_invalidlayer'    => 'Ugyldig «layer»-verdi «%1»',
	'slippymap_maperror'        => 'Kartfeil:',
	'slippymap_osmtext'         => 'Se dette kartet på OpenStreetMap.org',
	'slippymap_code'            => 'Wikikode for denne kartvisningen:',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'slippymap_desc'            => 'Umožňuje použitie značky <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> na zobrazenie posuvnej mapy OpenLayers. Mapy sú z [http://openstreetmap.org openstreetmap.org]',
	'slippymap_latmissing'      => 'Chýba hodnota lat (rovnobežka).',
	'slippymap_lonmissing'      => 'Chýba hodnota lon (poludník).',
	'slippymap_zoommissing'     => 'Chýba hodnota z (úroveň priblíženia)',
	'slippymap_longdepreciated' => 'Prosím, použite „lon” namiesto „long” (názov parametra sa zmenil).',
	'slippymap_widthnan'        => 'hodnota šírky (w) „%1” nie je platné celé číslo',
	'slippymap_heightnan'       => 'hodnota výšky (h) „%1” nie je platné celé číslo',
	'slippymap_zoomnan'         => 'hodnota úrovne priblíženia (z) „%1” nie je platné celé číslo',
	'slippymap_latnan'          => 'hodnota zemepisnej šírky (lat) „%1” nie je platné celé číslo',
	'slippymap_lonnan'          => 'hodnota zemepisnej dĺžky (lon) „%1” nie je platné celé číslo',
	'slippymap_widthbig'        => 'hodnota šírky (w) nemôže byť väčšia ako 1000',
	'slippymap_widthsmall'      => 'hodnota šírky (w) nemôže byť menšia ako 100',
	'slippymap_heightbig'       => 'hodnota výšky (h) nemôže byť väčšia ako 1000',
	'slippymap_heightsmall'     => 'hodnota výšky (h) nemôže byť menšia ako 100',
	'slippymap_latbig'          => 'hodnota zemepisnej dĺžky (h) nemôže byť väčšia ako 90',
	'slippymap_latsmall'        => 'hodnota zemepisnej dĺžky (h) nemôže byť menšia ako -90',
	'slippymap_lonbig'          => 'hodnota zemepisnej šírky (lon) nemôže byť väčšia ako 180',
	'slippymap_lonsmall'        => 'hodnota zemepisnej dĺžky (lon) nemôže byť menšia ako -180',
	'slippymap_zoomsmall'       => 'hodnota úrovne priblíženia (lon) nemôže byť menšia ako nula',
	'slippymap_zoom18'          => 'hodnota úrovne priblíženia (lon) nemôže byť väčšia ako 17. Toto rozšírenie MediaWiki využíva vrstvu „osmarender” OpenStreetMap, ktorá umožňuje úroveň priblíženia po 17. Vrstva Mapnik na openstreetmap.org umožňuje priblíženie do úrovne 18.',
	'slippymap_zoombig'         => 'hodnota úrovne priblíženia (lon) nemôže byť väčšia ako 17.',
	'slippymap_invalidlayer'    => 'Neplatná hodnota „layer” „%1”',
	'slippymap_maperror'        => 'Chyba mapy:',
	'slippymap_osmtext'         => 'Pozrite si túto mapu na OpenStreetMap.org',
	'slippymap_code'            => 'Wikikód tohto pohľadu na mapu:',
);

