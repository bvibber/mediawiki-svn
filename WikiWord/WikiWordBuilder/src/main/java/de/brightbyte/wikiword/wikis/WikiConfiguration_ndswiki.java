package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.sensor.HasCategorySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.TitleSensor;

public class WikiConfiguration_ndswiki extends WikiConfiguration {

	public WikiConfiguration_ndswiki() {
		super();
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^[Ll]\u00e4nner_in_.*", 0));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PLACE, "Oort"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PLACE, "Land"));
		
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PERSON, "Mann"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PERSON, "Fru"));

		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "V\u00f6rnaam_f\u00f6r_Deerns"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "V\u00f6rnaam_f\u00f6r_Jungs"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Familiennaam"));

		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Johr"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Dag"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Johrhunnert"));

		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox"));
		//TODO: cooperations & organizations
		
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Delete"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Gauweg"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Wegsmieten"));
		
		//resourceTypeSensors.add( new HasTemplateSensor(ResourceType.DISAMBIG, "Mehrd\u00fcdig_Begreep") );
		resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.LIST, "Lieste?_(van|mit).*", 0));

		disambigStripSectionPattern = sectionPattern("Kiek ok( bi)?:?", 0); //FIXME: often not as a section, but plain text! 

		//redirectPattern = Pattern.compile("^#(?:REDIRECT(?:ION)?|wiederleiden)"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE);
	}

}
