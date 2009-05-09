<?php
/**
 * Internationalisation file for Poll extension.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();
 

$messages['en'] = array(
	'poll' => 'Polls',
	'poll-desc' => 'Add a [[Special:Poll|special page]] for using polls',
  'poll-title-create' => 'Create a new poll',
  'poll-title-vote' => 'Voting page',
  'poll-title-score' => 'Score',
  'poll-create-right-error' => 'You are not allowed to create a new poll(needed right: poll-create)',
  'poll-create-block-error' => 'You are not allowed to create a new poll because you use a blocked user',
  'poll-vote-right-error' => 'You are not allowed to vote(needed right: poll-vote)',
  'poll-vote-block-error' => 'You are not allowed to vote because you use a blocked user',
);

$messages['de'] = array(
	'poll' => 'Umfragen',
	'poll-desc' => 'Erstellt eine [[Special:Poll|Spezialsite]], um Umfragen zu nutzen',
  'poll-title-create' => 'Eine neue Umfrage erstellen',
  'poll-title-vote' => 'Abstimmen',
  'poll-title-score' => 'Auswertung',
  'poll-create-right-error' => 'Leider darfst du keine neue Umfrage erstellen(benötige Gruppenberechttigung: poll-create)',
  'poll-create-block-error' => 'Leider darfst du keine neue Umfrage erstellen, weil du einen gesperten Benutzer benutzt',
  'poll-vote-right-error' => 'Leider darfst du nicht abstimmen(benötige Gruppenberechttigung: poll-vote)',
  'poll-vote-block-error' => 'Leider darfst du nicht abstimmen, weil du einen gesperten Benutzer benutzt',
);
