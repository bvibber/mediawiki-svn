package de.brightbyte.wikiword.wikis;

import java.util.HashMap;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_frwiki extends WikiConfiguration {

	public WikiConfiguration_frwiki() {
		super();
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("commons", 1, true), "[[commons:$1]]"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("commons[ _]?cat?", 1, true), "[[commons:Category:$1]]"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("Autres[ _]projets", 1, true), "[[commons:$1]]")); //FIXME: named params: commons=

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("er?|\u00e8?re|(?:mini[ _])?[IVXCM]+(?:e|re|er)?|\\d+r?er?|Mlle|Mme|elle", 0, true), "$1"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("romain|rom|rom-min|rom-maj|APIb|IPA", 1, true), "$2"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("avjc", 0, false), "av. J.-C."));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("[XVI]+es", 0, false), "$1"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("formatnum", 1, true), "$2"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("exp|me|gr|lle|\\d", 1, false), "<sup>$2</sup>"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("ind", 1, false), "<sub>$2</sub>"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("s(?: mini)?-?", 2, false), "$1$2 si\u00e8cle"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("s(?: mini)?-?", 1, false), "$1e si\u00e8cle"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("-?s(?: mini)?-?", 2, false), "$1$2 si\u00e8cle av. J.-C."));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("-?s(?: mini)?-?", 1, false), "$1e si\u00e8cle av. J.-C."));
		//TODO: {{sp}}, {{s2}} with all variants
		//TODO: {{Ier siècle}} etc
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("\\(\\(", 0, false), "{{"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("\\)\\)", 0, false), "}}"));
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("clr|Commons|Wiktionary", 0, true), ""));
		
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(
				templatePattern("lang(?:\\s*\\|\\s*(?:rtl|ltr)\\s*)?", 2, true), "$3"));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PLACE, "^(Pays|Territoire|R\u00e9publique|Subdivision|Ville|Municipalit\u00e9s|Ocean)(_|$)", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PLACE, new WikiTextAnalyzer.ExactNameMatcher("ODP"), new HashMap<String, WikiTextAnalyzer.NameMatcher>() { { put("1", new WikiTextAnalyzer.PatternNameMatcher("Regional/.*", 0, true)); } }));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PLACE, "^(Infobox_)?(Pays|Continent|Commune_)(_|$)", 0, null));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PERSON, "(^Homme$|^Femme$|^Naissance_en|D\u00e9c\u00e8s_en)", 0));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Pr\u00e9nom"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Patronyme"));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.TIME, "^(Ann\u00e9es|Portail_ann\u00e9es_\\d+|Portails_?I+er?_mill\u00e9naire(_av\\._J\\.-C\\.)?|Portails_d\u00e9cennies)$", 0, null));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.LIFEFORM, "^Taxobox_", 0, null));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.BAD, "^Suppression[ _/]", 0));
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.DISAMBIG, "^Homonymie(_|$)|_homonymes$|^Paronymie$|^Patronyme$|^Internationalisation$", 0) );
		resourceTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ResourceType.LIST, "^Liste(_|$)", 0));

		disambigStripSectionPattern = sectionPattern("^(Voir aussi|Liens internes)$", 0);  
		
		displayTitlePattern = Pattern.compile("DISPLAYTITLE|AFFICHERTITRE", Pattern.CASE_INSENSITIVE);
		defaultSortKeyPattern = Pattern.compile("DEFAULT(SORT(KEY)?|CATEGORYSORT)|CLEFDETRI|CLEDETRI", Pattern.CASE_INSENSITIVE);
	}

}
