<?php
/**
 * German language file for the 'StableVersion' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
			'stableversion_this_is_stable' => 'Dies ist eine stabile Version dieses Artikels. Sie können auch die aktuelle <a href="$1">Entwurfsversion</a> betrachten.',
			'stableversion_this_is_draft_no_stable' => 'Sie betrachten eine Entwurfsversion dieses Artikels; bisher gibt es keine stabile Version.',
			'stableversion_this_is_draft' => 'Dies ist eine Entwurfsversion dieses Artikels. Sie können auch die <a href="$1">stabile Version</a> betrachten.',
			'stableversion_reset_stable_version' => 'Klicken Sie <a href="$1">hier</a> um die Markierung als stabile Version zu entfernen!',
			'stableversion_set_stable_version' => 'Klicken Sie <a href="$1">hier</a> um diesen Artikel als stabile Version zu kennzeichnen!',
			'stableversion_set_ok' => 'Die stabile Version wurde erfolgreich gesetzt.',
			'stableversion_reset_ok' => 'Die stabile Version wurde erfolgreich entfernt. Diesesr Artikel hat aktuell keine stabile Version mehr.',
			'stableversion_return' => 'Zurück zu <a href="$1">$2</a>',
			
			'stableversion_reset_log' => 'Stabile Version wurde entfernt.',
			'stableversion_logpage' => 'Stabile-Version-Logbuch',
			'stableversion_logpagetext' => 'Dies ist ein Logbuch für Änderungen an stabilen Versionen.',
			'stableversion_logentry' => '',
			'stableversion_log' => 'Änderung #$1 ist nun eine stabile Version.',
			'stableversion_before_no' => 'Es gab bisher keine stabile Version.',
			'stableversion_before_yes' => 'Die letzte stablie Version war #$1.',
			'stableversion_this_is_stable_and_current' => "Das ist die stabile sowie auch die aktuellste Version.",
	)
);

?>
