<?php
/**
 * Internationalisation file for extension Throttle.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'acct_creation_global_soft_throttle_hit' => 'Account creation has been automatically suspended for a few moments due to an unusually large number of recent login attempts. Please wait a few minutes and try again.',
	'acct_creation_global_hard_throttle_hit' => "Account creation has been automatically suspended for a few seconds to reduce registration flood attacks. Please wait a moment and hit 'reload' in your browser to resubmit.",
);

$messages['de'] = array(
	'acct_creation_global_soft_throttle_hit' => 'Die Möglichkeit zur Erstellung von Benutzerkonten wurde für einen Moment aufgehoben, da es eine ungewöhnlich hohe Anzahl an Anmeldeversuchen gab. Bitte warte einige Minuten und versuche es dann erneut.',
	'acct_creation_global_hard_throttle_hit' => 'Die Möglichkeit zur Erstellung von Benutzerkonten wurde für einige Sekunden unterbrochen, um eine Flut von Registrierungen zu reduzieren. Bitte warte einen Moment und drücke dann den Neuladen-Knopf an deinem Browser.',
);

$messages['fr'] = array(
	'acct_creation_global_soft_throttle_hit' => "La création du compte a été automatiquement suspendue pour un certain temps. Ceci est du à un fort nombre de créations de comptes. Patientez pendant quelques minutes puis essayez à nouveau.",
	'acct_creation_global_hard_throttle_hit' => "La création a été automatiquement suspendue pendant quelques secondes afin de limiter les attaques informatiques par l'enregistrements en masse de nouveaux comptes. Patientez un moment et cliquez sur « recharger »  dans votre navigateur pour soumettre, une nouvelle fois, la demande.",
);
