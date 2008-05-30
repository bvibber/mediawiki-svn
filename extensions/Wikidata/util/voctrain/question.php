<?php

class MissingWordsException extends Exception{};


class Question {

	private $questionNode;
	private $questionDoc;
	private $dmid;
	private $exercise;
	private $questionLanguage;
	private $answerLanguage;

	/**constructor
	* @param $exercise	the exercuse this question is associated with
	*			(for easy callback).
	* $param $questionNode	a raw DOMElement with our defined meaning
	*/
	public function __construct($exercise, $questionNode) {
		$this->exercise=$exercise;
		
		$doc=new DOMDocument;
		$questionNode=$doc->importNode($questionNode,true);
		$doc->appendChild($questionNode);
		$this->questionNode=$questionNode;
		$this->questionDoc=$doc;
		$this->dmid=(int) $questionNode->getAttribute("defined-meaning-id");
	}
	
	/** returns the dmid */
	public function getDmid() {
		return $this->dmid;
	}
	
	public function getQuestionLanguage() {
		return $this->questionLanguage;
	}

	public function setQuestionLanguage($questionLanguage) {
		$this->questionLanguage=$questionLanguage;
	}

	public function getAnswerLanguage() {
		return $this->answerLanguage;
	}

	public function setAnswerLanguage($answerLanguage) {
		$this->answerLanguage=$answerLanguage;
	}

	/** a nice full definition of the word, (big clue) to help beginning students. */
	public function getQuestionDefinitions($language=null) {
		if ($language===null) $language=$this->getQuestionLanguage();
		if ($language===null) throw new Exception("No language specified");

		$xpath=new domxpath($this->questionDoc);
		$nodes=$xpath->query("//translated-text-list/translated-text[@language=\"$language\"]");
		$definitions=array(); #_typically_ there's only  one, but this is not enforced anywhere, afaik.
		foreach ($nodes as $node) {
			$definitions[]=$node->textContent;
		}
		return $definitions;

		
	}

	/** Get just the word */
	public function getQuestionWords($language=null) {
		if ($language===null) $language=$this->getQuestionLanguage();
		if ($language===null) throw new Exception("No language specified");

		return $this->getWordsForLanguage($language);
	}

	/** utility function, returns an array of words in the particular language for this question's defined meaning */
	public function getWordsForLanguage($language=null) {
		if ($language===null) throw new Exception("No language specified");
		$xpath=new domxpath($this->questionDoc);
		$nodes=$xpath->query("//synonyms-translations-list/synonyms-translations/expression[@language=\"$language\"]");
		$words=array(); 
		foreach ($nodes as $node) {
			$words[]=$node->textContent;
		}
		if (count($words)==0)
			throw new MissingWordsException("no words found in this context for language $language");
		return $words;
	}


	/** try some stuff, throws exceptions if it fails.
	 * currently throws MissingWordsException if something goes wrong with question or answer */
	public function selfCheck() {
		$this->getQuestionWords();
		$this->getAnswers();
	}

	/** return set of synonyms that are all valid answers (a world first?) */
	public function getAnswers($language=null) {
		if ($language===null) $language=$this->getAnswerLanguage();
		if ($language===null) throw new Exception("No language specified");

		return $this->getWordsForLanguage($language);

	}
	
	/** just check the answer, returns true if correct, false if answered wrong */
	public function checkAnswer($answer, $language=null) {
		if ($language===null) $language=$this->getAnswerLanguage();
		if ($language===null) throw new Exception("No language specified");

		$answers=$this->getAnswers($language);
		return in_array($answer, $answers);
	
	}

	/** Your one stop shop. check the answer, and submit it to the excersize node. */
	public function submitAnswer($answer, $language=null) {
		if ($language===null) $language=$this->getAnswerLanguage();
		if ($language===null) throw new Exception("No language specified");

		$correct=$this->checkAnswer($answer,$language);
		if ($correct) {
			$this->exercise->AnswerCorrect($this);
		} else {
			$this->exercise->AnswerIncorrect($this);
		}
		return $correct;
	}

}


?>
