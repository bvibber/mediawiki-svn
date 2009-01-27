package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_nlwiki extends WikiConfiguration {

	public WikiConfiguration_nlwiki() {
		super();
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("wrapper", 0, true), "{|"));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("e", 0, false), "$1"));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("unicode", 1, true), "$2"));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PLACE, "^(Landtabel|Gemeente|Plaats)($|_)|(^|_)plaats$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PLACE, "^(Gemeente|Stad|Land|Plaats)(_|$)", 0));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.TIME, "Jaarbox", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.TIME, "Kalenders", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Datum"));
		conceptTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ConceptType.TIME, "(\\d{1,4}|\\d{1,2}e_eeuw)(_v\\._Chr\\.)?", 0));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.PERSON, "(^|_)persoon(_|$)|(.*schapper|.*oloog|.*icus)$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PERSON, "^(Infobox_(artiest|Auteur|acteur|Comedian|.*speler|Presentator|regisseur)|Winnaars_.*)$", Pattern.CASE_INSENSITIVE));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PERSON, "^(Infobox_.*|.*cus|.*eur|.*ler|.*schapper)$", Pattern.CASE_INSENSITIVE, new String[] {"geboren"}));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasSectionLikeSensor(ConceptType.PERSON, "^((.* )?leven|Carri\u00e8re|Stamvader|Levensloop|Filmografie|Bibliografie|publicaties|(Eigen )?Biografie|Priv\u00e9|.*Loopbaan.*|Jeugd|Kinderen|Familie|Familieachtergrond)$", Pattern.CASE_INSENSITIVE) );

		//conceptTypeSensors.add( new WikiTextAnalyzer.TitleSensor(".*_\\(voornaam\\)", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Jongensnaam"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Meisjesnaam"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Achternaam"));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.LIFEFORM, "Taxobox_end", null));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ResourceType.BAD, "^(Weg|Ne)$|weg$", 0));
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ResourceType.DISAMBIG, "Dp", null) );
		resourceTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ResourceType.DISAMBIG, ".*\\(doorverwijspagina\\)", 0) );
		resourceTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ResourceType.LIST, "^Lijsten_|lijsten$", 0) );
		resourceTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ResourceType.LIST, "Lijst_.*|.*lijst", 0) );
		//resourceTypeSensors.add( new WikiTextAnalyzer.RegularExpressionTitleSensor("^Lijst_", 0) ); //NOTE: too broad. some concrete concepts have a name matching this.
		
		disambigStripSectionPattern = sectionPattern("Zie ook", 0); 

		redirectPattern = Pattern.compile("^#(?:REDIRECT(?:ION)?|DOORVERWIJZING)"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE);
		displayTitlePattern = Pattern.compile("DISPLAYTITLE|TOONTITEL|TITELTONEN", Pattern.CASE_INSENSITIVE);
		defaultSortKeyPattern = Pattern.compile("DEFAULT(SORT(KEY)?|CATEGORYSORT)|STANDAARDSORTERING", Pattern.CASE_INSENSITIVE);
	}

}
