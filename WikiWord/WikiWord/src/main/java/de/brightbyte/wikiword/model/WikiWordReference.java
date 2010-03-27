package de.brightbyte.wikiword.model;

import java.text.Collator;
import java.util.Arrays;
import java.util.Comparator;
import java.util.Locale;
import java.util.regex.Pattern;

public abstract class WikiWordReference<T> implements WikiWordRanking {
	
	public static class ByName implements Comparator<WikiWordReference> {
		protected Collator collator;
		
		public ByName() {
			this((Collator)null);
		}
		
		public ByName(Locale locale) {
			this.collator = Collator.getInstance(locale);
		}
		
		public ByName(Collator collator) {
			this.collator = collator;
		}
		
		public int compare(WikiWordReference a, WikiWordReference b) {
			if (collator==null) return a.getName().compareTo(b.getName());
			else return collator.compare(a.getName(), b.getName());
		}
	};
	
	public static final Comparator<WikiWordReference> byName = new ByName(); //FIXME: use a locale-specific one!
	
	public static interface Factory<T extends WikiWordReference> {
		public T newInstance(final int id, final String name, final int cardinality, final double relevance);
		public T[] newArray(int size);
	}
	
	protected final int id;
	protected final String name;

	protected int cardinality;
	protected double relevance;
	
	public WikiWordReference(final int id, final String name, final int cardinality, final double relevance) {
		this.cardinality = cardinality;
		this.relevance = relevance;
		this.id = id;
		this.name = name;
	}
	
	public int getCardinality() {
		return cardinality;
	}

	public double getRelevance() {
		return relevance;
	}
	
	public void setRelevance(double relevance) {
		this.relevance = relevance;
	}

	public void setCardinality(int cardinality) {
		this.cardinality = cardinality;
	}

	public int getId() {
		return id;
	}
	
	public String getName() {
		return name;
	}
	
	@Override
	public String toString() {
		if (name==null) return "#"+id;
		else return name;
	}
	
	public boolean hasRanking() {
		return cardinality>0 || relevance>0;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		if (id>0) result = PRIME * result + id;
		else result = PRIME * result + ((name == null) ? 0 : name.hashCode());
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
		final WikiWordReference other = (WikiWordReference) obj;
		
		if (id>0 && other.id>0) {
			return id == other.id;
		}
		else if (name != null && other.name != null) {
			return name.equals(other.name);
		}
		
		if (id<=0 && other.id>0) return false;
		if (id>0 && other.id<=0) return false;
		if (name==null && other.name!=null) return false;
		if (name!=null && other.name==null) return false;
		
		return true;
	}

	public static class ListFormatSpec {
		public final boolean useId;
		public final boolean useName;
		public final boolean useCardinality;
		public final boolean useRelevance;
		public final Pattern referenceSeparator;
		public final Pattern fieldSeparator;
		public final int width;
		
		public ListFormatSpec(Pattern referenceSeparator, Pattern fieldSeparator, boolean useId, boolean useName, boolean useCardinality, boolean useRelevance) {
			this.referenceSeparator = referenceSeparator;
			this.fieldSeparator = fieldSeparator;
			this.useId = useId;
			this.useName = useName;
			this.useCardinality = useCardinality;
			this.useRelevance = useRelevance;
			
			int w = 0;
			if (useId) w++;
			if (useName) w++;
			if (useCardinality) w++;
			if (useRelevance) w++;
			
			this.width = w;
		}
	}

	public static <T extends WikiWordReference> T[] parseList(String s, Factory<T> factory, ListFormatSpec spec) {
		if (s==null || s.length()==0) return factory.newArray(0);
		
		int id = -1;
		String name = null;
		int cardinality = -1;
		double relevance = -1;
		
		//TODO: use a scanner/tokenizer, or a single regex!
		
		String[] ss = spec.referenceSeparator.split(s);
		T[] a = factory.newArray(ss.length);
		int c = 0;
		
		for (String r: ss) {
			String[] rr = spec.fieldSeparator.split(r, 4);

			/*
			if (rr.length < spec.width) {
				//NOTE: value truncated!
				//TODO: log warning
				break;
			}
			*/
			
			int i=0;
			if (rr.length>=i+1 && spec.useId) id = Integer.parseInt(rr[i++]); 
			if (rr.length>=i+1 && spec.useName) name = rr[i++]; 
			if (rr.length>=i+1 && spec.useCardinality) cardinality = rr[i].length()==0 ? -1 : Integer.parseInt(rr[i++]); 
			if (rr.length>=i+1 && spec.useRelevance) relevance =     rr[i].length()==0 ? -1 : Double.parseDouble(rr[i++]);
			
			a[c++] = factory.newInstance(id, name, cardinality, relevance);
		}
		
		if (c<a.length) { //should not happen, but might, if values get truncated
			T[] b = factory.newArray(c);
			System.arraycopy(a, 0, b, 0, b.length);
			a = b;
		}
		
		if (spec.useRelevance) Arrays.sort(a, byRelevance);
		else if (spec.useCardinality) Arrays.sort(a, byCardinality);
		else if (spec.useName) Arrays.sort(a, byName);
		
		return a;
	}
	
}
