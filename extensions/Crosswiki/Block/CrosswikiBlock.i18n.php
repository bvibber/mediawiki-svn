<?php
/**
 * Internationalisation file for extension CrosswikiBlock.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	# Special page
	'crosswikiblock-desc'       => 'Allows to block users on other wikis using a [[Special:Crosswikiblock|special page]]',
	'crosswikiblock'            => 'Block user on other wiki',
	'crosswikiblock-header'     => 'This page allows to block user on other wiki.
Please check if you are allowed to act on this wiki and your actions match all policies.',
	'crosswikiblock-target'     => 'IP address or username and destination wiki:',
	'crosswikiblock-expiry'     => 'Expiry:',
	'crosswikiblock-reason'     => 'Reason:',
	'crosswikiblock-submit'     => 'Block this user',
	'crosswikiblock-anononly'   => 'Block anonymous users only',
	'crosswikiblock-nocreate'   => 'Prevent account creation',
	'crosswikiblock-autoblock'  => 'Automatically block the last IP address used by this user, and any subsequent IP addresses they try to edit from',
	'crosswikiblock-noemail'    => 'Prevent user from sending e-mail',

	# Special:Unblock
	'crosswikiunblock'              => 'Unblock user on other wiki',
	'crosswikiunblock-header'       => 'This page allows to unblock user on other wiki.
Please check if you are allowed to act on this wiki and your actions match all policies.',
	'crosswikiunblock-user'         => 'Username, IP address or block ID and destination wiki:',
	'crosswikiunblock-reason'       => 'Reason:',
	'crosswikiunblock-submit'       => 'Unblock this user',
	'crosswikiunblock-success'      => "User '''$1''' unblocked successfully.

Return to:
* [[Special:CrosswikiBlock|Block form]]
* [[$2]]",

	# Errors and success message
	'crosswikiblock-nousername'     => 'No username was inputed',
	'crosswikiblock-local'          => 'Local blocks are not supported via this interface. Use [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound'     => 'Database $1 does not exist',
	'crosswikiblock-noname'         => '"$1" is not a valid username.',
	'crosswikiblock-nouser'         => 'User "$3" is not found.',
	'crosswikiblock-noexpiry'       => 'Invalid expiry: $1.',
	'crosswikiblock-noreason'       => 'No reason specified.',
	'crosswikiblock-notoken'        => 'Invalid edit token.',
	'crosswikiblock-alreadyblocked' => 'User $3 is already blocked.',
	'crosswikiblock-noblock'        => 'This user is not blocked.',
	'crosswikiblock-success'        => "User '''$3''' blocked successfully.

Return to:
* [[Special:CrosswikiBlock|Block form]]
* [[$4]]",
	'crosswikiunblock-local'          => 'Local unblocks are not supported via this interface. Use [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author Purodha
 */
$messages['qqq'] = array(
	'crosswikiblock-desc' => 'Extension description displayed on [[Special:Version]].',
	'crosswikiblock-expiry' => '{{Identical|Expiry}}',
	'crosswikiblock-reason' => '{{Identical|Reason}}',
	'crosswikiblock-submit' => '{{Identical|Block this user}}',
	'crosswikiblock-anononly' => '{{Identical|Block anonymous users only}}',
	'crosswikiblock-nocreate' => '{{Identical|Prevent account creation}}',
	'crosswikiblock-autoblock' => '{{Identical|Automatically block ...}}',
	'crosswikiblock-noemail' => '{{Identical|Prevent user from sending e-mail}}',
	'crosswikiunblock-reason' => '{{Identical|Reason}}',
	'crosswikiblock-notoken' => '{{Identical|Invalid edit token}}',
	'crosswikiblock-alreadyblocked' => '{{Identical|$1 is already blocked}}',
);

/** Niuean (ko e vagahau Niuē)
 * @author Jose77
 */
$messages['niu'] = array(
	'crosswikiunblock-reason' => 'Kakano:',
);

/** Afrikaans (Afrikaans)
 * @author Arnobarnard
 * @author Naudefj
 */
$messages['af'] = array(
	'crosswikiblock-reason' => 'Rede:',
	'crosswikiblock-anononly' => 'Blokkeer slegs anonieme gebruikers',
	'crosswikiblock-nocreate' => 'Blokkeer registrasie van gebruikers',
	'crosswikiblock-noemail' => 'Verbied gebruiker om e-pos te stuur',
	'crosswikiunblock-reason' => 'Rede:',
	'crosswikiblock-alreadyblocked' => 'Gebruiker $3 is reeds geblok.',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'crosswikiblock-reason' => 'Razón:',
	'crosswikiblock-anononly' => 'Bloqueyar nomás os usuarios anonimos',
	'crosswikiunblock-reason' => 'Razón:',
	'crosswikiblock-alreadyblocked' => "L'usuario $3 ya yera bloqueyato.",
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'crosswikiblock-desc' => 'يسمح بمنع المستخدمين في ويكيات أخرى باستخدام [[Special:Crosswikiblock|صفحة خاصة]]',
	'crosswikiblock' => 'منع مستخدم في ويكي آخر',
	'crosswikiblock-header' => 'هذه الصفحة تسمح بمنع المستخدمين في ويكي آخر.
من فضلك تحقق لو كان مسموحا لك بالعمل في هذه الويكي وأفعالك تطابق كل السياسات.',
	'crosswikiblock-target' => 'عنوان الأيبي أو اسم المستخدم والويكي المستهدف:',
	'crosswikiblock-expiry' => 'الانتهاء:',
	'crosswikiblock-reason' => 'السبب:',
	'crosswikiblock-submit' => 'منع هذا المستخدم',
	'crosswikiblock-anononly' => 'امنع المستخدمين المجهولين فقط',
	'crosswikiblock-nocreate' => 'امنع إنشاء الحسابات',
	'crosswikiblock-autoblock' => 'تلقائيا امنع آخر عنوان أيبي تم استخدامه بواسطة هذا المستخدم، وأي أيبيهات لاحقة يحاول التعديل منها',
	'crosswikiblock-noemail' => 'امنع المستخدم من إرسال بريد إلكتروني',
	'crosswikiunblock' => 'رفع المنع عن مستخدم في ويكي أخرى',
	'crosswikiunblock-header' => 'هذه الصفحة تسمح برفع المنع عن مستخدم في ويكي أخرى.
من فضلك تحقق من أنه مسموح لك بالعمل على هذه الويكي وأن أفعالك تطابق كل السياسات.',
	'crosswikiunblock-user' => 'اسم المستخدم، عنوان الأيبي أو رقم المنع والويكي المستهدفة:',
	'crosswikiunblock-reason' => 'السبب:',
	'crosswikiunblock-submit' => 'رفع المنع عن هذا المستخدم',
	'crosswikiunblock-success' => "المستخدم '''$1''' تم رفع المنع عنه بنجاح.

ارجع إلى:
* [[Special:CrosswikiBlock|استمارة المنع]]
* [[$2]]",
	'crosswikiblock-nousername' => 'لا اسم مستخدم تم إدخاله',
	'crosswikiblock-local' => 'عمليات المنع المحلية غير مدعومة من خلال هذه الواجهة. استخدم [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'قاعدة البيانات $1 غير موجودة',
	'crosswikiblock-noname' => '"$1" ليس اسم مستخدم صحيحا.',
	'crosswikiblock-nouser' => 'المستخدم "$3" غير موجود.',
	'crosswikiblock-noexpiry' => 'تاريخ انتهاء غير صحيح: $1.',
	'crosswikiblock-noreason' => 'لا سبب تم تحديده.',
	'crosswikiblock-notoken' => 'نص تعديل غير صحيح.',
	'crosswikiblock-alreadyblocked' => 'المستخدم $3 ممنوع بالفعل.',
	'crosswikiblock-noblock' => 'هذا المستخدم ليس ممنوعا.',
	'crosswikiblock-success' => "المستخدم '''$3''' تم منعه بنجاح.

ارجع إلى:
* [[Special:CrosswikiBlock|استمارة المنع]]
* [[$4]]",
	'crosswikiunblock-local' => 'عمليات المنع المحلية غير مدعومة بواسطة هذه الواجهة. استخدم [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'crosswikiblock-desc' => 'يسمح بمنع المستخدمين فى ويكيات أخرى باستخدام [[Special:Crosswikiblock|صفحة خاصة]]',
	'crosswikiblock' => 'منع مستخدم فى ويكى آخر',
	'crosswikiblock-header' => 'هذه الصفحة تسمح بمنع المستخدمين فى ويكى آخر.
من فضلك تحقق لو كان مسموحا لك بالعمل فى هذه الويكى وأفعالك تطابق كل السياسات.',
	'crosswikiblock-target' => 'عنوان الأيبى أو اسم المستخدم والويكى المستهدف:',
	'crosswikiblock-expiry' => 'الانتهاء:',
	'crosswikiblock-reason' => 'السبب:',
	'crosswikiblock-submit' => 'منع هذا المستخدم',
	'crosswikiblock-anononly' => 'امنع المستخدمين المجهولين فقط',
	'crosswikiblock-nocreate' => 'امنع إنشاء الحسابات',
	'crosswikiblock-autoblock' => 'تلقائيا امنع آخر عنوان أيبى تم استخدامه بواسطة هذا المستخدم، وأى أيبيهات لاحقة يحاول التعديل منها',
	'crosswikiblock-noemail' => 'امنع المستخدم من إرسال بريد إلكتروني',
	'crosswikiunblock' => 'رفع المنع عن مستخدم فى ويكى أخرى',
	'crosswikiunblock-header' => 'هذه الصفحة تسمح برفع المنع عن مستخدم فى ويكى أخرى.
من فضلك تحقق من أنه مسموح لك بالعمل على هذه الويكى وأن أفعالك تطابق كل السياسات.',
	'crosswikiunblock-user' => 'اسم المستخدم، عنوان الأيبى أو رقم المنع والويكى المستهدفة:',
	'crosswikiunblock-reason' => 'السبب:',
	'crosswikiunblock-submit' => 'رفع المنع عن هذا المستخدم',
	'crosswikiunblock-success' => "المستخدم '''$1''' تم رفع المنع عنه بنجاح.

ارجع إلى:
* [[Special:CrosswikiBlock|استمارة المنع]]
* [[$2]]",
	'crosswikiblock-nousername' => 'لا اسم مستخدم تم إدخاله',
	'crosswikiblock-local' => 'عمليات المنع المحلية غير مدعومة من خلال هذه الواجهة. استخدم [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'قاعدة البيانات $1 غير موجودة',
	'crosswikiblock-noname' => '"$1" ليس اسم مستخدم صحيحا.',
	'crosswikiblock-nouser' => 'المستخدم "$3" غير موجود.',
	'crosswikiblock-noexpiry' => 'تاريخ انتهاء غير صحيح: $1.',
	'crosswikiblock-noreason' => 'لا سبب تم تحديده.',
	'crosswikiblock-notoken' => 'نص تعديل غير صحيح.',
	'crosswikiblock-alreadyblocked' => 'المستخدم $3 ممنوع بالفعل.',
	'crosswikiblock-noblock' => 'هذا المستخدم ليس ممنوعا.',
	'crosswikiblock-success' => "المستخدم '''$3''' تم منعه بنجاح.

ارجع إلى:
* [[Special:CrosswikiBlock|استمارة المنع]]
* [[$4]]",
	'crosswikiunblock-local' => 'عمليات المنع المحلية غير مدعومة بواسطة هذه الواجهة. استخدم [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 */
$messages['be-tarask'] = array(
	'crosswikiblock-reason' => 'Прычына:',
	'crosswikiunblock-reason' => 'Прычына:',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'crosswikiblock-desc' => 'Позволява блокирането на потребители в други уикита чрез [[Special:Crosswikiblock|специална страница]]',
	'crosswikiblock' => 'Блокиране на потребител в друго уики',
	'crosswikiblock-header' => 'Тази страница позволява блокирането на потребители в други уикита.
Необходимо е да проверите дали имате права да изпълните действието на това уики и дали не е в разрез с действащите политики.',
	'crosswikiblock-target' => 'IP адрес или потребителско име и целево уики:',
	'crosswikiblock-expiry' => 'Изтича на:',
	'crosswikiblock-reason' => 'Причина:',
	'crosswikiblock-submit' => 'Блокиране на този потребител',
	'crosswikiblock-anononly' => 'Блокиране само на нерегистрирани потребители',
	'crosswikiblock-nocreate' => 'Без създаване на сметки',
	'crosswikiblock-autoblock' => 'Автоматично блокиране на посления използван от потребителя IP адрес и всички адреси, от които направи опит за редактиране',
	'crosswikiblock-noemail' => 'Без възможност за изпращане на е-поща',
	'crosswikiunblock' => 'Отблокиране на потребител на друго уики',
	'crosswikiunblock-header' => 'Тази страница позволява отблокирането на потребители на други уикита.
Убедете се, че имате необходимите права за извършване на действието и че действието не е в разрез с текущата политика.',
	'crosswikiunblock-user' => 'Потребителско име, IP адрес или номер на блокирането и целево уики:',
	'crosswikiunblock-reason' => 'Причина:',
	'crosswikiunblock-submit' => 'Отблокиране на потребителя',
	'crosswikiunblock-success' => "Потребител '''$1''' беше успешно отблокиран.

Връщане към:
* [[Special:CrosswikiBlock|Формуляра за блокиране]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Не беше въведено потребителско име',
	'crosswikiblock-local' => 'Локалните блокирания не се поддържат от този интерфейс. Използва се [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Не съществува база данни $1',
	'crosswikiblock-noname' => '„$1“ не е валидно потребителско име.',
	'crosswikiblock-nouser' => 'Не беше намерен потребител „$3“',
	'crosswikiblock-noexpiry' => 'Невалиден срок за изтичане: $1.',
	'crosswikiblock-noreason' => 'Не е посочена причина.',
	'crosswikiblock-alreadyblocked' => 'Потребител $3 е вече блокиран.',
	'crosswikiblock-noblock' => 'Този потребител не е блокиран.',
	'crosswikiblock-success' => "Потребител '''$3''' беше блокиран успешно.

Връщане към:
* [[Special:CrosswikiBlock|Формуляра за блокиране]]
* [[$4]]",
	'crosswikiunblock-local' => 'Локалните отблокирания не се поддържат от този интерфейс. Използва се [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Catalan (Català)
 * @author SMP
 */
$messages['ca'] = array(
	'crosswikiblock-alreadyblocked' => "L'usuari $3 ja està blocat.",
);

/** Czech (Česky)
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'crosswikiblock-desc' => 'Umožňuje blokování uživatelů na jiných wiki pomocí [[Special:Crosswikiblock|speciální stránky]]',
	'crosswikiblock-expiry' => 'Vypršení:',
	'crosswikiblock-reason' => 'Důvod:',
	'crosswikiblock-submit' => 'Zablokovat tohoto uživatele',
	'crosswikiblock-anononly' => 'Zablokovat všechny anonymní uživatele',
	'crosswikiblock-nocreate' => 'Zabránit tvorbě účtů',
	'crosswikiblock-noemail' => 'Zabránit uživateli odesílat e-mail',
	'crosswikiunblock' => 'Odblokovat uživatele na jiné wiki',
	'crosswikiunblock-submit' => 'Odblokovat tohoto uživatele',
);

/** Danish (Dansk)
 * @author Jon Harald Søby
 */
$messages['da'] = array(
	'crosswikiblock-reason' => 'Begrundelse:',
	'crosswikiblock-submit' => 'Bloker denne bruger',
	'crosswikiblock-nocreate' => 'Forhindre oprettelse af brugerkonti',
	'crosswikiblock-autoblock' => 'Spærre den IP-adresse, der bruges af denne bruger samt automatisk alle følgende, hvorfra han foretager ændringer eller forsøger at anlægge brugerkonti',
	'crosswikiblock-noemail' => 'Spærre brugerens adgang til at sende mail',
	'crosswikiunblock-reason' => 'Begrundelse:',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'crosswikiblock-desc' => 'Erlaubt die Sperre von Benutzern in anderen Wikis über eine [[Special:Crosswikiblock|Spezialseite]]',
	'crosswikiblock' => 'Sperre Benutzer in einem anderen Wiki',
	'crosswikiblock-header' => 'Diese Spezialseite erlaubt die Sperre eines Benutzers in einem anderen Wiki.
	Bitte prüfe, ob du die Befugnis hast, in diesem anderen Wiki zu sperren und ob deine Aktion deren Richtlinien entspricht.',
	'crosswikiblock-target' => 'IP-Adresse oder Benutzername und Zielwiki:',
	'crosswikiblock-expiry' => 'Sperrdauer:',
	'crosswikiblock-reason' => 'Begründung:',
	'crosswikiblock-submit' => 'IP-Adresse/Benutzer sperren',
	'crosswikiblock-anononly' => 'Sperre nur anonyme Benutzer (angemeldete Benutzer mit dieser IP-Adresse werden nicht gesperrt). In vielen Fällen empfehlenswert.',
	'crosswikiblock-nocreate' => 'Erstellung von Benutzerkonten verhindern',
	'crosswikiblock-autoblock' => 'Sperre die aktuell von diesem Benutzer genutzte IP-Adresse sowie automatisch alle folgenden, von denen aus er Bearbeitungen oder das Anlegen von Benutzeraccounts versucht.',
	'crosswikiblock-noemail' => 'E-Mail-Versand sperren',
	'crosswikiunblock' => 'Entsperre Benutzer in einem anderen Wiki',
	'crosswikiunblock-header' => 'Diese Spezialseite erlaubt die Aufhebung einer Benutzersperre in einem anderen Wiki.
	Bitte prüfe, ob du die Befugnis hast, in diesem anderen Wiki zu sperren und ob deine Aktion deren Richtlinien entspricht.',
	'crosswikiunblock-user' => 'IP-Adresse oder Benutzername und Zielwiki:',
	'crosswikiunblock-reason' => 'Begründung:',
	'crosswikiunblock-submit' => 'Sperre für IP-Adresse/Benutzer aufheben',
	'crosswikiunblock-success' => "Benutzer '''„$1“''' erfolgreich entsperrt.

Zurück zu:
* [[Special:CrosswikiBlock|Sperrformular]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Es wurde kein Benutzername eingegeben',
	'crosswikiblock-local' => 'Lokale Sperren werden durch dieses Interface nicht unterstützt. Benutze [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Datenbank $1 ist nicht vorhanden',
	'crosswikiblock-noname' => '„$1“ ist kein gültiger Benutzername.',
	'crosswikiblock-nouser' => 'Benutzer „$3“ nicht gefunden.',
	'crosswikiblock-noexpiry' => 'Ungültige Sperrdauer: $1.',
	'crosswikiblock-noreason' => 'Begründung fehlt.',
	'crosswikiblock-notoken' => 'Ungültiges Bearbeitungs-Token.',
	'crosswikiblock-alreadyblocked' => 'Benutzer „$3“ ist bereits gesperrt.',
	'crosswikiblock-noblock' => 'Dieser Benutzer ist nicht gesperrt.',
	'crosswikiblock-success' => "Benutzer '''„$3“''' erfolgreich gesperrt.

Zurück zu:
* [[Special:CrosswikiBlock|Sperrformular]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokale Sperren werden über dieses Interfache nicht unterstützt. Bitte benutze [[Special:IPBlockList|{{int:ipblocklist}}]].',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'crosswikiblock-desc' => 'Dowólujo wužywarjow w drugich wikijach z pomocu [[Special:Crosswikiblock|specialnego boka]] blokěrowaś',
	'crosswikiblock' => 'Wužywarja na drugem wikiju blokěrowaś',
	'crosswikiblock-header' => 'Toś ten bok dowólujo wužywarja na drugem wikiju blokěrowaś.
Kontrolěruj pšosym, lěc smějoš na toś tom wikiju aktiwny byś a twóje akcije směrnicam wótpowěduju.',
	'crosswikiblock-target' => 'IP-adresa abo wužywarske mě a celowy wiki:',
	'crosswikiblock-expiry' => 'Pśepadnjenje:',
	'crosswikiblock-reason' => 'Pśicyna:',
	'crosswikiblock-submit' => 'Toś togo wužywarja blokěrowaś',
	'crosswikiblock-anononly' => 'Jano anonymnych wužywarjow blokěrowaś',
	'crosswikiblock-nocreate' => 'Napóranjeju kontow zajźowaś',
	'crosswikiblock-autoblock' => 'IP-adresu, kótaruž wužywaŕ jo ako slědnu wužył a wše slědujuce IP-adresy, z kótarychž wopytujo wobźěłaś, awtomatiski blokěrowaś',
	'crosswikiblock-noemail' => 'Wužiwarjeju pósłanje e-mailow zawóboraś',
	'crosswikiunblock' => 'Blokěrowanje wužywarja na drugem wikiju wótpóraś',
	'crosswikiunblock-header' => 'Toś ten bok dowólujo wótpóranje blokěrowanja wužywarja na drugem wikiju.
Kontrolěruj pšosym, lěc smějoš na toś tom wikiju aktiwny byś a twóje akcije wšym směrnicam wótpowěduju.',
	'crosswikiunblock-user' => 'Wužywarske mě, IP-adresa abo ID blokěrowanja a celowy wiki:',
	'crosswikiunblock-reason' => 'Pśicyna:',
	'crosswikiunblock-submit' => 'Blokěrowanje za toś togo wužywarja wótpóraś',
	'crosswikiunblock-success' => "Blokěrowanje za wužywarja '''$1''' wuspěšnje wótpórane.

Slědk k:
* [[Special:CrosswikiBlock|Blokěrowański formular]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Žedne wužywarske mě zapódane',
	'crosswikiblock-local' => 'Lokalne blokěrowanja njepódpěraju se pśez toś ten interfejs. Wužyj [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Datowa banka $1 njeeksistujo',
	'crosswikiblock-noname' => '"$1" njejo płaśiwe wužywarske mě.',
	'crosswikiblock-nouser' => 'Wužywaŕ "$3" njejo se namakał.',
	'crosswikiblock-noexpiry' => 'Njepłaśiwe pśepadnjenje: $1.',
	'crosswikiblock-noreason' => 'Žedna pśicyna pódana.',
	'crosswikiblock-notoken' => 'Njepłaśiwy wobźěłański token.',
	'crosswikiblock-alreadyblocked' => 'Wužywaŕ $3 jo južo blokěrowany.',
	'crosswikiblock-noblock' => 'Toś ten wužywaŕ njejo blokěrowany.',
	'crosswikiblock-success' => "Wužywaŕ '''$3''' wuspěšnje blokěrowany.

Slědk k:
* [[Special:CrosswikiBlock|Blokěrowański formular]]
* [[$4]]",
	'crosswikiunblock-local' => 'Wótpóranja lokalnych blokěrowanjow njepódpěraju se pśez toś ten interfejs. Wužyj [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'crosswikiblock-reason' => 'Λόγος:',
	'crosswikiunblock-reason' => 'Λόγος',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'crosswikiblock-desc' => 'Permesas forbari uzantojn ĉe aliaj vikioj uzante [[Special:Crosswikiblock|specialan paĝon]]',
	'crosswikiblock' => 'Forbari uzanton ĉe alia vikio',
	'crosswikiblock-header' => 'Ĉi paĝo permesas forbari uzanton ĉe alia vikio.
Bonvolu verigi se vi rajtas agi en ĉi vikio kaj viaj agoj sekvas ĉiujn kondutmanierojn.',
	'crosswikiblock-target' => 'IP-adreso aŭ uzanto-nomo kaj cela vikio:',
	'crosswikiblock-expiry' => 'Findato:',
	'crosswikiblock-reason' => 'Kialo:',
	'crosswikiblock-submit' => 'Forbari ĉi tiun uzanton',
	'crosswikiblock-anononly' => 'Forbari nur anonimajn uzantojn',
	'crosswikiblock-nocreate' => 'Preventi kreadon de kontoj',
	'crosswikiblock-autoblock' => 'Aŭtomate forbaru la lastan IP-adreson uzatan de ĉi uzanto, kaj iujn ajn postajn el kiujn ili provas redakti.',
	'crosswikiblock-noemail' => 'Preventu de uzanto sendi retpoŝton',
	'crosswikiunblock' => 'Restarigi uzanton ĉe alia vikio',
	'crosswikiunblock-header' => 'Ĉi tiu paĝo permesas malforbari uzanton ĉe alia vikio.
Bonvolu verigi se vi rajtas agi en ĉi vikio kaj viaj agoj sekvas ĉiujn kondutmanierojn.',
	'crosswikiunblock-user' => 'Uzanto-nomo, IP-adreso, aŭ forbaro-identigo kaj cela vikio:',
	'crosswikiunblock-reason' => 'Kialo:',
	'crosswikiunblock-submit' => 'Restarigi ĉi tiun uzanton',
	'crosswikiunblock-success' => "Uzanto '''$1''' malforbarita sukcese.

Reen:
* [[Special:CrosswikiBlock|Forbarpaĝo]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Neniu uzanto-nomo estis entajpita',
	'crosswikiblock-local' => 'Lokaj forbaroj ne estas subtenataj per ĉi interfaco. Uzu [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Datumbazo $1 ne ekzistas.',
	'crosswikiblock-noname' => '"$1" ne estas valida uzanto-nomo.',
	'crosswikiblock-nouser' => 'Uzanto "$3" ne estas trovita.',
	'crosswikiblock-noexpiry' => 'Nevalida findato: $1.',
	'crosswikiblock-noreason' => 'Nenia kialo donata.',
	'crosswikiblock-notoken' => 'Nevalida redakta ĵetono.',
	'crosswikiblock-alreadyblocked' => 'Uzanto $3 jam estas forbarita.',
	'crosswikiblock-noblock' => 'Ĉi tiu uzanto ne estas forbarita.',
	'crosswikiblock-success' => "Uzanto '''$3''' sukcese forbarita.

Reen:
* [[Special:CrosswikiBlock|Forbarpaĝo]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokaj malforbaroj ne estas subtenataj per ĉi interfaco. Uzu [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Basque (Euskara)
 * @author Theklan
 */
$messages['eu'] = array(
	'crosswikiblock-noemail' => 'Erabiltzaileak e-mailak bidal ditzan ekidin',
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 * @author Nike
 */
$messages['fi'] = array(
	'crosswikiblock' => 'Estä käyttäjä toisessa wikissä',
	'crosswikiblock-header' => 'Tämä sivu mahdollistaa käyttäjien estämisen toisessa wikissä.
Tarkista, saatko toimia tässä wikissä ja että toimesi ovat käytäntöjen mukaisia.',
	'crosswikiblock-target' => 'IP-osoite tai käyttäjänimi kohdewikissä',
	'crosswikiblock-expiry' => 'Kesto',
	'crosswikiblock-reason' => 'Syy',
	'crosswikiblock-submit' => 'Estä tämä käyttäjä',
	'crosswikiblock-anononly' => 'Estä vain kirjautumattomat käyttäjät',
	'crosswikiblock-nocreate' => 'Estä tunnusten luonti',
	'crosswikiblock-autoblock' => 'Estä viimeisin IP-osoite, josta käyttäjä on muokannut, sekä ne osoitteet, joista hän jatkossa yrittää muokata.',
	'crosswikiblock-noemail' => 'Estä käyttäjää lähettämästä sähköpostia',
	'crosswikiunblock' => 'Poista käyttäjän muokkausesto toisesta wikistä',
	'crosswikiunblock-header' => 'Tämä sivu mahdollistaa käyttäjien muokkauseston poistamisen toisesta wikistä.
Tarkista, saatko toimia tässä wikissä ja että toimesi ovat käytäntöjen mukaisia.',
	'crosswikiunblock-user' => 'Käyttäjänimi, IP-osoite tai eston ID ja kohdewiki',
	'crosswikiunblock-reason' => 'Syy',
	'crosswikiunblock-submit' => 'Poista tämän käyttäjän muokkausesto',
);

/** French (Français)
 * @author Grondin
 * @author IAlex
 * @author Meithal
 * @author Urhixidur
 */
$messages['fr'] = array(
	'crosswikiblock-desc' => "Permet de bloquer des utilisateurs sur d'autres wikis en utilisant [[Special:Crosswikiblock|une page spéciale]]",
	'crosswikiblock' => 'Bloquer un utilisateur sur un autre wiki',
	'crosswikiblock-header' => 'Cette page permet de bloquer un utilisateur sur un autre wiki.

Vérifiez si vous êtes habilité pour agir sur ce wiki et que vos actions respectent toutes les règles.',
	'crosswikiblock-target' => "Adresse IP ou nom d'utilisateur et wiki de destination :",
	'crosswikiblock-expiry' => 'Expiration :',
	'crosswikiblock-reason' => 'Motif :',
	'crosswikiblock-submit' => 'Bloquer cet utilisateur',
	'crosswikiblock-anononly' => 'Bloquer uniquement les utilisateurs anonymes',
	'crosswikiblock-nocreate' => 'Interdire la création de compte',
	'crosswikiblock-autoblock' => "Bloque automatiquement la dernière adresse IP utilisée par cet utilisateur, et toutes les IP subséquentes qui essaient d'éditer",
	'crosswikiblock-noemail' => "Interdire à l'utilisateur d'envoyer un courriel",
	'crosswikiunblock' => "Débloquer en écriture un utilisateur d'un autre wiki",
	'crosswikiunblock-header' => "Cette page permet de débloquer en écriture un utilisateur d'un autre wiki.
Veuillez vous assurer que vous possédez les droits et respectez les règles en vigueur sur ce wiki.",
	'crosswikiunblock-user' => "Nom d'utilisateur, adresse IP ou l'id de blocage et le wiki ciblé :",
	'crosswikiunblock-reason' => 'Motif :',
	'crosswikiunblock-submit' => 'Débloquer en écriture cet utilisateur',
	'crosswikiunblock-success' => "L'utilisateur '''$1''' a été débloqué en écriture avec succès.

Revenir à :
* [[Special:CrosswikiBlock|Formulaire de blocage]]
* [[$2]]",
	'crosswikiblock-nousername' => "Aucun nom d'utilisateur n'a été indiqué",
	'crosswikiblock-local' => 'Les blocages locaux ne sont pas supportés au travers de cette interface. Utilisez [[Special:BlockIP|{{int:blockip}}]].',
	'crosswikiblock-dbnotfound' => 'La base de données « $1 » n’existe pas',
	'crosswikiblock-noname' => '« $1 » n’est pas un nom d’utilisateur valide.',
	'crosswikiblock-nouser' => 'L’utilisateur « $3 » est introuvable.',
	'crosswikiblock-noexpiry' => 'Date ou durée d’expiration incorrecte : $1.',
	'crosswikiblock-noreason' => 'Aucun motif indiqué.',
	'crosswikiblock-notoken' => 'Édition prise incorrecte.',
	'crosswikiblock-alreadyblocked' => 'L’utilisateur « $3 » est déjà bloqué.',
	'crosswikiblock-noblock' => "Cet utilisateur n'est pas bloqué en écriture.",
	'crosswikiblock-success' => "L’utilisateur '''$3''' a été bloqué avec succès.

Revenir vers :
* [[Special:CrosswikiBlock|Le formulaire de blocage]] ;
* [[$4]].",
	'crosswikiunblock-local' => 'Les blocages en écriture locaux ne sont pas supportés via cette interface. Utilisez [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Western Frisian (Frysk)
 * @author Snakesteuben
 */
$messages['fy'] = array(
	'crosswikiblock-expiry' => 'Ferrint nei:',
	'crosswikiblock-anononly' => 'Slút allinich anonyme meidoggers út',
	'crosswikiblock-autoblock' => "Automatysk de lêste IP adressen útslute dy't troch dizze meidogger brûkt binne.",
	'crosswikiblock-alreadyblocked' => 'Meidogger $3 is al útsluten.',
);

/** Galician (Galego)
 * @author Alma
 * @author Toliño
 * @author Xosé
 */
$messages['gl'] = array(
	'crosswikiblock-desc' => 'Permite bloquear usuarios doutros wikis mediante unha [[Special:Crosswikiblock|páxina especial]]',
	'crosswikiblock' => 'Usuario bloqueado noutro wiki',
	'crosswikiblock-header' => 'Esta páxina permítelle bloquear un usuario noutro wiki.
Por favor, comprobe se ten permiso para actuar neste wiki que se as súas accións coinciden coas políticas.',
	'crosswikiblock-target' => 'Enderezo IP ou nome de usuario e wiki de destino:',
	'crosswikiblock-expiry' => 'Remate:',
	'crosswikiblock-reason' => 'Razón:',
	'crosswikiblock-submit' => 'Bloquear este usuario',
	'crosswikiblock-anononly' => 'Bloquear só usuarios anónimos',
	'crosswikiblock-nocreate' => 'Previr a creación de contas',
	'crosswikiblock-autoblock' => 'Bloquear automaticamente o último enderezo IP utilizado por este usuario, e calquera outro enderezo desde o que intente editar',
	'crosswikiblock-noemail' => 'Advertir ao usuario do envío de correo electrónico',
	'crosswikiunblock' => 'Desbloquear este usuario noutro wiki',
	'crosswikiunblock-header' => 'Esta páxina permitiralle desbloquear un usuario noutro wiki.
Por favor, comprobe se lle está permitido actuar neste wiki e se os seus actos coinciden coas políticas.',
	'crosswikiunblock-user' => 'Nome de usuario, enderezo IP ou ID de bloqueo e wiki de destino:',
	'crosswikiunblock-reason' => 'Razón:',
	'crosswikiunblock-submit' => 'Desbloquear este usuario',
	'crosswikiunblock-success' => "O usuario '''$1''' foi desbloqueado con éxito.

Voltar a:
* [[Special:CrosswikiBlock|Formulario de bloqueo]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Non foi inserido ningún alcume',
	'crosswikiblock-local' => 'Os bloqueos locais non están soportados mediante esta interface. Use [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'A base de datos $1 non existe',
	'crosswikiblock-noname' => '"$1" non é un nome de usuario válido.',
	'crosswikiblock-nouser' => 'Non se atopa o usuario "$3".',
	'crosswikiblock-noexpiry' => 'Caducidade non válida: $1.',
	'crosswikiblock-noreason' => 'Ningunha razón especificada.',
	'crosswikiblock-notoken' => 'Sinal de edición non válido.',
	'crosswikiblock-alreadyblocked' => 'O usuario $3 xa está bloqueado.',
	'crosswikiblock-noblock' => 'Este usuario non está bloqueado.',
	'crosswikiblock-success' => "O usuario '''$3''' foi bloqueado con éxito.

Voltar a:
* [[Special:CrosswikiBlock|Formulario de bloqueo]]
* [[$4]]",
	'crosswikiunblock-local' => 'Os desbloqueos locais non están soportados mediante esta interface. Use [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Gothic (𐌲𐌿𐍄𐌹𐍃𐌺)
 * @author Jocke Pirat
 */
$messages['got'] = array(
	'crosswikiblock-reason' => 'Faírina:',
	'crosswikiunblock-reason' => 'Faírina:',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'crosswikiblock-reason' => 'Fa:',
	'crosswikiunblock-reason' => 'Fa:',
);

/** Hawaiian (Hawai`i)
 * @author Singularity
 */
$messages['haw'] = array(
	'crosswikiblock-reason' => 'Kumu:',
	'crosswikiunblock-reason' => 'Kumu:',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 */
$messages['hi'] = array(
	'crosswikiblock-desc' => 'अन्य विकियोंपर [[Special:Crosswikiblock|विशेष पृष्ठ]] का इस्तेमाल करके सदस्य ब्लॉक करने की अनुमति देता हैं।',
	'crosswikiblock' => 'अन्य विकिपर सदस्यको ब्लॉक करें',
	'crosswikiblock-header' => 'यह पन्ना अन्य विकियोंपर सदस्य को ब्लॉक करने की अनुमति देता हैं।
कृपया यह क्रिया करनेके लिये पर्याप्त अधिकार आपको हैं और यह क्रिया नीती के अनुसार ही हैं यह जाँच लें।',
	'crosswikiblock-target' => 'आईपी एड्रेस या सदस्यनाम तथा लक्ष्य विकि:',
	'crosswikiblock-expiry' => 'समाप्ती:',
	'crosswikiblock-reason' => 'कारण:',
	'crosswikiblock-submit' => 'इस सदस्य को ब्लॉक करें',
	'crosswikiblock-anononly' => 'सिर्फ अनामक सदस्योंको ब्लॉक करें',
	'crosswikiblock-nocreate' => 'खाता खोलने पर प्रतिबंध लगायें',
	'crosswikiblock-noemail' => 'इ-मेल भेजने पर प्रतिबंध लगायें',
	'crosswikiunblock' => 'अन्य विकियोंपर सदस्यको अनब्लॉक करें',
	'crosswikiunblock-user' => 'सदस्य नाम, आईपी एड्रेस या ब्लॉक क्रमांक तथा लक्ष्य विकि:',
	'crosswikiunblock-reason' => 'कारण:',
	'crosswikiunblock-submit' => 'इस सदस्य को अनब्लॉक करें',
	'crosswikiblock-nousername' => 'सदस्यनाम दिया नहीं',
	'crosswikiblock-local' => 'स्थानिक ब्लॉक यहां पर बदले नहीं जा सकतें। [[Special:BlockIP|{{int:blockip}}]] का इस्तेमाल करें',
	'crosswikiblock-dbnotfound' => 'डाटाबेस $1 उपलब्ध नहीं हैं',
	'crosswikiblock-noname' => '"$1" यह वैध सदस्यनाम नहीं हैं।',
	'crosswikiblock-nouser' => 'सदस्य "$3" मिला नहीं।',
	'crosswikiblock-noexpiry' => 'गलत समाप्ती: $1।',
	'crosswikiblock-noreason' => 'कारण दिया नहीं।',
	'crosswikiblock-notoken' => 'गलत एडिट टोकन',
	'crosswikiblock-alreadyblocked' => 'सदस्य $3 को पहलेसे ब्लॉक किया हुआ हैं।',
	'crosswikiblock-noblock' => 'इस सदस्यको ब्लॉक नहीं किया हैं।',
	'crosswikiunblock-local' => 'स्थानिक अनब्लॉक यहां पर बदले नहीं जा सकतें। [[Special:IPBlockList|{{int:ipblocklist}}]] का इस्तेमाल करें',
);

/** Hiligaynon (Ilonggo)
 * @author Jose77
 */
$messages['hil'] = array(
	'crosswikiblock-reason' => 'Rason:',
	'crosswikiunblock-reason' => 'Rason:',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'crosswikiblock-desc' => 'Dowola wužiwarjow na druhich wikijach z pomocu [[Special:Crosswikiblock|specialneje strony]] blokować',
	'crosswikiblock' => 'Wužiwarja na druhim wikiju blokować',
	'crosswikiblock-header' => 'Tuta strona dowola wužiwarja na druhim wikiju blokować.
Prošu pruwuj, hač maš dowolnosć na tym wikiju skutkować a swoje akcije wšěm prawidłam wotpowěduja.',
	'crosswikiblock-target' => 'IP-adresa abo wužiwarske mjeno a cilowy wiki:',
	'crosswikiblock-expiry' => 'Spadnjenje:',
	'crosswikiblock-reason' => 'Přičina:',
	'crosswikiblock-submit' => 'Tutoho wužiwarja blokować',
	'crosswikiblock-anononly' => 'Jenož anonymnych wužiwarjow blokować',
	'crosswikiblock-nocreate' => 'Wutworjenju konta zadźěwać',
	'crosswikiblock-autoblock' => 'Awtomatisce poslednju IPa-dresu wužitu wot tutoho wužiwarja blokować, inkluziwnje naslědnych IP-adresow, z kotrychž pospytuje wobdźěłać',
	'crosswikiblock-noemail' => 'Słanju e-mejlkow wot wužiwarja zadźěwać',
	'crosswikiunblock' => 'Wužiwarja na druhim wikiju wotblokować',
	'crosswikiunblock-header' => 'Tuta strona zmóžnja wužiwarja na druhim wikiju wotblokować.
Přepruwuj prošu, hač směš na tutym wikiju skutkować a hač twoje akcije wšěm prawidłam wotpowěduja.',
	'crosswikiunblock-user' => 'Wužiwarske mjeno, IP-adresa abo ID blokowanja a cilowy wiki:',
	'crosswikiunblock-reason' => 'Přičina:',
	'crosswikiunblock-submit' => 'Tutoho wužiwarja wotblokować',
	'crosswikiunblock-success' => "Wužiwar '''$1''' bu wuspěšnje wotblokowany.

Wróćo k:
* [[Special:CrosswikiBlock|Formular blokowanjow]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Njebu wužiwarske mjeno zapodate',
	'crosswikiblock-local' => 'Lokalne blokowanja so přez tutón interfejs njepodpěruja. Wužij [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Datowa banka $1 njeeksistuje',
	'crosswikiblock-noname' => '"$1" płaćiwe wužiwarske mjeno njeje.',
	'crosswikiblock-nouser' => 'Wužiwar "$3" njebu namakany.',
	'crosswikiblock-noexpiry' => 'Njepłaćiwe spadnjenje: $1.',
	'crosswikiblock-noreason' => 'Žana přičina podata.',
	'crosswikiblock-notoken' => 'Njepłaćiwy wobdźełanski token.',
	'crosswikiblock-alreadyblocked' => 'Wužiwar $3 je hižo zablokowany.',
	'crosswikiblock-noblock' => 'Tutón wužiwar njeje zablokowany.',
	'crosswikiblock-success' => "Wužiwar '''$3''' wuspěšnje zablokowany.

Wróćo k:
* [[Special:CrosswikiBlock|Blokowanski formular]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokalne blokowanja so přez tutón interfejs njepodpěruja. Wužij [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'crosswikiblock-desc' => 'Permitte blocar usatores in altere wikis con un [[Special:Crosswikiblock|pagina special]]',
	'crosswikiblock' => 'Blocar usator in altere wiki',
	'crosswikiblock-header' => 'Iste pagina permitte blocar un usator in un altere wiki.
Per favor verifica que tu ha le permission de ager in iste wiki e que tu actiones sia conforme a tote le politicas.',
	'crosswikiblock-target' => 'Adresse IP o nomine de usator e wiki de destination:',
	'crosswikiblock-expiry' => 'Expiration:',
	'crosswikiblock-reason' => 'Motivo:',
	'crosswikiblock-submit' => 'Blocar iste usator',
	'crosswikiblock-anononly' => 'Blocar solmente usatores anonyme',
	'crosswikiblock-nocreate' => 'Impedir creation de contos',
	'crosswikiblock-autoblock' => 'Blocar automaticamente le adresse IP usate le plus recentemente per iste usator, e omne IPs successive desde le quales ille/-a prova facer modificationes',
	'crosswikiblock-noemail' => 'Impedir que le usator invia e-mail',
	'crosswikiunblock' => 'Disblocar usator in altere wiki',
	'crosswikiunblock-header' => 'Iste pagina permitte disblocar un usator in un altere wiki.
Per favor verifica que tu ha le permission de ager in iste wiki e que tu actiones sia conforme a tote le politicas.',
	'crosswikiunblock-user' => 'Nomine de usator, adresse IP o ID del blocada e wiki de destination:',
	'crosswikiunblock-reason' => 'Motivo:',
	'crosswikiunblock-submit' => 'Disblocar iste usator',
	'crosswikiunblock-success' => "Le usator '''$1''' ha essite disblocate con successo.

Retornar a:
* [[Special:CrosswikiBlock|Formulario de blocada]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Nulle nomine de usator esseva indicate',
	'crosswikiblock-local' => 'Le blocadas local non es supportate via iste interfacie. Usa [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Le base de datos $1 non existe',
	'crosswikiblock-noname' => '"$1" non es un nomine de usator valide.',
	'crosswikiblock-nouser' => 'Le usator "$3" non es trovate.',
	'crosswikiblock-noexpiry' => 'Expiration invalide: $1.',
	'crosswikiblock-noreason' => 'Nulle motivo specificate.',
	'crosswikiblock-notoken' => 'Indicio de modification invalide.',
	'crosswikiblock-alreadyblocked' => 'Le usator $3 es ja blocate.',
	'crosswikiblock-noblock' => 'Iste usator non es blocate.',
	'crosswikiblock-success' => "Le usator '''$3''' ha essite blocate con successo.

Retornar a:
* [[Special:CrosswikiBlock|Formulario de blocada]]
* [[$4]]",
	'crosswikiunblock-local' => 'Le disblocadas local non es supportate via iste interfacie. Usa [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Indonesian (Bahasa Indonesia)
 * @author Rex
 */
$messages['id'] = array(
	'crosswikiblock-reason' => 'Alasan:',
	'crosswikiunblock-reason' => 'Alasan:',
	'crosswikiblock-notoken' => 'Token penyuntingan tidak sah.',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'crosswikiblock-reason' => 'Ástæða:',
	'crosswikiunblock-reason' => 'Ástæða:',
	'crosswikiblock-alreadyblocked' => 'Notandi „$3“ er nú þegar í banni.',
);

/** Italian (Italiano)
 * @author Darth Kule
 * @author Nemo bis
 * @author Pietrodn
 */
$messages['it'] = array(
	'crosswikiblock-desc' => 'Permette di bloccare utenti su altre wiki usando una [[Special:Crosswikiblock|pagina speciale]]',
	'crosswikiblock' => "Blocca utente su un'altra wiki",
	'crosswikiblock-header' => "Questa pagina permette di bloccare un utente su un'altra wiki.
Per favore, controlla che tu sia autorizzato a farlo su questa wiki e che l'azione sia conforme a tutte le policy.",
	'crosswikiblock-target' => 'Indirizzo IP o nome utente e wiki di destinazione:',
	'crosswikiblock-expiry' => 'Scadenza del blocco:',
	'crosswikiblock-reason' => 'Motivo del blocco:',
	'crosswikiblock-submit' => "Blocca l'utente",
	'crosswikiblock-anononly' => 'Blocca solo utenti anonimi (gli utenti registrati che condividono lo stesso IP non vengono bloccati)',
	'crosswikiblock-nocreate' => 'Impedisci la creazione di altri account',
	'crosswikiblock-autoblock' => "Blocca automaticamente l'ultimo indirizzo IP usato dall'utente e i successivi con cui vengono  tentate modifiche",
	'crosswikiblock-noemail' => "Impedisci all'utente l'invio di e-mail",
	'crosswikiunblock' => "Sblocca utente su un'altra wiki",
	'crosswikiunblock-header' => "Questa pagina permette di sbloccare un utente su un'altra wiki.
Per favore, controlla che tu sia autorizzato a farlo su questa wiki e che l'azione sia conforme a tutte le policy.",
	'crosswikiunblock-user' => 'Nome utente, indirizzo IP o ID di blocco e wiki di destinazione',
	'crosswikiunblock-reason' => 'Motivo dello sblocco:',
	'crosswikiunblock-submit' => "Sblocca l'utente",
	'crosswikiunblock-success' => "L'utente '''$1''' è stato sbloccato con successo.

Torna a:
* [[Special:CrosswikiBlock|Modulo di blocco]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Non è stato inserito nessun nome utente',
	'crosswikiblock-local' => 'I blocchi locali non sono supportati da questa interfaccia. Usare [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Il database $1 non esiste',
	'crosswikiblock-noname' => '"$1" non è un nome utente valido.',
	'crosswikiblock-nouser' => 'L\'utente "$3" non è stato trovato.',
	'crosswikiblock-noexpiry' => 'Scadenza del blocco errata: $1.',
	'crosswikiblock-noreason' => 'Nessun motivo specificato.',
	'crosswikiblock-notoken' => 'Edit token non valido.',
	'crosswikiblock-alreadyblocked' => 'L\'utente "$3" è stato già bloccato.',
	'crosswikiblock-noblock' => 'Questo utente non è bloccato.',
	'crosswikiblock-success' => "L'utente '''$3''' è stato sbloccato con successo.

Torna a:
* [[Special:CrosswikiBlock|Modulo di blocco]]
* [[$4]]",
	'crosswikiunblock-local' => 'Gli sblocchi locali non sono supportati da questa interfaccia. Usare [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Japanese (日本語)
 * @author Aotake
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'crosswikiblock-desc' => '他ウィキでも利用者をブロックすることができる[[Special:Crosswikiblock|{{int:specialpage}}]]。',
	'crosswikiblock' => '他ウィキの利用者をブロック',
	'crosswikiblock-header' => 'このページでは他ウィキの利用者をブロックすることができます。
あなたのその行動は、影響を与えるウィキ全ての方針で適切かどうか、注意深く考えてください。',
	'crosswikiblock-target' => 'IPアドレスか利用者名、および対象となるウィキ:',
	'crosswikiblock-expiry' => 'ブロック期限:',
	'crosswikiblock-reason' => 'ブロック理由:',
	'crosswikiblock-submit' => 'この利用者をブロック',
	'crosswikiblock-anononly' => '匿名利用者以外はブロックできません',
	'crosswikiblock-nocreate' => 'アカウント作成が拒否されています',
	'crosswikiblock-autoblock' => 'この利用者が最近編集に使用したIPアドレスは、全て自動的にブロックされているものです',
	'crosswikiblock-noemail' => '電子メールを送ることのできない利用者です',
	'crosswikiunblock' => '他ウィキの利用者をブロック解除',
	'crosswikiunblock-header' => 'このページでは他ウィキの利用者をブロック解除することができます。
あなたのその行動は、影響を与えるウィキ全ての方針で適切かどうか、注意深く考えてください。',
	'crosswikiunblock-user' => '利用者名かIPアドレスまたはブロックID、および対象となるウィキ:',
	'crosswikiunblock-reason' => 'ブロック解除理由:',
	'crosswikiunblock-submit' => 'この利用者のブロックを解除',
	'crosswikiunblock-success' => "利用者 '''$1''' のブロックを解除しました。

元のページへ戻る:
* [[Special:CrosswikiBlock|他ウィキの利用者をブロック]]
* [[$2]]",
	'crosswikiblock-nousername' => '利用者名が入力されていません',
	'crosswikiblock-local' => 'このウィキ自身における利用者ブロックを、このページでは行えません。[[Special:BlockIP|{{int:blockip}}]]を利用してください。',
	'crosswikiblock-dbnotfound' => 'データベース $1 が存在しません',
	'crosswikiblock-noname' => '"$1" は、不正な利用者名です。',
	'crosswikiblock-nouser' => '利用者 "$3" が見つかりません。',
	'crosswikiblock-noexpiry' => '不正な期限指定です: $1',
	'crosswikiblock-noreason' => '理由が記入されていません。',
	'crosswikiblock-notoken' => '編集トークンが不正です。',
	'crosswikiblock-alreadyblocked' => '利用者 $3 は、既にブロックされています。',
	'crosswikiblock-noblock' => 'この利用者は、ブロックされていません。',
	'crosswikiblock-success' => "利用者 '''$3''' をブロックしました。

戻る:
* [[Special:CrosswikiBlock|ブロックフォーム]]
* [[$4]]",
	'crosswikiunblock-local' => 'このウィキ自身における利用者ブロック解除を、このページでは行えません。[[Special:IPBlockList|{{int:ipblocklist}}]]を利用してください。',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 */
$messages['jv'] = array(
	'crosswikiblock' => 'Blokir panganggo ing wiki liya',
	'crosswikiblock-expiry' => 'Kadaluwarsa:',
	'crosswikiblock-reason' => 'Alesan:',
	'crosswikiblock-submit' => 'Blokir panganggo iki',
	'crosswikiblock-anononly' => 'Blokir para panganggo anonim waé',
	'crosswikiblock-nocreate' => 'Menggak panggawéyan rékening',
	'crosswikiblock-noemail' => 'Panganggo dipenggak ora olèh ngirim e-mail',
	'crosswikiunblock-reason' => 'Alesan:',
	'crosswikiunblock-submit' => 'Batalna blokade panganggo iki',
	'crosswikiblock-dbnotfound' => 'Basis data $1 ora ana',
	'crosswikiblock-noexpiry' => 'Kadaluwarsa ora absah: $1.',
	'crosswikiblock-noreason' => 'Ora ana alesan sing dispésifikasi.',
	'crosswikiblock-alreadyblocked' => 'Panganggo $3 wis diblokir.',
	'crosswikiblock-noblock' => 'Panganggo iki ora diblokir.',
	'crosswikiblock-success' => "Panganggo '''$3''' bisa sacara suksès diblokir.

Bali menyang:
* [[Special:CrosswikiBlock|Formulir pamblokiran]]
* [[$4]]",
);

/** Georgian (ქართული)
 * @author Malafaya
 */
$messages['ka'] = array(
	'crosswikiblock-reason' => 'მიზეზი:',
	'crosswikiunblock-reason' => 'მიზეზი:',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'crosswikiblock-desc' => 'អនុញ្ញាត​អោយរាំងខ្ទប់​អ្នកប្រើប្រាស់​លើ​​វិគីផ្សេង​ដែលប្រើប្រាស់ [[Special:Crosswikiblock|ទំព័រពិសេស]]',
	'crosswikiblock' => 'រាំងខ្ទប់​អ្នកប្រើប្រាស់​លើ​វិគីផ្សេង',
	'crosswikiblock-target' => 'អាស័យដ្ឋាន IP ឬ ឈ្មោះអ្នកប្រើប្រាស់ និង វិគីគោលដៅ ៖',
	'crosswikiblock-expiry' => 'ផុតកំណត់ ៖',
	'crosswikiblock-reason' => 'មូលហេតុ៖',
	'crosswikiblock-submit' => 'រាំងខ្ទប់​អ្នកប្រើប្រាស់​នេះ',
	'crosswikiblock-anononly' => 'រាំងខ្ទប់​តែ​អ្នកប្រើប្រាស់​អនាមិក',
	'crosswikiblock-nocreate' => 'បង្ការ​ការបង្កើត​គណនី',
	'crosswikiblock-noemail' => 'បង្ការ​អ្នកប្រើប្រាស់​ពី​ការផ្ញើ​អ៊ីមែល',
	'crosswikiunblock' => 'លែងរាំងខ្ទប់​អ្នកប្រើប្រាស់​លើ​វិគី​ផ្សេង',
	'crosswikiunblock-header' => 'ទំព័រនេះអនុញ្ញាតអោយហាមឃាត់អ្នកប្រើប្រាស់នៅលើវិគីដ៏ទៃផ្សេងទៀត។

សូមត្រួតពិនិត្យមើលថាតើអ្នកត្រូវអនុញ្ញាតអោយធ្វើសកម្មភាពនៅលើវិគីនេះ និងសកម្មភាពរបស់អ្នកគោរពតាមរាល់គោលការណ៍។',
	'crosswikiunblock-user' => 'ឈ្មោះអ្នកប្រើប្រាស់, អាស័យដ្ឋាន IP ឬ រាំងខ្ទប់ ID និង វិគី គោលដៅ ៖',
	'crosswikiunblock-reason' => 'មូលហេតុ៖',
	'crosswikiunblock-submit' => 'លែងរាំងខ្ទប់ អ្នកប្រើប្រាស់ នេះ',
	'crosswikiunblock-success' => "អ្នកប្រើប្រាស់ '''$1''' បានឈប់ហាមឃាត់ដោយជោគជ័យហើយ។


ត្រលប់ទៅកាន់:
* [[Special:CrosswikiBlock|ទំរង់បែបបទសំរាប់ការហាមឃាត់]]
* [[$2]]",
	'crosswikiblock-nousername' => 'គ្មានឈ្មោះអ្នកប្រើប្រាស់ បានត្រូវបញ្ចូល',
	'crosswikiblock-dbnotfound' => 'មូលដ្ឋានទិន្នន័យ $1 មិនមាន',
	'crosswikiblock-noname' => 'ឈ្មោះអ្នកប្រើប្រាស់ "$1" គ្មានសុពលភាព ។',
	'crosswikiblock-nouser' => 'រកមិនឃើញ អ្នកប្រើប្រាស់ "$3" ។',
	'crosswikiblock-noreason' => 'គ្មានហេតុផល ត្រូវបានសំដៅ ។',
	'crosswikiblock-alreadyblocked' => 'អ្នកប្រើប្រាស់ $3 ត្រូវបាន រាំងខ្ទប់ ហើយ ។',
	'crosswikiblock-noblock' => 'អ្នកប្រើប្រាស់នេះ មិនត្រូវបាន​ រាំងខ្ទប់ ។',
	'crosswikiblock-success' => "អ្នកប្រើប្រាស់ '''$3''' បានហាមឃាត់ដោយជោគជ័យ។


ត្រលប់ទៅកាន់:
* [[Special:CrosswikiBlock|ទំរង់បែបបទសំរាប់ការហាមឃាត់]]
* [[$4]]",
);

/** Korean (한국어)
 * @author Kwj2772
 */
$messages['ko'] = array(
	'crosswikiblock-reason' => '이유:',
	'crosswikiblock-nocreate' => '계정 생성을 막기',
	'crosswikiblock-noemail' => '이메일을 보내지 못하도록 막기',
	'crosswikiunblock-reason' => '이유:',
	'crosswikiblock-nousername' => '사용자 이름이 입력되지 않았습니다.',
	'crosswikiblock-alreadyblocked' => '사용자 $3은 이미 차단되었습니다.',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'crosswikiblock-desc' => 'Määt et müjjelesch, Metmaacher op ander Wikis ze sperre övver en  [[Special:Crosswikiblock|Söndersigg]].',
	'crosswikiblock-reason' => 'Aanlass:',
	'crosswikiunblock-reason' => 'Aanlass:',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'crosswikiblock-desc' => "Erlaabt d'Späre vu Benotzer op anere Wikien iwwer eng [[Special:Crosswikiblock|Spezialsäit]]",
	'crosswikiblock' => 'E Benotzer op enger anerer Wiki spären',
	'crosswikiblock-header' => 'Dës Spezialsäit erlaabt et e Benotzer op enger anere Wiki ze spären.

Vergewëssert iech w.e.g. ob dir déi néideg Rechter op däer anerer Wiki dofir hutt an ob är Aktioun de Regegelen vun däer wiki entsprecht.',
	'crosswikiblock-target' => 'IP-Adress oder Benotzernumm an Zil-Wiki:',
	'crosswikiblock-reason' => 'Grond:',
	'crosswikiblock-submit' => 'Dëse Benotzer spären',
	'crosswikiblock-anononly' => 'Nëmmen anonym Benotzer spären',
	'crosswikiblock-nocreate' => 'Opmaache vun engem Benotzerkont verhënneren',
	'crosswikiblock-autoblock' => 'Automatesch déi lescht IP-Adress spären déi vun dësem Benotzer benotzt gouf, an all IP-Adressen vun denen dëse Benotzer versicht Ännerunge virzehuelen',
	'crosswikiblock-noemail' => 'Verhënneren datt de Benotzer E-Maile verschéckt',
	'crosswikiunblock' => "D'Spär vum Benotzer op enger anerer Wiki ophiewen",
	'crosswikiunblock-header' => "Dës Säit erlaabt et d'spär vu Benotzer op enger anerer Wiki opzehiewen.
Kukct w.e.g. no ob Dir berechtegt sidd fir dat op där Wiki ze maachen an ob är Aktiounen mat alle Richtlinnen iwwereneestëmmen.",
	'crosswikiunblock-user' => 'Benotzernumm, IP-Adress oder Nummer vun der Spär an Zilwiki:',
	'crosswikiunblock-reason' => 'Grond:',
	'crosswikiunblock-submit' => 'Spär fir dëse Benotzer ophiewen',
	'crosswikiunblock-success' => "D'spär vum Benotzer '''$1''' gouf opgehuewen.

Zréck op:
* [[Special:CrosswikiBlock|Spärformulaire]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Dir hutt kee Benotzernumm aginn',
	'crosswikiblock-local' => 'Op dëser Säit kënne keng lokal Spären ageriicht ginn. Benotzt w.e.g. [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => "D'Datebank $1 gëtt et net.",
	'crosswikiblock-noname' => '"$1" ass kee gültege Benotzernumm.',
	'crosswikiblock-nouser' => 'De Benotzer "$3" gouf net fonnt.',
	'crosswikiblock-noreason' => 'Kee Grond uginn.',
	'crosswikiblock-alreadyblocked' => 'De Benotzer $3 ass scho gespaart.',
	'crosswikiblock-noblock' => 'Dëse Benotzer ass net gespaart.',
	'crosswikiblock-success' => "De Benotzer '''$3''' ass gespaart.

Zréck op:
* [[Special:CrosswikiBlock|Spär-Formulaire]]
* [[$4]]",
	'crosswikiunblock-local' => 'Op dëser Säit kënne lokal Spären net opgehuewe ginn. Benotzt w.e.g. [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Moksha (Мокшень)
 * @author Khazar II
 */
$messages['mdf'] = array(
	'crosswikiblock-alreadyblocked' => '"$1" сёлкфоль ни',
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$messages['ml'] = array(
	'crosswikiblock-desc' => 'ഒരു [[Special:Crosswikiblock|പ്രത്യേക താള്‍]] ഉപയോഗിച്ച് ഉപയോക്താക്കളെ മറ്റ് വിക്കികളില്‍ തടയാന്‍ സാധിക്കുന്നു.',
	'crosswikiblock' => 'ഉപയോക്താവിനെ മറ്റ് വിക്കികളില്‍ തടയുക',
	'crosswikiblock-header' => 'ഉപയോക്താക്കളെ മറ്റ് വിക്കികളില്‍ തടയാന്‍ ഈ താള്‍ സഹായിക്കുന്നു. പ്രസ്തുത വിക്കികളില്‍ പ്രവര്‍ത്തിക്കുവാന്‍ താങ്കള്‍ക്ക് അനുമതിയുണ്ടോ എന്നും താങ്കളുടെ പ്രവര്‍ത്തി അവിടുത്തെ നയങ്ങള്‍ക്കനുസരിച്ചാണെന്നും ഉറപ്പ് വരുത്തുക.',
	'crosswikiblock-target' => 'ഐപി വിലാസം അല്ലെങ്കില്‍ ഉപയോക്തൃനാമവും ലക്ഷ്യവിക്കിയും:',
	'crosswikiblock-expiry' => 'കാലാവധി:',
	'crosswikiblock-reason' => 'കാരണം:',
	'crosswikiblock-submit' => 'ഈ ഉപയോക്താവിനെ തടയുക',
	'crosswikiblock-anononly' => 'അജ്ഞാത ഉപയോക്താക്കളെ മാത്രം തടയുക',
	'crosswikiblock-nocreate' => 'അക്കൗണ്ട് സൃഷ്ടിക്കുന്നത് തടയുക',
	'crosswikiblock-autoblock' => 'ഈ ഉപയോക്താവ് അവസാനം ഉപയോഗിച്ച ഐപിയും തുടര്‍ന്ന് ഉപയോഗിക്കാന്‍ സാദ്ധ്യതയുള്ള ഐപികളും യാന്ത്രികമായി തടയുക',
	'crosswikiblock-noemail' => 'ഇമെയില്‍ അയക്കുന്നതില്‍ നിന്നു ഉപയോക്താവിനെ തടയുക',
	'crosswikiunblock' => 'ഉപയോക്താവിനെ മറ്റൊരു വിക്കിയില്‍ സ്വതന്ത്രമാക്കുക',
	'crosswikiunblock-header' => 'ഈ താള്‍ മറ്റു വിക്കികളീല്‍ ഉപയോക്താക്കളെ സ്വതന്ത്രമാക്കാന്‍ സഹായിക്കുന്നു.  പ്രസ്തുത വിക്കിയില്‍ പ്രവര്‍ത്തിക്കുവാന്‍ താങ്കള്‍ക്ക് അനുമതിയുണ്ട് എന്നും,  താങ്കളുടെ പ്രവൃത്തി വിക്കിയുടെ നയങ്ങള്‍ക്ക് അനുസരിച്ചാണെന്നും ഉറപ്പാക്കുക.',
	'crosswikiunblock-user' => 'ഉപയോക്തൃനാമം, ഐപി വിലാസം അല്ലെങ്കില്‍ തടയല്‍ ഐഡി ഇവയിലൊന്നും ലക്ഷ്യ വിക്കിയും:',
	'crosswikiunblock-reason' => 'കാരണം:',
	'crosswikiunblock-submit' => 'ഈ ഉപയോക്താവിനെ സ്വതന്ത്രമാക്കുക',
	'crosswikiunblock-success' => "'''$1''' എന്ന ഉപയോക്താവിനെ വിജയകരമായി സ്വതന്ത്രമാക്കിയിരിക്കുന്നു.

താഴെ കൊടുത്തിരിക്കുന്ന താളുകളിലൊന്നിലേക്കു തിരിച്ചു പോവുക:
* [[Special:CrosswikiBlock|തടയല്‍ ഫോം]]
* [[$2]]",
	'crosswikiblock-nousername' => 'ഉപയോക്തൃനാമം ചേര്‍ത്തില്ല',
	'crosswikiblock-local' => 'ഈ ഇന്റര്‍ഫേസ് വഴി പ്രാദേശിക തടയല്‍ സാധിക്കില്ല. [[Special:BlockIP|{{int:blockip}}]] ഉപയോഗിക്കുക.',
	'crosswikiblock-dbnotfound' => '$1 എന്ന ഡാറ്റബേസ് നിലവിലില്ല',
	'crosswikiblock-noname' => '"$1" എന്നതു സാധുവായ ഉപയോക്തൃനാമമല്ല.',
	'crosswikiblock-nouser' => '"$3" എന്ന ഉപയോക്താവിനെ കണ്ടില്ല.',
	'crosswikiblock-noexpiry' => 'അസാധുവായ കാലാവധി: $1.',
	'crosswikiblock-noreason' => 'കാരണമൊന്നും സൂചിപ്പിച്ചിട്ടില്ല.',
	'crosswikiblock-alreadyblocked' => '$3 എന്ന ഉപയോക്താവ് ഇതിനകം തന്നെ തടയപ്പെട്ടിരിക്കുന്നു.',
	'crosswikiblock-noblock' => 'ഈ ഉപയോക്താവിനെ തടഞ്ഞിട്ടില്ല.',
	'crosswikiblock-success' => "'''$3''' എന്ന ഉപയോക്താവിനെ വിജയകരമായി തടഞ്ഞിരിക്കുന്നു

താഴെ കൊടുത്തിരിക്കുന്ന താളുകളിലൊന്നിലേക്കു തിരിച്ചു പോവുക:
* [[Special:CrosswikiBlock|തടയല്‍ ഫോം]]
* [[$4]]",
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$messages['mr'] = array(
	'crosswikiblock-desc' => 'इतर विकिंवर [[Special:Crosswikiblock|विशेष पान]] वापरून सदस्य ब्लॉक करायची परवानगी देते',
	'crosswikiblock' => 'इतर विकिवर सदस्याला ब्लॉक करा',
	'crosswikiblock-header' => 'हे पान इतर विकिवर सदस्याला ब्लॉक करायची परवानगी देते.
कृपया ही क्रिया करण्याची तुम्हाला परवानगी आहे तसेच तुम्ही करीत असलेली क्रिया नीतीला धरुन आहे याची खात्री करा.',
	'crosswikiblock-target' => 'आयपी अंकपत्ता किंवा सदस्यनाव तसेच कुठल्या विकिवर करायचे तो विकि:',
	'crosswikiblock-expiry' => 'रद्दीकरण:',
	'crosswikiblock-reason' => 'कारण:',
	'crosswikiblock-submit' => 'या सदस्याला ब्लॉक करा',
	'crosswikiblock-anononly' => 'फक्त अनामिक सदस्यांना ब्लॉक करा',
	'crosswikiblock-nocreate' => 'खाते उघडणी बंद करा',
	'crosswikiblock-autoblock' => 'या सदस्याचा आपोआप शेवटचा आयपी अंकपत्ता ब्लॉक करा, तसेच यानंतरच्या कुठल्याही आयपी वरुन संपादने करण्याचा प्रयत्न केल्यास ते अंकपत्ते सुद्धा ब्लॉक करा',
	'crosswikiblock-noemail' => 'सदस्याला इमेल पाठविण्यापासून रोखा',
	'crosswikiunblock' => 'इतर विकिंवर सदस्याचा ब्लॉक काढा',
	'crosswikiunblock-header' => 'हे पान इतर विकिंवर सदस्याचा ब्लॉक काढण्यासाठी वापरण्यात येते.
कृपया या विकिवर ही क्रिया करण्याचे अधिकार तुम्हाला आहेत तसेच तुम्ही करीत असलेली क्रिया नीतीला धरुन आहे याची खात्री करा.',
	'crosswikiunblock-user' => 'सदस्यनाव, आयपी अंकपत्ता किंवा ब्लॉक क्रमांक तसेच कुठल्या विकिवर ब्लॉक काढायचा आहे:',
	'crosswikiunblock-reason' => 'कारण:',
	'crosswikiunblock-submit' => 'या सदस्याचा ब्लॉक काढा',
	'crosswikiunblock-success' => "'''$1''' चा ब्लॉक यशस्वीरित्या काढलेला आहे.

परत जा:
* [[Special:CrosswikiBlock|ब्लॉक अर्ज]]
* [[$2]]",
	'crosswikiblock-nousername' => 'सदस्यनाव दिलेले नाही',
	'crosswikiblock-local' => 'स्थानिक ब्लॉक या ठिकाणी बदलता येत नाहीत. [[Special:BlockIP|{{int:blockip}}]] चा वापर करा',
	'crosswikiblock-dbnotfound' => 'डाटाबेस $1 उपलब्ध नाही',
	'crosswikiblock-noname' => '"$1" हे योग्य सदस्यनाव नाही.',
	'crosswikiblock-nouser' => '"$3" नावाचा सदस्य सापडला नाही.',
	'crosswikiblock-noexpiry' => 'चुकीचे रद्दीकरण: $1.',
	'crosswikiblock-noreason' => 'कारण दिलेले नाही',
	'crosswikiblock-notoken' => 'चुकीची संपादन चावी.',
	'crosswikiblock-alreadyblocked' => 'सदस्य $3 ला अगोदरच ब्लॉक केलेले आहे.',
	'crosswikiblock-noblock' => 'ह्या सदस्याला ब्लॉक केलेले नाही.',
	'crosswikiblock-success' => "सदस्य '''$3''' ला ब्लॉक केलेले आहे.

परत जा:
* [[Special:CrosswikiBlock|ब्लॉक अर्ज]]
* [[$4]]",
	'crosswikiunblock-local' => 'स्थानिक अनब्लॉक इथे बदलता येत नाहीत. [[Special:IPBlockList|{{int:ipblocklist}}]] चा उपयोग करा',
);

/** Maltese (Malti)
 * @author Roderick Mallia
 */
$messages['mt'] = array(
	'crosswikiblock-anononly' => 'Ibblokkja l-utenti anonimi biss',
	'crosswikiblock-alreadyblocked' => 'L-utent "$3" diġà bblokkjat',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'crosswikiblock-reason' => 'Тувталось:',
	'crosswikiunblock-reason' => 'Тувталось:',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$messages['nah'] = array(
	'crosswikiblock-reason' => 'Īxtlamatiliztli:',
	'crosswikiunblock-reason' => 'Īxtlamatiliztli:',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$messages['nds'] = array(
	'crosswikiblock-reason' => 'Grund:',
	'crosswikiblock-submit' => 'Dissen Bruker sperren',
	'crosswikiunblock-reason' => 'Grund:',
	'crosswikiblock-nousername' => 'Is keen Brukernaam ingeven worrn',
	'crosswikiblock-dbnotfound' => 'Datenbank $1 gifft dat nich',
	'crosswikiblock-nouser' => 'Bruker „$3“ nich funnen.',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'crosswikiblock-desc' => 'Laat toe om gebruikers op andere wikis te blokkeren via een [[Special:Crosswikiblock|speciale pagina]]',
	'crosswikiblock' => 'Gebruiker blokkeren op een andere wiki',
	'crosswikiblock-header' => 'Deze pagina laat toe om gebruikers te blokkeren op een andere wiki.
Gelieve te controleren of u de juiste rechten hebt op deze wiki en of uw acties het beleid volgt.',
	'crosswikiblock-target' => 'IP-adres of gebruikersnaam en bestemmingswiki:',
	'crosswikiblock-expiry' => 'Duur:',
	'crosswikiblock-reason' => 'Reden:',
	'crosswikiblock-submit' => 'Deze gebruiker blokkeren',
	'crosswikiblock-anononly' => 'Alleen anonieme gebruikers blokkeren',
	'crosswikiblock-nocreate' => 'Gebruiker aanmaken voorkomen',
	'crosswikiblock-autoblock' => "Automatisch het laatste IP-adres gebruikt door deze gebruiker blokkeren, en elke volgende IP's waarmee ze proberen te bewerken",
	'crosswikiblock-noemail' => 'Het verzenden van e-mails door deze gebruiker voorkomen',
	'crosswikiunblock' => 'Gebruiker op een andere wiki deblokkeren',
	'crosswikiunblock-header' => 'Via deze pagina kunt u een gebruiker op een andere wiki deblokkeren.
Controleer of u dit op die wiki mag doen en of u in overeenstemming met het beleid handelt.',
	'crosswikiunblock-user' => 'Gebruiker, IP-adres of blokkadenummer en bestemmingswiki:',
	'crosswikiunblock-reason' => 'Reden:',
	'crosswikiunblock-submit' => 'Gebruiker deblokkeren',
	'crosswikiunblock-success' => "Gebruiker '''$1''' is gedeblokkeerd.

Ga terug naar:
* [[Special:CrosswikiBlock|Blokkadeformulier]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Er werd geen gebruikersnaam opgegeven',
	'crosswikiblock-local' => 'Plaatselijke blokkades worden niet ondersteund door dit formulier. Gebruik daarvoor [[Special:BlockIP|{{int:blockip}}]].',
	'crosswikiblock-dbnotfound' => 'Database $1 bestaat niet',
	'crosswikiblock-noname' => '"$1" is geen geldige gebruikersnaam.',
	'crosswikiblock-nouser' => 'Gebruiker "$3" is niet gevonden.',
	'crosswikiblock-noexpiry' => 'Ongeldige duur: $1.',
	'crosswikiblock-noreason' => 'Geen reden opgegeven.',
	'crosswikiblock-notoken' => 'Onjuist bewerkingstoken.',
	'crosswikiblock-alreadyblocked' => 'Gebruiker $3 is al geblokkeerd.',
	'crosswikiblock-noblock' => 'Deze gebruiker is niet geblokkeerd',
	'crosswikiblock-success' => "Gebruiker '''$3''' succesvol geblokkeerd.

Teruggaan naar:
* [[Special:CrosswikiBlock|Blokkeerformulier]]
* [[$4]]",
	'crosswikiunblock-local' => 'Plaatselijke deblokkades worden niet ondersteund door dit formulier. Gebruik daarvoor [[Special:IPBlockList|{{int:ipblocklist}}]].',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Jon Harald Søby
 */
$messages['nn'] = array(
	'crosswikiblock-reason' => 'Årsak:',
	'crosswikiblock-submit' => 'Blokker denne brukaren',
	'crosswikiblock-nocreate' => 'Hindre kontooppretting',
	'crosswikiblock-autoblock' => 'Blokker den førre IP-adressa som vart brukt av denne brukaren automatisk, og alle andre IP-adresser brukaren prøvar å endre sider med i framtida',
	'crosswikiblock-noemail' => 'Hindre sending av e-post til andre brukarar',
	'crosswikiunblock-reason' => 'Årsak:',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'crosswikiblock-desc' => 'Gjør det mulig å blokkere brukere på andre wikier ved hjelp av en [[Special:Crosswikiblock|spesialside]]',
	'crosswikiblock' => 'Blokker brukere på andre wikier',
	'crosswikiblock-header' => 'Denne siden gjør at man kan blokkere brukere på andre wikier. Sjekk om du har tillatelse til å gjøre det på denne wikien, og at du følger alle retningslinjene.',
	'crosswikiblock-target' => 'IP-adresse eller brukernavn og målwiki:',
	'crosswikiblock-expiry' => 'Varighet:',
	'crosswikiblock-reason' => 'Årsak:',
	'crosswikiblock-submit' => 'Blokker denne brukeren',
	'crosswikiblock-anononly' => 'Blokker kun anonyme brukere',
	'crosswikiblock-nocreate' => 'Hindre kontoopprettelse',
	'crosswikiblock-autoblock' => 'Blokker forrige IP-adresse brukt av denne brukeren automatisk, samt alle IP-adresser brukeren forsøker å redigere med i framtiden',
	'crosswikiblock-noemail' => 'Forhindre brukeren fra å sende e-post',
	'crosswikiunblock' => 'Avblokker brukeren på andre wikier',
	'crosswikiunblock-header' => 'Denne siden lar deg avblokkere brukere på andre wikier. Sjekk om du har lov til å gjøre dette på den lokale wikien i henhold til deres retningslinjer.',
	'crosswikiunblock-user' => 'Brukernavn, IP-adresse eller blokkerings-ID og målwiki:',
	'crosswikiunblock-reason' => 'Årsak:',
	'crosswikiunblock-submit' => 'Avblokker brukeren',
	'crosswikiunblock-success' => "Brukeren '''$1''' ble avblokkert.

Tilbake til:
* [[Special:CrosswikiBlock|Blokkeringsskjema]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Ingen brukernavn ble skrevet inn',
	'crosswikiblock-local' => 'Lokale blokkeringer støttes ikke av dette grensesnittet. Bruk [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Databasen $1 finnes ikke',
	'crosswikiblock-noname' => '«$1» er ikke et gyldig brukernavn.',
	'crosswikiblock-nouser' => 'Brukeren «$3» ble ikke funnet.',
	'crosswikiblock-noexpiry' => 'Ugyldig utløpstid: $1.',
	'crosswikiblock-noreason' => 'Ingen begrunnelse gitt.',
	'crosswikiblock-notoken' => 'Ugyldig redigeringstegn.',
	'crosswikiblock-alreadyblocked' => '«$3» er allerede blokkert.',
	'crosswikiblock-noblock' => 'Denne brukeren er ikke blokkert.',
	'crosswikiblock-success' => "'''$3''' er blokkert.

Tilbake til:
* [[Special:CrosswikiBlock|Blokkeringsskjemaet]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokale blokkeringer støttes ikke via dette grensesnittet. Bruk [[Special:IPBlockList|{{int:ipblocklist}}]].',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'crosswikiblock-desc' => "Permet de blocar d'utilizaires sus d'autres wikis en utilizant [[Special:Crosswikiblock|una pagina especiala]]",
	'crosswikiblock' => 'Blocar un utilizaire sus un autre wiki',
	'crosswikiblock-header' => 'Aquesta pagina permet de blocar un utilizaire sus un autre wiki.

Verificatz se sètz abilitat per agir sus aqueste wiki e que vòstras accions respèctan totas las règlas.',
	'crosswikiblock-target' => "Adreça IP o nom d'utilizaire e wiki de destinacion :",
	'crosswikiblock-expiry' => 'Expiracion :',
	'crosswikiblock-reason' => 'Motiu :',
	'crosswikiblock-submit' => 'Blocar aqueste utilizaire',
	'crosswikiblock-anononly' => 'Blocar unicament los utilizaires anonims',
	'crosswikiblock-nocreate' => 'Interdire la creacion de compte',
	'crosswikiblock-autoblock' => "Bloca automaticament la darrièra adreça IP utilizada per aqueste utilizaire, e totas las IP subsequentas que ensajan d'editar",
	'crosswikiblock-noemail' => "Interdire a l'utilizaire de mandar un corrièr electronic",
	'crosswikiunblock' => "Deblocar en escritura un utilizaire d'un autre wiki",
	'crosswikiunblock-header' => "Aquesta pagina permet de deblocar en escritura un utilizaire d'un autre wiki.
Asseguratz-vos qu'avètz los dreches e respectatz las règlas en vigor sus aqueste wiki.",
	'crosswikiunblock-user' => "Nom d'utilizaire, adreça IP o l'id de blocatge e lo wiki ciblat :",
	'crosswikiunblock-reason' => 'Motiu :',
	'crosswikiunblock-submit' => 'Deblocar en escritura aqueste utilizaire',
	'crosswikiunblock-success' => "L'utilizaire '''$1''' es estat desblocat en escritura amb succès.

Tornar a :
* [[Special:CrosswikiBlock|Formulari de blocatge]]
* [[$2]]",
	'crosswikiblock-nousername' => "Cap de nom d'utilizaire es pas estat indicat",
	'crosswikiblock-local' => 'Los blocatges locals son pas suportats a travèrs aquesta interfàcia. Utilizatz [[Special:BlockIP|{{int:blockip}}]].',
	'crosswikiblock-dbnotfound' => 'La banca de donadas « $1 » existís pas',
	'crosswikiblock-noname' => '« $1 » es pas un nom d’utilizaire valid.',
	'crosswikiblock-nouser' => 'L’utilizaire « $3 » es introbable.',
	'crosswikiblock-noexpiry' => 'Data o durada d’expiracion incorrècta : $1.',
	'crosswikiblock-noreason' => 'Cap de motiu indicat.',
	'crosswikiblock-notoken' => 'Geton d’edicion invalida.',
	'crosswikiblock-alreadyblocked' => 'L’utilizaire « $3 » ja es blocat.',
	'crosswikiblock-noblock' => 'Aqueste utilizaire es pas blocat en escritura.',
	'crosswikiblock-success' => "L’utilizaire '''$3''' es estat blocat amb succès.

Tornar cap a :
* [[Special:CrosswikiBlock|Lo formulari de blocatge]] ;
* [[$4]].",
	'crosswikiunblock-local' => 'Los blocatges en escritura locals son pas suportats via aquesta interfàcia. Utilizatz [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Polish (Polski)
 * @author Derbeth
 * @author Equadus
 * @author Masti
 * @author McMonster
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'crosswikiblock-desc' => 'Umożliwia blokowanie użytkowników na innych wiki za pomocą [[Special:Crosswikiblock|strony specjalnej]]',
	'crosswikiblock' => 'Zablokuj użytkownika na innych wiki',
	'crosswikiblock-header' => 'Ta strona pozawala zablokować użytkownika na innych wiki.
Upewnij się czy masz prawo to zrobić i czy to co robisz jest w zgodzie z zasadami.',
	'crosswikiblock-target' => 'Adres IP lub nazwa użytkownika i docelowa wiki:',
	'crosswikiblock-expiry' => 'Czas blokady:',
	'crosswikiblock-reason' => 'Powód:',
	'crosswikiblock-submit' => 'Zablokuj użytkownika',
	'crosswikiblock-anononly' => 'Zablokuj tylko anonimowych użytkowników',
	'crosswikiblock-nocreate' => 'Zablokuj tworzenie konta',
	'crosswikiblock-autoblock' => 'Zablokuj ostatni adres IP tego użytkownika i automatycznie wszystkie kolejne, z których będzie próbował edytować',
	'crosswikiblock-noemail' => 'Zablokuj możliwość wysyłania e-maili',
	'crosswikiunblock' => 'Odblokuj użytkownika na innych wiki',
	'crosswikiunblock-header' => 'Ta strona pozwala na odblokowanie użytkownika na innych wiki.
Upewnij się czy masz prawo to zrobić i czy to co robisz jest w zgodzie z zasadami.',
	'crosswikiunblock-user' => 'Nazwa użytkownika, adres IP lub ID blokady i docelowa wiki:',
	'crosswikiunblock-reason' => 'Powód:',
	'crosswikiunblock-submit' => 'Odblokuj użytkownika',
	'crosswikiunblock-success' => "Użytkownik '''$1''' został odblokowany.

Wróć do:
* [[Special:CrosswikiBlock|Strona blokowania]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Nie wprowadzono nazwy użytkownika',
	'crosswikiblock-local' => 'Lokalne blokowanie nie jest możliwe przy pomocy tego interfejsu. Użyj strony [[Special:BlockIP|blokowania adresów IP]].',
	'crosswikiblock-dbnotfound' => 'Baza $1 nie istnieje',
	'crosswikiblock-noname' => '"$1" nie jest poprawną nazwą użytkownika.',
	'crosswikiblock-nouser' => 'Nie znaleziono użytkownika "$3".',
	'crosswikiblock-noexpiry' => 'Nieprawidłowy czas blokady: $1.',
	'crosswikiblock-noreason' => 'Nie podano powodu.',
	'crosswikiblock-notoken' => 'Nieprawidłowy żeton edycji.',
	'crosswikiblock-alreadyblocked' => 'Użytkownik $3 jest już zablokowany.',
	'crosswikiblock-noblock' => 'Ten użytkownik nie jest zablokowany.',
	'crosswikiblock-success' => "Pomyślnie zablokowano użytkownika '''$3'''.

Powrót do:
* [[Special:CrosswikiBlock|Formularz blokowania]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokalne odblokowywanie nie jest obsługiwane w tym interfejsie. Użyj [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'crosswikiblock-reason' => 'سبب:',
	'crosswikiunblock-reason' => 'سبب:',
);

/** Portuguese (Português)
 * @author Lijealso
 * @author Malafaya
 */
$messages['pt'] = array(
	'crosswikiblock-desc' => 'Permite bloquear utilizadores noutros wikis usando uma [[Special:Crosswikiblock|página especial]]',
	'crosswikiblock' => 'Bloquear utilizador noutro wiki',
	'crosswikiblock-header' => 'Esta página permite bloquear um utilizador noutro wiki.
Por favor, verifique se tem permissão para agir neste wiki e as suas acções respeitam todas as políticas.',
	'crosswikiblock-target' => 'Endereço IP ou nome de utilizador e wiki destino:',
	'crosswikiblock-expiry' => 'Expiração:',
	'crosswikiblock-reason' => 'Motivo:',
	'crosswikiblock-submit' => 'Bloquear este utilizador',
	'crosswikiblock-anononly' => 'Bloquear apenas utilizadores anónimos',
	'crosswikiblock-nocreate' => 'Impedir criação de conta',
	'crosswikiblock-autoblock' => 'Bloquear automaticamente o último endereço IP usado por este utilizador, e qualquer endereço IP subsequente a partir do qual ele tente editar',
	'crosswikiblock-noemail' => 'Impedir utilizador de enviar email',
	'crosswikiunblock' => 'Desbloquear utilizador noutro wiki',
	'crosswikiunblock-header' => 'Esta página permite desbloquear um utilizador noutro wiki.
Por favor, verifique se tem permissão para agir neste wiki e as suas acções respeitam todas as políticas.',
	'crosswikiunblock-user' => 'Nome de utilizador, endereço IP ou ID de bloqueio e wiki destino:',
	'crosswikiunblock-reason' => 'Motivo:',
	'crosswikiunblock-submit' => 'Desbloquear este utilizador',
	'crosswikiunblock-success' => "Usuário '''$1''' desbloqueado com sucesso.

Regressar a:
* [[Special:CrosswikiBlock|Formulário de bloqueio]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Nenhum nome de utilizador foi introduzido',
	'crosswikiblock-local' => 'Bloqueios locais não podem ser efectuados a partir deste interface. Use [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'A base de dados $1 não existe',
	'crosswikiblock-noname' => '"$1" não é um nome de utilizador válido.',
	'crosswikiblock-nouser' => 'O utilizador "$3" não foi encontrado.',
	'crosswikiblock-noexpiry' => 'Expiração inválida: $1.',
	'crosswikiblock-noreason' => 'Nenhum motivo especificado.',
	'crosswikiblock-alreadyblocked' => 'O utilizador $3 já está bloqueado.',
	'crosswikiblock-noblock' => 'Este utilizador não está bloqueado.',
	'crosswikiblock-success' => "Utilizador '''$3''' bloqueado com sucesso.

Voltar para:
* [[Special:CrosswikiBlock|Formulário de bloqueio]]
* [[$4]]",
	'crosswikiunblock-local' => 'Desbloqueios locais são podem ser efectuados a partir deste interface. Use [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'crosswikiblock' => 'Blochează utilizator pe alt wiki',
	'crosswikiblock-reason' => 'Motiv:',
	'crosswikiblock-submit' => 'Blochează acest utilizator',
	'crosswikiblock-anononly' => 'Blochează doar utilizatorii anonimi',
	'crosswikiblock-nocreate' => 'Nu permite crearea de conturi',
	'crosswikiblock-noemail' => 'Nu permite utilizatorului să trimită e-mail',
	'crosswikiunblock' => 'Deblochează utilizator pe alt wiki',
	'crosswikiunblock-header' => 'Această pagină permite deblocarea utilizatorilor de pe alte wiki.
Vă rugăm să verificaţi dacă vi se permite să acţionaţi pe acest wiki şi că respectaţi toate politicile.',
	'crosswikiunblock-reason' => 'Motiv:',
	'crosswikiunblock-submit' => 'Deblochează acest utilizator',
	'crosswikiblock-nousername' => 'Nu a fost introdus nici un nume de utilizator',
	'crosswikiblock-dbnotfound' => 'Baza de date $1 nu există',
	'crosswikiblock-noname' => '"$1" nu este un nume de utilizator valid.',
	'crosswikiblock-nouser' => 'Utilizatorul "$3" nu este găsit.',
	'crosswikiblock-noreason' => 'Nici un motiv specificat.',
	'crosswikiblock-alreadyblocked' => 'Utilizatorul $3 este deja blocat.',
	'crosswikiblock-noblock' => 'Acest utilizator nu este blocat.',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'crosswikiblock-desc' => 'Позволяет блокировать участников на других вики с помощью [[Special:Crosswikiblock|служебной страницы]]',
	'crosswikiblock' => 'Блокировка участников на других вики',
	'crosswikiblock-header' => 'Эта страница позволяет блокировать участников на других вики.
Пожалуйста, убедитесь, что вам разрешено производить подобные действия на этой вики и что вы следуете всем правилам.',
	'crosswikiblock-target' => 'IP-адрес или имя участника и целевая вики:',
	'crosswikiblock-expiry' => 'Истекает:',
	'crosswikiblock-reason' => 'Причина:',
	'crosswikiblock-submit' => 'Заблокировать этого участника',
	'crosswikiblock-anononly' => 'Заблокировать только анонимных участников',
	'crosswikiblock-nocreate' => 'Запретить создание учётных записей',
	'crosswikiblock-autoblock' => 'Автоматически заблокировать последний использованный этим участником IP-адрес и любые последующие IP-адреса с которых производятся попытки редактирования',
	'crosswikiblock-noemail' => 'Запретить участнику отправку электронной почты',
	'crosswikiunblock' => 'Разблокировать участника в этой вики',
	'crosswikiunblock-header' => 'Эта страница позволяет разблокировать участников в других вики.
Пожалуйста, убедитесь что вам разрешены подобные действия и что что они соответствуют всем правилам.',
	'crosswikiunblock-user' => 'Имя участника, IP-адрес или идентификатор блокировки на целевой вики:',
	'crosswikiunblock-reason' => 'Причина:',
	'crosswikiunblock-submit' => 'Разблокировать участника',
	'crosswikiunblock-success' => "Участник '''$1''' успешно разблокирован.

Вернуться к:
* [[Special:CrosswikiBlock|Форма блокировки]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Не введено имя участника',
	'crosswikiblock-local' => 'Локальные блокировки не поддерживаются через этот интерфейс. Используйте [[Special:BlockIP|{{int:blockip}}]].',
	'crosswikiblock-dbnotfound' => 'База данных $1 не существует',
	'crosswikiblock-noname' => '«$1» не является допустимым именем участника.',
	'crosswikiblock-nouser' => 'Участник «$3» не найден.',
	'crosswikiblock-noexpiry' => 'Ошибочный срок окончания: $1.',
	'crosswikiblock-noreason' => 'Не указана причина.',
	'crosswikiblock-notoken' => 'Ошибочный маркер правки.',
	'crosswikiblock-alreadyblocked' => 'Участник $3 уже заблокирован.',
	'crosswikiblock-noblock' => 'Этот участник не был заблокирован.',
	'crosswikiblock-success' => "Участник '''$3''' успешно заблокирован.

Вернуться к:
* [[Special:CrosswikiBlock|форма блокировки]]
* [[$4]]",
	'crosswikiunblock-local' => 'Локальные блокировки не поддерживаются с помощью этого интерфейса. Используйте [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Sassaresu (Sassaresu)
 * @author Felis
 */
$messages['sdc'] = array(
	'crosswikiblock-alreadyblocked' => "L'utenti $3 è già broccaddu.",
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'crosswikiblock-desc' => 'Umožňuje blokovanie používateľov na iných wiki pomocou [[Special:Crosswikiblock|špeciálnej stránky]]',
	'crosswikiblock' => 'Zablokovať používateľa na inej wiki',
	'crosswikiblock-header' => 'Táto stránka umožňuje zablokovať používateľa na inej wiki.
Prosím, overte si, či máte povolené na danej wiki konať a vaše konanie je v súlade so všetkými pravidlami.',
	'crosswikiblock-target' => 'IP adresa alebo používateľské meno a cieľová wiki:',
	'crosswikiblock-expiry' => 'Expirácia:',
	'crosswikiblock-reason' => 'Dôvod:',
	'crosswikiblock-submit' => 'Zablokovať tohto používateľa',
	'crosswikiblock-anononly' => 'Zablokovať iba anonymných používateľov',
	'crosswikiblock-nocreate' => 'Zabrániť tvorbe účtov',
	'crosswikiblock-autoblock' => 'Automaticky blokovať poslednú IP adresu, ktorú tento používateľ použil a akékoľvek ďalšie adresy, z ktorých sa pokúsia upravovať.',
	'crosswikiblock-noemail' => 'Zabrániť používateľovi odosielať email',
	'crosswikiunblock' => 'Odblokovať používateľa na inej wiki',
	'crosswikiunblock-header' => 'Táto stránka umožňuje odblokovanie používateľa na inej wiki.
Prosím, uistite sa, že máte povolenie konať na tejto wiki a vaše konanie je v súlade so všetkými pravidlami.',
	'crosswikiunblock-user' => 'Používateľské meno, IP adresa alebo ID blokovania a cieľová wiki:',
	'crosswikiunblock-reason' => 'Dôvod:',
	'crosswikiunblock-submit' => 'Odblokovať tohto používateľa',
	'crosswikiunblock-success' => "Používateľ '''$1''' bol úspešne odblokovaný.

Vrátiť sa na:
* [[Special:CrosswikiBlock|Formulár blokovania]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Nebolo zadané používateľské meno',
	'crosswikiblock-local' => 'Toto rozhranie nepodporuje lokálne blokovanie. Použite [[Special:BlockIP|{{int:blockip}}]].',
	'crosswikiblock-dbnotfound' => 'Databáza $1 neexistuje',
	'crosswikiblock-noname' => '„$1“ nie je platné používateľské meno.',
	'crosswikiblock-nouser' => 'Používateľ „$3“ nebol nájdený.',
	'crosswikiblock-noexpiry' => 'Neplatná expirácia: $1.',
	'crosswikiblock-noreason' => 'Nebol uvedený dôvod.',
	'crosswikiblock-notoken' => 'Neplatný upravovací token.',
	'crosswikiblock-alreadyblocked' => 'Používateľ $3 je už zablokovaný.',
	'crosswikiblock-noblock' => 'Tento používateľ nie je zablokovaný.',
	'crosswikiblock-success' => "Používateľ '''$3''' bol úspešne zablokovaný.

Vrátiť sa na:
* [[Special:CrosswikiBlock|Blokovací formulár]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokálne blokovania nie sú týmto rozhraním podporované. Použite [[Special:IPBlockList|{{int:ipblocklist}}]].',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'crosswikiblock-reason' => 'Разлог:',
	'crosswikiunblock-reason' => 'Разлог:',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'crosswikiblock-desc' => "Ferlööwet ju Speere fon Benutsere in uur Wiki's uur ne [[Special:Crosswikiblock|Spezioalsiede]]",
	'crosswikiblock' => 'Speer Benutser in n uur Wiki',
	'crosswikiblock-header' => 'Disse Spezioalsiede ferlööwet ju Speere fon n Benutser in n uur Wiki.
Wröich, of du ju Beföichnis hääst, in dissen uur Wiki tou speeren un of dien Aktion do Gjuchtlienjen fon do äntspräkt.',
	'crosswikiblock-target' => 'IP-Adresse of Benutsernoome un Sielwiki:',
	'crosswikiblock-expiry' => 'Speerduur:',
	'crosswikiblock-reason' => 'Begruundenge:',
	'crosswikiblock-submit' => 'IP-Adresse/Benutser speere',
	'crosswikiblock-anononly' => 'Speer bloot anonyme Benutsere (anmäldede Benutsere mäd disse IP-Adresse wäide nit speerd). In fuul Falle is dät beeter.',
	'crosswikiblock-nocreate' => 'Dät Moakjen fon Benutserkonten ferhinnerje',
	'crosswikiblock-autoblock' => 'Speer ju aktuell fon dissen Benutser bruukte IP-Adresse as uk automatisk aal do foulgjende, fon do uut hie Beoarbaidengen of dät Anlääsen fon Benutserkonten fersäkt.',
	'crosswikiblock-noemail' => 'E-Mail-Ferseenden speere',
	'crosswikiunblock' => 'Äntspeer Benutser in n uur Wiki',
	'crosswikiunblock-header' => 'Disse Spezioalsiede ferlööwet ju Aphieuwenge fon ne Benutserspeere in n uur Wiki.
Wröich, of du ju Beföichnis hääst, in dissen uur Wiki tou speeren un of dien Aktion hiere Gjuchlienjen äntspräkt.',
	'crosswikiunblock-user' => 'IP-Adresse of Benutsernoome un Sielwiki:',
	'crosswikiunblock-reason' => 'Begruundenge:',
	'crosswikiunblock-submit' => 'Speere foar IP-Adresse/Benutser aphieuwje',
	'crosswikiunblock-success' => "Benutser '''„$1“''' mäd Ärfoulch äntspeerd.

Tourääch tou:
* [[Special:CrosswikiBlock|Speerformular]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Der wuude naan Benutsernoome ienroat',
	'crosswikiblock-local' => 'Lokoale Speeren wäide truch disse Interface nit unnerstutsed. Benutsje [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Doatenboank $1 is nit deer',
	'crosswikiblock-noname' => '„$1“ is naan gultigen Benutsernoome.',
	'crosswikiblock-nouser' => 'Benutser "$3" nit fuunen.',
	'crosswikiblock-noexpiry' => 'Uungultige Speerduur: $1.',
	'crosswikiblock-noreason' => 'Begruundenge failt.',
	'crosswikiblock-notoken' => 'Uungultich Beoarbaidengs-Token.',
	'crosswikiblock-alreadyblocked' => 'Benutser "$3" is al speerd.',
	'crosswikiblock-noblock' => 'Dissen Benutser is nit speerd.',
	'crosswikiblock-success' => "Benutser '''„$3“''' mäd Ärfoulch speerd.

Tourääch tou:
* [[Special:CrosswikiBlock|Speerformular]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokoale Speeren wäide uur dit Interface nit unnerstutsed. Benutsje [[{{#special:Ipblocklist}}]].',
);

/** Sundanese (Basa Sunda)
 * @author Irwangatot
 */
$messages['su'] = array(
	'crosswikiblock-reason' => 'Alesan:',
	'crosswikiblock-noemail' => 'Henteu kaci pamaké ngirimkeun surélék',
	'crosswikiunblock-reason' => 'Alesan:',
	'crosswikiblock-alreadyblocked' => 'Pamaké $3 geus dipeungpeuk.',
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author M.M.S.
 */
$messages['sv'] = array(
	'crosswikiblock-desc' => 'Gör det möjligt att blockera användare på andra wikier med hjälp av en [[Special:Crosswikiblock|specialsida]]',
	'crosswikiblock' => 'Blockera användare på en annan wiki',
	'crosswikiblock-header' => 'Den här sidan används för att blockera användare på andra wikier.
Kontrollera att du har tillåtelse att utföra åtgärder på den andra wikin, och att du följer alla policyer.',
	'crosswikiblock-target' => 'IP-adress eller användarnamn och målwiki:',
	'crosswikiblock-expiry' => 'Varaktighet:',
	'crosswikiblock-reason' => 'Anledning:',
	'crosswikiblock-submit' => 'Blockera användaren',
	'crosswikiblock-anononly' => 'Blockera bara oinloggade användare',
	'crosswikiblock-nocreate' => 'Förhindra registrering av användarkonton',
	'crosswikiblock-autoblock' => 'Blockera automatiskt den IP-adress som användaren använde senast, samt alla adresser som användaren försöker redigera ifrån',
	'crosswikiblock-noemail' => 'Hindra användaren från att skicka e-post',
	'crosswikiunblock' => 'Ta bort blockering av användare på en annan wiki',
	'crosswikiunblock-header' => 'Den här sidan används för att ta bort blockeringar av användare på andra wikier.
Kontrollera att du har tillåtelse att utföra åtgärder på den andra wikin, och att du följer alla policyer.',
	'crosswikiunblock-user' => 'Användarnamn, IP-adress eller blockerings-ID och målwiki:',
	'crosswikiunblock-reason' => 'Anledning:',
	'crosswikiunblock-submit' => 'Ta bort blockeringen',
	'crosswikiunblock-success' => "Blockeringen av '''$1''' har tagits bort.

Gå tillbaka till:
* [[Special:CrosswikiBlock|Blockeringsformuläret]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Inget användarnamn angavs',
	'crosswikiblock-local' => 'Lokala blockeringar kan inte göras från den här sidan. Använd [[Special:BlockIP|{{int:blockip}}]] istället.',
	'crosswikiblock-dbnotfound' => 'Databasen "$1" existerar inte',
	'crosswikiblock-noname' => '"$1" är inte ett giltigt användarnamn.',
	'crosswikiblock-nouser' => 'Användaren "$3" hittades inte.',
	'crosswikiblock-noexpiry' => 'Ogiltig varaktighet: $1.',
	'crosswikiblock-noreason' => 'Ingen anledning angavs.',
	'crosswikiblock-notoken' => 'Ogiltigt redigerings-token.',
	'crosswikiblock-alreadyblocked' => 'Användaren $3 är redan blockerad.',
	'crosswikiblock-noblock' => 'Användaren är inte blockerad.',
	'crosswikiblock-success' => "Blockeringen av användaren '''$3''' lyckades.

Gå tillbaka till:
* [[Special:CrosswikiBlock|Blockeringsformuläret]]
* [[$4]]",
	'crosswikiunblock-local' => 'Lokala blockeringar kan inte tas bort via det här formuläret. Använd [[Special:IPBlockList|{{int:ipblocklist}}]] istället.',
);

/** Silesian (Ślůnski)
 * @author Herr Kriss
 */
$messages['szl'] = array(
	'crosswikiblock-expiry' => 'Wygaso:',
	'crosswikiblock-reason' => 'Čymu:',
	'crosswikiunblock-reason' => 'Čymu:',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'crosswikiblock-desc' => '[[Special:Crosswikiblock|ప్రత్యేక పేజీ]] ద్వారా వాడుకర్లని ఇతర వికీల్లో కూడా నిరోధించే వీలుకల్పిస్తుంది',
	'crosswikiblock' => 'ఇతర వికీలో వాడుకరిని నిరోధించండి',
	'crosswikiblock-target' => 'IP చిరునామా లేదా వాడుకరిపేరు మరియు గమ్యస్థానపు వికీ:',
	'crosswikiblock-expiry' => 'కాలపరిమితి:',
	'crosswikiblock-reason' => 'కారణం:',
	'crosswikiblock-submit' => 'ఈ వాడుకరిని నిరోధించండి',
	'crosswikiblock-anononly' => 'అనామక వాడుకరులను మాత్రమే నిరోధించు',
	'crosswikiblock-nocreate' => 'ఖాతా సృష్టింపుని నివారించు',
	'crosswikiblock-noemail' => 'వాడుకరి ఈ-మెయిల్ పంపించడం నియంత్రించండి',
	'crosswikiunblock-user' => 'వాడుకరిపేరు, ఐపీ చిరునామా లేదా నిరోధపు ID మరియు లక్ష్యిత వికీ:',
	'crosswikiunblock-reason' => 'కారణం:',
	'crosswikiunblock-submit' => 'ఈ వాడుకరిపై నిరోధం ఎత్తివేయండి',
	'crosswikiunblock-success' => "'''$1''' అనే వాడుకరిపై నిరోధాన్ని విజయవంతంగా ఎత్తివేసాం.

తిరిగి:
* [[Special:CrosswikiBlock|నిరోధపు ఫారం]]
* [[$2]]",
	'crosswikiblock-nousername' => 'వాడుకరిపేరు ఇవ్వలేదు',
	'crosswikiblock-dbnotfound' => '$1 అనే డాటాబేసు లేదు',
	'crosswikiblock-noname' => '"$1" అన్నది సరైన వాడుకరిపేరు కాదు.',
	'crosswikiblock-nouser' => '"$3" అనే వాడుకరి కనబడలేదు.',
	'crosswikiblock-noexpiry' => 'తప్పుడు కాలపరిమితి: $1.',
	'crosswikiblock-noreason' => 'కారణం తెలుపలేదు.',
	'crosswikiblock-alreadyblocked' => '$3 అనే వాడుకరిని ఇదివరకే నిరోధించాం.',
	'crosswikiblock-noblock' => 'ఈ వాడుకరిని నిరోధించలేదు.',
	'crosswikiblock-success' => "'''$3''' అనే వాడుకరిని విజయవంతంగా నిరోధించాం.

తిరిగి:
* [[Special:CrosswikiBlock|నిరోధపు ఫారం]]
* [[$4]]",
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'crosswikiblock-target' => 'Нишонаи IP ё номи корбарӣ ва викии мақсад:',
	'crosswikiblock-reason' => 'Далел:',
	'crosswikiblock-submit' => 'Бастани ин корбар',
	'crosswikiblock-anononly' => 'Фақат бастани корбарони гумном',
	'crosswikiblock-nocreate' => 'Ҷилавгирӣ аз эҷоди ҳисоб',
	'crosswikiblock-noemail' => 'Ҷилавгирии корбар аз фиристодани почтаи электронӣ',
	'crosswikiunblock' => 'Аз бастан озод кардани корбар дар дигар вики',
	'crosswikiunblock-user' => 'Номи корбарӣ, нишонаи IP  ё нишонаи бастан ва викии мақсад:',
	'crosswikiunblock-reason' => 'Сабаб:',
	'crosswikiunblock-submit' => 'Боз кардани ин корбар',
	'crosswikiunblock-success' => "Корбар '''$1''' бо муваффақият боз шуд.

Баргардед ба:
* [[Special:CrosswikiBlock|Форми бастан]]
* [[$2]]",
	'crosswikiblock-dbnotfound' => 'Пойгоҳи додаи $1 вуҷуд надорад',
	'crosswikiblock-noname' => '"$1" номи корбарии номӯътабар аст.',
	'crosswikiblock-nouser' => 'Корбар "$3" ёфт нашуд.',
	'crosswikiblock-noreason' => 'Сабабе мушаххас нашудааст.',
	'crosswikiblock-alreadyblocked' => 'Корбар $3 аллакай баста шудааст.',
	'crosswikiblock-noblock' => 'Ин корбар баста нашудааст.',
	'crosswikiblock-success' => "Корбар '''$3''' бо муваффақият баста шуд.

Баргардед ба:
* [[Special:CrosswikiBlock|Форми бастан]]
* [[$4]]",
);

/** Turkish (Türkçe)
 * @author Mach
 * @author Suelnur
 */
$messages['tr'] = array(
	'crosswikiblock-reason' => 'Gerekçe:',
	'crosswikiblock-submit' => 'Bu kullanıcıyı engelle',
	'crosswikiunblock-reason' => 'Neden:',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'crosswikiblock-desc' => 'Permete de blocar utenti de altre wiki doparando na [[Special:Crosswikiblock|pagina special]]',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'crosswikiblock-desc' => 'Cho phép cấm thành viên tại các wiki khác qua một [[Special:Crosswikiblock|trang đặc biệt]]',
	'crosswikiblock' => 'Cấm thành viên tại wiki khác',
	'crosswikiblock-header' => 'Trang này cho phép cấm thành viên tại wiki khác.
Xin hãy kiểm tra xem bạn có được phép thực hiện điều này tại wiki này hay không và hành động của bạn có theo đúng tất cả các quy định hay không.',
	'crosswikiblock-target' => 'Địa chỉ IP hoặc tên người dùng và wiki đích:',
	'crosswikiblock-expiry' => 'Hết hạn:',
	'crosswikiblock-reason' => 'Lý do:',
	'crosswikiblock-submit' => 'Cấm',
	'crosswikiblock-anononly' => 'Chỉ cấm thành viên vô danh',
	'crosswikiblock-nocreate' => 'Không cho tạo tài khoản',
	'crosswikiblock-autoblock' => 'Tự động cấm các địa chỉ IP mà thành viên này sử dụng',
	'crosswikiblock-noemail' => 'Không cho gửi thư điện tử',
	'crosswikiunblock' => 'Bỏ cấm thành viên tại wiki khác',
	'crosswikiunblock-header' => 'Trang này cho phép bỏ cấm thành viên tại wiki khác.
Xin hãy kiểm tra xem bạn có được phép thực hiện điều này tại wiki này hay không và hành động của bạn có theo đúng tất cả các quy định hay không.',
	'crosswikiunblock-user' => 'Tên người dùng, địa chỉ IP hoặc mã số cấm và wiki đích:',
	'crosswikiunblock-reason' => 'Lý do:',
	'crosswikiunblock-submit' => 'Bỏ cấm thành viên này',
	'crosswikiunblock-success' => "Thành viên '''$1''' đã được bỏ cấm.

Quay trở lại:
* [[Special:CrosswikiBlock|Mẫu cấm]]
* [[$2]]",
	'crosswikiblock-nousername' => 'Chưa nhập tên người dùng',
	'crosswikiblock-local' => 'Giao diện này không hỗ trợ cấm tại wiki này. Hãy dùng [[Special:BlockIP|{{int:blockip}}]]',
	'crosswikiblock-dbnotfound' => 'Cơ sở dữ liệu $1 không tồn tại',
	'crosswikiblock-noname' => '“$1” không phải là tên hợp lệ.',
	'crosswikiblock-nouser' => 'Không tìm thấy thành viên “$3”.',
	'crosswikiblock-noexpiry' => 'Thời hạn cấm không hợp lệ: $1.',
	'crosswikiblock-noreason' => 'Chưa đưa ra lý do.',
	'crosswikiblock-notoken' => 'Bằng chứng sửa đổi không hợp lệ.',
	'crosswikiblock-alreadyblocked' => 'Thành viên “$3” đã bị cấm rồi.',
	'crosswikiblock-noblock' => 'Thành viên này không bị cấm.',
	'crosswikiblock-success' => "Thành viên '''$3''' đã bị cấm.

Quay trở về:
* [[Special:CrosswikiBlock|Mẫu cấm]]
* [[$4]]",
	'crosswikiunblock-local' => 'Giao diện này không hỗ trợ bỏ cấm tại wiki này. Hãy dùng [[Special:IPBlockList|{{int:ipblocklist}}]]',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'crosswikiblock-reason' => 'Kod:',
	'crosswikiunblock-reason' => 'Kod:',
);

