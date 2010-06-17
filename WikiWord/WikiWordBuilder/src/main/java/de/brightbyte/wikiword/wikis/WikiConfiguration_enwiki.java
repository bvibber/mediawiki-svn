package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterValueExtractor;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasCategorySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.TitleSensor;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();
		
		//conceptNamespacecs.add(Namespace.PORTAL); //FIXME: how to add portal namespace?!
		
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
		
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("Birth[_ ]date|BrithDate|Bday|Dob|Age|Birth[-_ ]date[_ ]and[_ ]age|BirthDateAndAge|Bda", 3, true, true), "$2-$3-$4" ) );
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("Death[_ ]date[_ ]and[_ ]age|DeathDateAndAge|Dda", 6, true, true), "$2-$3-$4 &ndash; $5-$6-$7" ) );
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("Death[_ ]date|DeathDate|Dod", 3, true, true), "$2-$3-$4" ) );
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("(start|end|birth|death)-date|OldStyleDate", 1, true, true), "$1" ) );
		
		stripClutterManglers.add( new RegularExpressionMangler("^"+templatePatternString("wrapper", 0, true), "{|", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new RegularExpressionMangler("^"+templatePatternString("end|col-end", 0, true), "|}", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commons(?:-inline|[ _]left|show\\d)?", 1, true), "[[commons:$2]]"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commons[ _+]?cat(?:-inline|[ _]left|show\\d)?", 1, true), "[[commons:Category:$2]]"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("wikimedia", 1, true), "[[commons:$2]]")); //FIXME: named params: commons=
		//FIXME: Commonscat-N, Commons_cat_multi...
		stripClutterManglers.add( new RegularExpressionMangler("\\[\\[:commons:", "[[commons:", Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("Okina", 0, false), "\u02BB"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("0", 0, true), " "));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("ndash", 0, true), "&ndash;"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("\u00b7|moddot|dot", 0, false), "\u00b7"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("spaces", 1, true), " "));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("nbsp", 1, true), " "));
		
		stripClutterManglers.add( new RegularExpressionMangler(
			templatePattern(
				"fact|flag|Unreferenced(section)?|wrong[- \\w]*|cite[- \\w]*|" +
				"Portal|Stub[-\\w]*|commons|Cong(Bio2?|Links)|" +
				"Tnavbar|Navbox([ _]generic)?|redirect|pp-.*?|" +
				"ambox|wikify|pov|cleanup|globalize|split|current|issue|merge|" +
				"reflist|precision[-\\w\\d]+|nowrap[ _]begin|" +
				"Audio|\\w+[ _]icon|lang-\\w+|Flagicon|Flag|Flagcountry|" +
				"Main|ref|note|MSW3[_ ]\\w+|AUT|NLD" +
				"redirect" //maybe keep that? but we need this for the :'' stripping
			, 0, true), ""));

		//leading indented stuff
		stripClutterManglers.add( new RegularExpressionMangler(
				"^(\\s*[\r\n]+|:[^\r\n]*[\r\n]+)+", "", 0));
				
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("th|1?st|2?nd|3?rd|LORD|GOD", 0, false), "$1"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("resize|smaller", 1, false), "$2"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("resize|color|background_color", 2, true), "$3"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("chem", 0, true), "$2$3$4$5$6$7$8$9"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("city-state|city-region", 2, true), "$1, $2"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("nowrap(?:links)?|main|" +
					"en|de|it|fr|ArabDIN|ISOtranslit|polytonic|" +
					"IPA|IAST|Unicode|music|PIE|runic|semxlit|" +
					"ssub|sub|sup|smallsup|small|scinote|" +
					"smallcaps|allcaps|nocaps|" +
					"nihongo|Ivrit|Hebrew|my|formatnum|" +
					"tz|bday|EnglishDistrictPopulation|aut"
				, 1, true), "$2"));
		
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("km[ _]to[ _]mi", 1, true), "$1km"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("mi[ _]to[ _]km", 1, true), "$1mi"));

		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("audio|fontcolor", 2, true), "$3") );
		
		//HACK for implied sort key and categories for people
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("Lifetime|BD|BIRTH-DEATH-SORT", 3, true),
			    "{{DEFAULTSORT:$4}} [[Category:$2 births]] [[Category:$3 deaths]]"));
		
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("Lifetime|BD|BIRTH-DEATH-SORT", 3, true),
			    "{{DEFAULTSORT:$4}} [[Category:$2 births]] [[Category:$3 deaths]]"));
		
		//FIXME: handle {{sc}}, as in {{sc|B|ioy| C|asares}}
		
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("[Cc]onvert", 2, true), "$2 $3"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("[Ee]xp", 2, true), "$2^$3"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("[Ff]rac", 2, true), "$2/$3"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("[Mm]p", 2, true), "$2_$3"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("rtl-lang|lang|transl", 2, false), "$3"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("transl", 3, false), "$4"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("e|[Ee]sp", 1, true), "x10^$2"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("[Aa]uto[ _](.+?)", 2, true), "$1 $2"));
		
		//cruft regarding english/welsh census templates
		stripClutterManglers.add( new RegularExpressionMangler("rank\\s*=\\s*\\[\\[List[ _]of[ _][-\\w\\d\\s]+?\\|\\s*Ranked\\s+\\{\\{[-\\w\\d\\s]+?counties\\s*\\|\\s*\\w+=[-\\w\\d\\s]+\\}\\}\\]\\]", "", 0));

		//strip coodinate boxes only after template processing
		stripBoxesManglers.add( new RegularExpressionMangler(templatePattern("Coor([ _]\\w+)?", 0, true), ""));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PLACE, 
				"^(NUTS|Geography_of|Places|Villages|Towns|Cities|Captials?|Constituencies|Counties|Countries|Municipalities|Settlements|States|Provinces|Territories|Federal_states|Islands|Regions|Domains|Communes|Districts|Locations)" +
				       "(_|$)|_(places|villages|towns|cities|capitals|constituencies(_.*)?|counties|countries|municipalities|settlements|states|provinces|territories|federal_states|islands|regions|domains|communes|districts|locations)$", 0));
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Geobox|Infobox_(.*_)?([Ss]ettlement|[Cc]ountry|[Ss]tate|[Ll]ocation|[Cc]ounty|[Ll]ake)|.*_constituency_infobox)$", 0));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "^(Male|Female|People)_|_(people|men|women|births|deaths)$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Persondata|Lifetime|BD|BIRTH-DEATH-SORT|Infobox.*_(person|[aA]rtist|creator|writer|musician|biography|clergy|scientist))$", 0));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.NAME, "(^G|_g)iven_names$", 0));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.NAME, "(^S|_s)urnames$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.NAME, "Infobox_.*_name$", 0));
		conceptTypeSensors.add( new TitleSensor<ConceptType>(ConceptType.NAME, ".*\\(name\\)", 0));

		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Centuries"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Millennia"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.TIME, "Year_nav"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.TIME, "Decadebox"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.TIME, "Day"));

		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.WORK, "(^|_)(statue|work|play|album|song|painting|opera|novel|musical|novel|composition)s?(_|$)", Pattern.CASE_INSENSITIVE));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.WORK, "^Infobox_.*$", Pattern.CASE_INSENSITIVE, "artist"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.WORK, "^Infobox_.*$", Pattern.CASE_INSENSITIVE, "author"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.WORK, "^Infobox_.*$", Pattern.CASE_INSENSITIVE, "composer"));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.EVENT, "(^|_)(event|war|battle|siege|treaties|flood|famine|fire|conflict|crisis|disaster|riot|assasination|execution|crime)s?(_|$)", Pattern.CASE_INSENSITIVE));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.EVENT, "^Infobox_.*$", Pattern.CASE_INSENSITIVE, "date"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.EVENT, "^Infobox_.*$", Pattern.CASE_INSENSITIVE, "year"));

		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox"));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.NUMBER, "^Integers$|(^N|_n)umbers$", 0));
		conceptTypeSensors.add( new TitleSensor<ConceptType>(ConceptType.NUMBER, ".*\\(number\\)", 0));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.LIST, "^Year_nav_", 0));
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^Year_in_", 0));
		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Afd[mx]?|Vfd|Rfd|Prod|Copyvio|Delete|Del|Speedy|Db-[-\\w\\d]+)$", Pattern.CASE_INSENSITIVE));
		
		//resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.DISAMBIG, "^(Dis(amb(ig(uation)?)?)?)$|^(Geo|Hn|Hospital|POW|Road|School)dis$|^(Mountain|Ship)index$|^(Math)dab$", 0) );
		//resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.DISAMBIG, "^Disambiguation(_|$)", 0) );
		
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^Lists($|_of_)|_lists$", 0));
		resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.LIST, "List_of_-*|.*_list", 0));

		disambigStripSectionPattern = sectionPattern("See also", 0);  
		//FIXME: disambig pages marked with {{shipindex}} are tabular!
		
		aliasExtractors.add( new TemplateParameterValueExtractor("Catmore2?", 0, "1") ); //FIXME: testme
		aliasExtractors.add( new TemplateParameterValueExtractor("Catmore1", 0, "1").setManger( new RegularExpressionMangler("^.*\\[\\[ *(.+?) *(\\||\\]\\])", "$1", 0) ) );
		//TODO: Catmoresub
		
		useCategoryAliases = true; //enwiki uses plural category names. resolve them.
	}

}
