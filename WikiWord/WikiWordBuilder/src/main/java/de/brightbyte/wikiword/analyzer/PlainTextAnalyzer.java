package de.brightbyte.wikiword.analyzer;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;

import de.brightbyte.application.Arguments;
import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.Lookup;
import de.brightbyte.data.MapLookup;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.data.filter.FixedSetFilter;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.disambig.PhraseExtractor;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;

public class PlainTextAnalyzer extends AbstractAnalyzer implements PhraseExtractor {
	private LanguageConfiguration config; 
	
	private Matcher sentenceMatcher;
	private Matcher sentenceTailGlueMatcher;
	private Matcher sentenceFollowGlueMatcher;
	private Matcher wordMatcher;
	private Matcher wordPartMatcher;
	private Matcher namePartMatcher;
	private Matcher nameGlueMatcher;

	protected Filter<String> stopwordFilter;
	protected Matcher phraseBreakeMatcher;
	protected Lookup<String, String> bracketLookup;
	
	private Corpus corpus;

	public PlainTextAnalyzer(Corpus corpus) throws IOException {
		this.corpus = corpus;
		
		config = new LanguageConfiguration(corpus.getLanguage()); 
		config.defaults();
	}
	
	public static PlainTextAnalyzer getPlainTextAnalyzer(Corpus corpus, TweakSet tweaks) throws InstantiationException {
		Class[] acc = getSpecializedClasses(corpus, PlainTextAnalyzer.class, "PlainTextAnalyzer");
		
		try {
			Constructor<PlainTextAnalyzer> ctor = acc[0].getConstructor(new Class[] { Corpus.class });
			PlainTextAnalyzer analyzer = ctor.newInstance(new Object[] { corpus } );
	
			analyzer.configure(corpus, tweaks);
			return analyzer;
		} catch (SecurityException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (IllegalArgumentException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (NoSuchMethodException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (InvocationTargetException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (IllegalAccessException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		}
	}
	
	protected void  configure(Corpus corpus, TweakSet tweaks) throws InstantiationException {
		Class[] ccc = getSpecializedClasses(corpus, LanguageConfiguration.class, "LanguageConfiguration", corpus.getConfigPackages());
		
		try {
			for (int i = ccc.length-1; i >= 0; i--) { //NOTE: most specific last, because last write wins.
				LanguageConfiguration conf ;
				Constructor<LanguageConfiguration> ctor;
				
				try {
					ctor = ccc[i].getConstructor(new Class[] { });
					conf = ctor.newInstance(new Object[] { } );
				} 
				catch (NoSuchMethodException ex) {
					ctor = ccc[i].getConstructor(new Class[] { String.class });
					conf = ctor.newInstance(new Object[] { corpus.getLanguage() } );
				}
				
				this.configure(conf, tweaks);
			}
			
		} catch (SecurityException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (IllegalArgumentException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (NoSuchMethodException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (InvocationTargetException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		} catch (IllegalAccessException e) {
			throw (InstantiationException)new InstantiationException().initCause(e);
		}
	}
	
	public void configure(LanguageConfiguration config, TweakSet tweaks) {
		if (tweaks==null) throw new NullPointerException();
		
		this.tweaks = tweaks;
		this.config.merge(config);
	}
	
	public void initialize() {
		sentenceMatcher = config.sentencePattern.matcher("");
		sentenceTailGlueMatcher = config.sentenceTailGluePattern.matcher("");
		sentenceFollowGlueMatcher = config.sentenceFollowGluePattern.matcher("");
		wordMatcher = config.wordPattern.matcher("");
		wordPartMatcher = config.wordPartPattern.matcher("");
		namePartMatcher = config.namePartPattern.matcher("");
		nameGlueMatcher = config.nameGluePattern == null ? null : config.nameGluePattern.matcher("");
		
		phraseBreakeMatcher = config.phraseBreakerPattern.matcher("");
		stopwordFilter = new FixedSetFilter<String>(config.stopwords);
		bracketLookup = new MapLookup<String, String>(config.parentacies);
	}	
	
	
	/**
	 * expects plain text
	 * @param text
	 * @return
	 */
	public CharSequence extractFirstSentence(CharSequence text) {
		if (text==null || text.length()==0) return "";
		
		text = applyManglers(config.sentenceManglers, text);
		if (text.length()==0) return "";

		CharSequence s = scanNextSentence(text, null);
		s = AnalyzerUtils.trim(s);

		return s;
	}
	
	public CharSequence scanNextSentence(CharSequence text, ParsePosition position) {
		if (text==null || text.length()==0) return "";
		
		sentenceMatcher.reset(text);
		sentenceTailGlueMatcher.reset(text);
		sentenceFollowGlueMatcher.reset(text);
		
		int ofs = 0;
		if (position!=null) {
			ofs = position.getIndex();
			if (ofs>=text.length()) return "";
				
			sentenceMatcher.region(ofs, text.length());
			sentenceTailGlueMatcher.region(ofs, text.length());
			sentenceFollowGlueMatcher.region(ofs, text.length());
		}
		
		StringBuilder s = null;
		boolean add = false;
		while (sentenceMatcher.find()) {
			int start = ofs;
			ofs = sentenceMatcher.end();
			if (position!=null) position.setIndex(ofs);
			
			if (s==null) s = new StringBuilder();
			s.append(text, start, sentenceMatcher.end());
			
			if (sentenceMatcher.group(1)!=null) {
				add = false;
				break; //found line break //XXX: arcane knowledge!
			}
			
			sentenceTailGlueMatcher.region(start, sentenceMatcher.start());
			if (sentenceTailGlueMatcher.find()) {
				add = true;
				continue;
			}
			
			sentenceFollowGlueMatcher.region(ofs, text.length());
			if (sentenceFollowGlueMatcher.lookingAt()) {
				add = true;
				continue;
			}
			
			add = false;
			break;
		}
		
		if (add) {
			s.append(text.subSequence(ofs, text.length()));
			ofs = text.length();
			if (position!=null) position.setIndex(ofs);
		}
		
		if (s!=null) {
			text = s;
		} else {
			int end= text.length();
			
			if (position!=null && position.getIndex()>0) {
				text = text.subSequence(position.getIndex(), end);  
			} 

			if (position!=null) position.setIndex(end);
		}
		
		return text;
	}
	
	public List<String> extractWords(CharSequence s) {
		ArrayList<String> words = new ArrayList<String>();
		
		wordMatcher.reset(s); 
		while (wordMatcher.find()) {
			if (wordMatcher.groupCount()>0) words.add(wordMatcher.group(1));
			else words.add(wordMatcher.group(0));
		}
		
		return words;
	}

	public List<PhraseOccurance> extractWordOccurrance(CharSequence s) {
		ArrayList<PhraseOccurance> words = new ArrayList<PhraseOccurance>();
		
		wordMatcher.reset(s); 
		while (wordMatcher.find()) {
			int g;
			if (wordMatcher.groupCount()>0) g = 1;
			else g = 0;
			
			PhraseOccurance w = new PhraseOccurance(wordMatcher.group(g), 1, wordMatcher.start(g), wordMatcher.end(g) - wordMatcher.start(g));
			words.add(w);
		}
		
		return words;
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
	/*
	public PhraseOccuranceSet extractPhrases(CharSequence text, int maxWeight) {
		ArrayList<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();
		
		text = applyManglers(config.sentenceManglers, text);
		
		ParsePosition pos = new ParsePosition(0);
		while (pos.getIndex() < text.length()) {
			int ofs = pos.getIndex();
			CharSequence s = extractNextSentence(text, pos, false);
			if (s==null || s.length()==0) break;
			
			buildPhrases(s, ofs, phrases, maxWeight);
		}
		
		return new PhraseOccuranceSet(text.toString(), phrases);
	}

	private PhraseAggregator buildPhrasesAggregator = null; 
	
	private void buildPhrases(CharSequence text, int offset, ArrayList<PhraseOccurance> into, int maxWeight) {
		if (buildPhrasesAggregator==null) buildPhrasesAggregator = new PhraseAggregator(phraseBreakeMatcher);
		buildPhrasesAggregator.reset(offset, maxWeight);
		
		int i = 0;
		wordMatcher.reset(text); 
		while (wordMatcher.find()) {
			if (i != wordMatcher.start()) {
				CharSequence space =  text.subSequence(i, wordMatcher.start());
				buildPhrasesAggregator.update(i, space, -1, into);
			}
			
			i = wordMatcher.end();
			String w;
			int weight = 1;
			
			if (wordMatcher.groupCount()>0) w = wordMatcher.group(1);
			else w = wordMatcher.group(0);
			
			if (stopwordFilter.matches(w)) weight = 0;
			buildPhrasesAggregator.update(wordMatcher.start(), w, weight, into);
			
			//after adding the word, now register word parts 
			int j = 0;
			wordPartMatcher.reset(w);
			while (wordPartMatcher.find()) {
				if (wordPartMatcher.start() == 0  && wordPartMatcher.end() == w.length()) {
					break; //full word matched as a single part. no need to register parts.
				}
				
				if (j != wordPartMatcher.start()) {
					CharSequence glue =  w.subSequence(j, wordPartMatcher.start());
					buildPhrasesAggregator.update(i, glue, -1, into);
				}
				
				j = wordPartMatcher.end();
				weight = 1;
				String p;
				
				if (wordPartMatcher.groupCount()>0) p = wordPartMatcher.group(1);
				else p = wordPartMatcher.group(0);
				
				if (stopwordFilter.matches(p)) weight = 0;
				buildPhrasesAggregator.update(i+wordPartMatcher.start(), p, weight, into);
			}

			if (j>0 && j < w.length()) {
				CharSequence glue =  text.subSequence(j, w.length());
				buildPhrasesAggregator.update(j, glue, -1, into);
			}
		}

		if (i < text.length()) {
			CharSequence space =  text.subSequence(i, text.length());
			buildPhrasesAggregator.update(i, space, -1, into);
		}
	} */
	
	public PhraseOccuranceSet extractNames(CharSequence text, int maxWeight) {
		text = applyManglers(config.sentenceManglers, text);
		PhraseOccuranceSet names= new PhraseOccuranceSet(text.toString(), new ArrayList<PhraseOccurance>());
		
		ParsePosition pos = new ParsePosition(0);
		while (pos.getIndex() < text.length()) {
			int ofs = pos.getIndex();
			CharSequence s = scanNextSentence(text, pos);
			if (s==null || s.length()==0) break;
			
			extractNamesFromSentence(s, ofs, names, maxWeight);
		}

		names.prune(1);
		return names;
	}
	
	private void extractNamesFromSentence(CharSequence s, int ofs, PhraseOccuranceSet names, int maxWeight) {
		StringBuilder n = new StringBuilder();
		StringBuilder glue = new StringBuilder();
		
		int start = -1;
		int weight = 0;
		int pos = 0;

		wordMatcher.reset(s); 
		while (wordMatcher.find()) {
			int g;
			if (wordMatcher.groupCount()>0) g = 1;
			else g = 0;
			
			String w = wordMatcher.group(g);
			int i = wordMatcher.start(g);
			CharSequence space = s.subSequence(pos, i);
			
			if (start>=0) { //in name
				boolean doEndName = false;
				
				if ( weight >= maxWeight ) {
					doEndName = true;
				} else {
						phraseBreakeMatcher.reset(space);
						
						if (phraseBreakeMatcher.matches()) { // phrase break encountered, end name
							doEndName = true;
						}
				}
				
				if ( doEndName ) {
						names.add( new PhraseOccurance(n.toString(), weight, start, n.length()));
						
						n.setLength(0);
						start = -1;
						weight = 0;
						
						glue.setLength(0);
				}
			}

			namePartMatcher.reset(w);
			
			if (namePartMatcher.matches()) { // looks like a name part (capitalized word)
				boolean doConcat;
				if (start>=0) { //already in a name
					doConcat = true;
				} else { //new name starts
					doConcat = true;
					
					if ( i == 0 && stopwordFilter!=null ) { //start of sentence, other stuff also gets capitalized. filter at least stop words.
						String t = w.toLowerCase();
						if ( stopwordFilter.matches(t) ) {
							doConcat = false;
						}
					}
				}

				if ( doConcat ) {
					if (start<0) { // start new name
						start = i; 
					} else { // in name, process glue
						n.append(glue); 
						n.append(space); 
					}
					
					n.append(w);
					weight += 1;
					
					glue.setLength(0);
				}
			} else if (start>=0) { //not a name part, but already in a name
				if (nameGlueMatcher!=null) nameGlueMatcher.reset(w);
				
				if (nameGlueMatcher!=null && nameGlueMatcher.matches()) { // is glue word
					glue.append(space);
					glue.append(w);
				} else { //name finished
					names.add( new PhraseOccurance(n.toString(), weight, ofs + start, n.length()));
					
					n.setLength(0);
					start = -1;
					weight = 0;
					
					glue.setLength(0);
				}
			}
			
			pos = i + w.length();
		}

		if (start>=0) { //name finished
			names.add( new PhraseOccurance(n.toString(), weight, ofs + start, n.length()));
		}
	}

	public PhraseOccuranceSet extractPhrases(CharSequence text, int maxWeight, int maxDepth) {
		text = applyManglers(config.sentenceManglers, text);
		PhraseOccuranceSet phrases = new PhraseOccuranceSet(text.toString(), new ArrayList<PhraseOccurance>());
		
		ParsePosition pos = new ParsePosition(0);
		while (pos.getIndex() < text.length()) {
			int ofs = pos.getIndex();
			CharSequence s = scanNextSentence(text, pos);
			if (s==null || s.length()==0) break;
			
			buildPhrases(s, ofs, phrases, maxWeight);
			if (phrases.isEmpty()) continue;
			
			phrases.buildAggregatePhrases(ofs, 0, maxWeight, maxDepth, phraseBreakeMatcher);
		}

		if (phrases.isEmpty()) return phrases; 
		
		phrases.prune(1);
		return phrases;
	}

	private void buildPhrases(CharSequence text, int offset, PhraseOccuranceSet into, int maxWeight) {
		int i = 0;
		wordMatcher.reset(text); 
		while (wordMatcher.find()) {
			if (i != wordMatcher.start()) {
				CharSequence space =  text.subSequence(i, wordMatcher.start());
				into.add( new PhraseOccurance(space.toString(), -1, offset+i, space.length()) );
			}
			
			i = wordMatcher.end();
			String w;
			int weight = 1;
			
			if (wordMatcher.groupCount()>0) w = wordMatcher.group(1);
			else w = wordMatcher.group(0);
			
			if (stopwordFilter.matches(w)) weight = 0;
			into.add( new PhraseOccurance(w, weight, offset+wordMatcher.start(), w.length()) );
			
			//after adding the word, now register word parts 
			int j = 0;
			int b = wordMatcher.start();
			wordPartMatcher.reset(w);
			while (wordPartMatcher.find()) {
				if (wordPartMatcher.start() == 0  && wordPartMatcher.end() == w.length()) {
					break; //full word matched as a single part. no need to register parts.
				}
				
				if (j != wordPartMatcher.start()) {
					CharSequence glue =  w.subSequence(j, wordPartMatcher.start());
					into.add( new PhraseOccurance(glue.toString(), -1, offset+b+j, glue.length()) );
				}
				
				j = wordPartMatcher.end();
				weight = 1;
				String p;
				
				if (wordPartMatcher.groupCount()>0) p = wordPartMatcher.group(1);
				else p = wordPartMatcher.group(0);
				
				if (stopwordFilter.matches(p)) weight = 0;
				into.add( new PhraseOccurance(p, weight, offset+b+wordPartMatcher.start(), p.length()) );
			}

			if (j>0 && j < w.length()) {
				CharSequence glue =  text.subSequence(j, w.length());
				into.add( new PhraseOccurance(glue.toString(), -1, offset+b+j, glue.length()) );
			}
		}

		if (i < text.length()) {
			CharSequence space =  text.subSequence(i, text.length());
			into.add( new PhraseOccurance(space.toString(), -1, offset+i, space.length()) );
		}
	}
	
	public static void main(String[] argv) throws IOException, InstantiationException {
		Arguments args = new Arguments();
		args.declare("tweaks", null, true, String.class, "tweak file");
		
		args.parse(argv);
		
		String lang = args.getParameter(0);
		
		TweakSet tweaks = new TweakSet();

		String tf = args.getStringOption("tweaks", null);
		if (tf!=null) tweaks.loadTweaks(new File(tf));
		
		tweaks.setTweaks(System.getProperties(), "wikiword.tweak."); //XXX: doc
		tweaks.setTweaks(args, "tweak."); //XXX: doc
		
		Corpus corpus = Corpus.forName("TEST", lang, tweaks);
		PlainTextAnalyzer analyzer = PlainTextAnalyzer.getPlainTextAnalyzer(corpus, tweaks);
		analyzer.initialize();
		
		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));
		String s ;
		 while ( (s = in.readLine()) != null ) {
			 PhraseOccuranceSet phrases = analyzer.extractPhrases(s, 6, 6);
			 DebugUtil.dump("", phrases, ConsoleIO.output);
		}
	}
}
