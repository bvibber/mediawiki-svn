package org.wikimedia.lsearch.analyzers;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.zip.GZIPInputStream;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

public class WordNet {
	protected static Logger log = Logger.getLogger(WordNet.class);
	
	protected static enum State { NOT_INITIALIZED, FAILED, OK};
	/** initi only when needed */
	protected static State state = State.NOT_INITIALIZED;
	
	/** search tree, at each node there is a string and hashmap of next strings that would match some collocation */
	protected static final Node searchTree = new Node();
	
	protected static Boolean disabled = null;
	
	/** 
	 * Take an array of words and return variants of the array when exactly
	 * one collocation is replaced with wordnet synonym
	 * @param words
	 * @return
	 */
	public static ArrayList<ArrayList<String>> replaceOne(ArrayList<String> words, String langCode){
		if(disabled == null)
			disabled = Configuration.open().getBoolean("Search","disablewordnet");
		if(disabled)
			return null;
		if(!langCode.equals("en"))
			return null;
		ensureLoaded();
		ArrayList<ArrayList<String>> ret = new ArrayList<ArrayList<String>>();
		if(state == State.FAILED)
			return ret;
		
		for(int i=0;i<words.size();i++){
			// see if we can replace some words beginning at i
			Object[] retObjects = findSynSet(i,words);
			int len = (Integer)retObjects[0];
			String[][] synset = (String[][])retObjects[1];
			if(len != 0 && synset != null){
				// replace the matched word with each synonym
				for(String[] synonymWords : synset){
					if(!synEqual(words.subList(i,i+len),synonymWords)){
						// each replacement is a new list of words
						ArrayList<String> r = new ArrayList<String>();
						r.addAll(words.subList(0,i));
						for(String s : synonymWords)
							r.add(s);
						r.addAll(words.subList(i+len,words.size()));
						ret.add(r);
					}
				}				
			}
		}
		return ret;
	}
	
	private static boolean synEqual(List<String> s1, String[] s2) {
		if(s1.size() != s2.length)
			return false;
		for(int i=0;i<s1.size();i++){
			if(!s1.get(i).equals(s2[i]))
				return false;
		}
		return true;
	}

	protected static final Object[] findSynSet(int start, ArrayList<String> words){
		Node p = searchTree;
		String[][] synset = null;
		int len = 0;
		for(int i=start;i<words.size() && p!=null;i++){
			p = p.get(words.get(i));
			if(p != null && p.synset != null){
				synset = p.synset;
				len = i-start+1;
			}
		}
		return new Object[]{len,synset};		
	}
	
	
	protected static final void ensureLoaded() {
		// non-sync check to prevent costly syncronization
		if(state == State.NOT_INITIALIZED){
			synchronized (state) {
				// to be sure do a syncronized check
				if(state == State.NOT_INITIALIZED)
					loadWordNet();				
			}
		}
	}

	protected static class Node {
		String word = null;
		String[][] synset = null;
		HashMap<String,Node> next = null;
		
		Node() {}
		Node(String word) {
			this.word = word;
		}
		
		Node get(String word){
			if(next == null)
				return null;
			return next.get(word);
		}
		
		Node toNext(String word){
			if(next == null)
				next = new HashMap<String,Node>();
			Node n = next.get(word);
			if(n == null){
				n = new Node(word);
				next.put(word,n);
			}
			return n;
		}
		
		void initRoot(){
			next = new HashMap<String,Node>();
		}
	}
	
	protected static void loadWordNet(){		
		long start = System.currentTimeMillis();
		String path = Configuration.open().getLibraryPath() + Configuration.PATH_SEP + "dict" + Configuration.PATH_SEP + "wordnet-en.txt.gz";
		try{
			BufferedReader in;
			if(path.endsWith(".gz"))
				in = new BufferedReader(
						new InputStreamReader(
								new GZIPInputStream(
										new FileInputStream(path))));
			else 
				in = new BufferedReader(
						new InputStreamReader(
								new FileInputStream(path)));
			
			String line="";
			searchTree.initRoot();
			while((line = in.readLine())!=null){
				line = line.toLowerCase();
				String[] words = line.split(" ");
				String[][] synset = new String[words.length][]; 
				for(int i=0;i<words.length;i++)
					synset[i] = words[i].split("_|\\-"); 		
				
				for(int i=0;i<words.length;i++){
					String[] s = synset[i];
					Node p = searchTree;
					for(String w : s)
						p = p.toNext(w);
					if(p.synset != null)
						throw new RuntimeException("Inconsistent synset tree for "+Arrays.toString(s));
					p.synset = synset;
				}
			}
			
			state = State.OK;
			log.info("Loaded WordNet synonyms in "+(System.currentTimeMillis()-start)+" ms");
		} catch(Exception e){
			e.printStackTrace();
			log.warn("Cannot load WordNet synonym file : "+e.getMessage());
			state = State.FAILED;
		}
	}
}
