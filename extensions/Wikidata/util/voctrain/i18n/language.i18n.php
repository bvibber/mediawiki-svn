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
	"voctrain- Hello World"=>"HELLO WIKI!",
	"voctrain- Permission Denied"=>"Permission Denied",
	"voctrain- try again?"=>"try again?",
	"voctrain- Action unknown"=>"Action unknown",
	"voctrain- I don't know what to do with '%action'." =>
		"I don't know what to do with '%action'.",
	"voctrain- User added" => "User added",
	"voctrain- Hello, %username, welcome to the omega language trainer" => 
		"Hello, %username, welcome to the omega language trainer",
	"voctrain- continue"=>"continue",
	"voctrain- hello %place"=>"hello there %place",
	"voctrain- bye"=>"goodbye",
	"voctrain- Set up your exercise"=>"Set up your exercise",
	"voctrain- Number of questions"=>"Number of questions",
	"voctrain- Languages"=>"Languages",
	"voctrain- Please specify the languages you want to test in"=>"Please specify the languages you want to test in",
	"voctrain- (eg, eng for English, deu for Deutch (German))."=>"(eg, eng for English, deu for Deutch (German)).",
	"voctrain- Depending on your test set, some combinations might work better than others."=>"Depending on your test set, some combinations might work better than others.",
	"voctrain- Questions"=>"Questions",
	"voctrain- Answers"=>"Answers",
	"voctrain- start exercise"=>"start exercise",
	"voctrain- collection"=>"collection",
	"voctrain- ISO-639-3 format"=>"ISO-639-3 format",
	"voctrain- There are %questions_remaining questions remaining, out of a total of %questions_total."=>"There are %questions_remaining questions remaining, out of a total of %questions_total.",
	"voctrain- Definition"=>"Definition",
	"voctrain- Dictionary definition to help you"=>"Dictionary definition to help you",
	"voctrain- Word"=>"Word",
	"voctrain- Please type your answer here"=>"Please type your answer here",
	"voctrain- submit answer"=>"submit answer",
	"voctrain- peek"=>"peek",
	"voctrain- skip"=>"skip",
	"voctrain- I know it/do not ask again"=>"I know it/do not ask again",
	"voctrain- abort exercise"=>"abort exercise",
	"voctrain- list answers"=>"list answers",
	"voctrain- Question"=>"Question",
	"voctrain- The word to translate"=>"The word to translate",
	"voctrain- Answer"=>"Answer",
	"voctrain- one of"=>"one of",
	"voctrain- list of questions and answers"=>"list of questions and answers",
	"voctrain- Answer(s)"=>"Answer(s)",
	"voctrain- logout"=>"logout",
	"voctrain- Powered by"=>"Powered by",
	"voctrain- Omegawiki"=>"Omegawiki",
	"voctrain- Exercise complete"=>"Exercise complete",
	"voctrain- Exercise terminated"=>"Exercise terminated",
	"voctrain- Start a new exercise"=>"Start a new exercise",
	"voctrain- User name"=>"User name",
	"voctrain- Password"=>"Password",
	"voctrain- Login"=>"Login",
	"voctrain- Create new user"=>"Create new user",
	"voctrain- Switch language"=>"Switch language",
	"voctrain- Language"=>"Language",
	"voctrain- Log in"=>"Log in",
	"voctrain- Omegawiki vocabulary trainer"=>"Omegawiki vocabulary trainer"
);

/** Message documentation */

$messages["qqq"] = array(
	"voctrain- Hello World"=>"Test message",
	"voctrain- Permission Denied"=>"login: access is denied",
	"voctrain- try again?"=>"An operation failed, link back to normal voctrainer (used in multiple locations)",
	"voctrain- Action unknown"=>"Action unknown: Page title",
	"voctrain- I don't know what to do with '%action'." =>
		"Action unknown: body text of page (don't translate %action).",
	"voctrain- User added" => "login: title of User added page",
	"voctrain- Hello, %username, welcome to the omega language trainer" => 
		"login: Greeting when user created. (Don't translate %username)",
	"voctrain- continue"=>"continue operation (used in multiple locations)",
	"voctrain- hello %place"=>"Test message (don't translate %place)",
	"voctrain- bye"=>"Test message",
	"voctrain- Set up your exercise"=>"setup: Page title",
	"voctrain- Number of questions"=>"setup: subheading",
	"voctrain- Languages"=>"Languages",
	"voctrain- Please specify the languages you want to test in"=>"Setup:text the in refers to '...in iso-693-3 format'",
	"voctrain- (eg, eng for English, deu for Deutch (German))."=>"Setup:text",
	"voctrain- Depending on your test set, some combinations might work better than others."=>"setup:text",
	"voctrain- Questions"=>"Questions",
	"voctrain- Answers"=>"Answers",
	"voctrain- start exercise"=>"button:start exercise",
	"voctrain- collection"=>"a wikidata collection",
	"voctrain- ISO-639-3 format"=>"ISO-639-3 format",
	"voctrain- There are %questions_remaining questions remaining, out of a total of %questions_total."=>"exercise: status at top of page (don't translate %questions_remaining and %questions_total)",
	"voctrain- Definition"=>"exercise: subheading",
	"voctrain- Dictionary definition to help you"=>"exercise: text",
	"voctrain- Word"=>"exercise: subheading",
	"voctrain- Please type your answer here"=>"exercise: text",
	"voctrain- submit answer"=>"exercise: button",
	"voctrain- peek"=>"exercise: button",
	"voctrain- skip"=>"exercise: button",
	"voctrain- I know it/do not ask again"=>"exercise: button",
	"voctrain- abort exercise"=>"exercise: button",
	"voctrain- list answers"=>"exercise: button",
	"voctrain- Question"=>"Question",
	"voctrain- The word to translate"=>"The word to translate",
	"voctrain- Answer"=>"Answer",
	"voctrain- one of"=>"one of",
	"voctrain- list of questions and answers"=>"list: heading",
	"voctrain- Answer(s)"=>"list: table header",
	"voctrain- logout"=>"logout button on all pages",
	"voctrain- Powered by"=>"footer: Powered by",
	"voctrain- Omegawiki"=>"footer: Omegawiki",
	"voctrain- Exercise complete"=>"end exercise: page heading",
	"voctrain- Exercise terminated"=>"end exercise: page heading",
	"voctrain- Start a new exercise"=>"end exercise: Start a new exercise",
	"voctrain- User name"=>"login: User name",
	"voctrain- Password"=>"login: Password",
	"voctrain- Login"=>"login: button",
	"voctrain- Create new user"=>"login: button",
	"voctrain- Switch language"=>"login: button",
	"voctrain- Language"=>"login: label",
	"voctrain- Log in"=>"login: header",
	"voctrain- Omegawiki vocabulary trainer"=>"login: header"
);

/** Dutch */
$messages["nl"]=array(
	"voctrain- Hello World"=>"HALLO WIKI!",
	"voctrain- Permission Denied"=>"Toestemming Geweigerd",
	"voctrain- try again?"=>"probeer opnieuw?",
	"voctrain- Action unknown"=>"Aktie (action) onbekend",
	"voctrain- I don't know what to do with '%action'." =>
		"Ik weet niet wat ik doen moet met '%action'.",
	"User addvoctrain- ed" => "Gebruiker toegevoegd",
	"voctrain- Hello, %username, welcome to the omega language trainer" => 
		"Hallo, %username, welkom bij de omega taaltrainer",
	"voctrain- continue"=>"doorgaan",
	"voctrain- hello %place"=>"hallo daar %place",
	"voctrain- bye"=>"tot ziens",
	"voctrain- Set up your exercise"=>"Stel uw oefening op",
	"voctrain- Number of questions"=>"Aantal vragen",
	"voctrain- Languages"=>"Talen",
	"voctrain- Please specify the languages you want to test in"=>"Geef aub de talen op waarin u wilt oefenen in",
	"voctrain- (eg, eng for English, deu for Deutch (German))."=>"(bijv, eng voor English (Engels), deu voor Deutch (Duits)).",
	"voctrain- Depending on your test set, some combinations might work better than others."=>"Afhankelijk van uw testset, werken sommige combinaties beter dan anderen.",
	"voctrain- Questions"=>"Vragen",
	"voctrain- Answers"=>"Antwoorden",
	"voctrain- start exercise"=>"start oefening",
	"voctrain- collection"=>"collectie",
	"voctrain- ISO-639-3 format"=>"ISO-639-3 formaat",
	"voctrain- There are %questions_remaining questions remaining, out of a total of %questions_total."=>
		"Er zijn nog %questions_remaining over, uit een totaal van %questions_total.",
	"voctrain- Definition"=>"Definitie",
	"voctrain- Dictionary definition to help you"=>"Woordenboek definitie om u te helpen",
	"voctrain- Word"=>"Woord",
	"voctrain- Please type your answer here"=>"Typ alstublieft hier uw antwoord.",
	"voctrain- submit answer"=>"antwoord submitten",
	"voctrain- peek"=>"spiek",
	"voctrain- skip"=>"sla over",
	"voctrain- I know it/do not ask again"=>"Ik weet dit antwoord/vraag niet nogmaals",
	"voctrain- abort exercise"=>"Oefening afbreken",
	"voctrain- list answers"=>"antwoordlijst",
	"voctrain- Question"=>"Vraag",
	"voctrain- The word to translate"=>"Het te-vertalen woord",
	"voctrain- Answer"=>"Antwoord",
	"voctrain- one of"=>"een van",
	"voctrain- list of questions and answers"=>"list van vragen en antwoorden",
	"voctrain- Answer(s)"=>"Antwoord(en)",
	"voctrain- logout"=>"uitloggen",
	"voctrain- Powered by"=>"Angedreven door",
	"voctrain- Omegawiki"=>"Omegawiki",
	"voctrain- Exercise complete"=>"Oefening voltooid",
	"voctrain- Exercise terminated"=>"Oefening afgebroken",
	"voctrain- Start a new exercise"=>"Start een nieuwe oefening",
	"voctrain- User name"=>"Gebruikersnaam",
	"voctrain- Password"=>"Wachtwoord",
	"voctrain- Login"=>"Inloggen",
	"voctrain- Create new user"=>"Nieuwe gebruiker aanmaken",
	"voctrain- Switch language"=>"Wijzig taal",
	"voctrain- Language"=>"Taal"
);

?>
