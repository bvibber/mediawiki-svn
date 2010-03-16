package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;

import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestResult;

public class SpellCheckPerformance {
	
	/* public static void testSpellCheck(String dbname) throws IOException{
		IndexId iid = IndexId.get(dbname);
		SpellChecker sc = new SpellChecker(FSDirectory.getDirectory(iid.getSpellcheckPath(),false));
		IndexReader ir = IndexReader.open(iid.getTempPath());
		int good=0;
		int bad=0;
		long start = System.currentTimeMillis();
		for(String[] m : DATA){
			String[] res = sc.suggestSimilar(m[0],20,ir,"contents",true);
			if(res.length > 0 && m[1].equals(res[0]))
				good++;
			else{
				reportBad(m[0],m[1],res.length>0? res[0] : "");
				bad++;
			}
		}
		int total = good + bad;
		long delta = System.currentTimeMillis() - start;
		System.out.println("SpellCheck test ("+delta+"ms): good: "+good+" ("+((double)good/total*100)+"%), bad: "+bad+", total="+total);
	} */
	
	public static void testSuggest(String dbname) throws IOException{
		IndexId iid = IndexId.get(dbname);
		Suggest sc = new Suggest(iid);
		int good=0;
		int bad=0;
		long start = System.currentTimeMillis();
		for(String[] m : DATA){
			ArrayList<SuggestResult> res = sc.suggestWords(m[0],5,null);
			if(res.size() > 0){
				SuggestResult r = res.get(0);
				if(r.getWord().equals(m[1]))
					good++;
				else if(r.getWord().equals(m[0]) && res.size()>1 && res.get(1).getFrequency()>r.getFrequency() 
						&& res.get(1).getWord().equals(m[1]))
					good++;
				else if(r.getDist() > 1){
					SuggestResult split = sc.suggestSplit(m[0],null);
					if(split!=null && m[1].equals(split.getWord()))
						good++;
					else{
						reportBad(m[0],m[1],r.getWord());
						bad++;
					}
					
				}
				else{
					reportBad(m[0],m[1],r.getWord());
					bad++;
				}
			} else{
				reportBad(m[0],m[1],"");
				bad++;
			}
		}
		int total = good + bad;
		long delta = System.currentTimeMillis() - start;
		System.out.println("Suggest test ("+delta+"ms): good: "+good+" ("+((double)good/total*100)+"%), bad: "+bad+", total="+total);
	}
	
	public static void reportBad(String bad, String expected, String got){
		System.out.println("FOR ["+bad+"] EXPECTED: ["+expected+"], BUT GOT ["+got+"]");
	}
	
	public static void main(String[] args) throws IOException{
		Configuration.open();
		String dbname = "wikilucene";
		if(args.length==1)
			dbname = args[0];
		
		//testSpellCheck(dbname);
		testSuggest(dbname);
	}
	
	
   private static final String[][] DATA = { {
      "abilitey", "ability" }, {
      "abouy", "about" }, {
      "absorbtion", "absorption" }, {
      "accidently", "accidentally" }, {
      "accomodate", "accommodate" }, {
      "acommadate", "accommodate" }, {
      "acord", "accord" }, {
      "adultry", "adultery" }, {
      "aggresive", "aggressive" }, {
      "alchohol", "alcohol" }, {
      "alchoholic", "alcoholic" }, {
      "allieve", "alive" }, {
      "alot", "a lot" }, {
      "alright", "all right" }, {
      "amature", "amateur" }, {
      "ambivilant", "ambivalent" }, {
      "amification", "amplification" }, {
      "amourfous", "amorphous" }, {
      "annoint", "anoint" }, {
      "annonsment", "announcement" }, {
      "annoyting", "anting" }, {
      "annuncio", "announce" }, {
      "anonomy", "anatomy" }, {
      "anotomy", "anatomy" }, {
      "antidesestablishmentarianism", "antidisestablishmentarianism" }, {
      "antidisestablishmentarism", "antidisestablishmentarianism" }, {
      "anynomous", "anonymous" }, {
      "appelet", "applet" }, {
      "appreceiated", "appreciated" }, {
      "appresteate", "appreciate" }, {
      "aquantance", "acquaintance" }, {
      "aratictature", "architecture" }, {
      "archeype", "archetype" }, {
      "aricticure", "architecture" }, {
      "artic", "arctic" }, {
      "asentote", "asymptote" }, {
      "ast", "at" }, {
      "asterick", "asterisk" }, {
      "asymetric", "asymmetric" }, {
      "atentively", "attentively" }, {
      "autoamlly", "automatically" }, {
      "bankrot", "bankrupt" }, {
      "basicly", "basically" }, {
      "batallion", "battalion" }, {
      "bbrose", "browse" }, {
      "beauro", "bureau" }, {
      "beaurocracy", "bureaucracy" }, {
      "beggining", "beginning" }, {
      "beging", "beginning" }, {
      "behaviour", "behavior" }, {
      "beleive", "believe" }, {
      "belive", "believe" }, {
      "benidifs", "benefits" }, {
      "bigginging", "beginning" }, {
      "blait", "bleat" }, {
      "bouyant", "buoyant" }, {
      "boygot", "boycott" }, {
      "brocolli", "broccoli" }, {
      "buch", "bush" }, {
      "buder", "butter" }, {
      "budr", "butter" }, {
      "budter", "butter" }, {
      "buracracy", "bureaucracy" }, {
      "burracracy", "bureaucracy" }, {
      "buton", "button" }, {
      "byby", "by by" }, {
      "cauler", "caller" }, {
      "ceasar", "caesar" }, {
      "cemetary", "cemetery" }, {
      "changeing", "changing" }, {
      "cheet", "cheat" }, {
      "cicle", "circle" }, {
      "cimplicity", "simplicity" }, {
      "circumstaces", "circumstances" }, {
      "clob", "club" }, {
      "coaln", "colon" }, {
      "cocamena", "cockamamie" }, {
      "colleaque", "colleague" }, {
      "colloquilism", "colloquialism" }, {
      "columne", "column" }, {
      "comiler", "compiler" }, {
      "comitmment", "commitment" }, {
      "comitte", "committee" }, {
      "comittmen", "commitment" }, {
      "comittmend", "commitment" }, {
      "commerciasl", "commercials" }, {
      "commited", "committed" }, {
      "commitee", "committee" }, {
      "companys", "companies" }, {
      "compicated", "complicated" }, {
      "comupter", "computer" }, {
      "concensus", "consensus" }, {
      "confusionism", "confucianism" }, {
      "congradulations", "congratulations" }, {
      "conibation", "contribution" }, {
      "consident", "consistent" }, {
      "consident", "consonant" }, {
      "contast", "constant" }, {
      "contastant", "constant" }, {
      "contunie", "continue" }, {
      "cooly", "coolly" }, {
      "copping", "coping" }, {
      "cosmoplyton", "cosmopolitan" }, {
      "courst", "court" }, {
      "crasy", "crazy" }, {
      "cravets", "caveats" }, {
      "credetability", "credibility" }, {
      "criqitue", "critique" }, {
      "croke", "croak" }, {
      "crucifiction", "crucifixion" }, {
      "crusifed", "crucified" }, {
      "ctitique", "critique" }, {
      "cumba", "combo" }, {
      "custamisation", "customization" }, {
      "dag", "dog" }, {
      "daly", "daily" }, {
      "danguages", "dangerous" }, {
      "deaft", "draft" }, {
      "defence", "defense" }, {
      "defenly", "defiantly" }, {
      "definate", "definite" }, {
      "definately", "definitely" }, {
      "dependeble", "dependable" }, {
      "descrption", "description" }, {
      "descrptn", "description" }, {
      "desparate", "desperate" }, {
      "dessicate", "desiccate" }, {
      "destint", "distant" }, {
      "develepment", "developments" }, {
      "developement", "development" }, {
      "develpond", "development" }, {
      "devulge", "divulge" }, {
      "diagree", "disagree" }, {
      "dieties", "deities" }, {
      "dinasaur", "dinosaur" }, {
      "dinasour", "dinosaur" }, {
      "direcyly", "directly" }, {
      "discuess", "discuss" }, {
      "disect", "dissect" }, {
      "disippate", "dissipate" }, {
      "disition", "decision" }, {
      "dispair", "despair" }, {
      "disssicion", "discussion" }, {
      "distarct", "distract" }, {
      "distart", "distort" }, {
      "distroy", "destroy" }, {
      "documtations", "documentation" }, {
      "doenload", "download" }, {
      "dongle", "dangle" }, {
      "doog", "dog" }, {
      "dramaticly", "dramatically" }, {
      "drunkeness", "drunkenness" }, {
      "ductioneery", "dictionary" }, {
      "dur", "due" }, {
      "duren", "during" }, {
      "dymatic", "dynamic" }, {
      "dynaic", "dynamic" }, {
      "ecstacy", "ecstasy" }, {
      "efficat", "efficient" }, {
      "efficity", "efficacy" }, {
      "effots", "efforts" }, {
      "egsistence", "existence" }, {
      "eitiology", "etiology" }, {
      "elagent", "elegant" }, {
      "elligit", "elegant" }, {
      "embarass", "embarrass" }, {
      "embarassment", "embarrassment" }, {
      "embaress", "embarrass" }, {
      "encapsualtion", "encapsulation" }, {
      "encyclapidia", "encyclopedia" }, {
      "encyclopia", "encyclopedia" }, {
      "engins", "engine" }, {
      "enhence", "enhance" }, {
      "enligtment", "Enlightenment" }, {
      "ennuui", "ennui" }, {
      "enought", "enough" }, {
      "enventions", "inventions" }, {
      "envireminakl", "environmental" }, {
      "enviroment", "environment" }, {
      "epitomy", "epitome" }, {
      "equire", "acquire" }, {
      "errara", "error" }, {
      "erro", "error" }, {
      "evaualtion", "evaluation" }, {
      "evething", "everything" }, {
      "evtually", "eventually" }, {
      "excede", "exceed" }, {
      "excercise", "exercise" }, {
      "excpt", "except" }, {
      "excution", "execution" }, {
      "exhileration", "exhilaration" }, {
      "existance", "existence" }, {
      "expleyly", "explicitly" }, {
      "explity", "explicitly" }, {
      "expresso", "espresso" }, {
      "exspidient", "expedient" }, {
      "extions", "extensions" }, {
      "factontion", "factorization" }, {
      "failer", "failure" }, {
      "famdasy", "fantasy" }, {
      "faver", "favor" }, {
      "faxe", "fax" }, {
      "febuary", "february" }, {
      "firey", "fiery" }, {
      "fistival", "festival" }, {
      "flatterring", "flattering" }, {
      "fluk", "flux" }, {
      "flukse", "flux" }, {
      "fone", "phone" }, {
      "forsee", "foresee" }, {
      "frustartaion", "frustrating" }, {
      "fuction", "function" }, {
      "funetik", "phonetic" }, {
      "futs", "guts" }, {
      "gamne", "came" }, {
      "gaurd", "guard" }, {
      "generly", "generally" }, {
      "ghandi", "gandhi" }, {
      "goberment", "government" }, {
      "gobernement", "government" }, {
      "gobernment", "government" }, {
      "gotton", "gotten" }, {
      "gracefull", "graceful" }, {
      "gradualy", "gradually" }, {
      "grammer", "grammar" }, {
      "hallo", "hello" }, {
      "hapily", "happily" }, {
      "harrass", "harass" }, {
      "havne", "have" }, {
      "heellp", "help" }, {
      "heighth", "height" }, {
      "hellp", "help" }, {
      "helo", "hello" }, {
      "herlo", "hello" }, {
      "hifin", "hyphen" }, {
      "hifine", "hyphen" }, {
      "higer", "higher" }, {
      "hiphine", "hyphen" }, {
      "hippie", "hippy" }, {
      "hippopotamous", "hippopotamus" }, {
      "hlp", "help" }, {
      "hourse", "horse" }, {
      "houssing", "housing" }, {
      "howaver", "however" }, {
      "howver", "however" }, {
      "humaniti", "humanity" }, {
      "hyfin", "hyphen" }, {
      "hypotathes", "hypothesis" }, {
      "hypotathese", "hypothesis" }, {
      "hystrical", "hysterical" }, {
      "ident", "indent" }, {
      "illegitament", "illegitimate" }, {
      "imbed", "embed" }, {
      "imediaetly", "immediately" }, {
      "imfamy", "infamy" }, {
      "immenant", "immanent" }, {
      "implemtes", "implements" }, {
      "inadvertant", "inadvertent" }, {
      "incase", "in case" }, {
      "incedious", "insidious" }, {
      "incompleet", "incomplete" }, {
      "incomplot", "incomplete" }, {
      "inconvenant", "inconvenient" }, {
      "inconvience", "inconvenience" }, {
      "independant", "independent" }, {
      "independenent", "independent" }, {
      "indepnends", "independent" }, {
      "indepth", "in depth" }, {
      "indispensible", "indispensable" }, {
      "inefficite", "inefficient" }, {
      "inerface", "interface" }, {
      "infact", "in fact" }, {
      "influencial", "influential" }, {
      "inital", "initial" }, {
      "initinized", "initialized" }, {
      "initized", "initialized" }, {
      "innoculate", "inoculate" }, {
      "insistant", "insistent" }, {
      "insistenet", "insistent" }, {
      "instulation", "installation" }, {
      "intealignt", "intelligent" }, {
      "intejilent", "intelligent" }, {
      "intelegent", "intelligent" }, {
      "intelegnent", "intelligent" }, {
      "intelejent", "intelligent" }, {
      "inteligent", "intelligent" }, {
      "intelignt", "intelligent" }, {
      "intellagant", "intelligent" }, {
      "intellegent", "intelligent" }, {
      "intellegint", "intelligent" }, {
      "intellgnt", "intelligent" }, {
      "intensionality", "intensionally" }, {
      "interate", "iterate" }, {
      "internation", "international" }, {
      "interpretate", "interpret" }, {
      "interpretter", "interpreter" }, {
      "intertes", "interested" }, {
      "intertesd", "interested" }, {
      "invermeantial", "environmental" }, {
      "irregardless", "regardless" }, {
      "irresistable", "irresistible" }, {
      "irritible", "irritable" }, {
      "islams", "muslims" }, {
      "isotrop", "isotope" }, {
      "isreal", "israel" }, {
      "johhn", "john" }, {
      "judgement", "judgment" }, {
      "kippur", "kipper" }, {
      "knawing", "knowing" }, {
      "latext", "latest" }, {
      "leasve", "leave" }, {
      "lesure", "leisure" }, {
      "liasion", "lesion" }, {
      "liason", "liaison" }, {
      "libary", "library" }, {
      "likly", "likely" }, {
      "lilometer", "kilometer" }, {
      "liquify", "liquefy" }, {
      "lloyer", "layer" }, {
      "lossing", "losing" }, {
      "luser", "laser" }, {
      "maintanence", "maintenance" }, {
      "majaerly", "majority" }, {
      "majoraly", "majority" }, {
      "maks", "masks" }, {
      "mandelbrot", "Mandelbrot" }, {
      "mant", "want" }, {
      "marshall", "marshal" }, {
      "maxium", "maximum" }, {
      "meory", "memory" }, {
      "metter", "better" }, {
      "mic", "mike" }, {
      "midia", "media" }, {
      "millenium", "millennium" }, {
      "miniscule", "minuscule" }, {
      "minkay", "monkey" }, {
      "minum", "minimum" }, {
      "mischievious", "mischievous" }, {
      "misilous", "miscellaneous" }, {
      "momento", "memento" }, {
      "monkay", "monkey" }, {
      "mosaik", "mosaic" }, {
      "mostlikely", "most likely" }, {
      "mousr", "mouser" }, {
      "mroe", "more" }, {
      "neccessary", "necessary" }, {
      "necesary", "necessary" }, {
      "necesser", "necessary" }, {
      "neice", "niece" }, {
      "neighbour", "neighbor" }, {
      "nemonic", "pneumonic" }, {
      "nevade", "Nevada" }, {
      "nickleodeon", "nickelodeon" }, {
      "nieve", "naive" }, {
      "noone", "no one" }, {
      "noticably", "noticeably" }, {
      "notin", "not in" }, {
      "nozled", "nuzzled" }, {
      "objectsion", "objects" }, {
      "obsfuscate", "obfuscate" }, {
      "ocassion", "occasion" }, {
      "occuppied", "occupied" }, {
      "occurence", "occurrence" }, {
      "octagenarian", "octogenarian" }, {
      "olf", "old" }, {
      "opposim", "opossum" }, {
      "organise", "organize" }, {
      "organiz", "organize" }, {
      "orientate", "orient" }, {
      "oscilascope", "oscilloscope" }, {
      "oving", "moving" }, {
      "paramers", "parameters" }, {
      "parametic", "parameter" }, {
      "paranets", "parameters" }, {
      "partrucal", "particular" }, {
      "pataphysical", "metaphysical" }, {
      "patten", "pattern" }, {
      "permissable", "permissible" }, {
      "permition", "permission" }, {
      "permmasivie", "permissive" }, {
      "perogative", "prerogative" }, {
      "persue", "pursue" }, {
      "phantasia", "fantasia" }, {
      "phenominal", "phenomenal" }, {
      "picaresque", "picturesque" }, {
      "playwrite", "playwright" }, {
      "poeses", "poesies" }, {
      "polation", "politician" }, {
      "poligamy", "polygamy" }, {
      "politict", "politic" }, {
      "pollice", "police" }, {
      "polypropalene", "polypropylene" }, {
      "pompom", "pompon" }, {
      "possable", "possible" }, {
      "practicle", "practical" }, {
      "pragmaticism", "pragmatism" }, {
      "preceeding", "preceding" }, {
      "precion", "precision" }, {
      "precios", "precision" }, {
      "preemptory", "peremptory" }, {
      "prefices", "prefixes" }, {
      "prefixt", "prefixed" }, {
      "presbyterian", "Presbyterian" }, {
      "presue", "pursue" }, {
      "presued", "pursued" }, {
      "privielage", "privilege" }, {
      "priviledge", "privilege" }, {
      "proceedures", "procedures" }, {
      "pronensiation", "pronunciation" }, {
      "pronisation", "pronunciation" }, {
      "pronounciation", "pronunciation" }, {
      "properally", "properly" }, {
      "proplematic", "problematic" }, {
      "protray", "portray" }, {
      "pscolgst", "psychologist" }, {
      "psicolagest", "psychologist" }, {
      "psycolagest", "psychologist" }, {
      "quoz", "quiz" }, {
      "radious", "radius" }, {
      "ramplily", "rampantly" }, {
      "reccomend", "recommend" }, {
      "reccona", "raccoon" }, {
      "recieve", "receive" }, {
      "reconise", "recognize" }, {
      "rectangeles", "rectangle" }, {
      "redign", "redesign" }, {
      "reoccurring", "recurring" }, {
      "repitition", "repetition" }, {
      "replasments", "replacement" }, {
      "reposable", "responsible" }, {
      "reseblence", "resemblance" }, {
      "respct", "respect" }, {
      "respecally", "respectfully" }, {
      "roon", "room" }, {
      "rought", "roughly" }, {
      "rsx", "RSX" }, {
      "rudemtry", "rudimentary" }, {
      "runnung", "running" }, {
      "sacreligious", "sacrilegious" }, {
      "saftly", "safely" }, {
      "salut", "salute" }, {
      "satifly", "satisfy" }, {
      "scrabdle", "scrabble" }, {
      "searcheable", "searchable" }, {
      "secion", "section" }, {
      "seferal", "several" }, {
      "segements", "segments" }, {
      "sence", "sense" }, {
      "seperate", "separate" }, {
      "sherbert", "sherbet" }, {
      "sicolagest", "psychologist" }, {
      "sieze", "seize" }, {
      "simpfilty", "simplicity" }, {
      "simplye", "simply" }, {
      "singal", "signal" }, {
      "sitte", "site" }, {
      "situration", "situation" }, {
      "slyph", "sylph" }, {
      "smil", "smile" }, {
      "snuck", "sneaked" }, {
      "sometmes", "sometimes" }, {
      "soonec", "sonic" }, {
      "specificialy", "specifically" }, {
      "spel", "spell" }, {
      "spoak", "spoke" }, {
      "sponsered", "sponsored" }, {
      "stering", "steering" }, {
      "straightjacket", "straitjacket" }, {
      "stumach", "stomach" }, {
      "stutent", "student" }, {
      "styleguide", "style guide" }, {
      "subisitions", "substitutions" }, {
      "subjecribed", "subscribed" }, {
      "subpena", "subpoena" }, {
      "substations", "substitutions" }, {
      "suger", "sugar" }, {
      "supercede", "supersede" }, {
      "superfulous", "superfluous" }, {
      "susan", "Susan" }, {
      "swimwear", "swim wear" }, {
      "syncorization", "synchronization" }, {
      "taff", "tough" }, {
      "taht", "that" }, {
      "tattos", "tattoos" }, {
      "techniquely", "technically" }, {
      "teh", "the" }, {
      "tem", "team" }, {
      "teo", "two" }, {
      "teridical", "theoretical" }, {
      "tesst", "test" }, {
      "tets", "tests" }, {
      "thanot", "than or" }, {
      "theirselves", "themselves" }, {
      "theridically", "theoretical" }, {
      "thredically", "theoretically" }, {
      "thruout", "throughout" }, {
      "ths", "this" }, {
      "titalate", "titillate" }, {
      "tobagan", "tobaggon" }, {
      "tommorrow", "tomorrow" }, {
      "tomorow", "tomorrow" }, {
      "tradegy", "tragedy" }, {
      "trubbel", "trouble" }, {
      "ttest", "test" }, {
      "tunnellike", "tunnel like" }, {
      "tured", "turned" }, {
      "tyrrany", "tyranny" }, {
      "unatourral", "unnatural" }, {
      "unaturral", "unnatural" }, {
      "unconisitional", "unconstitutional" }, {
      "unconscience", "unconscious" }, {
      "underladder", "under ladder" }, {
      "unentelegible", "unintelligible" }, {
      "unfortunently", "unfortunately" }, {
      "unnaturral", "unnatural" }, {
      "upcast", "up cast" }, {
      "upmost", "utmost" }, {
      "uranisium", "uranium" }, {
      "verison", "version" }, {
      "vinagarette", "vinaigrette" }, {
      "volumptuous", "voluptuous" }, {
      "volunteerism", "voluntarism" }, {
      "volye", "volley" }, {
      "wadting", "wasting" }, {
      "waite", "wait" }, {
      "wan't", "won't" }, {
      "warloord", "warlord" }, {
      "whaaat", "what" }, {
      "whard", "ward" }, {
      "whimp", "wimp" }, {
      "wicken", "weaken" }, {
      "wierd", "weird" }, {
      "wrank", "rank" }, {
      "writeen", "righten" }, {
      "writting", "writing" }, {
      "wundeews", "windows" }, {
      "yeild", "yield" }, {
      "youe", "your" }
};
}
