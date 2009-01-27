package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_ndswiki extends WikiConfiguration {

	public WikiConfiguration_ndswiki() {
		super();
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PLACE, "^[Ll]\u00e4nner_in_.*", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.PLACE, "Oort"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.PLACE, "Land"));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.PERSON, "Mann"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.PERSON, "Fru"));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "V\u00f6rnaam_f\u00f6r_Deerns"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "V\u00f6rnaam_f\u00f6r_Jungs"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.NAME, "Familiennaam"));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Johr"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Dag"));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor(ConceptType.TIME, "Johrhunnert"));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ConceptType.LIFEFORM, "Taxobox", null));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ResourceType.BAD, "Delete", null));
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ResourceType.BAD, "Gauweg", null));
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ResourceType.BAD, "Wegsmieten", null));
		
		resourceTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor(ResourceType.DISAMBIG, "Mehrd\u00fcdig_Begreep", null) );
		resourceTypeSensors.add( new WikiTextAnalyzer.TitleSensor(ResourceType.LIST, "Lieste?_(van|mit).*", 0));

		disambigStripSectionPattern = sectionPattern("Kiek ok( bi)?:?", 0); //FIXME: often not as a section, but plain text! 

		redirectPattern = Pattern.compile("^#(?:REDIRECT(?:ION)?|wiederleiden)"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE);
	}

}
