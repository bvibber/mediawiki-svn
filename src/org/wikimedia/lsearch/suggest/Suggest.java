package org.wikimedia.lsearch.suggest;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import javax.naming.directory.SearchResult;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.suggest.api.NgramIndexer;
import org.wikimedia.lsearch.suggest.api.NamespaceFreq;
import org.wikimedia.lsearch.suggest.api.WordsIndexer;
import org.wikimedia.lsearch.suggest.dist.DoubleMetaphone;
import org.wikimedia.lsearch.suggest.dist.EditDistance;
import org.wikimedia.lsearch.suggest.dist.JaroWinkler;

public class Suggest {
	static Logger log = Logger.getLogger(Suggest.class);
	protected IndexId iid;
	protected IndexSearcher searcher;
	protected IndexSearcher phrases;
	protected DoubleMetaphone dmeta;
	/** Number of results to fetch */
	public static final int POOL = 300;
	
	/** Lower limit to hit rate for joining */
	public static final int JOIN_FREQ = 1;
	
	public Suggest(IndexId iid) throws IOException{
		this.iid = iid;
		this.searcher = new IndexSearcher(iid.getSuggestWordsPath());
		this.phrases = new IndexSearcher(iid.getSuggestTitlesPath());
		this.dmeta = new DoubleMetaphone();
	}
	
	public ArrayList<SuggestResult> suggestWords(String word, int num){
		String meta1 = dmeta.doubleMetaphone(word);
		String meta2 = dmeta.doubleMetaphone(word,true);
		BooleanQuery bq = new BooleanQuery();		
		addQuery(bq,"metaphone1",meta1,2);
		addQuery(bq,"metaphone2",meta2,2);
		bq.add(makeWordQuery(word,""),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = searcher.search(bq,null,POOL);			
			EditDistance sd = new EditDistance(word);
			EditDistance sdmeta1 = new EditDistance(meta1);
			EditDistance sdmeta2 = new EditDistance(meta2);
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			JaroWinkler jaro = new JaroWinkler();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = searcher.doc(sc.doc);
				SuggestResult r = new SuggestResult(d.get("word"),
						Integer.parseInt(d.get("freq")));
				if(word.equals(r.word)){
					minfreq = r.frequency;
				}
				r.dist = sd.getDistance(r.word);
				r.distMetaphone = sdmeta1.getDistance(dmeta.doubleMetaphone(r.word));
				r.distMetaphone2 = sdmeta2.getDistance(dmeta.doubleMetaphone(r.word,true));
				r.jaro = jaro.getSimilarity(word,r.word);
				//r.jaro*=r.jaro;
				r.jaro = 1;
				if((r.distMetaphone < meta1.length() || r.distMetaphone2 < meta2.length()) && (r.distMetaphone<=2 || r.distMetaphone2<=2)) // don't add if the pronunciatio is something completely unrelated
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency <= minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new Comparator<SuggestResult>() {
				public int compare(SuggestResult o1, SuggestResult o2){					
					if(o1.dist == o2.dist){
						/*if(o1.distMetaphone == 0 && o2.distMetaphone !=0 && o2.distMetaphone2 != 0)
							return -1;
						else if (o2.distMetaphone == 0 && o1.distMetaphone !=0 && o1.distMetaphone2 != 0)
							return 1; */
						
						double d = o2.getFrequency()*o2.jaro - o1.getFrequency()*o1.jaro ;
						if(d == 0) return 0;
						if(d > 0) return 1;
						return -1;
						/*if(o1.distMetaphone == o2.distMetaphone)
							return o2.getFrequency() - o1.getFrequency();
						else
							return o1.distMetaphone - o2.distMetaphone; */
					} else 
						return o1.dist - o2.dist;					
				}
			});
			while(res.size() > num){
				res.remove(res.size()-1);
			}
			return res;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+word+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}
	
	public double transformScore(double score){
		return Math.log10(1+score*99)/2;		
	}
	
	public Query makeWordQuery(String word, String prefix){
		BooleanQuery bq = new BooleanQuery(true);
		int min = NgramIndexer.getMinNgram(word);
		int max = NgramIndexer.getMaxNgram(word);
		String fieldBase = NgramIndexer.getNgramField(prefix);
		for(int i=min; i <= max; i++ ){
			String[] ngrams = NgramIndexer.nGrams(word,i);
			String field = fieldBase+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];
				/*if(j == 0)
					addQuery(bq,"start"+i,ngram,2);
				else if(j == ngrams.length-1)
					addQuery(bq,"end"+i,ngram,1); */
				// finally add regular ngram
				addQuery(bq,field,ngram,1);
			}
		}
		return bq;
	}

	protected void addQuery(BooleanQuery q, String field, String value, float boost) {
		Query tq = new TermQuery(new Term(field, value));
		tq.setBoost(boost);
		q.add(new BooleanClause(tq, BooleanClause.Occur.SHOULD));
	}
	
	public static class SuggestSplit {
		String word1;
		int freq1;
		String word2;		
		int freq2;
		public SuggestSplit(String word1, int freq1, String word2, int freq2) {
			this.word1 = word1;
			this.freq1 = freq1;
			this.word2 = word2;
			this.freq2 = freq2;
		}
		public String toString(){
			return word1+":"+freq1+","+word2+":"+freq2;
		}
		public String getWord(){
			return word1+" "+word2;
		}
		
	}
	
	public ArrayList<SuggestSplit> suggestSplit(String word, int num){
		ArrayList<SuggestSplit> res = new ArrayList<SuggestSplit>();
		if(word.length() <= 3)
			return res;		
		HashSet<String> parts = new HashSet<String>();
		HashMap<String,String> split = new HashMap<String,String>();
		for(int i=1;i<word.length()-1;i++){
			String s1 = word.substring(0,i);
			String s2 = word.substring(i);
			split.put(s1,s2);
			parts.add(s1);
			parts.add(s2);
		}
		BooleanQuery bq = new BooleanQuery();
		for(String s : parts){
			bq.add(new TermQuery(new Term("word",s)),BooleanClause.Occur.SHOULD);
		}
		try {			
			Hits hits = searcher.search(bq);
			// word -> freq
			HashMap<String,Integer> found = new HashMap<String,Integer>();
			for(int i=0;i<hits.length();i++){
				found.put(hits.doc(i).get("word"),new Integer(hits.doc(i).get("freq")));
			}
			for(Entry<String,String> es : split.entrySet() ){
				String s1 = es.getKey();
				String s2 = es.getValue();
				if(found.containsKey(s1) && found.containsKey(s2)){
					res.add(new SuggestSplit(s1,found.get(s1),s2,found.get(s2)));
				}
			}
			
		} catch (IOException e) {
			log.error("Error trying to find splits for "+word+" : "+bq);
			e.printStackTrace();			
		}
		return res;
	}
	
	public ArrayList<SuggestSplit> suggestSplitFromTitle(String word){
		int freq = 0;
		Hits hits;
		ArrayList<SuggestSplit> res = new ArrayList<SuggestSplit>();
		try {
			// find frequency
			hits = searcher.search(new TermQuery(new Term("word",word)));
			if(hits.length() == 1)
				freq = Integer.parseInt(hits.doc(0).get("freq"));

			// try different splits 
			for(int i=1;i<word.length()-1;i++){
				String s1 = word.substring(0,i);
				String s2 = word.substring(i);
				hits = phrases.search(new TermQuery(new Term("phrase",s1+"_"+s2)));
				if(hits.length() > 0){
					int pfreq = Integer.parseInt(hits.doc(0).get("freq"));
					if(pfreq >= freq)
						res.add(new SuggestSplit(s1,pfreq,s2,pfreq));
				}
			}
			
			return res;
		} catch (IOException e) {
			e.printStackTrace();			
		}
		return res;
	}
	
	public SuggestResult suggestJoinFromTitle(String word1, String word2){
		try {
			Hits hits = phrases.search(new TermQuery(new Term("word",word1+word2)));
			if(hits.length() > 0){
				int freq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(0);
				if(freq >= JOIN_FREQ)
					return new SuggestResult(word1+word2,freq);
			}
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
	}
	
	public ArrayList<SuggestResult> suggestPhrase(String word1, String word2, int num){
		String phrase = word1+"_"+word2;		
		Query q = makeWordQuery(phrase,"phrase");
		
		try {
			TopDocs docs = phrases.search(q,null,200);			
			EditDistance sd = new EditDistance(phrase);
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = phrases.doc(sc.doc);
				SuggestResult r = new SuggestResult(d.get("phrase"),
						new NamespaceFreq(d.get("freq")).getFrequency(0));
				if(phrase.equals(r.word)){
					minfreq = r.frequency;
				}
				r.dist = sd.getDistance(r.word);
				res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency <= minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new Comparator<SuggestResult>() {
				public int compare(SuggestResult o1, SuggestResult o2){					
					if(o1.dist == o2.dist){
						/*if(o1.distMetaphone == 0 && o2.distMetaphone !=0 && o2.distMetaphone2 != 0)
							return -1;
						else if (o2.distMetaphone == 0 && o1.distMetaphone !=0 && o1.distMetaphone2 != 0)
							return 1; */
						
						double d = o2.getFrequency()*o2.jaro - o1.getFrequency()*o1.jaro ;
						if(d == 0) return 0;
						if(d > 0) return 1;
						return -1;
						/*if(o1.distMetaphone == o2.distMetaphone)
							return o2.getFrequency() - o1.getFrequency();
						else
							return o1.distMetaphone - o2.distMetaphone; */
					} else 
						return o1.dist - o2.dist;					
				}
			});
			while(res.size() > num){
				res.remove(res.size()-1);
			}
			return res;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+phrase+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}
}
