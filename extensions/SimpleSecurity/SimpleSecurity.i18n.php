<?php
/**
 * Internationalisation for SimpleSecurity extension
 *
 * @author Nad
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Nad
 */
$messages['en'] = array(
	'security'                 => 'Security log',
	'security-desc'            => 'Extends the MediaWiki page protection to allow restricting viewing of page content',
	'security-logpage'         => 'Security log',
	'security-logpagetext'     => 'This is a log of actions blocked by the [http://www.mediawiki.org/wiki/Extension:SimpleSecurity SimpleSecurity extension].',
	'security-logentry'        => '', # do not translate or duplicate this message to other languages
	'badaccess-read'           => 'Warning: "$1" is referred to here, but you do not have sufficient permissions to access it.',
	'security-info'            => 'There are $1 on this page',
	'security-info-toggle'     => 'security restrictions',
	'security-inforestrict'    => '$1 is restricted to $2',
	'security-desc-LS'         => "''(applies because this page is in the '''$2 $1''')''",
	'security-desc-PR'         => "''(set from the '''protect tab''')''",
	'security-desc-CR'         => "''(this restriction is '''in effect now''')''",
	'security-infosysops'      => "No restrictions are in effect because you are a member of the '''sysop''' group",
	'security-manygroups'      => 'groups $1 and $2',
	# 'protect-unchain'          => 'Modify actions individually', # message key conflicts with core. Do not translate or duplicate this message to other languages
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 */
$messages['qqq'] = array(
	'badaccess-read' => '$1 is a page title that is restricted to access.',
	'security-info' => '$1 is {{msg-mw|Security-info-toggle}} with a link',
	'security-inforestrict' => '* $1 is an action name
* $2 contains user group name(s)',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'security' => 'سجل الأمن',
	'security-desc' => 'يمدد حماية المقالات في ميدياويكي للسماح بتحديد رؤية محتوى المقالات',
	'security-logpage' => 'سجل الأمن',
	'security-logpagetext' => 'هذا سجل بالأفعال الممنوعة بواسطة [http://www.mediawiki.org/wiki/Extension:SimpleSecurity امتداد الأمن البسيط].',
	'badaccess-read' => 'تحذير: "$1" ترجع إلى هنا، لكنك لا تمتلك سماحات كافية للوصول إليها.',
	'security-info' => 'توجد $1 على هذه المقالة',
	'security-info-toggle' => 'ضوابط الأمن',
	'security-inforestrict' => '$1 مضبوط إلى $2',
	'security-desc-LS' => "''(يطبق لأن هذه المقالة موجودة في '''$2 $1''')''",
	'security-desc-PR' => "''(اضبط من '''لسان الحماية''')''",
	'security-desc-CR' => "''(هذا الضابط '''فعال الآن''')''",
	'security-infosysops' => "لا ضوابط مفعلة لأنك عضو في مجموعة '''sysop'''",
	'security-manygroups' => 'المجموعات $1 و $2',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'security' => 'Zapisnik sigurnosti',
	'security-logpage' => 'Zapisnik sigurnosti',
	'security-info' => 'Postoji $1 na ovom članku',
	'security-manygroups' => 'grupe $1 i $2',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'security' => 'Wěstotny protokol',
	'security-desc' => 'Rozšyrja nastawkowy šćit MediaWiki wó móžnosć wobglědanje nastawkowego wopśimjeśa wobgranicowaś',
	'security-logpage' => 'Wěstotny protokol',
	'security-logpagetext' => 'To jo protokol akcijow blokěrowanych pséz [http://www.mediawiki.org/wiki/Extension:SimpleSecurity rozšyrjenje Simple Security].',
	'badaccess-read' => 'Warnowanje: How se na "$1" póśěgujo, ale njamaš pšawa, aby měł na njen pśistup.',
	'security-info' => 'Su $1 wó toś tom nastawku',
	'security-info-toggle' => 'wěstotne wobgranicowanja',
	'security-inforestrict' => '$1 jo na $2 wobgranicowany',
	'security-desc-LS' => "''(nałožujo se, dokulaž toś ten nastawk jo w '''$2 $1''')''",
	'security-desc-PR' => "''(ze '''šćitowego rejtarka''' stajony)''",
	'security-desc-CR' => "''(toś to wobgranicowanje '''něnto statkujo''')''",
	'security-infosysops' => "Njejsu žedne wobgranicowanja, dokulaž sy cłonk w kupce '''administratorow'''",
	'security-manygroups' => 'kupce $1 a $2',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'security' => 'Registro de seguridad',
	'security-desc' => 'Extiende la protección de artículos MediaWiki para permitir vista restringida del contenido del artículo',
	'security-logpage' => 'Registro de seguridad',
	'security-logpagetext' => 'Esto es un registro de bloqueo de acciones hechos por [http://www.mediawiki.org/wiki/Extension:SimpleSecurity la extensión SimpleSecurity].',
	'badaccess-read' => 'Advertencia:"$1" está referenciado aquí, pero no tienes permisos suficientes para acceder a el.',
	'security-info' => 'Hay $1 en este artículo',
	'security-info-toggle' => 'restricciones de seguridad',
	'security-inforestrict' => '$1 está restringido a $2',
	'security-manygroups' => 'grupos $1 y $2',
);

/** French (Français)
 * @author Crochet.david
 */
$messages['fr'] = array(
	'security' => 'Journal de sécurité',
	'security-logpage' => 'Journal de sécurité',
	'security-logpagetext' => "Ceci est un journal des actions bloquées par l'[http://www.mediawiki.org/wiki/Extension:SimpleSecurity extension SimpleSecurity].",
	'badaccess-read' => 'Attention : « $1 » est référencé ici, mais vous ne disposez pas des autorisations pour y accéder.',
	'security-info' => 'Il y a $1 sur cet article',
	'security-info-toggle' => 'restrictions de sécurité',
	'security-inforestrict' => '$1 est limité à $2',
	'security-desc-LS' => "''(s'applique parce que cet article est dans le '''$2 $1''')''",
	'security-desc-CR' => "''(cette restriction est '''effective maintenant''')''",
	'security-infosysops' => "Aucune restriction en vigueur parce que vous êtes un membre du groupe '''administrateur'''",
	'security-manygroups' => 'groupes $1 et $2',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'security' => 'Sicherheits-Logbuech',
	'security-desc' => 'Erwyteret dr Mediawiki-Artikelschutz um d Megligkeit, dass Artikelinhalt chenne gsperrt wäre fir s Aaluege',
	'security-logpage' => 'Sicherheits-Logbuech',
	'security-logpagetext' => 'Des isch s Logbuech vu dr Aktione, wu gsperrt sin dur d [http://www.mediawiki.org/wiki/Extension:SimpleSecurity SimpleSecurity-Erwyterig].',
	'badaccess-read' => 'Warnig: "$1" isch do aagee, aber Du hesch nit d netig Berächtigung go s durchfiere.',
	'security-info' => 'S het $1 iber dää Artikel',
	'security-info-toggle' => 'Sicherheitsyyschränkige',
	'security-inforestrict' => '$1 isch yygschränkt fir $2',
	'security-desc-LS' => "''(wel dää Artikel in '''$2 $1''' isch)''",
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'security' => 'Wěstotny protokol',
	'security-desc' => 'Rozšěrja nastawkowy škit MediaWiki wo móžnosć wobhladanje nastawkoweho wobsaha wobmjezować',
	'security-logpage' => 'Wěstotny protokol',
	'security-logpagetext' => 'To je protokol akcijow zablokowanych přez [http://www.mediawiki.org/wiki/Extension:SimpleSecurity rozšěrjenje Simple Security].',
	'badaccess-read' => 'Warnowanje: Na "$1 so tu poćahuje, ale nimaš prawa, zo by přistup na njón měł.',
	'security-info' => 'Su $1 wo tutym nastawku',
	'security-info-toggle' => 'wěstotne wobmjezowanja',
	'security-inforestrict' => '$1 je na $2 wobmjezowany',
	'security-desc-LS' => "''(nałožuje so, dokelž tutón nastawk je w '''$2 $1''')''",
	'security-desc-PR' => "''(ze '''škitoweho rajtarka''' stajeny)''",
	'security-desc-CR' => "''(tute wobmjezowanje '''nětko skutkuje''')''",
	'security-infosysops' => "Njejsu wobmjezowanja, dokelž sy čłon skupiny '''administratorow'''",
	'security-manygroups' => 'skupinje $1 a $2',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Hosiryuhosi
 */
$messages['ja'] = array(
	'security' => 'セキュリティ記録',
	'security-desc' => 'MediaWiki のページ保護機能を、ページの閲覧を制限できるように拡張する',
	'security-logpage' => 'セキュリティ記録',
	'security-logpagetext' => 'これは、[http://www.mediawiki.org/wiki/Extension:SimpleSecurity SimpleSecurity 拡張機能]によって阻止された操作の記録です。',
	'badaccess-read' => '警告:「$1」はここを参照していますが、あなたにはアクセスに必要な権限がありません。',
	'security-info' => 'このページには$1があります',
	'security-info-toggle' => 'セキュリティ制限',
	'security-inforestrict' => '$1は$2に限定されています',
	'security-desc-LS' => "''(この記事が'''$2 $1'''にあるため)''",
	'security-desc-PR' => "''('''保護タブ'''からの設定)''",
	'security-desc-CR' => "''(この制限は現在有効です)''",
	'security-infosysops' => "あなたは'''{{int:group-sysop}}'''グループに所属しているため、制限は無効です",
	'security-manygroups' => 'グループ $1 および $2',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'security-manygroups' => 'grupos $1 e $2',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'security' => 'Bezpečnostný záznam',
	'security-desc' => 'Rozširuje zamykanie stránok MediaWiki o možnosť obmedziť zobrazovanie obsahu článku',
	'security-logpage' => 'Bezpečnostný záznam',
	'security-logpagetext' => 'Toto je záznam operácií, ktoré zablokovalo [http://www.mediawiki.org/wiki/Extension:SimpleSecurity rozšírenie SimpleSecurity].',
	'badaccess-read' => 'Upozornenie: odkazuje sa tu na „$1“, ale nemáte dostatočné oprácnenia na prístup k nemu.',
	'security-info' => 'Táto stránka má $1',
	'security-info-toggle' => 'bezpečnostné obmedzenia',
	'security-inforestrict' => '$1 nemá povolené $2',
	'security-desc-LS' => "''(týka sa tejto stránky, pretože je na '''$2 $1''')''",
	'security-desc-PR' => "''(nastavené zo '''záložky zamykania''')''",
	'security-desc-CR' => "''(toto obmedzenie je '''teraz účinné''')''",
	'security-infosysops' => "Žiadne obmedzenia nie sú účinné, pretože ste členom skupiny '''sysop'''",
	'security-manygroups' => 'skupiny $1 a $2',
);

