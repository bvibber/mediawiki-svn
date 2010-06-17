package de.brightbyte.wikiword.model;

import java.text.Collator;
import java.util.Arrays;
import java.util.Comparator;
import java.util.Locale;
import java.util.regex.Pattern;

import de.brightbyte.data.measure.Measure;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.DatasetIdentifier;

public class WikiWordConcept {
	public static class ByName implements Comparator<WikiWordConcept> {
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
		
		public int compare(WikiWordConcept a, WikiWordConcept b) {
			if (collator==null) return a.getName().compareTo(b.getName());
			else return collator.compare(a.getName(), b.getName());
		}
	};
	
	private DatasetIdentifier dataset;
	private ConceptType type;
	
	private int id;
	private String name;

	private int cardinality = 1;
	private double relevance = 1;
	
	private ConceptRelations relations;
	private ConceptFeatures features;
	private TermReference[] terms;
	
	public WikiWordConcept(DatasetIdentifier dataset, int id,  ConceptType type) {
		this.id = id;
		this.dataset = dataset;
		this.type = type;
	}
	
	@Override
	public String toString() {
		if (name!=null) return "#"+id+":[["+name+"]]";
		else return "#"+id;
	}
	
	public String getName() {
		return name;
	}

	public void setName(String name) {
		if (this.name!=null) throw new IllegalStateException("property already initialized");
		this.name = name;
	}

	public DatasetIdentifier getDatasetIdentifier() {
		return dataset;
	}
	
	public int getId() {
		return id;
	}

	public int getCardinality() {
		return cardinality;
	}

	public void setCardinality(int cardinality) {
		this.cardinality = cardinality;
	}

	public ConceptFeatures getFeatures() {
		return features;
	}

	public void setFeatures(ConceptFeatures features) {
		if (this.features!=null) throw new IllegalStateException("property already initialized");
		if (features.getConcept()!=null && !this.equals(features.getConcept())) throw new IllegalArgumentException("ConceptFeatures bound to a different concept: "+features.getConcept());
		this.features = features;
	}

	public ConceptRelations getRelations() {
		return relations;
	}

	public void setRelations(ConceptRelations relations) {
		if (this.relations!=null) throw new IllegalStateException("property already initialized");
		this.relations = relations;
	}

	public double getRelevance() {
		return relevance;
	}

	public void setRelevance(double relevance) {
		this.relevance = relevance;
	}

	public TermReference[] getTerms() {
		return terms;
	}

	public void setTerms(TermReference[] terms) {
		if (this.terms!=null) throw new IllegalStateException("property already initialized");
		this.terms = terms;
	}

	public ConceptType getType() {
		return type;
	}

	public void setType(ConceptType type) {
		if (this.type!=null && !this.type.equals(ConceptType.UNKNOWN)) throw new IllegalStateException("property already initialized");
		this.type = type;
	}

	@Override
	public int hashCode() {
		return dataset.hashCode() ^ id;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final WikiWordConcept other = (WikiWordConcept) obj;
		
		if (!dataset.equals(other.dataset)) return false;
		
		return id == other.id;
	}	
	
	public static final Measure<WikiWordConcept> theCardinality = new Measure<WikiWordConcept>(){
		public double measure(WikiWordConcept r) {
			return r.getCardinality();
		}
	};
	
	public static final Measure<WikiWordConcept> theRelevance = new Measure<WikiWordConcept>(){
		public double measure(WikiWordConcept r) {
			return r.getRelevance();
		}
	};
	
	public static final Comparator<WikiWordConcept> byCardinality = new Comparator<WikiWordConcept>(){
		public int compare(WikiWordConcept a, WikiWordConcept b) {
			return b.getCardinality() - a.getCardinality(); //note: descending! 
		}
	};
	
	public static final Comparator<WikiWordConcept> byRelevance = new Comparator<WikiWordConcept>(){
		public int compare(WikiWordConcept a, WikiWordConcept b) {
			double ra = a.getRelevance();
			double rb = b.getRelevance();
			
			//note: descending!
			if (ra==rb) return 0;
			else if (ra>rb) return (int)(rb-ra) -1;
			else return (int)(rb-ra) +1;
		}
	};
	
	public static final Comparator<WikiWordConcept> byName = new ByName();
	
	public static interface Factory<T extends WikiWordConcept> {
		public T newInstance(int id, String name,  ConceptType type) throws PersistenceException;
		public T newInstance(int id,  String name, ConceptType type, int card, double rel) throws PersistenceException;
		public T[] newArray(int size);
	}

	public static final int LIST_FORMAT_USE_ID = 0x0001;
	public static final int LIST_FORMAT_USE_NAME = 0x0001;
	public static final int LIST_FORMAT_USE_CARDINALITY = 0x0010;
	public static final int LIST_FORMAT_USE_RELEVANCE = 0x0020;
	public static final int LIST_FORMAT_USE_LANGUAGE_PREFIX= 0x0100;
	
	public static class ListFormatSpec {
		public final boolean useId;
		public final boolean useName;
		public final boolean useCardinality;
		public final boolean useRelevance;
		public final boolean useLanguagePrefix;
		public final Pattern referenceSeparator;
		public final Pattern fieldSeparator;
		public final Pattern prefixSeparator;
		public final int width;
		
		public ListFormatSpec(Pattern referenceSeparator, Pattern fieldSeparator, Pattern prefixSeparator, int flags) {
			this.referenceSeparator = referenceSeparator;
			this.fieldSeparator = fieldSeparator;
			this.prefixSeparator = prefixSeparator;
			
			useId = (flags & LIST_FORMAT_USE_ID) > 0;
			useName = (flags & LIST_FORMAT_USE_NAME) > 0;
			useCardinality = (flags & LIST_FORMAT_USE_CARDINALITY) > 0;
			useRelevance = (flags & LIST_FORMAT_USE_RELEVANCE) > 0;
			useLanguagePrefix = (flags & LIST_FORMAT_USE_LANGUAGE_PREFIX) > 0;
			
			int w = 0;
			if (useId) w++;
			if (useName) w++;
			if (useCardinality) w++;
			if (useRelevance) w++;
			
			this.width = w;
		}
	}

	public static <T extends WikiWordConcept> T[] parseList(String s, Factory<T> factory, ListFormatSpec spec) throws PersistenceException {
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
			String lang = null;
			
			if (spec.useLanguagePrefix) {
				String[] nn = spec.prefixSeparator.split(name, 2);
				if (nn.length>1) {
					name = nn[1];
					lang = nn[0];
				}
			}
			
			T t = factory.newInstance(id, name, null, cardinality, relevance);
			if (lang!=null && t instanceof LocalConcept) ((LocalConcept)t).setLanguage(lang);
			a[c++] = t;
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