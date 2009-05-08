package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.extractor.CategoryPatternParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.PagePropertyValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterExtractor;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.sensor.HasPropertySensor;
import de.brightbyte.wikiword.analyzer.template.DefaultTemplateParameterPropertySpec;

public class WikiConfiguration_dewiki extends WikiConfiguration {

	public WikiConfiguration_dewiki() {
		super();

		propertyExtractors.add( new CategoryPatternParameterExtractor("^Geboren_(\\d+(_v\\._Chr\\.)?)$", "$1", 0, "person-birth-date") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^Gestorben_(\\d+(_v\\._Chr\\.)?)$", "$1", 0, "person-death-date") );
 
		propertyExtractors.add( new CategoryPatternParameterExtractor("^Maler_(der|des)_(.+)$", "$2", 0, "artist-group") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^(Maler|Bildhauer|Fotograf)(_|$).*$", "$1", 0, "artist-group") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^.*[^_](maler|bildhauer|fotograf)$", "$1", 0, "artist-group").setCapitalize(true) );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^.*?([-_\\wäöü]+)(maler|bildhauer|fotograf)$", "$2", 0, "artist-group") );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Personendaten"),
				new DefaultTemplateParameterPropertySpec("NAME", "person-sortname").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("NAME", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("ALTERNATIVNAMEN", "person-name").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new DefaultTemplateParameterPropertySpec("KURZBESCHREIBUNG", "person-occupation").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("GEBURTSDATUM", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("STERBEDATUM", "person-death-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("GEBURTSORT", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("STERBEORT", "person-death-place").setStripMarkup(true)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("PND"),
				new DefaultTemplateParameterPropertySpec("1", "ID-PND").setStripMarkup(true) ) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("LeMO"),
				new DefaultTemplateParameterPropertySpec("1", "ID-LeMO").setStripMarkup(true) ) );
		
		//TODO: {{BAM|Kohl|Helmut}}

		pageTermExtractors.add( new PagePropertyValueExtractor("person-sortname") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("person-name") );
		
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-name") );
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-birth-date") );
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "artist-group") );
	}
	
}
