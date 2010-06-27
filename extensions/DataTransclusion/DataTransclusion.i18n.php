<?php
/**
 * Internationalisation file for the extension DataTransclusion
 *
 * @file
 * @ingroup Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

$messages = array();

/** English
 */
$messages['en'] = array(
	'datatransclusion-desc'         => 'Import and rendering of data records from external data sources',

	'datatransclusion-test-wikitext' => 'some <span class="test">html</span> and \'\'markup\'\'.', // Do not translate.
	'datatransclusion-test-evil-html' => 'some <object>evil</object> html.', // Do not translate.
	'datatransclusion-test-nowiki' => 'some <nowiki>{{nowiki}}</nowiki> code.', // Do not translate.

	'datatransclusion-missing-source'            => 'No data source specified.
Second or "source" argument is required.', #FUZZ!
	'datatransclusion-unknown-source'            => 'Bad data source specified.
"$1" is not known.',
	'datatransclusion-missing-key'           => 'No key specified.
$2 are valid keys in data source $1.',
	'datatransclusion-bad-argument-by'           => 'Bad key field specified.
"$2" is not a key field in data source "$1".
{{PLURAL:$4|Valid key|Valid keys are}}: $3.',
	'datatransclusion-missing-argument-key'      => 'No key value specified.
Second or "key" argument is required.',
	'datatransclusion-missing-argument-template' => 'No template specified.
First or "template" argument is required.', #FUZZ!
	'datatransclusion-record-not-found'          => 'No record matching $2 = $3 was found in data source $1.',
	'datatransclusion-bad-template-name'         => 'Bad template name: $1.',
	'datatransclusion-unknown-template'          => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> does not exist.',
);

/** Message documentation (Message documentation)
 * @author McDutchie
 * @author Siebrand
 */
$messages['qqq'] = array(
	'datatransclusion-desc' => '{{desc}}',
	'datatransclusion-missing-source' => 'Issued if no data "source" or second positional argument was specified.',
	'datatransclusion-unknown-source' => 'Issued if an unknown data source was specified. Parameters:
* $1 is the name of the data source.',
	'datatransclusion-missing-key' => 'Issued if no argument matches an entry in the list of key field. Parameters:
* $1 is the name of the data source
* $2 is a list of all valid keys for this data source',
	'datatransclusion-bad-argument-by' => 'Issued if a bad value was specified for the "by" argument, that is, an unknown key field was selected. Parameters:
* $1 is the name of the data source
* $2 is the value of the by argument
* $3 is a list of all valid keys for this data source
* $4 is the number of valid keys for this data source.',
	'datatransclusion-missing-argument-key' => 'Issued if no "key" or second positional argument was given provided. A key value is always required.',
	'datatransclusion-missing-argument-template' => 'Issued if no "template" or first positional argument was given provided. A target template is always required.',
	'datatransclusion-record-not-found' => 'issued if the record specified using the "by" and "key" arguments was nout found in the data source.  Parameters:
* $1 is the name of the data source
* $2 is the key filed used
* $3 is the key value to select by.',
	'datatransclusion-bad-template-name' => 'Issued if the template name specified is not valid. Parameters:
* $1 is the given template name.',
	'datatransclusion-unknown-template' => 'Issued if the template specified does not exist. Parameters:
* $1 is the given template name.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'datatransclusion-desc' => 'Імпарт і паказ зьвестак з вонкавых крыніц',
	'datatransclusion-missing-source' => 'Крыніца зьвестак не пазначаная.
Другі ці «крынічны» парамэтар — абавязковы.',
	'datatransclusion-unknown-source' => 'Няслушная крыніца зьвестак.
$1 — невядомая.',
	'datatransclusion-missing-key' => 'Ключ не пазначаны.
Слушнымі ключамі ў крыніцы зьвестак $1 зьяўляюцца $2.',
	'datatransclusion-bad-argument-by' => 'Пазначана няслушнае ключавое поле.
«$2» не зьяўляецца ключавым полем ў крыніцы зьвестак «$1».
{{PLURAL:$4|Слушным ключом зьяўляецца|Слушнымі ключамі зьяўляюцца}}: $3.',
	'datatransclusion-missing-argument-key' => 'Ключавое значэньне не пазначана.
Неабходны другі ці «ключавы» аргумэнт.',
	'datatransclusion-missing-argument-template' => 'Шаблён не пазначаны. 
Неабходны першы ці «шаблённы» аргумэнт.',
	'datatransclusion-record-not-found' => 'Ня знойдзеныя супадаючыя запісы $2 = $3 ў крыніцы зьвестак $1.',
	'datatransclusion-bad-template-name' => 'Няслушная назва шаблёну: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> не існуе.',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'datatransclusion-bad-template-name' => 'Anv patrom direizh : $1.',
	'datatransclusion-unknown-template' => "N'eus ket eus <nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki>.",
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'datatransclusion-desc' => 'Ermöglicht den Import und die Darstellung von Datensätzen aus externen Datenquellen',
	'datatransclusion-missing-source' => 'Es wurde keine Datenquelle angegeben.
Ein zweites oder ein „Quell“-Argument ist erforderlich.',
	'datatransclusion-unknown-source' => 'Es wurde eine mangelhafte Datenquelle angegeben.
„$1“ ist nicht bekannt.',
	'datatransclusion-missing-key' => 'Es wurde kein Schlüssel angegeben.
„$2“ sind gültige Schlüssel in Datenquelle „$1“.',
	'datatransclusion-bad-argument-by' => 'Es wurde ein mangelhaftes Schlüsselfeld angegeben.
„$2“ ist kein Schlüsselfeld in der Datenquelle „$1“.
{{PLURAL:$4|Ein gültiger Schlüssel ist|Gültige Schlüssel sind}}: $3.',
	'datatransclusion-missing-argument-key' => 'Es wurde kein Schlüsselwert angegeben.
Ein zweites oder ein „Schlüssel“-Argument ist erforderlich.',
	'datatransclusion-missing-argument-template' => 'Es wurde keine Vorlage angegeben.
Das erste oder ein „Vorlagen“-Argument ist erforderlich.',
	'datatransclusion-record-not-found' => 'Es wurde kein passender Datensatz $2 = $3 in der Datenquelle „$1“ gefunden.',
	'datatransclusion-bad-template-name' => 'Mangelhafter Vorlagenname: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> existiert nicht.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'datatransclusion-desc' => 'Importowanje a pśedstajenje datowych sajźbow z eksternych datowych žrědłow',
	'datatransclusion-missing-source' => 'Žedne datowe žrědło pódane.
Drugi abo "žrědłowy" argument jo trěbny.',
	'datatransclusion-unknown-source' => 'Wopacne datowe žrědło pódane.
$1 jo njeznaty.',
	'datatransclusion-missing-key' => 'Žeden kluc pódany.
$2 su płaśiwe kluce w datowem žrědle $1.',
	'datatransclusion-bad-argument-by' => 'Wopacne pólo pódane.
"$2" njejo klucowe pólo w datowem žrědle "$1".
{{PLURAL:$4|Płaíswy kluc jo|Płaśiwej kluca stej|Płaśiwe kluce su|Płaśiwe kluce su}}: $3.',
	'datatransclusion-missing-argument-key' => 'Žedna datowa gódnota pódana.
Drugi abo "klucowy" argument je trěbny.',
	'datatransclusion-missing-argument-template' => 'Žedna pśedłoga pódana.
Prědny abo "pśedłogowy" argument jo trěbny.',
	'datatransclusion-record-not-found' => 'W datowem žrědle $1 njejo se žedna sajźba namakała, kótaraž $2=$3 wótpowědujo.',
	'datatransclusion-bad-template-name' => 'Wopacne mě pśedłogi: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> njeeksistěrujo.',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'datatransclusion-desc' => 'Importación y representación de registro de datos desde fuentes externas de datos',
	'datatransclusion-missing-source' => 'Ninguna fuente de datos especificada.
Argumento segundo o "fuente" es obligatorio.',
	'datatransclusion-unknown-source' => 'Fuente de datos mal especificado.
$1 es desconocido.',
	'datatransclusion-missing-key' => 'Sin clave especificada.
$2 son claves válidas en fuente de datos $1.',
	'datatransclusion-bad-argument-by' => 'Campo clave mal especificado.
"$2" no es un campo clave en la fuente de datos "$1".
{{PLURAL:$4|Clave válida|Claves válidas son}}: $3.',
	'datatransclusion-missing-argument-key' => 'Ningún valor clave especificado.
Argumento segundo o "clave" es obligatorio.',
	'datatransclusion-missing-argument-template' => 'Ninguna plantilla especificada.
Argumento primero o "plantilla" es obligatorio.',
	'datatransclusion-record-not-found' => 'Ningún registro coincidente $2 = $3 fue encontrado en la fuente de datos $1.',
	'datatransclusion-bad-template-name' => 'Mal nombre de plantilla: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> no existe.',
);

/** French (Français)
 * @author IAlex
 * @author Peter17
 */
$messages['fr'] = array(
	'datatransclusion-desc' => 'Importer et mettre en forme des données depuis des sources externes',
	'datatransclusion-missing-source' => 'Aucune source de données n’est spécifiée.
Le deuxième argument ou « source » est obligatoire.',
	'datatransclusion-unknown-source' => 'Mauvaise source de données spécifiée.
$1 est inconnu.',
	'datatransclusion-missing-key' => 'Aucune clé n’est spécifiée.
$2 sont les clés valides pour la source de données $1.',
	'datatransclusion-bad-argument-by' => 'Mauvaise clé de champ spécifiée.
« $2 » n’est pas une clé de champ dans la source de données « $1 ».
{{PLURAL:$4|La clé valide est|Les clés valides sont}} : $3.',
	'datatransclusion-missing-argument-key' => 'Aucune valeur de clé spécifiée.
Le deuxième argument ou « clé » est obligatoire.',
	'datatransclusion-missing-argument-template' => 'Aucun modèle n’est spécifié.
Le premier argument ou « modèle » est obligatoire.',
	'datatransclusion-record-not-found' => 'Aucun enregistrement vérifiant $2 = $3 n’a été trouvé dans la source de données $1.',
	'datatransclusion-bad-template-name' => 'Mauvais nom de modèle : $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> n’existe pas.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'datatransclusion-desc' => 'Importación e procesamento de rexistros de datos de fontes externas',
	'datatransclusion-missing-source' => 'Non se especificou ningunha fonte de datos.
Necesítase o segundo argumento ou "fonte".',
	'datatransclusion-unknown-source' => 'A fonte de datos que se especificou é incorrecta.
Descoñécese o que é "$1".',
	'datatransclusion-missing-key' => 'Non se especificou ningunha clave.
"$2" son claves válidas na fonte de datos "$1".',
	'datatransclusion-bad-argument-by' => 'A clave de campo que se especificou é incorrecta.
"$2" non é unha clave de campo na fonte de datos "$1".
{{PLURAL:$4|Exemplo de clave válida|Exemplos de claves válidas}}: $3.',
	'datatransclusion-missing-argument-key' => 'Non se especificou ningún valor para a chave.
Necesítase o segundo argumento ou "clave".',
	'datatransclusion-missing-argument-template' => 'Non se especificou ningún modelo.
Necesítase o primeiro argumento ou "modelo".',
	'datatransclusion-record-not-found' => 'Non se atopou ningún rexistro que coincidise $2 = $3 na fonte de datos "$1".',
	'datatransclusion-bad-template-name' => 'O nome do modelo é incorrecto: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> non existe.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'datatransclusion-desc' => 'Importowanje a předstajenje datowych sadźbow z eksternych datowych žórłow',
	'datatransclusion-missing-source' => 'Žane datowe žórło podate.
Druhi abo "žórłowy" argument je trěbny.',
	'datatransclusion-unknown-source' => 'Wopačne datowe žórło podate.
$1 je njeznaty.',
	'datatransclusion-missing-key' => 'Žadyn kluč podaty.
$2 su płaćiwe kluče w datowym žórle $1.',
	'datatransclusion-bad-argument-by' => 'Wopačne klučowe polo podate.
$2 njeje klučowe polo w datowym žórle "$1".
{{PLURAL:$4|Płaćiwy kluč je|Płaćiwej klučej stej|Płaćiwe kluče su|Płaćiwe kluče su}}: $3',
	'datatransclusion-missing-argument-key' => 'Žana klučowa hódnota podata.
Druhi abo "klučowy" argument je trěbny.',
	'datatransclusion-missing-argument-template' => 'Žana předłoha podata.
Prěni abo "předłohowy" argument je trěbny.',
	'datatransclusion-record-not-found' => 'W datowym žórle $1 njeje so žana datowa sadźba namakała, kotraž $2=$3 wotpowěduje.',
	'datatransclusion-bad-template-name' => 'Wopačne mjeno předłohi: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> njeeksistuje.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'datatransclusion-desc' => 'Importation e rendition de datos ex fontes externe',
	'datatransclusion-missing-source' => 'Nulle fonte de datos specificate.
Un secunde parametro "source" es obligatori.',
	'datatransclusion-unknown-source' => 'Un fonte de datos invalide ha essite specificate.
$1 non es cognoscite.',
	'datatransclusion-missing-key' => 'Nulle clave specificate.
$2 es le claves valide in le fonte de datos $1.',
	'datatransclusion-bad-argument-by' => 'Un campo de clave invalide ha essite specificate.
"$2" non es un campo de clave in le fonte de datos "$1".
Le {{PLURAL:$4|clave|claves}} valide es: $3.',
	'datatransclusion-missing-argument-key' => 'Nulle valor de clave specificate.
Un secunde parametro "key" es obligatori.',
	'datatransclusion-missing-argument-template' => 'Nulle patrono specificate.
Un prime parametro "template" es obligatori.',
	'datatransclusion-record-not-found' => 'Nulle dato correspondente a $2 = $3 ha essite trovate in le fonte de datos $1.',
	'datatransclusion-bad-template-name' => 'Nomine de patrono incorrecte: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> non existe.',
);

/** Italian (Italiano)
 * @author EdoDodo
 */
$messages['it'] = array(
	'datatransclusion-unknown-source' => "Origine dati incorreta specificata.
''$1'' non è noto.",
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'datatransclusion-desc' => 'Import and Duerstellung vun Daten aus externe Quellen',
	'datatransclusion-bad-template-name' => 'Schlechten Numm fir eng Schabloun: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> gëtt et net.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'datatransclusion-desc' => 'Увоз и обликување на податотечни записи од надворешни податотечни извори',
	'datatransclusion-missing-source' => 'Не е укажан податотечен извор. 
Се бара вториот аргумент или „извор“.',
	'datatransclusion-unknown-source' => 'укажан е лош податотечен извор ($1 е непознат)',
	'datatransclusion-missing-key' => 'Нема укажано клуч.
$2 се важечки клучеви во податотечниот извор $1.',
	'datatransclusion-bad-argument-by' => 'Укажано е лошо клучно поле.
„$2“ не е клучно поле во податочниот извор „$1“.
{{PLURAL:$4|Важечки клуч|Важечки клучеви се}}: $3.',
	'datatransclusion-missing-argument-key' => 'нема укажано вредност за клучот (се бара вториот аргумент или „клуч“)',
	'datatransclusion-missing-argument-template' => 'Нема укажано шаблон. 
Се бара првиот аргумент или „шаблон“.',
	'datatransclusion-record-not-found' => 'во податочниот извор $1 нема пронајдено запис што одговара на $2 = $3',
	'datatransclusion-bad-template-name' => 'лошо име на шаблон: $1',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[Template:$1|$1]]<nowiki>}}</nowiki> не постои.',
);

/** Marathi (मराठी)
 * @author V.narsikar
 */
$messages['mr'] = array(
	'datatransclusion-bad-template-name' => 'चुकीचे साचानाव:$1',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'datatransclusion-desc' => 'Importeren en renderen van gegevens uit externe bronnen',
	'datatransclusion-missing-source' => 'Er is geen gegevensbron aangegeven.
Een tweede of "bron"-argument is vereist.',
	'datatransclusion-unknown-source' => 'Er is een ongeldige gegevensbron aangegeven.
$1 is niet bekend.',
	'datatransclusion-missing-key' => 'Geen sleutel aangegeven.
$2 zijn geldige sleutels in gegevensbron $1.',
	'datatransclusion-bad-argument-by' => 'Ongeldig sleutelveld aangegeven.
"$2" is geen sleutelveld in gegevensbron "$1".
Geldige {{PLURAL:$4|sleutel is|sleutels zijn}}: $3.',
	'datatransclusion-missing-argument-key' => 'Er is geen sleutelwaarde aangegeven.
Een tweede argument of "sleutel" is verplicht.',
	'datatransclusion-missing-argument-template' => 'Geen sjabloon aangegeven.
Een eerste argument of "template"-argument is verplicht.',
	'datatransclusion-record-not-found' => 'Er is geen overeenkomstig gegeven $2 = $3 gevonden in de gegevensbron $1.',
	'datatransclusion-bad-template-name' => 'Ongeldige sjabloonnaam: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki>  bestaat niet.',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'datatransclusion-desc' => 'Importação e apresentação de registos de dados vindos de fontes externas',
	'datatransclusion-missing-source' => 'Não foi especificada a fonte dos dados.
O segundo argumento, ou argumento "fonte", é obrigatório.',
	'datatransclusion-unknown-source' => 'A fonte de dados especificada é incorrecta.
$1 não é conhecido.',
	'datatransclusion-missing-key' => 'Não foi especificada uma chave.
$2 são chaves válidas na fonte de dados $1.',
	'datatransclusion-bad-argument-by' => 'Foi especificado um campo chave incorrecto.
"$2" não é um campo chave na fonte de dados "$1".
{{PLURAL:$4|O único campo chave válido é|Os campos chave válidos são}}: $3.',
	'datatransclusion-missing-argument-key' => 'Não foi especificado um campo chave.
O segundo argumento, ou argumento "chave", é obrigatório.',
	'datatransclusion-missing-argument-template' => 'Não foi especificada uma predefinição.
O primeiro argumento, ou argumento "predefinição", é obrigatório.',
	'datatransclusion-record-not-found' => 'Não foi encontrado nenhum registo $2 = $3 na fonte de dados $1.',
	'datatransclusion-bad-template-name' => 'Nome da predefinição incorrecto: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> não existe.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Giro720
 */
$messages['pt-br'] = array(
	'datatransclusion-desc' => 'Importação e apresentação de registros de dados vindos de fontes externas',
	'datatransclusion-missing-source' => 'Não foi especificada a fonte dos dados.
O segundo argumento, ou argumento "fonte", é obrigatório.',
	'datatransclusion-unknown-source' => 'A fonte de dados especificada é incorreta.
$1 não é conhecido.',
	'datatransclusion-missing-key' => 'Não foi especificada uma chave.
$2 são chaves válidas na fonte de dados $1.',
	'datatransclusion-bad-argument-by' => 'Foi especificado um campo chave incorreto.
"$2" não é um campo chave na fonte de dados "$1".
{{PLURAL:$4|O único campo chave válido é|Os campos chave válidos são}}: $3.',
	'datatransclusion-missing-argument-key' => 'Não foi especificado um campo chave.
O segundo argumento, ou argumento "chave", é obrigatório.',
	'datatransclusion-missing-argument-template' => 'Não foi especificada uma predefinição.
O primeiro argumento, ou argumento "predefinição", é obrigatório.',
	'datatransclusion-record-not-found' => 'Não foi encontrado nenhum registro $2 = $3 na fonte de dados $1.',
	'datatransclusion-bad-template-name' => 'Nome da predefinição incorreto: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki>  não existe.',
);

/** Russian (Русский)
 * @author G0rn
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'datatransclusion-desc' => 'Импорт и обработка данных из внешних источников данных',
	'datatransclusion-missing-source' => 'Не указан источник данных.
Первый аргумент (аргумент источника) является обязательным.',
	'datatransclusion-unknown-source' => 'Указан неправильный источник данных.
$1 — неизвестен.',
	'datatransclusion-missing-key' => 'Не задан ключ.
Допустимыми ключами источника данных $1 являются $2.',
	'datatransclusion-bad-argument-by' => 'Указано неправильное ключевое поле.
$2 не является ключевым полем в источнике данных $1.  
{{PLURAL:$4|Действительный ключ|Действительными ключами являются}}: $3.',
	'datatransclusion-missing-argument-key' => 'Не указано значение ключа.
Второй или «ключевой» аргумент является обязательным.',
	'datatransclusion-missing-argument-template' => 'Не указан шаблон.
Третий («шаблонный») аргумент является обязательным.',
	'datatransclusion-record-not-found' => 'В источнике данных $1 не найдено записи, соответствующей $2 = $3',
	'datatransclusion-bad-template-name' => 'Неправильное название шаблона: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki>  не существуе.',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'datatransclusion-desc' => 'Pag-aangkat at pagdudulog ng mga talaan ng dato mula sa mga pinagmulan ng datong panlabas',
	'datatransclusion-missing-source' => 'Walang tinukoy na pinagmulan ng dato.
Kailangan ang unang argumento.',
	'datatransclusion-unknown-source' => 'Natukoy ang masamang pinagmulan ng dato.
Hindi alam ang $1.',
	'datatransclusion-bad-argument-by' => 'Natukoy ang isang larangan ng masamang susi.
Ang $2 ay hindi isang susing larangan sa loob ng pinagmulan ng dato na $1, ang tanggap na mga susi ay: $3.',
	'datatransclusion-missing-argument-key' => 'Walang tinukoy na halaga ng susi.
Kailangan ang pangalawa o "susi" na argumento.',
	'datatransclusion-missing-argument-template' => 'Walang tinukoy na suleras.
Kailangan ang pangatlo o argumentong "suleras".',
	'datatransclusion-record-not-found' => 'Walang natagpuang rekord na tumutugma sa $2 = $3 na nasa loob ng pinagmulan ng dato na $1.',
	'datatransclusion-bad-template-name' => 'Masamang pangalan ng suleras: $1.',
	'datatransclusion-unknown-template' => 'Hindi umiiral ang <nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki>.',
);

/** Turkish (Türkçe)
 * @author Manco Capac
 */
$messages['tr'] = array(
	'datatransclusion-desc' => 'Dış veri kaynaklarından veri kayıtlarının aktarılması ve işlenmesi',
	'datatransclusion-missing-source' => 'Hiç veri kaynağı belirtilmedi.
İkinci bir kaynak ya da "kaynak" ispatı gerekmektedir.',
	'datatransclusion-unknown-source' => 'Belirtilen veri kaynağı kötüdür.
"$1" bilinmemektedir.',
	'datatransclusion-missing-key' => 'Hiç anahtar belirtilmedi.
$2, $1 veri kaynağındaki geçerli anahtarlardır.',
	'datatransclusion-bad-argument-by' => 'Kötü anahtar alanı belirtildi.
"$2", "$1" veri kaynağı içinde bir anahtar alanı değildir.
{{PLURAL:$4|Geçerli anahtar|Geçerli anahtarlar}}: $3.',
	'datatransclusion-missing-argument-key' => 'Hiç anahtar değeri belirtilmedi.
İkinci bir anahtar ya da "anahtar" ispatı gerekmektedir.',
	'datatransclusion-missing-argument-template' => 'Hiç şablon belirtilmedi.
Birincisi ya da "şablon" ispatı gerekmektedir.',
	'datatransclusion-record-not-found' => '$1 veri kaynağında, $2 = $3 şekline uyan hiç bir kayıt bulunamadı.',
	'datatransclusion-bad-template-name' => 'Kötü şablon adı: $1.',
	'datatransclusion-unknown-template' => '<nowiki>{{</nowiki>[[{{ns:template}}:$1|$1]]<nowiki>}}</nowiki> varolmamaktadır.',
);

