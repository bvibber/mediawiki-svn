package de.brightbyte.wikiword.analyzer.extractor;

import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.sensor.Sensor;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;

public class SensoricPropertyExtractor implements PropertyExtractor, TemplateUser {
	protected Sensor<String>[] sensors;
	protected String property;
	protected transient String templateNamePattern;
	
	public SensoricPropertyExtractor(String property, Sensor<String>... sensors) {
		if (sensors==null) throw new NullPointerException();
		if (property==null) throw new NullPointerException();
		
		this.sensors = sensors;
		this.property = property;
	}

	public MultiMap<String, CharSequence, Set<CharSequence>> extract(WikiPage page, MultiMap<String, CharSequence, Set<CharSequence>> into) {
		for (Sensor<String> sensor: sensors) {
			if (sensor.sense(page)) {
				if (into==null) into = new ValueSetMultiMap<String, CharSequence>();
				into.put(property, sensor.getValue());
			}
		}
		
		return into;
	}

	public String getTemplateNamePattern() {
		if ( templateNamePattern != null ) {
			if ( templateNamePattern.length() == 0 ) return null;
			else return templateNamePattern;
		}
		
		StringBuilder s = null;
		
		for (Sensor<String> sensor: sensors) {
			if (sensor instanceof TemplateUser) {
				String pattern = ((TemplateUser)sensor).getTemplateNamePattern();
				if (s==null) s = new StringBuilder("(");
				else s.append(")|(");
				
				s.append(pattern);
			}
		}
		
		if (s==null || s.length()==0) {
			templateNamePattern = "";
		} else {
			s.append(')');
			templateNamePattern = s.toString();
		}
		
		return templateNamePattern;
	}
	
}
