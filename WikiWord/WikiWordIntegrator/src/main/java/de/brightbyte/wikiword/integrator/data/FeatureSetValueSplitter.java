package de.brightbyte.wikiword.integrator.data;

import java.text.ParseException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.regex.Pattern;

import de.brightbyte.text.Chunker;
import de.brightbyte.text.RegularExpressionChunker;


public class FeatureSetValueSplitter implements FeatureSetMangler {
	
	public static MultiMangler multi(FeatureSetValueSplitter... splitters) {
		return new MultiMangler((FeatureSetMangler[])splitters);
	}

	public static MultiMangler multiFromSplitters(Iterable<FeatureSetValueSplitter> splitters) {
		MultiMangler m = new MultiMangler();
		
		for (FeatureSetValueSplitter s: splitters) {
			m.addMangler(s);
		}
		
		return m;
	}

	public static MultiMangler multiFromPatternMap(Map<String, Pattern> splitters) {
		MultiMangler m = new MultiMangler();
		
		for (Map.Entry<String, Pattern>e: splitters.entrySet()) {
			m.addMangler(new FeatureSetValueSplitter(e.getKey(), e.getValue()));
		}
		
		return m;
	}

	public static MultiMangler multiFromChunkerMap(Map<String, Chunker> splitters) {
		MultiMangler m = new MultiMangler();
		
		for (Map.Entry<String, Chunker>e: splitters.entrySet()) {
			m.addMangler(new FeatureSetValueSplitter(e.getKey(), e.getValue()));
		}
		
		return m;
	}

	public static MultiMangler multiFromStringMap(Map<String, String> splitters, int flags) {
		MultiMangler m = new MultiMangler();
		
		for (Map.Entry<String, String>e: splitters.entrySet()) {
			m.addMangler(new FeatureSetValueSplitter(e.getKey(), e.getValue(), flags));
		}
		
		return m;
	}

	protected String field;
	protected Chunker chunker;
	
	public FeatureSetValueSplitter(String field, String regex, int flags) {
		this(field, Pattern.compile(regex, flags));
	}
	
	public FeatureSetValueSplitter(String field, Pattern pattern) {
		this(field, new RegularExpressionChunker(pattern));
	}

	public FeatureSetValueSplitter(String field, Chunker chunker) {
		this.field = field;
		this.chunker = chunker;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((field == null) ? 0 : field.hashCode());
		result = PRIME * result + ((chunker == null) ? 0 : chunker.hashCode());
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
		if (chunker == null) {
			if (other.chunker != null)
				return false;
		} else if (!chunker.equals(chunker))
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
			
			try {
				Iterable<String> vv = chunker.chunk(s);

				//TODO: detecting trivial items might speed things up.
				//if (w!=null || vv.size()!=1 || !vv.get(0).equals(s)) {
					if (w==null) {
						w = new ArrayList<Object>(v.size()*2);
						if (c>0) w.addAll(v.subList(0, c));
					}
					
					for (String a: vv)
							w.add(a);
				//}
			} catch (ParseException e) {
				//split failed, so keep value as-is
				//XXX: somehow report?
				if (w!=null) w.add(s);
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
