package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();
		
		/*
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^\\{\\{(wrapper)\\s*(\\|[^\\}\\r\\n]*)?\\}\\}\\s*$", "{|", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^\\{\\{(end|col-end)\\s*\\}\\}\\s*$", "|}", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*Okina\\s*\\}\\}", "\u02BB", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(\u00b7|moddot|dot)\\s*\\}\\}", "\u00b7", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{spaces(\\s*\\|.*?)\\}\\}", " ", Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^\\{\\{(" +
				"fact|Unreferenced(section)?|wrong[-\\w]*|cite[-\\w]*|" +
				"Portal|Stub[-\\w]*|commons|Cong(Bio2?|Links)|" +
				"Tnavbar|Navbox([ _]generic)?|redirect|pp-.*?|" +
				"ambox|wikify|pov|cleanup|globalize|split|current|issue|merge|" +
				"Coor([ _]\\w+)?|Coord|reflist|precision[-\\w\\d]+|nowrap[ _]begin|" +
				"Audio|\\w+[ _]icon|lang-\\w+|Flagicon|" +
				"Main|" +
				"redirect" + //maybe keep that? but we need this for the :'' stripping
				")\\s*(\\|[^\\{\\}\\r\\n]*)?\\}\\}", "", Pattern.DOTALL | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^(\\s*[\r\n]+|:[^\r\n]*[\r\n]+)+", "", 0));
				
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*(" +
				"th|1?st|2?nd|3?rd|LORD|GOD" +
				")\\s*\\}\\}", "$1", Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:" +
				"nowrap(?:links)?|main|" +
				"en|de|it|fr|ArabDIN|ISOtranslit|polytonic|" +
				"IPA|IAST|Unicode|music|PIE|runic|semxlit|" +
				"ssub|sub|sup|smallsup|small|" +
				"smallcaps|allcaps|nocaps|" +
				"nihongo|Ivrit|Hebrew" +
				")\\s*\\|\\s*([^|}]+).*?\\}\\}", "$1", Pattern.DOTALL | Pattern.CASE_INSENSITIVE));
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:" +
				"audio" +
				")\\s*\\|(?:[^|}]*)\\|\\s*(.*?)\\s*\\}\\}", "$1", Pattern.DOTALL | Pattern.CASE_INSENSITIVE));
		
		//FIXME: handle {{sc}}, as in {{sc|B|ioy| C|asares}}
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:convert)\\s*\\|\\s*(.*?)\\|(.*?)\\|(.*?)\\}\\}", "$1 $2", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:exp)\\s*\\|\\s*(.*?)\\s*\\|\\s*(.*?)\\s*\\}\\}", "$1^$2", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:frac)\\s*\\|\\s*(.*?)\\s*\\|\\s*(.*?)\\s*\\}\\}", "$1/$2", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:mp)\\s*\\|\\s*(.*?)\\s*\\|\\s*(.*?)\\s*\\}\\}", "$1_$2", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{(?:lang|transl).*?\\|\\s*([^|]+?)\\s*\\}\\}", "$1", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*e\\s*\\|\\s*(.*?)\\s*\\}\\}", "x10$1", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*Auto[ _](.+?)\\s*\\|\\s*(.*?)(\\|.*?)?\\s*\\}\\}", "$1 $2", Pattern.CASE_INSENSITIVE));
		*/
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^"+templatePatternString("wrapper", 0, true), "{|", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^"+templatePatternString("end|col-end", 0, true), "|}", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("Okina", 0, false), "\u02BB"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("\u00b7|moddot|dot", 0, false), "\u00b7"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("spaces", 1, true), " "));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
			templatePattern(
				"fact|Unreferenced(section)?|wrong[-\\w]*|cite[-\\w]*|" +
				"Portal|Stub[-\\w]*|commons|Cong(Bio2?|Links)|" +
				"Tnavbar|Navbox([ _]generic)?|redirect|pp-.*?|" +
				"ambox|wikify|pov|cleanup|globalize|split|current|issue|merge|" +
				"Coor([ _]\\w+)?|Coord|reflist|precision[-\\w\\d]+|nowrap[ _]begin|" +
				"Audio|\\w+[ _]icon|lang-\\w+|Flagicon|" +
				"Main|" +
				"redirect" //maybe keep that? but we need this for the :'' stripping
			, 0, true), ""));

		//leading indented stuff
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				"^(\\s*[\r\n]+|:[^\r\n]*[\r\n]+)+", "", 0));
				
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("th|1?st|2?nd|3?rd|LORD|GOD", 0, false), "$1"));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("nowrap(?:links)?|main|" +
					"en|de|it|fr|ArabDIN|ISOtranslit|polytonic|" +
					"IPA|IAST|Unicode|music|PIE|runic|semxlit|" +
					"ssub|sub|sup|smallsup|small|" +
					"smallcaps|allcaps|nocaps|" +
					"nihongo|Ivrit|Hebrew"
				, 1, true), "$2"));
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("audio", 2, true), "$3") );
		
		//HACK for implied sort key and categories for people
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("Lifetime|BD|BIRTH-DEATH-SORT", 3, true),
			    "{{DEFAULTSORT:$4}} [[Category:$2 births]] [[Category:$3 deaths]]"));
		
		//FIXME: handle {{sc}}, as in {{sc|B|ioy| C|asares}}
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("convert", 2, true), "$2 $3"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("exp", 2, true), "$2^$3"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("frac", 2, true), "$2/$3"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("mp", 2, true), "$2_$3"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("lang|transl", 2, true), "$2"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("e", 2, true), "x10^$2"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("Auto[ _](.+?)", 2, true), "$1 $2"));
		
		//cruft regarding english/welsh census templates
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("rank\\s*=\\s*\\[\\[List[ _]of[ _][-\\w\\d\\s]+?\\|\\s*Ranked\\s+\\{\\{[-\\w\\d\\s]+?counties\\s*\\|\\s*\\w+=[-\\w\\d\\s]+\\}\\}\\]\\]", "", 0));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PLACE, 
				"^(Geography_of|Places|Villages|Towns|Cities|Counties|Countries|Municipalities|States|Provinces|Territories|Federal_states|Islands|Regions|Domains|Communes|Districts)" +
				       "(_|$)|_(places|villages|towns|cities|counties|countries|municipalities|states|provinces|territories|federal_states|islands|regions|domains|communes|districts)$", 0));
		
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.PLACE, "Geobox", null));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PERSON, "^(Male|Female|People)_|_(people|man|woman|birth|death)$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PERSON, "^(Persondata|Lifetime|BD|BIRTH-DEATH-SORT)$", 0));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Given_names"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Surnames"));
		conceptTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ConceptType.NAME, ".*\\(name\\)", 0));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Centuries"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Millennia"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.TIME, "Year_nav", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.TIME, "Decadebox", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.TIME, "Day", null));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.LIFEFORM, "Taxobox", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.NUMBER, "^Integers$|(^N|_n)umbers$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ConceptType.NUMBER, ".*\\(number\\)", 0));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.LIST, "^Year_nav_", 0));
		resourceTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ResourceType.LIST, "^Year_in_", 0));
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.BAD, "^(Afd[mx]?|Vfd|Rfd|Prod|Copyvio|Delete|Del|Speedy|Db-[-\\w\\d]+)$", Pattern.CASE_INSENSITIVE));
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.DISAMBIG, "^(Dis(amb(ig(uation)?)?)?)$|^(Geo|Hn|Hospital|POW|Road|School)dis$|^(Mountain|Ship)index$|^(Math)dab$", 0) );
		resourceTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ResourceType.DISAMBIG, "^Disambiguation(_|$)", 0) );
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ResourceType.LIST, "^Lists($|_of_)|_lists$", 0));
		resourceTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ResourceType.LIST, "List_of_-*|.*_list", 0));

		disambigStripSectionPattern = sectionPattern("See also", 0);  
		//FIXME: disambig pages marked with {{shipindex}} are tabular!
		
		useCategoryAliases = true; //enwiki uses plural category names. resolve them.
	}

}
