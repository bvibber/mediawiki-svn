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

public class WikiConfiguration_commonswiki extends WikiConfiguration {

	public WikiConfiguration_commonswiki() {
		definitionsSupported = false;

		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PLACE, 
				"^(Geography_of|Places|Villages|Towns|Cities|Counties|Countries|Municipalities|States|Provinces|Territories|Federal_states|Islands|Regions|Domains|Communes|Districts)" +
				       "(_|$)|_(places|villages|towns|cities|counties|countries|municipalities|states|provinces|territories|federal_states|islands|regions|domains|communes|districts)$", 0));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "^(Male|Female|People)_|_(people|man|woman|birth|death)$", 0));
		
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Centuries"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Millennia"));

		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.WORK, "(^|_)(statue|work|play|album|song|painting|opera|novel|musical|novel|composition)s?(_|$)", Pattern.CASE_INSENSITIVE));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.EVENT, "(^|_)(event|war|battle|siege|treaties|flood|famine|fire|conflict|crisis|disaster|riot|assasination|execution|crime)s?(_|$)", Pattern.CASE_INSENSITIVE));

		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^Year_in_", 0));
		
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^Lists($|_of_)|_lists$", 0));
		resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.LIST, "List_of_-*|.*_list", 0));

		disambigStripSectionPattern = sectionPattern("See also", 0);  
		//FIXME: disambig pages marked with {{shipindex}} are tabular!
		
		aliasExtractors.add( new TemplateParameterValueExtractor("Catmore2?", 0, "1") ); //FIXME: testme
		aliasExtractors.add( new TemplateParameterValueExtractor("Catmore1", 0, "1").setManger( new RegularExpressionMangler("^.*\\[\\[ *(.+?) *(\\||\\]\\])", "$1", 0) ) );
		//TODO: Catmoresub
		
		useCategoryAliases = true; //commons uses plural category names. resolve them.
	}

}
