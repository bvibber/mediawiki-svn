package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;


public class FeatureSetValueSplitter implements FeatureSetMangler {
	
	public static FeatureSetMultiMangler multi(FeatureSetValueSplitter... splitters) {
		return new FeatureSetMultiMangler((FeatureSetMangler[])splitters);
	}

	public static FeatureSetMultiMangler multiFromSplitters(Iterable<FeatureSetValueSplitter> splitters) {
		FeatureSetMultiMangler m = new FeatureSetMultiMangler();
		
		for (FeatureSetValueSplitter s: splitters) {
			m.addMangler(s);
		}
		
		return m;
	}

	public static FeatureSetMultiMangler multiFromPatternMap(Map<String, Pattern> splitters) {
		FeatureSetMultiMangler m = new FeatureSetMultiMangler();
		
		for (Map.Entry<String, Pattern>e: splitters.entrySet()) {
			m.addMangler(new FeatureSetValueSplitter(e.getKey(), e.getValue()));
		}
		
		return m;
	}

	public static FeatureSetMultiMangler multiFromStringMap(Map<String, String> splitters, int flags) {
		FeatureSetMultiMangler m = new FeatureSetMultiMangler();
		
		for (Map.Entry<String, String>e: splitters.entrySet()) {
			m.addMangler(new FeatureSetValueSplitter(e.getKey(), e.getValue(), flags));
		}
		
		return m;
	}

	protected String field;
	protected Matcher splitter;
	
	public FeatureSetValueSplitter(String field, String regex, int flags) {
		this(field, Pattern.compile(regex, flags));
	}
	
	public FeatureSetValueSplitter(String field, Pattern splitter) {
		this.field = field;
		this.splitter = splitter.matcher("");
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((field == null) ? 0 : field.hashCode());
		result = PRIME * result + ((splitter == null) ? 0 : splitter.pattern().hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final FeatureSetValueSplitter other = (FeatureSetValueSplitter) obj;
		if (field == null) {
			if (other.field != null)
				return false;
		} else if (!field.equals(other.field))
			return false;
		if (splitter == null) {
			if (other.splitter != null)
				return false;
		} else if (!splitter.pattern().equals(other.splitter.pattern()))
			return false;
		return true;
	}

	public FeatureSet apply(FeatureSet fts) {
		List<Object> v = fts.get(field);
		if (v==null || v.size()==0) return fts;
		
		List<Object> w = null;
		
		int c = 0;
		for (Object obj: v) {
			String s = obj.toString();
			splitter.reset(s);
			
			int i = 0;
			while (splitter.find()) {
				if (w==null) {
					w = new ArrayList<Object>(v.size()*2);
					if (c>0) w.addAll(v.subList(0, c));
				}
				
				String t = s.substring(i, splitter.start());
				if (t.length()>0) w.add(t);
				
				i = splitter.end();
			}
			
			if (i<s.length() && w!=null) {
				String t = s.substring(i);
				if (t.length()>0) w.add(t);
			}
			
			c++;
		}
		
		if (w!=null) {
			fts.remove(field);
			fts.putAll(field, w);
		}
		
		return fts;
	}

}
