package de.brightbyte.wikiword.analyzer;

import java.util.ArrayList;
import java.util.Collection;
import java.util.List;

import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.mangler.Armorer;
import de.brightbyte.wikiword.analyzer.mangler.Mangler;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;

/**
 * Abstract base class for classes that analyze text. 
 * Defines several auxilliary functions, classes and interfaces for handling text.
 * 
 * @author daniel
 */
public class AbstractAnalyzer {

	protected TweakSet tweaks;

	protected static CharSequence applyManglers(Collection<Mangler> manglers, CharSequence text) {
		if (manglers==null) return text;
		
		for (Mangler mangler : manglers) {
			CharSequence t = mangler.mangle(text);
			text = t;
		}
		
		return text;
	}
	
	protected static CharSequence applyArmorers(Collection<Armorer> armorers, CharSequence text, TextArmor armor) {
		if (armorers==null) return text;
		
		for (Armorer armorer : armorers) {
			text = armorer.armor(text, armor);
		}
		
		return text;
	}
	
	/**
	 * Conveniance method for determining implementations specialized for a given language 
	 * and/or corpus. This is used by factory methods that should return language- or
	 * project-specific instances. 
	 */
	protected static Class[] getSpecializedClasses(Corpus corpus, Class baseClass, String baseName, String... prefixes) {
		List<Class> classes = new ArrayList<Class>(3);
		
		if (baseName==null) {
			baseName = baseClass.getName();
		}
		
		if (prefixes.length==0) {
			prefixes = new String[] { baseName.replaceAll("\\..*?$", "") };
			baseName = baseName.replaceAll("^.*\\.", "");
		}
		
		addSpecializedClasses(corpus, "de.brightbyte.wikiword.wikis." + baseName, classes);
		
		for (String pfx: prefixes) {
			addSpecializedClasses(corpus, pfx + "." + baseName, classes);
		}

		if (classes.size()==0) classes.add( baseClass );
		
		return (Class[]) classes.toArray(new Class[classes.size()]);
	}
	
	protected static void addSpecializedClasses(Corpus corpus, String baseName, Collection<Class> classes) {
		try {
			String n = baseName+"_"+corpus.getClassSuffix();
			Class c = Class.forName(n);
			if (!classes.contains(c)) classes.add( c );
		} catch (ClassNotFoundException e) {
			//ignore 
		}

		try {
			String n = baseName+"_"+corpus.getFamily();
			Class c = Class.forName(n);
			if (!classes.contains(c)) classes.add( c );
		} catch (ClassNotFoundException e) {
			//ignore 
		}
 
		try {
			String n = baseName+"_"+corpus.getLanguage();
			Class c = Class.forName(n);
			if (!classes.contains(c)) classes.add( c );
		} catch (ClassNotFoundException e) {
			//ignore 
		}
	}
	
	
}
