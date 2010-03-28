package de.brightbyte.wikiword.model;

import java.util.Arrays;
import java.util.Comparator;
import java.util.regex.Pattern;

import de.brightbyte.data.measure.Measure;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class WikiWordConcept {
	protected DatasetIdentifier dataset;
	protected ConceptType type;
	
	protected int id;

	protected int cardinality = 1;
	protected double relevance = 1;
	
	protected ConceptRelations relations;
	protected ConceptFeatures features;
	protected TermReference[] terms;
	
	public WikiWordConcept(DatasetIdentifier dataset, int id,  ConceptType type) {
		this.id = id;
		this.dataset = dataset;
		this.type = type;
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
		if (this.type!=null) throw new IllegalStateException("property already initialized");
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

	
	@Override
	public String toString() {
		return "#"+id;
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

	public static interface Factory<T extends WikiWordConcept> {
		public T newInstance(int id, String name,  ConceptType type) throws PersistenceException;
		public T newInstance(int id,  String name, ConceptType type, int card, double rel) throws PersistenceException;
		public T[] newArray(int size);
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
			
			a[c++] = factory.newInstance(id, name, null, cardinality, relevance);
		}
		
		if (c<a.length) { //should not happen, but might, if values get truncated
			T[] b = factory.newArray(c);
			System.arraycopy(a, 0, b, 0, b.length);
			a = b;
		}
		
		if (spec.useRelevance) Arrays.sort(a, byRelevance);
		else if (spec.useCardinality) Arrays.sort(a, byCardinality);
		//else if (spec.useName) Arrays.sort(a, byName);
		
		return a;
	}
	
}