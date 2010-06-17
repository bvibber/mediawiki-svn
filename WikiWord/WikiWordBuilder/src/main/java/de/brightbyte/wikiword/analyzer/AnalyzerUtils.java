package de.brightbyte.wikiword.analyzer;

import java.lang.reflect.Array;
import java.util.Collection;
import java.util.HashSet;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.extractor.PropertyExtractor;
import de.brightbyte.wikiword.analyzer.sensor.Sensor;

public class AnalyzerUtils {
	
	protected static final Pattern classNameSuffixPattern = Pattern.compile(".*_(\\w+)$");
	
	public static String getClassNameSuffix(Class cls) {
		String n = cls.getName();
		Matcher m = classNameSuffixPattern.matcher(n);
		
		if (m.matches()) {
			return m.group(1);
		} else {
			return null;
		}
	}

	public static <V> Set<V> addToSet(Set<V> set, V... values) {
		if (values==null) return set;
		if (values.length==0) return set;
		
		for (V v: values) {
			if (v==null) continue;
			
			if (set==null) set = new HashSet<V>();
			set.add(v);
		}
		
		return set;
	}

	@SuppressWarnings("unchecked")
	public static <V> V[] append(V[] arr, V v, Class<V> c) {
		V[] a = (V[])Array.newInstance(c, arr==null ? 1 : arr.length+1);
		
		if (arr!=null) System.arraycopy(arr, 0, a, 0, arr.length);
		arr = a;
		arr[ arr.length -1 ] = v;
	
		return arr;
	}

	public static String getRegularExpression(Pattern pattern, boolean anchored) {
		int f = pattern.flags();
		String p = pattern.pattern();

		return getRegularExpression(p, f, anchored);
	}
	
	public static String getRegularExpression(String p, int f, boolean anchored) {
		if ((f & Pattern.LITERAL) > 0) return "(" + Pattern.quote(p) + ")";
		
		StringBuilder s = new StringBuilder();
		
		if ((f & Pattern.CASE_INSENSITIVE) > 0) s.append('i');
		if ((f & Pattern.UNIX_LINES) > 0) s.append('d');
		if ((f & Pattern.MULTILINE) > 0) s.append('m');
		if ((f & Pattern.DOTALL) > 0) s.append('s');
		if ((f & Pattern.UNICODE_CASE) > 0) s.append('u');
		if ((f & Pattern.COMMENTS) > 0) s.append('x');
		
		boolean wrap = s.length()>0;
		
		if (wrap) s.insert(0, "(?").append(':');

		if (anchored) s.append("^(?:");
		s.append(p);
		if (anchored) s.append(")$");

		if (wrap) s.append(')');
		
		return s.toString();
	}

	public static <V> V evalSensors(Collection<Sensor<V>> sensors, WikiPage page, V defValue) {
		if (sensors==null) return defValue;
		
		for (Sensor<V> sensor : sensors) {
			if (sensor.sense(page)) 
				return sensor.getValue();
		}
		
		return defValue;
	}

	public static MultiMap<String, CharSequence, Set<CharSequence>> evalPropertyExtractors(Collection<PropertyExtractor> extractors, WikiPage page) {
		if (extractors==null) return null;
		
		MultiMap<String, CharSequence, Set<CharSequence>> m = null;
		
		for (PropertyExtractor extractor : extractors) {
			m = extractor.extract(page, m);
		}
		
		if (m==null) m = ValueSetMultiMap.empty();
		return m;
	}

	public static CharSequence replaceSpaceByUnderscore(CharSequence s) {
		return StringUtils.replace(s, ' ', '_');
	}

	public static CharSequence replaceUnderscoreBySpace(CharSequence s) {
		return StringUtils.replace(s, '_', ' ');
	}

	public static void substSpaceByUnderscore(StringBuilder s) {
		StringUtils.subst(s, ' ', '_');
	}

	public static void substUnderscoreBySpace(StringBuilder s) {
		StringUtils.subst(s, '_', ' ');
	}

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

	public static CharSequence titleCase(CharSequence s) {
		if (s.length()==0) return s;
		
		if (s.charAt(0)<='Z') return s; //fast check for ascii caps first
			
		int ch = Character.codePointAt(s, 0);
		if (Character.isLowerCase(ch)) {
			int uch = Character.toUpperCase(ch);
			if (uch!=ch) {
				int w = Character.charCount(ch);
				int len= s.length();
				
				char[] uchars = Character.toChars(uch);
				char[] chars = new char[uchars.length + len - w];
				System.arraycopy(uchars, 0, chars, 0, uchars.length);
				
				if (s instanceof String) {
					((String)s).getChars(w, len, chars, uchars.length);
				}
				else if (s instanceof StringBuilder) {
					((StringBuilder)s).getChars(w, len, chars, uchars.length);
				}
				else {
					for (int i = 0; i<len-w; i++) {
						chars[i+uchars.length] = s.charAt(i);
					}
				}
				
				s = new String(chars);
			}
		}
		
		return s;
	}
}
