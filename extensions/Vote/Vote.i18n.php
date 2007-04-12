<?php

/**
 * Internationalisation file for the Vote extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * Please see the LICENCE file for terms of use and redistribution
 */

function efVoteMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'vote' => 'Vote',
'vote-header' => "You can vote for '''Supreme Overlord of the World''' here!",
'vote-current' => "Your current vote is for '''$1'''.",
'vote-legend' => 'Place or amend vote',
'vote-caption' => 'Your selection:',
'vote-choices' => "joker|The Joker
penguin|The Penguin
riddler|Riddler",
'vote-submit' => 'Vote',
'vote-registered' => 'Your vote has been registered.',
'vote-view-results' => 'View results',
'vote-results' => 'Vote results',
'vote-results-choice' => 'Choice',
'vote-results-count' => 'Count',
'vote-results-none' => 'No votes have been placed at this time.',
'vote-login' => 'You must $1 to vote.',
'vote-login-link' => 'log in',
'vote-invalid-choice' => 'You must select one of the available options.',
),

/* French (Ashar Voultoiz) */
'fr' => array(
'vote' => 'Vote',
'vote-header' => "Vous pouvez voter pour le '''maître de l'Univers''' ici !",
'vote-current' => "Votre vote actuel est pour '''$1'''.",
'vote-legend' => 'Placer ou modifier un vote',
'vote-caption' => 'Votre sélection :',
'vote-choices' => "joker|Le Joker
pingouin|Le Pingouin
sphinx|Sphinx",
'vote-submit' => 'Voter',
'vote-registered' => 'Votre vote a été enregistré.',
'vote-view-results' => 'Résultats',
'vote-results' => 'Résultats',
'vote-results-choice' => 'Choix',
'vote-results-count' => 'Voix',
'vote-results-none' => 'Aucun vote n\'a été placé pour le moment.',
'vote-login' => 'Vous devez $1 pour voter.',
'vote-login-link' => 'vous connecter',
'vote-invalid-choice' => 'Vous devez choisir une des options disponible.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'vote' => 'Pemilihan',
'vote-header' => "Anda dapat memilih '''Penguasa Tertinggi Dunia''' di sini!",
'vote-current' => "Pilihan Anda saat ini adalah '''$1'''.",
'vote-legend' => 'Berikan atau ubah pilihan',
'vote-caption' => 'Pilihan Anda:',
'vote-choices' => "joker|The Joker
penguin|The Penguin
riddler|Riddler",
'vote-submit' => 'Pilih',
'vote-registered' => 'Pilihan Anda telah didaftarkan.',
'vote-view-results' => 'Lihat hasil',
'vote-results' => 'Hasil pemungutan suara',
'vote-results-choice' => 'Pilihan',
'vote-results-count' => 'Suara',
'vote-results-none' => 'Saat ini belum ada suara yang masuk.',
'vote-login' => 'Anda harus $1 untuk memilih.',
'vote-login-link' => 'masuk log',
'vote-invalid-choice' => 'Anda harus memilih salah satu pilihan yang tersedia.',
),

	);
	return $messages;
}

?>
