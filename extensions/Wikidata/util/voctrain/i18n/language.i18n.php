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
	"voctrain_Hello_World"=>"HELLO WIKI!",
	"voctrain_Permission_Denied"=>"Permission Denied",
	"voctrain_try_again_"=>"try again?",
	"voctrain_Action_unknown"=>"Action unknown",
	"voctrain_I_don_t_know_what_to_do_with___action__" =>
		"I don't know what to do with '%action'.",
	"voctrain_User_added" => "User added",
	"voctrain_Hello___username__welcome_to_the_omega_language_trainer" => 
		"Hello, %username, welcome to the omega language trainer",
	"voctrain_continue"=>"continue",
	"voctrain_hello__place"=>"hello there %place",
	"voctrain_bye"=>"goodbye",
	"voctrain_Set_up_your_exercise"=>"Set up your exercise",
	"voctrain_Number_of_questions"=>"Number of questions",
	"voctrain_Languages"=>"Languages",
	"voctrain_Please_specify_the_languages_you_want_to_test_in"=>"Please specify the languages you want to test in",
	"voctrain__eg__eng_for_English__deu_for_Deutch__German___"=>"(eg, eng for English, deu for Deutch (German)).",
	"voctrain_Depending_on_your_test_set__some_combinations_might_work_better_than_others_"=>"Depending on your test set, some combinations might work better than others.",
	"voctrain_Questions"=>"Questions",
	"voctrain_Answers"=>"Answers",
	"voctrain_start_exercise"=>"start exercise",
	"voctrain_collection"=>"collection",
	"voctrain_ISO_639_3_format"=>"ISO-639-3 format",
	"voctrain_There_are__questions_remaining_questions_remaining__out_of_a_total_of__questions_total_"=>"There are %questions_remaining questions remaining, out of a total of %questions_total.",
	"voctrain_Definition"=>"Definition",
	"voctrain_Dictionary_definition_to_help_you"=>"Dictionary definition to help you",
	"voctrain_Word"=>"Word",
	"voctrain_Please_type_your_answer_here"=>"Please type your answer here",
	"voctrain_submit_answer"=>"submit answer",
	"voctrain_peek"=>"peek",
	"voctrain_skip"=>"skip",
	"voctrain_I_know_it_do_not_ask_again"=>"I know it/do not ask again",
	"voctrain_abort_exercise"=>"abort exercise",
	"voctrain_list_answers"=>"list answers",
	"voctrain_Question"=>"Question",
	"voctrain_The_word_to_translate"=>"The word to translate",
	"voctrain_Answer"=>"Answer",
	"voctrain_one_of"=>"one of",
	"voctrain_list_of_questions_and_answers"=>"list of questions and answers",
	"voctrain_Answer_s_"=>"Answer(s)",
	"voctrain_logout"=>"logout",
	"voctrain_Powered_by"=>"Powered by",
	"voctrain_Omegawiki"=>"Omegawiki",
	"voctrain_Exercise_complete"=>"Exercise complete",
	"voctrain_Exercise_terminated"=>"Exercise terminated",
	"voctrain_Start_a_new_exercise"=>"Start a new exercise",
	"voctrain_User_name"=>"User name",
	"voctrain_Password"=>"Password",
	"voctrain_Login"=>"Login",
	"voctrain_Create_new_user"=>"Create new user",
	"voctrain_Switch_language"=>"Switch language",
	"voctrain_Language"=>"Language",
	"voctrain_Log_in"=>"Log in",
	"voctrain_Omegawiki_vocabulary_trainer"=>"Omegawiki vocabulary trainer"
);

/** Message documentation */

$messages["qqq"] = array(
	"voctrain_Hello_World"=>"Test message",
	"voctrain_Permission_Denied"=>"login: access is denied",
	"voctrain_try_again_"=>"An operation failed, link back to normal voctrainer (used in multiple locations)",
	"voctrain_Action_unknown"=>"Action unknown: Page title",
	"voctrain_I_don_t_know_what_to_do_with___action__" =>
		"Action unknown: body text of page (don't translate %action).",
	"voctrain_User_added" => "login: title of User added page",
	"voctrain_Hello___username__welcome_to_the_omega_language_trainer" => 
		"login: Greeting when user created. (Don't translate %username)",
	"voctrain_continue"=>"continue operation (used in multiple locations)",
	"voctrain_hello__place"=>"Test message (don't translate %place)",
	"voctrain_bye"=>"Test message",
	"voctrain_Set_up_your_exercise"=>"setup: Page title",
	"voctrain_Number_of_questions"=>"setup: subheading",
	"voctrain_Languages"=>"Languages",
	"voctrain_Please_specify_the_languages_you_want_to_test_in"=>"Setup:text the in refers to '...in iso-693-3 format'",
	"voctrain__eg__eng_for_English__deu_for_Deutch__German___"=>"Setup:text",
	"voctrain_Depending_on_your_test_set__some_combinations_might_work_better_than_others_"=>"setup:text",
	"voctrain_Questions"=>"Questions",
	"voctrain_Answers"=>"Answers",
	"voctrain_start_exercise"=>"button:start exercise",
	"voctrain_collection"=>"a wikidata collection",
	"voctrain_ISO_639_3_format"=>"ISO-639-3 format",
	"voctrain_There_are__questions_remaining_questions_remaining__out_of_a_total_of__questions_total_"=>"exercise: status at top of page (don't translate %questions_remaining and %questions_total)",
	"voctrain_Definition"=>"exercise: subheading",
	"voctrain_Dictionary_definition_to_help_you"=>"exercise: text",
	"voctrain_Word"=>"exercise: subheading",
	"voctrain_Please_type_your_answer_here"=>"exercise: text",
	"voctrain_submit_answer"=>"exercise: button",
	"voctrain_peek"=>"exercise: button",
	"voctrain_skip"=>"exercise: button",
	"voctrain_I_know_it_do_not_ask_again"=>"exercise: button",
	"voctrain_abort_exercise"=>"exercise: button",
	"voctrain_list_answers"=>"exercise: button",
	"voctrain_Question"=>"Question",
	"voctrain_The_word_to_translate"=>"The word to translate",
	"voctrain_Answer"=>"Answer",
	"voctrain_one_of"=>"one of",
	"voctrain_list_of_questions_and_answers"=>"list: heading",
	"voctrain_Answer_s_"=>"list: table header",
	"voctrain_logout"=>"logout button on all pages",
	"voctrain_Powered_by"=>"footer: Powered by",
	"voctrain_Omegawiki"=>"footer: Omegawiki",
	"voctrain_Exercise_complete"=>"end exercise: page heading",
	"voctrain_Exercise_terminated"=>"end exercise: page heading",
	"voctrain_Start_a_new_exercise"=>"end exercise: Start a new exercise",
	"voctrain_User_name"=>"login: User name",
	"voctrain_Password"=>"login: Password",
	"voctrain_Login"=>"login: button",
	"voctrain_Create_new_user"=>"login: button",
	"voctrain_Switch_language"=>"login: button",
	"voctrain_Language"=>"login: label",
	"voctrain_Log_in"=>"login: header",
	"voctrain_Omegawiki_vocabulary_trainer"=>"login: header"
);

/** Dutch */
$messages["nl"]=array(
	"voctrain_Hello_World"=>"HALLO WIKI!",
	"voctrain_Permission_Denied"=>"Toestemming Geweigerd",
	"voctrain_try_again_"=>"probeer opnieuw?",
	"voctrain_Action_unknown"=>"Aktie (action) onbekend",
	"voctrain_I_don_t_know_what_to_do_with___action__" =>
		"Ik weet niet wat ik doen moet met '%action'.",
	"voctrain_User_added" => "Gebruiker toegevoegd",
	"voctrain_Hello___username__welcome_to_the_omega_language_trainer" => 
		"Hallo, %username, welkom bij de omega taaltrainer",
	"voctrain_continue"=>"doorgaan",
	"voctrain_hello__place"=>"hallo daar %place",
	"voctrain_bye"=>"tot ziens",
	"voctrain_Set_up_your_exercise"=>"Stel uw oefening op",
	"voctrain_Number_of_questions"=>"Aantal vragen",
	"voctrain_Languages"=>"Talen",
	"voctrain_Please_specify_the_languages_you_want_to_test_in"=>"Geef aub de talen op waarin u wilt oefenen in",
	"voctrain__eg__eng_for_English__deu_for_Deutch__German___"=>"(bijv, eng voor English (Engels), deu voor Deutch (Duits)).",
	"voctrain_Depending_on_your_test_set__some_combinations_might_work_better_than_others_"=>"Afhankelijk van uw testset, werken sommige combinaties beter dan anderen.",
	"voctrain_Questions"=>"Vragen",
	"voctrain_Answers"=>"Antwoorden",
	"voctrain_start_exercise"=>"start oefening",
	"voctrain_collection"=>"collectie",
	"voctrain_ISO_639_3_format"=>"ISO-639-3 formaat",
	"voctrain_There_are__questions_remaining_questions_remaining__out_of_a_total_of__questions_total_"=>
		"Er zijn nog %questions_remaining vragen over, uit een totaal van %questions_total.",
	"voctrain_Definition"=>"Definitie",
	"voctrain_Dictionary_definition_to_help_you"=>"Woordenboek definitie om u te helpen",
	"voctrain_Word"=>"Woord",
	"voctrain_Please_type_your_answer_here"=>"Typ alstublieft hier uw antwoord.",
	"voctrain_submit_answer"=>"antwoord submitten",
	"voctrain_peek"=>"spiek",
	"voctrain_skip"=>"sla over",
	"voctrain_I_know_it_do_not_ask_again"=>"Ik weet dit antwoord/vraag niet nogmaals",
	"voctrain_abort_exercise"=>"Oefening afbreken",
	"voctrain_list_answers"=>"antwoordlijst",
	"voctrain_Question"=>"Vraag",
	"voctrain_The_word_to_translate"=>"Het te-vertalen woord",
	"voctrain_Answer"=>"Antwoord",
	"voctrain_one_of"=>"een van",
	"voctrain_list_of_questions_and_answers"=>"list van vragen en antwoorden",
	"voctrain_Answer_s_"=>"Antwoord(en)",
	"voctrain_logout"=>"uitloggen",
	"voctrain_Powered_by"=>"Aangedreven door",
	"voctrain_Omegawiki"=>"Omegawiki",
	"voctrain_Exercise_complete"=>"Oefening voltooid",
	"voctrain_Exercise_terminated"=>"Oefening afgebroken",
	"voctrain_Start_a_new_exercise"=>"Start een nieuwe oefening",
	"voctrain_User_name"=>"Gebruikersnaam",
	"voctrain_Password"=>"Wachtwoord",
	"voctrain_Login"=>"Inloggen",
	"voctrain_Create_new_user"=>"Nieuwe gebruiker aanmaken",
	"voctrain_Switch_language"=>"Wijzig taal",
	"voctrain_Language"=>"Taal"
);

?>
