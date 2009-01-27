package de.brightbyte.wikiword.analyzer;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;

import de.brightbyte.wikiword.Corpus;

public class PlainTextAnalyzer extends AbstractAnalyzer {
	private LanguageConfiguration config; 
	
	private Matcher sentenceMatcher;
	private Matcher sentenceTailGlueMatcher;
	private Matcher sentenceFollowGlueMatcher;
	private Matcher wordMatcher;

	private Corpus corpus;

	public PlainTextAnalyzer(Corpus corpus) {
		this.corpus = corpus;
		
		config = new LanguageConfiguration(); 
		config.defaults();
	}
	
	public static PlainTextAnalyzer getPlainTextAnalyzer(Corpus corpus) throws InstantiationException {
		Class[] acc = getSpecializedClasses(corpus, PlainTextAnalyzer.class, "PlainTextAnalyzer");
		Class[] ccc = getSpecializedClasses(corpus, LanguageConfiguration.class, "LanguageConfiguration", corpus.getConfigPackages());
		
		try {
			Constructor ctor = acc[0].getConstructor(new Class[] { Corpus.class });
			PlainTextAnalyzer analyzer = (PlainTextAnalyzer)ctor.newInstance(new Object[] { corpus } );
			
			for (int i = ccc.length-1; i >= 0; i--) { //NOTE: most specific last, because last write wins. 
				ctor = ccc[i].getConstructor(new Class[] { });
				LanguageConfiguration conf = (LanguageConfiguration)ctor.newInstance(new Object[] { } );
				analyzer.configure(conf);
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
	
	public void configure(LanguageConfiguration config) {
		this.config.merge(config);
	}
	
	public void initialize() {
		sentenceMatcher = config.sentencePattern.matcher("");
		sentenceTailGlueMatcher = config.sentenceTailGluePattern.matcher("");
		sentenceFollowGlueMatcher = config.sentenceFollowGluePattern.matcher("");
		wordMatcher = config.wordPattern.matcher("");
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

		sentenceMatcher.reset(text);
		sentenceTailGlueMatcher.reset(text);
		sentenceFollowGlueMatcher.reset(text);
		
		StringBuilder s = new StringBuilder();
		int pos = 0;
		boolean add = false;
		while (sentenceMatcher.find()) {
			int start = pos;
			pos = sentenceMatcher.end();
			
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
			
			sentenceFollowGlueMatcher.region(pos, text.length());
			if (sentenceFollowGlueMatcher.lookingAt()) {
				add = true;
				continue;
			}
			
			add = false;
			break;
		}
		
		if (add) {
			s.append(text.subSequence(pos, text.length()));
			pos = text.length();
		}
		
		if (pos!=0) text = trim(s);
		else trim(text);
		
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
	
}
