package de.brightbyte.wikiword.analyzer;

import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.ArmorEntry;

/**
 * Abstract base class for classes that analyze text. 
 * Defines several auxilliary functions, classes and interfaces for handling text.
 * 
 * @author daniel
 */
public class AbstractAnalyzer {

	/**
	 * A Mangler changes text in some way.
	 */
	public interface Mangler {
		public CharSequence mangle(CharSequence text);
	}
	
	/**
	 * A SuccessiveMangler changes text in some way, starting at a 
	 * given position and stopping at some point it determines based on
	 * some internal logic. May be implemented for instance to extract one 
	 * paragraph after another from a text.
	 */
	public interface SuccessiveMangler {
		public CharSequence mangle(CharSequence text, ParsePosition pp);
	}
	
	/**
	 * An Armorer replaces parts of a text by a placeholder, and stores the 
	 * placeholder along with the text that was removed in a TextArmor object,
	 * so it can be put back later. This is used to protect some parts of a
	 * text against processing.
	 */
	public interface Armorer {
		public CharSequence armor(CharSequence text, TextArmor armor);
	}

	public static final char ARMOR_MARKER_CHAR = '\u007F';
	
	public static CharSequence trim(CharSequence s) {
		return StringUtils.trim(s);
	}

	public static void strip(StringBuilder s) {
		StringUtils.strip(s);
	}
	
	public static CharSequence toLowerCase(CharSequence s) {
		return StringUtils.toLowerCase(s);
	}
	
	public static CharSequence trimAndLower(CharSequence s) {
		return toLowerCase(trim(s));
	}

	/**
	 * An TextArmor object allows an Armorer to associate placeholders the
	 * the bits of text they replaced, and can be used later to put thoses
	 * texts back into place, by replacing the placeholders. 
	 */
	public static class TextArmor implements Iterable<ArmorEntry> {
		protected List<ArmorEntry> list;
		
		public void put(String marker, String value) {
			if (list==null) list = new ArrayList<ArmorEntry>();
			list.add(new ArmorEntry(marker, value));
		}
		
		public int size() {
			return list==null ? 0 : list.size();
		}
		
		public Iterator<ArmorEntry> iterator() {
			if (list==null) return Collections.<ArmorEntry>emptyList().iterator();
			
			final ListIterator<ArmorEntry> it = list.listIterator(list.size());
			
			//NOTE: inverse iterator!
			return new Iterator<ArmorEntry>(){
			
				public void remove() {
					it.remove();
				}
			
				public ArmorEntry next() {
					return it.previous();
				}
			
				public boolean hasNext() {
					return it.hasPrevious();
				}
			
			}; 
		}

		public CharSequence unarmor(CharSequence text) {
			if (text==null) return text;
			if (list==null) return text;
			
			//prescan and bypass
			int c = text.length();
			int i=0;
			for (; i<c; i++) {
				if (text.charAt(i) == ARMOR_MARKER_CHAR) break;
			}
			
			if (i>=c) {
				return text;
			}
			
			//NOTE: armor must be undone in-sequence, in reverse order it was applied!
			
			for (ArmorEntry e: this) {
				text = StringUtils.replace(text, e.getMarker(), e.getValue());
			}
			
			return text;
		}
		
	}
	
	protected static CharSequence applyManglers(Collection<Mangler> manglers, CharSequence text) {
		if (manglers==null) return text;
		
		for (Mangler mangler : manglers) {
			text = mangler.mangle(text);
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
	
	
	/**
	 * An implementation of the Mangler interface based on regular expressions.
	 */
	public static class RegularExpressionMangler implements Mangler {
		//protected Pattern pattern;
		protected Matcher matcher;
		protected String replacement;
		
		public RegularExpressionMangler(String pattern, String replacement, int flags) {
			this(Pattern.compile(pattern, flags), replacement);
		}
		
		public RegularExpressionMangler(Pattern pattern, String replacement) {
			super();
			this.matcher = pattern.matcher("");
			this.replacement = replacement;
		}

		public CharSequence mangle(CharSequence text) {
			matcher.reset(text);
			return matcher.replaceAll(replacement);
		}
	}

	/**
	 * An implementation of the Armorer interface based on regular expressions.
	 */
	public static class RegularExpressionArmorer implements Armorer {
		protected String name;
		protected Matcher matcher;
		protected int replacementGroup;
		
		public RegularExpressionArmorer(String name, String pattern, int replacementGroup, int flags) {
			this(name, Pattern.compile(pattern, flags), replacementGroup);
		}
		
		public RegularExpressionArmorer(String name, Pattern pattern, int replacementGroup) {
			super();
			this.matcher = pattern.matcher("");
			this.name = name;
			this.replacementGroup = replacementGroup;
		}

		public CharSequence armor(CharSequence text, TextArmor armor) {
			matcher.reset(text);
			
			int i = armor.size();
			StringBuffer s = new StringBuffer(text.length());
			while(matcher.find()) {
				i++;
				if (replacementGroup>=0) {
					String r = matcher.group(replacementGroup);
					if (r==null) r = ""; //NOTE: don't skip armoring, construct might escape some syntax 
						
					String marker = ARMOR_MARKER_CHAR+"+@@@+"+ARMOR_MARKER_CHAR+name+"#"+i+ARMOR_MARKER_CHAR+"-@@@-"+ARMOR_MARKER_CHAR;
					matcher.appendReplacement(s, marker);
				
					armor.put(marker, r);
				}
				else {
					matcher.appendReplacement(s, "");
				}
			}
			
			matcher.appendTail(s);
			return s;
		}
	}
	
	
}
