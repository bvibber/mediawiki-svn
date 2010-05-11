package de.brightbyte.wikiword.geography.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterExtractor;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.sensor.HasPropertySensor;
import de.brightbyte.wikiword.analyzer.template.DeepTemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.DefaultTemplateParameterPropertySpec;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor.Context;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();
		
		templateExtractorFactory= new TemplateExtractor.Factory() { 
			public TemplateExtractor newTemplateExtractor(Context context, TextArmor armor) {
				DeepTemplateExtractor extractor = new DeepTemplateExtractor(context, armor);
				//FIXME: this needs to accumulate!!!! //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME
				return extractor;
			}
		};

		//XXX: coord may appear nested. check if it works.
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Coord"),
				new DefaultTemplateParameterPropertySpec("1", "coord-lat-deg").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("2", "coord-lat-min").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("3", "coord-lat-sec").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("4", "coord-lat-NS").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("5", "coord-long-deg").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("6", "coord-long-min").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("7", "coord-long-sec").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("8", "coord-long-EW").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("9", "coord-args").setStripMarkup(true)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("(Geobox|Infobox_(.*_)?([Ss]ettlement|[Cc]ountry|[Ss]tate|[Ll]ocation|[Cc]ounty|[Ll]ake)|.*_constituency_infobox)", 0, true),
				new DefaultTemplateParameterPropertySpec("name", "place-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("native_name", "place-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("common_name", "place-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("conventional_long_name", "place-name").setStripMarkup(true),

				new DefaultTemplateParameterPropertySpec("area_magnitude", "area-magnitude").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("area", "area").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("area_km2", "area-km2").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("area_sq_mi", "area-mi2").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("area_total_km2", "area-km2").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("area_total_sq_mi", "area-mi2").setStripMarkup(true),
				
				new DefaultTemplateParameterPropertySpec("timezone", "time-zone").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("time_zone", "time-zone").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("utc_offset", "time-zone").setStripMarkup(true),
				
				new DefaultTemplateParameterPropertySpec("population_census", "population").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("population_total", "population").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("population_density_km2", "population-density-km2").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("population_density_sq_mi", "population-density-mi2").setStripMarkup(true),

				new DefaultTemplateParameterPropertySpec("latd", "coord-lat-deg").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("latm", "coord-lat-min").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lats", "coord-lat-sec").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("latNS", "coord-lat-NS").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("longd", "coord-long-deg").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("longm", "coord-long-min").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("longs", "coord-long-sec").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("longEW", "coord-long-EW").setStripMarkup(true),

				new DefaultTemplateParameterPropertySpec("lat_deg", "coord-lat-deg").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lat_min", "coord-lat-min").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lat_sec", "coord-lat-sec").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lat_NS", "coord-lat-NS").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("long_deg", "coord-long-d").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("long_min", "coord-long-m").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("long_sec", "coord-long-s").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("long_EW", "coord-long-EW").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lon_deg", "coord-long-d").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lon_min", "coord-long-m").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lon_sec", "coord-long-s").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("lon_EW", "coord-long-EW").setStripMarkup(true)
		) );
		
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PLACE, "area") );
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PLACE, "area-km2") );
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PLACE, "area-mi2") );
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PLACE, "population") );
	}
	
}
