<?php
/**
 * Internationalisation file for the RefreshSpecial extension.
 *
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Bartek Łapiński
 */
$messages['en'] = array (
	'refreshspecial' => 'Refresh special pages',
	'refreshspecial-desc' => 'Allows [[Special:RefreshSpecial|manual special page refresh]] of special pages',
	'refreshspecial-title' => 'Refresh special pages',
	'refreshspecial-help' =>  'This special page provides means to manually refresh special pages. When you have chosen all pages that you want to refresh, click on the Refresh button below to make it go. Warning: the refresh may take a while on larger wikis.',
	'refreshspecial-button' => 'Refresh selected',
	'refreshspecial-fail' => 'Please check at least one special page to refresh.',
	'refreshspecial-refreshing' => 'refreshing special pages',
	'refreshspecial-skipped' => 'cheap, skipped',
	'refreshspecial-success-subtitle' => 'refreshing special pages',
	'refreshspecial-choice' => 'refreshing special pages',
	'refreshspecial-js-disabled' => '(<i>You cannot select all pages when JavaScript is disabled</i>)',
	'refreshspecial-select-all-pages' => ' select all pages ',
	'refreshspecial-link-back' => 'Go back to extension ',
	'refreshspecial-here' => '<b>here</b>',
	'refreshspecial-none-selected' => 'You have not selected any special pages. Reverting to default selection.',
	'refreshspecial-db-error' => 'Failed: database error',
	'refreshspecial-no-page' => 'No such special page',
	'refreshspecial-slave-lagged' => 'Slave lagged, waiting...',
	'refreshspecial-reconnected' => 'Reconnected.',
	'refreshspecial-reconnecting' => 'Connection failed, reconnecting in 10 seconds...',
	'refreshspecial-total-display' => '<br />Refreshed $1 pages totaling $2 rows in time $3 (complete time of the script run is $4)',
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 */
$messages['fi'] = array(
	'refreshspecial' => 'Päivitä toimintosivuja',
	'refreshspecial-title' => 'Päivitä toimintosivuja',
	'refreshspecial-help' =>  'Tämä toimintosivu tarjoaa keinoja päivittää toimintosivuja manuaalisesti. Kun olet valinnut kaikki sivut, jotka haluat päivittää, napsauta "Päivitä"-nappia alapuolella päivittääksesi valitut. Varoitus: päivittäminen saattaa kestää jonkin aikaa isommissa wikeissä.',
	'refreshspecial-button' => 'Päivitä valitut',
	'refreshspecial-fail' => 'Valitse ainakin yksi päivitettävä toimintosivu.',
	'refreshspecial-refreshing' => 'päivitetään toimintosivuja',
	'refreshspecial-skipped' => 'halpa, ohitettu',
	'refreshspecial-success-subtitle' => 'päivitetään toimintosivuja',
	'refreshspecial-choice' => 'päivitetään toimintosivuja',
	'refreshspecial-js-disabled' => '(<i>Et voi valita kaikkia sivuja kun JavaScript on pois käytöstä</i>)',
	'refreshspecial-select-all-pages' => ' valitse kaikki sivut ',
	'refreshspecial-link-back' => 'Palaa lisäosaan ',
	'refreshspecial-here' => '<b>täällä</b>',
	'refreshspecial-none-selected' => 'Et ole valinnut yhtään toimintosivua. Palataan oletusasetuksiin.',
	'refreshspecial-db-error' => 'EPÄONNISTUI: tietokantavirhe',
	'refreshspecial-no-page' => 'Kyseistä toimintosivua ei ole',
	'refreshspecial-reconnected' => 'Yhdistetty uudelleen.',
	'refreshspecial-reconnecting' => 'Yhteys epäonnistui, yritetään uudelleen 10 sekunnin kuluttua...',
	'refreshspecial-total-display' => '<br />Päivitettiin $1 sivua; yhteensä $2 riviä ajassa $3 (yhteensä skriptin suorittamiseen meni aikaa $4)',
);