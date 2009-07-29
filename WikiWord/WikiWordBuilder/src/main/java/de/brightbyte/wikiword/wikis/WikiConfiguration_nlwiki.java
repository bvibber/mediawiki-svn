package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasCategorySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasSectionLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.TitleSensor;

public class WikiConfiguration_nlwiki extends WikiConfiguration {

	public WikiConfiguration_nlwiki() {
		super();
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("wrapper", 0, true), "{|"));

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("0", 0, true), " "));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("e", 0, false), "$1"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("unicode", 1, true), "$2"));
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Landtabel|Gemeente|Plaats)($|_)|(^|_)plaats$", 0));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PLACE, "^(Gemeente|Stad|Land|Plaats)(_|$)", 0));

		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.TIME, "Jaarbox"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.TIME, "Kalenders"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Datum"));
		conceptTypeSensors.add( new TitleSensor<ConceptType>(ConceptType.TIME, "(\\d{1,4}|\\d{1,2}e_eeuw)(_v\\._Chr\\.)?", 0));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "(^|_)persoon(_|$)|(.*schapper|.*oloog|.*icus)$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Infobox_(artiest|Auteur|acteur|Comedian|.*speler|Presentator|regisseur)|Winnaars_.*)$", Pattern.CASE_INSENSITIVE));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Infobox_.*|.*cus|.*eur|.*ler|.*schapper)$", Pattern.CASE_INSENSITIVE, new String[] {"geboren"}));
		conceptTypeSensors.add( new HasSectionLikeSensor<ConceptType>(ConceptType.PERSON, "^((.* )?leven|Carri\u00e8re|Stamvader|Levensloop|Filmografie|Bibliografie|publicaties|(Eigen )?Biografie|Priv\u00e9|.*Loopbaan.*|Jeugd|Kinderen|Familie|Familieachtergrond)$", Pattern.CASE_INSENSITIVE) );

		//conceptTypeSensors.add( new WikiTextAnalyzer.TitleSensor(".*_\\(voornaam\\)", 0));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Jongensnaam"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Meisjesnaam"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Achternaam"));
		
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox_end"));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Weg|Ne)$|weg$", 0));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.DISAMBIG, "Dp") );
		//resourceTypeSensors.add( new TitleSensor(ResourceType.DISAMBIG, ".*\\(doorverwijspagina\\)", 0) );
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^Lijsten_|lijsten$", 0) );
		resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.LIST, "Lijst_.*|.*lijst", 0) );
		//resourceTypeSensors.add( new WikiTextAnalyzer.RegularExpressionTitleSensor("^Lijst_", 0) ); //NOTE: too broad. some concrete concepts have a name matching this.
		
		disambigStripSectionPattern = sectionPattern("Zie ook", 0); 

		//redirectPattern = Pattern.compile("^#(?:REDIRECT(?:ION)?|DOORVERWIJZING)"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE);
		//displayTitlePattern = Pattern.compile("DISPLAYTITLE|TOONTITEL|TITELTONEN", Pattern.CASE_INSENSITIVE);
		//defaultSortKeyPattern = Pattern.compile("DEFAULT(SORT(KEY)?|CATEGORYSORT)|STANDAARDSORTERING", Pattern.CASE_INSENSITIVE);
	}

}
