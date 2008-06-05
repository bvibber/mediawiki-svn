<?php

# messages for voctrain
# now mediawiki style -ish

# Words starting with '%' (ie %action, or %questions_total) are
# "variable names", for use by the program. Don't translate those!



$fallback=array(
	"en"=>false,
	"nl"=>"en"
);

$messages=array();

/** English */

$messages["en"] =array(
	"Hello World"=>"HELLO WIKI!",
	"Permission Denied"=>"Permission Denied",
	"try again?"=>"try again?",
	"Action unknown"=>"Action unknown",
	"I don't know what to do with '%action'." =>
		"I don't know what to do with '%action'.",
	"User added" => "User added",
	"Hello, %username, welcome to the omega language trainer" => 
		"Hello, %username, welcome to the omega language trainer",
	"continue"=>"continue",
	"hello %place"=>"hello there %place",
	"bye"=>"goodbye",
	"Set up your exercise"=>"Set up your exercise",
	"Number of questions"=>"Number of questions",
	"Languages"=>"Languages",
	"Please specify the languages you want to test in"=>"Please specify the languages you want to test in",
	"(eg, eng for English, deu for Deutch (German))."=>"(eg, eng for English, deu for Deutch (German)).",
	"Depending on your test set, some combinations might work better than others."=>"Depending on your test set, some combinations might work better than others.",
	"Questions"=>"Questions",
	"Answers"=>"Answers",
	"start exercise"=>"start exercise",
	"collection"=>"collection",
	"ISO-639-3 format"=>"ISO-639-3 format",
	"There are %questions_remaining questions remaining, out of a total of %questions_total."=>"There are %questions_remaining questions remaining, out of a total of %questions_total.",
	"Definition"=>"Definition",
	"Dictionary definition to help you"=>"Dictionary definition to help you",
	"Word"=>"Word",
	"Please type your answer here"=>"Please type your answer here",
	"submit answer"=>"submit answer",
	"peek"=>"peek",
	"skip"=>"skip",
	"I know it/do not ask again"=>"I know it/do not ask again",
	"abort exercise"=>"abort exercise",
	"list answers"=>"list answers",
	"Question"=>"Question",
	"The word to translate"=>"The word to translate",
	"Answer"=>"Answer",
	"one of"=>"one of",
	"list of questions and answers"=>"list of questions and answers",
	"Answer(s)"=>"Answer(s)",
	"logout"=>"logout",
	"Powered by"=>"Powered by",
	"Omegawiki"=>"Omegawiki",
	"Exercise complete"=>"Exercise complete",
	"Exercise terminated"=>"Exercise terminated",
	"Start a new exercise"=>"Start a new exercise",
	"User name"=>"User name",
	"Password"=>"Password",
	"Login"=>"Login",
	"Create new user"=>"Create new user",
	"Switch language"=>"Switch language",
	"Language"=>"Language",
	"Log in"=>"Log in",
	"Omegawiki vocabulary trainer"=>"Omegawiki vocabulary trainer"
);

/** Message documentation */

$messages["qqq"] = array(
	"Hello World"=>"Test message",
	"Permission Denied"=>"login: access is denied",
	"try again?"=>"An operation failed, link back to normal voctrainer (used in multiple locations)",
	"Action unknown"=>"Action unknown: Page title",
	"I don't know what to do with '%action'." =>
		"Action unknown: body text of page (don't translate %action).",
	"User added" => "login: title of User added page",
	"Hello, %username, welcome to the omega language trainer" => 
		"login: Greeting when user created. (Don't translate %username)",
	"continue"=>"continue operation (used in multiple locations)",
	"hello %place"=>"Test message (don't translate %place)",
	"bye"=>"Test message",
	"Set up your exercise"=>"setup: Page title",
	"Number of questions"=>"setup: subheading",
	"Languages"=>"Languages",
	"Please specify the languages you want to test in"=>"Setup:text the in refers to '...in iso-693-3 format'",
	"(eg, eng for English, deu for Deutch (German))."=>"Setup:text",
	"Depending on your test set, some combinations might work better than others."=>"setup:text",
	"Questions"=>"Questions",
	"Answers"=>"Answers",
	"start exercise"=>"button:start exercise",
	"collection"=>"a wikidata collection",
	"ISO-639-3 format"=>"ISO-639-3 format",
	"There are %questions_remaining questions remaining, out of a total of %questions_total."=>"exercise: status at top of page (don't translate %questions_remaining and %questions_total)",
	"Definition"=>"exercise: subheading",
	"Dictionary definition to help you"=>"exercise: text",
	"Word"=>"exercise: subheading",
	"Please type your answer here"=>"exercise: text",
	"submit answer"=>"exercise: button",
	"peek"=>"exercise: button",
	"skip"=>"exercise: button",
	"I know it/do not ask again"=>"exercise: button",
	"abort exercise"=>"exercise: button",
	"list answers"=>"exercise: button",
	"Question"=>"Question",
	"The word to translate"=>"The word to translate",
	"Answer"=>"Answer",
	"one of"=>"one of",
	"list of questions and answers"=>"list: heading",
	"Answer(s)"=>"list: table header",
	"logout"=>"logout button on all pages",
	"Powered by"=>"footer: Powered by",
	"Omegawiki"=>"footer: Omegawiki",
	"Exercise complete"=>"end exercise: page heading",
	"Exercise terminated"=>"end exercise: page heading",
	"Start a new exercise"=>"end exercise: Start a new exercise",
	"User name"=>"login: User name",
	"Password"=>"login: Password",
	"Login"=>"login: button",
	"Create new user"=>"login: button",
	"Switch language"=>"login: button",
	"Language"=>"login: label",
	"Log in"=>"login: header",
	"Omegawiki vocabulary trainer"=>"login: header"
);

/** Dutch */
$messages["nl"]=array(
	"Hello World"=>"HALLO WIKI!",
	"Permission Denied"=>"Toestemming Geweigerd",
	"try again?"=>"probeer opnieuw?",
	"Action unknown"=>"Aktie (action) onbekend",
	"I don't know what to do with '%action'." =>
		"Ik weet niet wat ik doen moet met '%action'.",
	"User added" => "Gebruiker toegevoegd",
	"Hello, %username, welcome to the omega language trainer" => 
		"Hallo, %username, welkom bij de omega taaltrainer",
	"continue"=>"doorgaan",
	"hello %place"=>"hallo daar %place",
	"bye"=>"tot ziens",
	"Set up your exercise"=>"Stel uw oefening op",
	"Number of questions"=>"Aantal vragen",
	"Languages"=>"Talen",
	"Please specify the languages you want to test in"=>"Geef aub de talen op waarin u wilt oefenen in",
	"(eg, eng for English, deu for Deutch (German))."=>"(bijv, eng voor English (Engels), deu voor Deutch (Duits)).",
	"Depending on your test set, some combinations might work better than others."=>"Afhankelijk van uw testset, werken sommige combinaties beter dan anderen.",
	"Questions"=>"Vragen",
	"Answers"=>"Antwoorden",
	"start exercise"=>"start oefening",
	"collection"=>"collectie",
	"ISO-639-3 format"=>"ISO-639-3 formaat",
	"There are %questions_remaining questions remaining, out of a total of %questions_total."=>
		"Er zikn nog %questions_remaining over, uit een totaal van %questions_total.",
	"Definition"=>"Definitie",
	"Dictionary definition to help you"=>"Woordenboek definitie om u te helpen",
	"Word"=>"Woord",
	"Please type your answer here"=>"Typ alstublieft hier uw antwoord.",
	"submit answer"=>"antwoord submitten",
	"peek"=>"spiek",
	"skip"=>"sla over",
	"I know it/do not ask again"=>"Ik weet dit antwoord/vraag niet nogmaals",
	"abort exercise"=>"Oefening afbreken",
	"list answers"=>"antwoordlijst",
	"Question"=>"Vraag",
	"The word to translate"=>"Het te-vertalen woord",
	"Answer"=>"Antwoord",
	"one of"=>"een van",
	"list of questions and answers"=>"list van vragen en antwoorden",
	"Answer(s)"=>"Antwoord(en)",
	"logout"=>"uitloggen",
	"Powered by"=>"Angedreven door",
	"Omegawiki"=>"Omegawiki",
	"Exercise complete"=>"Oefening voltooid",
	"Exercise terminated"=>"Oefening afgebroken",
	"Start a new exercise"=>"Start een nieuwe oefening",
	"User name"=>"Gebruikersnaam",
	"Password"=>"Wachtwoord",
	"Login"=>"Inloggen",
	"Create new user"=>"Nieuwe gebruiker aanmaken",
	"Switch language"=>"Wijzig taal",
	"Language"=>"Taal"
);

?>
