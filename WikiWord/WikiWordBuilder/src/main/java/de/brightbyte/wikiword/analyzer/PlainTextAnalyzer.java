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
import de.brightbyte.data.DefaultLookup;
import de.brightbyte.data.Lookup;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.data.filter.FixedSetFilter;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;

public class PlainTextAnalyzer extends AbstractAnalyzer {
	private LanguageConfiguration config; 
	
	private Matcher sentenceMatcher;
	private Matcher sentenceTailGlueMatcher;
	private Matcher sentenceFollowGlueMatcher;
	private Matcher wordMatcher;

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
		Class[] ccc = getSpecializedClasses(corpus, LanguageConfiguration.class, "LanguageConfiguration", corpus.getConfigPackages());
		
		try {
			Constructor ctor = acc[0].getConstructor(new Class[] { Corpus.class });
			PlainTextAnalyzer analyzer = (PlainTextAnalyzer)ctor.newInstance(new Object[] { corpus } );
			
			for (int i = ccc.length-1; i >= 0; i--) { //NOTE: most specific last, because last write wins.
				LanguageConfiguration conf ;
			
				try {
					ctor = ccc[i].getConstructor(new Class[] { });
					conf = (LanguageConfiguration)ctor.newInstance(new Object[] { } );
				} 
				catch (NoSuchMethodException ex) {
					ctor = ccc[i].getConstructor(new Class[] { String.class });
					conf = (LanguageConfiguration)ctor.newInstance(new Object[] { corpus.getLanguage() } );
				}
				
				analyzer.configure(conf, tweaks);
			}
			
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
		
		phraseBreakeMatcher = config.phraseBreakerPattern.matcher("");
		stopwordFilter = new FixedSetFilter<String>(config.stopwords);
		bracketLookup = new DefaultLookup<String, String>(config.parentacies);
	}	
	
	
	/**
	 * expects plain text
	 * @param text
	 * @return
	 */
	public CharSequence extractFirstSentence(CharSequence text) {
		return extractNextSentence(text, null, true);
	}
	
	public CharSequence extractNextSentence(CharSequence text, ParsePosition position, boolean mangle) {
		if (text==null || text.length()==0) return "";
		
		if (mangle) text = applyManglers(config.sentenceManglers, text);
		if (text.length()==0) return "";

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
		
		StringBuilder s = new StringBuilder();
		boolean add = false;
		while (sentenceMatcher.find()) {
			int start = ofs;
			ofs = sentenceMatcher.end();
			if (position!=null) position.setIndex(ofs);
			
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
		
		if (ofs!=0) text = AnalyzerUtils.trim(s);
		else {
			if (position!=null) position.setIndex(text.length());
			AnalyzerUtils.trim(text);
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

	public Corpus getCorpus() {
		return corpus;
	}
	
	public PhraseOccuranceSequence extractPhrases(CharSequence text, int maxWeight) {
		ArrayList<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();
		
		text = applyManglers(config.sentenceManglers, text);
		
		ParsePosition pos = new ParsePosition(0);
		while (pos.getIndex() < text.length()) {
			int ofs = pos.getIndex();
			CharSequence s = extractNextSentence(text, pos, false);
			if (s==null || s.length()==0) break;
			
			buildPhrases(s, ofs, phrases, maxWeight);
		}
		
		return new PhraseOccuranceSequence(text.toString(), phrases);
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
		}

		if (i < text.length()) {
			CharSequence space =  text.subSequence(i, text.length());
			buildPhrasesAggregator.update(i, space, 0, into);
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
			 PhraseOccuranceSequence phrases = analyzer.extractPhrases(s, 6);
			 DebugUtil.dump("", phrases, ConsoleIO.output);
		}
	}
}
