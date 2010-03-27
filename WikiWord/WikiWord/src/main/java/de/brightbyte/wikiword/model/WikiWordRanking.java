package de.brightbyte.wikiword.model;

import java.util.Comparator;

import de.brightbyte.data.measure.Measure;

public interface WikiWordRanking {

	public static final Measure<WikiWordRanking> theCardinality = new Measure<WikiWordRanking>(){
		public double measure(WikiWordRanking r) {
			return r.getCardinality();
		}
	};
	
	public static final Measure<WikiWordRanking> theRelevance = new Measure<WikiWordRanking>(){
		public double measure(WikiWordRanking r) {
			return r.getRelevance();
		}
	};
	
	public static final Comparator<WikiWordRanking> byCardinality = new Comparator<WikiWordRanking>(){
		public int compare(WikiWordRanking a, WikiWordRanking b) {
			return b.getCardinality() - a.getCardinality(); //note: descending! 
		}
	};
	
	public static final Comparator<WikiWordRanking> byRelevance = new Comparator<WikiWordRanking>(){
		public int compare(WikiWordRanking a, WikiWordRanking b) {
			double ra = a.getRelevance();
			double rb = b.getRelevance();
			
			//note: descending!
			if (ra==rb) return 0;
			else if (ra>rb) return (int)(rb-ra) -1;
			else return (int)(rb-ra) +1;
		}
	};

	public int getCardinality();

	public double getRelevance();

}
