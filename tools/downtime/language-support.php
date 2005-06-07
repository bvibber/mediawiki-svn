<?php

$lang = "en";
$our_langs = array(
    "Dansk" => "da",
    "Deutsch" => "de",
    "English" => "en",
    "Esperanto" => "eo",
    "Nederlands" => "nl",
    "Polski" => "pl",
	"Slovensky" => "sk",
	"Русский" => "ru",
);

if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    $langs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
else
    $langs = array();

foreach ($langs as $l) {
    $code = explode(";", $l);
    $code = explode("-", $code[0]);
    if (in_array($code[0], $our_langs)) {
        $lang = $code[0];
        break;
    }
}

if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], $our_langs))
    $lang = $_REQUEST['lang'];

function langet($name) {
    global $text, $lang;
    if (isset($text[$lang][$name]))
        return $text[$lang][$name];
    else
        return $text["en"][$name];
}

function langbar() {
    global $lang, $our_langs;
    $a = array();
    foreach ($our_langs as $name => $code) {
        if ($code == $lang)
            $a[] = "<strong>$name</strong>";
        else
            $a[] = "<a href=\"/downtime.php?lang=$code\">$name</a>";
    }
    return implode(" | ", $a);
}

$text = array(
"en" => array(
    "maintitle" => "Wikimedia Site Maintenance",
    "mainp1" => "All Wikimedia projects (Wikipedia, Wiktionary, Wikibooks, Wikiquote, Wikisource, and Wikinews) are currently offline while hardware maintenance is carried out.",
    "mainp2" => "We don't expect this maintenance to extend past 21:00 UTC. In the meantime, you may wish to use one of the following mirrors of Wikipedia content:",
    "mainp3" => "We apologise for any inconvenience this may cause you.",
    "mainp4" => "Love,<br/>The Administration.",
    "credit" => "Page design based on www.wikipedia.org template by <a class=\"dark\" href=\"mailto:forseti@autograf.pl\">Forseti</a>.",

    "abouttitle" => "About the Wikimedia Foundation",
    "projectstitle" => "Wikimedia Projects",
    "about" =>
"The Wikimedia Foundation, Inc. is an international non-profit organization dedicated to encouraging
the growth, development and distribution of free, multilingual content, and to providing the full content
of these wiki-based projects to the public free of charge. Wikimedia relies on public donations to meet
its goal of providing free knowledge to every person in the world.",
    "projects" =>
"The Wikimedia Foundation is the parent organization of some of the largest collaboratively-edited
reference projects in the world, including Wikipedia, one of the 100 most visited websites in the world.
Other online projects include Wiktionary, a multilingual dictionary; Wikibooks, a collection of free
content textbooks; Wikiquote, a repository of famous quotes; Wikinews, a free news source; Wikisource,
a repository for primary source material; and Wikimedia Commons, a repository for images and sound data."
),

"de-old" => array(
    "abouttitle" => "Über die Wikimedia Foundation",
    "projectstitle" => "Unsere Projekte",
    "about" =>
"Die Wikimedia Foundation ist eine internationale gemeinnützige Organisation, die sich der Sammlung,
Entwicklung und Verbreitung von Freien Inhalten in den unterschiedlichsten Sprachen verschrieben hat
und diese Inhalte über Wiki-basierte Projekte der Öffentlichkeit kostenlos zur Verfügung stellt.
Wikimedia ist auf Spenden angewiesen, um dem Ziel näherzukommen, jedem Menschen auf der Welt freies
Wissen zugänglich zu machen.",
    "projects" =>
"Wikimedia ist die Dachorganisation einiger der größten gemeinschaftlich erarbeiteten Informationsquellen - darunter die freie Enzyklopädie Wikipedia, die mittlerweile zu den 500 meistbesuchten Websites der Welt zählt. Weitere Projekte sind das Wiktionary, ein mehrsprachiges Wörterbuch; Wikibooks, eine Sammlung freier Lehrbücher; Wikiquote, eine Sammlung bekannter Zitate und Wikisource, eine Sammlung von Literaturquellen."
),

"de" => array(
    "maintitle" => "Seitenwartung der Wikimedia",
    "mainp1" => "Alle Wikimedia-Projekte (Wikipedia, Wiktionary, Wikibooks, Wikiquote, Wikisource und Wikinews) sind gegenwärtig offline, solange wir eine Wartung der Hardware durchführen.",
    "mainp2" => "Wir gehen nicht davon aus, daß die Wartung länger als 21 Uhr UTC dauern wird. In der Zwischenzeit können sie auf einer der folgenden Seiten Mirror der Wikipedia besuchen:",
    "mainp3" => "Wir entschuldigen uns für alle Unannehmlichkeiten, die das für Sie bedeuten mag.",
    "mainp4" => "Alles Liebe,<br/> Die Verwaltung",
    "abouttitle" => "Über die Wikimedia Foundation",
    "projectstitle" => "Unsere Projekte",
    "about" =>
"Die Wikimedia Foundation ist eine internationale gemeinnützige Organisation, die sich der Sammlung,
Entwicklung und Verbreitung von Freien Inhalten in den unterschiedlichsten Sprachen verschrieben hat
und diese Inhalte über Wiki-basierte Projekte der Öffentlichkeit kostenlos zur Verfügung stellt.
Wikimedia ist auf Spenden angewiesen, um dem Ziel näherzukommen, jedem Menschen auf der Welt freies
Wissen zugänglich zu machen.",
    "projects" =>
"Wikimedia ist die Dachorganisation einiger der größten gemeinschaftlich erarbeiteten Informationsquellen - darunter die freie Enzyklopädie Wikipedia, die mittlerweile zu den 500 meistbesuchten Websites der Welt zählt. Weitere Projekte sind das Wiktionary, ein mehrsprachiges Wörterbuch; Wikibooks, eine Sammlung freier Lehrbücher; Wikiquote, eine Sammlung bekannter Zitate und Wikisource, eine Sammlung von Literaturquellen."
),

"pl" => array(
    "maintitle" => "Prace Administracyjne Wikimedia",
    "mainp1" =>
"Wszystkie projekty Wikimedia (Wikipedia, Wikisłownik, Wikibooks, Wikicytaty, Wikisource i Wikinews) są obecnie niedostępne, do czasu zakończenia prac administracyjnych.",
    "mainp2" =>
"Prawdopodobny termin zakończenia prac to 23:00 CET. W międzyczasie możesz skorzystać z jednego z poniższych mirrorów angielskiej Wikipedii:",
    "mainp3" =>
"Przepraszamy za wszelkie niedogodności.",
    "mainp4" =>
"Pozdrawiamy,<br />Administracja",
    "abouttitle" => "O Fundacji Wikimedia",
    "about" =>
"Wikimedia Foundation Inc. jest organizacją non-profit, której celem jest tworzenie i rozwijanie wielojęzycznych projektów opartych o technologię wiki, oraz udostępnianie ich zawartości całkowicie za darmo. Darowizny pozwalają Fundacji Wikimedia pozostać darmowym źródłem wiedzy dla wszystkich ludzi na świecie.",
    "projectstitle" => "Projekty Wikimedia",
    "projects" =>
    "Fundacja Wikimedia jest organizacją-matką jednych z największych publicznie edytowanych źródeł wiedzy na świecie, takich jak Wikipedia, jedna ze 100 najczęściej odwiedzanych stron internetowych na świecie. Inne projekty to Wikisłownik, wielojęzyczny słownik; Wikibooks, serwis z podręcznikami o otwartej treści; Wikicytaty, zbiór cytatów; Wikinews, darmowy serwis informacyjny; Wikisource, repozytorium tekstów źródłowych; oraz Wikimedia Commons, repozytorium mediów.",
    "credit" => "Szablon strony oparty na www.wikipedia.org stworzonej przez <a class=\"dark\" href=\"mailto:forseti@autograf.pl\">Forseti</a>.",
),

"da" => array(
    "maintitle" => "Vedligeholdelse af Wikimedias websted",
    "mainp1" => "Alle Wikimedia-projekter (Wikipedia, Wiktionary, Wikibooks, Wikiquote, Wikisource og Wikinews) er ikke tilgængelige lige nu på grund af arbejde med vores hardware.",
    "mainp2" => "Vi forventer ikke at være nede længere end til 23.00 dansk tid. I mellemtiden kan du bruge et af de websteder, der viser en kopi af vores indhold:",
    "mainp3" => "Vi beklager på forhånd de gener, dette måtte medføre for dig.",
    "mainp4" => "Med venlig hilsen,<br/>administratorerne.",
    "abouttitle" => "Om Wikimedia",
    "about" =>
"<strong>Wikimedia</strong> er fonden der står bag wikipedia, wiktionary og andre projekter. I Danmark står Wikimedia-fonden for netleksikonet <strong>wikipedia</strong> og netordbogen <strong>wiktionary</strong>.</p>

<p><strong>Den danske Wikipedia</strong> samler og formidler al den frie viden på dansk, så den kan indgå i den store krydsrefererede flersprogede encyklopædi. I forhold til den engelske Wikipedia kan den danske have en national vinkel, hvor også lokale artikler kan komme med. Artiklerne skrives af brugerne, og alle kan være med. Den danske udgave startede den 1. februar 2002. På det seneste er mange interessante, både danske og internationale, tiltag inden for arbejdet med encyklopædien vokset frem:</p>

<p><strong>Kvalitetsoffensiven - tjek på troværdigheden.</strong> For at imødegå kritik omkring troværdigheden af Wikipedias artikler har den danske Wikipedia netop indledt en målrettet kvalitetsoffensiv. Kvalitetsoffensiven organiserer samarbejdet, hvor mange bidragsydere hjælper med at sortere artiklerne, så tvivlsomme oplysninger, manglende kildeoplysninger og for dårligt materiale opdages og rettes til eller fjernes fra encyklopædien. Wikipedias politik, om at tilsigte en neutral synsvinkel, udgør en mærkbar forskel, der adskiller denne fra mange andre frie leksika.</p>

<p><strong>Commons - frit mediearkiv.</strong> Et grænseoverskridende wikiprojekt er 'Commons' (Fælleden), der er et frit mediearkiv på tværs af de forskellige sprogs wikipædier. Arkivet er først og fremmest oprettet for at støtte fildelingen mellem de forskellige wikiprojekter, men det er samtidig også et arkiv over frie og gratis illustrationer, fotos, kortmateriale og lydfiler, der er tilgængelige for alle. Commons sparer diskplads, og arbejdsgange. Når materiale er uploadet til Commons, er det straks klar til brug for samtlige sprog og projekter. Naturligvis kan materialet opdateres og forbedres efter samme princip som artiklerne i Wikipedia.</p>

<p><strong>Skanwiki - på tværs af sproggrænser.</strong> For at koordinere indsatsen og katalysere gode ideer og samarbejder på de forskellige skandinaviske sprog er forumet Skanwiki oprettet. Mange af de 25.000 danske artikler er krydsrefererede til tilsvarende artikler på de andre nordiske sprog. Igennem disse krydsreferencer er det nemt at finde beslægtede emner, som den danske udgave endnu ikke har et opslag om. Samlet har det skandinaviske sprogområde mere end 120.000 opslag, nogle er dog dubletter, der findes på mere end et sprog.</p>

<p><strong>Wikipedia - for børn og unge.</strong> Den danske Wikipedia er den første til at udbyde en sektion specielt tilrettelagt børn og unge. Her findes f.eks. initiativet Minibussen, en samling af små serier af artikler inden for bestemte emner. Desuden er der gæstebog, mulighed for at stille spørgsmål til Wikipedia, hjælp til opgaveskrivning samt mange enkle eksperimenter. Folkeskolelærere inviteres til at bruge vores materiale i undervisningen. For at lette dette, er det muligt at få opsat restriktioner for en specifik skoles adgang til Wikipedia, så det for en periode kun er muligt at læse artiklerne."

),

"eo" => array(
    "maintitle" => "Servilozorgado ĉe Wikimedia",
    "mainp1" => "Ĉiu projekto de Wikimedia (Vikipedio, Vikivortaro, Vikilibroj, Vikicitaro, Vikifonto, kaj Wikinews) estas neatingebla ĉi-momente, dum agordoj al la serviloj.",
    "mainp2" => "Ni atendas, ke ni finlaboros antaŭ horo 21:00 UTC. Intertempe, vi povas konsulti spegulojn de la enhavo de Vikipedio, ekzemple:",
    "mainp3" => "Ni petas pardonon por la malkonveno!",
    "mainp4" => "Ĝis la rereto!<br/>La Servilestroj.",
    "credit" => "Paĝaspekto bazita sur proponita www.wikipedia.org-ŝablono de <a class=\"dark\" href=\"mailto:forseti@autograf.pl\">Forseti</a>.",

    "abouttitle" => "Pri la Fondaĵo Wikimedia",
    "projectstitle" => "Projektoj de Wikimedia",
    "about" =>
"La Fondaĵo Wikimedia estas internacia neprofitcela organizaĵo dediĉita je
la kreskado, verkado, kaj distribuado de libere kopiebla, plurlingva eduka
enhavo, kaj provizado de la tuta enhavo de ĉiuj viki-projektoj al la publiko
senkoste. Wikimedia baziĝas sur donaĵoj de la publiko por atingi la celon
provizi liberan, senkostan scion al la tuta homaro.",
    "projects" =>
"La Fondaĵo Wikimedia estas gepatra organizaĵo de kelkaj el la plej grandaj
kolaborative redaktitaj edukadaj projektoj en la mondo, ĉefe el ili
Vikipedio, kiu estas unu el la 100 plej vizititaj paĝaaroj en la reto.
Inter la aliaj projektoj star Wikivortaro, plurlingva vortaro;
Vikilibroj, kolekto de eduklibroj liberaj;
Vikicitaro, kolekto de famaj citaĵoj;
Wikinews, libera novaĵejo;
Vikifonto, kolekto de historiaj dokumentoj;
kaj la Komunejo Wikimedia, kolekto de bildoj kaj sondosieroj."
),

"nl" => array(
    "maintitle" => "Wikimedia Site Onderhoud",
    "mainp1" => "Alle Wikimedia projecten (Wikipedia, Wiktionary, Wikibooks, Wikiquote, Wikisource, and Wikinews) zijn op dit moment offline vanwege een gepland onderhoud aan de hardware.",
    "mainp2" => "Naar verwachting zal het onderhoud om uiterlijk 23 uur Nederlandse/Belgische tijd afgerond zijn. In de tussentijd kunt u een van de volgende mirrors van wikipedia content bezoeken:",
    "mainp3" => "We bieden onze verontschuldigingen aan voor het ongemak dat dit met zich mee kan brengen.",
    "mainp4" => "Met vriendelijke groet,<br/>De beheerders",
    "credit" => "Page design based on www.wikipedia.org template by <a class=\"dark\" href=\"mailto:forseti@autograf.pl\">Forseti</a>.",

    "abouttitle" => "Over de Wikimedia Foundation",
    "projectstitle" => "Onze projecten",
    "about" =>
"De Wikimedia Foundation Inc. is een internationale organisatie zonder winstoogmerk met als doel het bevorderen van groei, ontwikkeling en distributie van een meertalige website waarvan de inhoud vrij beschikbaar is voor iedereen. Dit wordt gedaan door middel van op het Wikiconcept gebaseerde projecten. Wikimedia is afhankelijk van donaties van het publiek om zijn doelstelling om informatie gratis beschikbaar te maken voor iedereen in de wereld te bewerkstelligen.",
    "projects" =>
"De Wikimedia Foundation is de moederorganisatie van een aantal van de grootste publiek bewerkte referentie projecten in de wereld. Wikipedia een van de 100 meest bezochte websites in de wereld vormt een onderdeel van Wikimedia. Andere online projecten zijn Wiktionary, een meertalig woordenboek; Wikibooks, een verzameling van tekstboeken; Wikiquote, een verzameling van beroemde uitspraken; Wikinews, een gratis bron voor nieuws; Wikisource, een verzameling van publiek bronnenmateriaal en de Wikimedia commons, een verzameling van afbeeldingen, film- en geluidsbestanden."
),

"sk" => array(
	"maintitle" => "Údržba serverov Wikimédie",
	"mainp1" => "Všetky projekty Wikimédie (Wikipédia, Wikislovník, Wikiknihy, Wikicitáty, Wikizdroj, a Wikinoviny) sú momentálne nedostupné (offline), pretože prebieha údržba hardvéru.",
	"mainp2" => "Neočakávame, že táto údržba bude trvať dlhšie ako do 23:00 CEST. V tomto čase môžete použiť nasledujúce zrkadlá obsahu Wikipédie:",
	"mainp3" => "Ospravedlňujeme sa Vám za ťažkosti, ktoré Vám tento výpadok môže spôsobiť.",
	"mainp4" => "Vaši<br/>Administrátori",
	"abouttitle" => "O Nadácii Wikimédia",
	"about" => "Nadácia Wikimédia je medzinárodná nezisková organizácia, ktorá si kladie za ciele povzbudiť rast, rozvoj a distribúciu slobodného, multilinguálneho obsahu a zároveň poskytnúť plný (rozumej neskrátený) obsah týchto wiki projektov širokej verejnosti zadarmo. Wikimédia sa opiera o verejné dary a dotácie, aby dosiahla svoj cieľ - poskytnúť slobodne znalosti každej osobe na svete.",
	"projectstitle" => "Projekty Wikimédia",
	"projects" => "Nadácia Wikimédia je materská organizácia najväčšieho spoločne upravovaného zdroja vedomostí na svete, vrátane Wikipédie, jednej zo 100 najviac navštevovaných stránok na svete. Medzi ďalšie projekty patria Wikislovník, multilinguálny slovník; Wikiknihy, zbierka slobodných textov; Wikicitáty, studnica známych citátov; Wikinoviny, slobodný zdroj novín; Wikizdroj, zdroj primárnych materiálov; a Wikimédia Commons, úložisko obrázkov a zvukov.",
	"credit" => "Návrh stránky založený na www.wikipedia.org šablóne od <a class=\"dark\" href=\"mailto:forseti@autograf.pl\">Forseti-ho</a>",
),

"ru" => array(
	"maintitle" => "Обслуживание сайтов Викимедии",
	"mainp1" => "Все проекты фонда Викимедия (Википедия, Викисловарь,  Викиучебники, Викицитатник, Викисорс и Викиновости) в настоящее время в оффлайне для проведения обслуживания оборудования.",
	"mainp2" => "Скорее всего обслуживание будет длится до 21:00 UTC. Тем временем вы можете воспользоваться одним из следующих зеркал Википедии:",
	"mainp3" => "Просим прощения за причинённые неудобства.",
	"mainp4" => "С любовью,<br/> Администрация.",
	"abouttitle" => "О Фонде Викимедия",
	"about" => "The Wikimedia Foundation, Inc. - международная некоммерческая организация занимающаяся поддержкой развития, создания и распространения свободной многоязычной информации и предоставлением полного содержания этих проектов, основанных на вики, бесплатно. Викимедия полагается на пожертвования для достижения своей цели: предоставления свободного знания каждому человеку в мире.",
	"projectstitle" => "Проекты Викимедии",
	"projects" => "Фонд Викимедия является материнской организацией наибольших совместно-редактируемых справочных проектов в мире, включая Википедию, один из 100 наиболее посещаемых сайтов в мире. Другие онлайновые проекты включают Викисловарь, многоязычный толковый словарь, Викиучебники, коллекцию учебников со свободным содержимым, Викицитатник, коллекцию знаменитых цитат, Викиновости, источник свободных новостей, Wikisource, репозиторий первоисточников и Викисклад, репозиторий изображений и звуков.",
),

);

?>
