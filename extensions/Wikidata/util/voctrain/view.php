<?php

require_once("settings.php");

/** ~MVC:  Generate html for user interface */
class View {

	public $model;
	
	/** print everyones favorite friendly message! */
	public function hello() {
		print "<h1>HELLO WIKI!</h1>";
	}
	
	/** @deprecated */
	public function permissionDenied() {
		print "<h1>Permission Denied</h1>";
		print "<a href='trainer.php'>Try again?</a>";
	}

	/** an action was provided, but we've never heard of it 
	    "?action=UnintelligibleGibberish" */
	public function actionUnknown($action){
		print "<h1>Action unknown</h1>";
		print "I don't know what to do with '$action'";
		print "<a href='trainer.php'>Try again?</a>";
	}

	/** say hello to the new user */
	public function userAdded($username) {
		print "<h1>User added</h1>";
		print "<p>Hello, $username, welcome to the omega language trainer</p>";
		print "<p><a href='trainer.php'>continue</a></p>";
	}

	/** Big form, allows user to set parameters for their next exercise */
	public function exercise_setup($collectionList) {
		print "
		<form method='post' action='trainer.php'>
		<h2> choose collection </h2>
		".$this->collectionTable($collectionList)."

		<h2>number of questions</h2>
		<ul><!-- this needs css, or will look fugly -->
		<li><input type='radio' value='10' name='exercise_size' />10</li>
		<li><input type='radio' value='25' name='exercise_size' checked />25</li>
		<li><input type='radio' value='50' name='exercise_size'/>50</li>
		<li><input type='radio' value='100' name='exercise_size'/>100</li>
		<li><input type='text' size='4' value='' name='exercise_size_other'>other</li>
		</ul>
		<h2>languages</h2>
		<!-- should be a dropdown, perhaps -->
		Please specify the languages you want to test in <a href='http://www.sil.org/ISO639-3/codes.asp'>ISO-639-3 format</a>. (eg, eng for English, deu for Deutch (German)). Depending on your test set, some combinations might work better than others.
		<ul>
		<li>Questions: <input type='text' value='eng' name='questionLanguage'/></li>
		<li>Answers: <input type='text' value='deu' name='answerLanguage'/></li>
		</ul>
		<hr/>
		<input type='submit' value='start exercise'/> 
		";
	}

	public function collectionTable($collectionList) {
		$table="<table>\n";
		$table.="
			<tr>
				<th>collection</th>
				<th>max number of questions</th>
			</tr>\n";
		foreach ($collectionList as $collection) {
			$table .= "
				<tr>
					<td> 
						<input 
							type='radio' 
							value='".$collection["id"]."' 
							name='collection'";

			global $default_collection; # can be set in settings.php
			if ((int) $collection["id"] == $default_collection) { # check-mark default collection
				$table.= "
							checked";
			}

			$table.= "
						/> 
					".$collection->name." </td>
					<th> ".$collection->count." </th>
				</tr>
			";
		}
		$table.= "</table>\n";
		return $table;
	}

	/** ask a question */
	public function ask($exercise) { #throws NoMoreQuestionsException
		$question=$exercise->nextQuestion();
		$definitions=implode(",<br/>",$question->getQuestionDefinitions());
		$words=implode(", ",$question->getQuestionWords());
		$questionDmid=$question->getDmid();
		$questions_remaining=$exercise->countQuestionsRemaining();
		$questions_total=$exercise->countQuestionsTotal();
		

		print"<form method='post' action='?action=run_exercise'>
			There are $questions_remaining questions remaining, out of a total of $questions_total.
			<h2>Question</h2>
			<hr>
			<h3>Definition</h3>
			<small><i>Dictionary definition to help you</i></small>
			<p>
			$definitions 
			</p>
			<hr>
			<h3>Word</h3>
			<small><i>The word to translate</i></small>
			<p>
			$words
			</p>
			<hr>
			<input type='hidden' name='questionDmid' value='$questionDmid'/>
			<h3>Answer</h3>
			<small><i>Please type your answer here</i></small>
			<p>
			<b>Answer</b>: <input type='text' value='' name='userAnswer' />
			</p>
			<input type='submit' value='submit answer' name='submitAnswer' />
			<input type='submit' value='peek' name='peek' />
			<input type='submit' value='skip ->' name='skip' />
			<input type='submit' value='abort exercise' name='abort' />
			</form>
		";
	}

	/** Show the answer to a question */
	public function answer($question, $correct) {
		$definitions=implode(",<br/>",$question->getQuestionDefinitions());
		$words=implode(", ",$question->getQuestionWords());
		$answers=implode(", ",$question->getAnswers());

		#we should make a nice css for this
		$result="";
		if ($correct===true) {
			$result="<span style='color:#00DD00'>CORRECT</span>";
		} elseif ($correct===false) {
			$result="<span style='color:#DD0000'>WRONG</span>";
		} elseif ($correct===null) {
			$result="PEEK";
		} else {
			throw new Exception("unexpected outcome from question");
		}

		print"<form method='post' action='?action=run_exercise'>
			<h2>$result</h2>
			Definitions: $definitions 
			<hr>
			Question: $words
			<hr>
			Answer: $answers
			<hr>
			<input type='hidden' name='questionDmid' value='$questionDmid'/>
			<input type='submit' value='continue ->' name='continue' />
			</form>
		";
	}

	/** show a nice final table on completion of the exercise */
	public function complete($exercise) {
		
		print "<h1> Exercise complete </h1>";
		print "<table>";
		$exercise->rewind();
		foreach ($exercise as $question) {
			print "<tr>";
			print "<td>".implode(",<br/>",$question->getQuestionDefinitions())."</td>";
			print "<td>".implode(", ",$question->getQuestionWords())."</td>";
			print "<td>".implode(", ",$question->getAnswers())."</td>";
			print "</tr>";
		}
		print "</table>";
		print "<a href='?action=create_exercise'>Start a new exercise</a>";
	}


	/** Aborted the exercise. We don't show the nice table like in
	 * complete(), because the lazy fetcher might take a long time to get
	 * all the untouched questions 
	*/
	public function aborted() {
		print "<h1> Exercise terminated </h1>\n";
		print "<a href='?action=create_exercise'>Start a new exercise</a>";
	}

	/** fugly function to print HTML header */
	public function header() {
print'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />



                <link rel="stylesheet" type="text/css" media="screen, projection" href="../ow/styles.css" />
                <link rel="stylesheet" type="text/css" media="screen, projection" href="http://www.omegawiki.org/extensions/Wikidata/OmegaWiki/tables.css" />
                <link rel="shortcut icon" href="http://www.omegawiki.org/favicon.ico" />
                <title>OmegaWiki gateway</title>
                <style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "http://www.omegawiki.org/skins/monobook/main.css?55"; /*]]>*/</style>
                <link rel="stylesheet" type="text/css" media="print" href="http://www.omegawiki.org/skins/common/commonPrint.css?55" />
                <link rel="stylesheet" type="text/css" media="handheld" href="http://www.omegawiki.org/skins/monobook/handheld.css?55" />

</head>
<body>
<a href="?action=logout">logout</a>
';
	}

	/** fugly function to print HTML footer */
	public function footer() {
print'
</body>
</html>
';
	}


}

?>
