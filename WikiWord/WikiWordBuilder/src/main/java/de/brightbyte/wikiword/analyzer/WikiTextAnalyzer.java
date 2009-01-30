package de.brightbyte.wikiword.analyzer;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Array;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.data.filter.Filters;
import de.brightbyte.data.measure.LevenshteinDistance;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.io.IOUtil;
import de.brightbyte.net.UrlUtils;
import de.brightbyte.util.Holder;
import de.brightbyte.util.Logger;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ConceptTypeSet;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.Languages;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.TemplateExtractor.TemplateData;
import de.brightbyte.xml.HtmlEntities;

/**
 * A WikiTextAnalyzer builds an internal represenations of a wiki page (WikiPage objects) 
 * from raw wiki text. A WikiTextAnalyzer specializes for a given Corpus (wiki project)
 * using a WikiConfiguration object, which provides language specific and project 
 * specific settings and patterns. 
 * <p><b>NOTE:</b> Instances of WikiTextAnalyzer are generally <b>not</b> thread safe!
 * You should have one instance per thread, concurrent access may result in eratic results.</p>
 */
public class WikiTextAnalyzer extends AbstractAnalyzer implements TemplateExtractor.Context {
	
	private static final Logger logger = Logger.getLogger(WikiTextAnalyzer.class); 
	
	public interface TemplateUser {
		public String getTemplateNamePattern();
	}
	
	public static class DummyTemplateUser implements TemplateUser {
		protected String pattern;
		//protected boolean anchored;
		
		public DummyTemplateUser(Matcher matcher, boolean anchored) {
			this(getRegularExpression(matcher.pattern(), anchored));
		}
		
		public DummyTemplateUser(Pattern pattern, boolean anchored) {
			this(getRegularExpression(pattern, anchored));
		}
		
		public DummyTemplateUser(String s) {
			this.pattern = s;
		}
		
		public String getTemplateNamePattern() {
			return this.pattern;
		}
	}
	
	/*
	protected class LinksByTarget implements Comparator<WikiLink> {

		private Measure<String> measure;
		private boolean strip;
		private boolean sensitive;

		public LinksByTarget(Measure<String> measure, boolean strip, boolean sensitive) {
			this.measure = measure;
			this.strip = strip;
			this.sensitive = sensitive;
		}

		public int compare(WikiLink a, WikiLink b) {
			if (a==b) return 0;
			
			String na = a.getPage();
			String nb = b.getPage();
			if (na==nb || StringUtils.equals(na, nb)) return 0;

			String ta = a.getText();
			String tb = b.getText();
			if (ta==tb || StringUtils.equals(ta, tb)) return 0;
			
			if (strip) {
				na = extractBaseName(na);
				nb = extractBaseName(nb);
				ta = extractBaseName(ta);
				tb = extractBaseName(tb);
			}
			
			if (!sensitive) {
				na = na.toLowerCase();
				nb = nb.toLowerCase();
				ta = ta.toLowerCase();
				tb = tb.toLowerCase();
			}
			
			double d = Math.min(measure.measure(na), measure.measure(ta)) 
				- Math.min(measure.measure(nb), measure.measure(tb)); //XXX: provide max option?
			
			if (d<0) return (int)(d-1);
			else if (d>0) return (int)(d+1);
			else return 0;
		}

	}*/

	protected abstract class LinkFilter implements Filter<WikiLink> {

		protected CharSequence basename;
		
		public LinkFilter(CharSequence name) {
			this.basename = name;
		}

		public boolean matches(WikiLink link) {
			CharSequence n = link.getLenientPage();
			if (matches(n)) return true;

			CharSequence s = link.getLenientSection();
			if (s!=null && !StringUtils.equals(s, n) && matches(s)) return true; 
			
			CharSequence t = link.getLenientText();
			if (!StringUtils.equals(t, n) && matches(t)) return true; 
			
			return false;
		}

		protected abstract boolean matches(CharSequence t);

	}

	protected class SubStringLinkFilter extends LinkFilter {

		public SubStringLinkFilter(CharSequence name) {
			super(name);
		}

		@Override
		protected boolean matches(CharSequence t) {
			return StringUtils.indexOf(basename, t) >= 0;
		}

	}

	/*
	protected class BaseNameLinkFilter extends LinkFilter {

		public BaseNameLinkFilter(String name, boolean sensitive) {
			super(name, sensitive);
		}

		@Override
		protected boolean matches(String t) {
			return StringUtils.equals(basename, t);
		}

	}
    */
	
	public static class ArmorEntry {
		protected String marker;
		protected String value;
		
		public ArmorEntry(String marker, String value) {
			if (marker==null) throw new NullPointerException();
			if (value==null) throw new NullPointerException();

			this.marker = marker;
			this.value = value;
		}
		
		public String getMarker() {
			return marker;
		}
		
		public String getValue() {
			return value;
		}
		
		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((marker == null) ? 0 : marker.hashCode());
			result = PRIME * result + ((value == null) ? 0 : value.hashCode());
			return result;
		}
		
		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final ArmorEntry other = (ArmorEntry) obj;
			if (marker == null) {
				if (other.marker != null)
					return false;
			} else if (!StringUtils.equals(marker, other.marker))
				return false;
			if (value == null) {
				if (other.value != null)
					return false;
			} else if (!StringUtils.equals(value, other.value))
				return false;
			return true;
		}
		
		@Override
		public String toString() {
			return marker + " => " + value;
		}
	}
	
	public interface NameMatcher {
		
		public boolean matches(CharSequence s);
		public boolean matchesLine(String lines);
		
		public String getRegularExpression();
		
		public <V> Iterable<V> matches(Map<? extends CharSequence, V> m);
		public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m);
		public <V extends CharSequence> Iterable<V> matches(Set<V> values);

	}

	public static abstract class AbstractNameMatcher implements NameMatcher {

		public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m) {
			final Iterator it = m.entrySet().iterator();
			return matches(it);
		}
		
		public <V> Iterable<V> matches(Map<? extends CharSequence, V> m) {
			final Iterator it = m.entrySet().iterator();
			return matches(it);
		}
		
		public <V extends CharSequence> Iterable<V> matches(Set<V> m) {
			final Iterator it = m.iterator();
			return matches(it);
		}
		
		protected <V> Iterable<V> matches(final Iterator it) {
			
			return new Iterable<V>() {
				public Iterator<V> iterator() {
					return new Iterator<V>() {
						
						private V next;
						private boolean hasNext;
						
						{ scan(); }
						
						private void scan() {
							hasNext = false;
							while (it.hasNext()) {
								hasNext = true;
								Map.Entry<? extends CharSequence, V> e = (Map.Entry<? extends CharSequence, V>) it.next();
								next = e.getValue();
								if (matches(e.getKey())) break;
								hasNext = false;
							}
						}
					
						public void remove() {
							throw new UnsupportedOperationException();
						}
					
						public V next() {
							if (!hasNext) return null;
							
							V n = next;
							scan();
							return n;
						}
					
						public boolean hasNext() {
							return hasNext;
						}
					
					};
				}
			};
		}
	}
	
	public static class AnyNameMatcher implements NameMatcher {
		
		public AnyNameMatcher() {
			//noop
		}

		public String getRegularExpression() {
			return ".*";
		}

		public boolean matches(CharSequence s) {
			return true;
		}

		public boolean matchesLine(String s) {
			return true;
		}

		public <V> Iterable<V> matches(Map<? extends CharSequence, V> m) {
			return m.values();
		}

		public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m) {
			return m.values();
		}

		public <V extends CharSequence> Iterable<V> matches(Set<V> values) {
			return values;
		}
	}
	
	public static class ExactNameMatcher implements NameMatcher {
		private String name;
		
		public ExactNameMatcher(String name) {
			if (name==null) throw new NullPointerException();
			this.name = name;
		}

		public String getRegularExpression() {
			return "^" + Pattern.quote(name) + "$";
		}

		public boolean matches(CharSequence s) {
			return StringUtils.equals(name, s);
		}

		public boolean matchesLine(String s) {
			int i = s.indexOf(name);
			if (i<0) return false;
			if (i>0 && s.charAt(i-1)!='\n') return false;
			
			int c = name.length();
			int j = i+c;
			
			if (j<s.length()-1 && s.charAt(j+1)!='\n') return false;
			
			return true;
		}

		public <V> Iterable<V> matches(Map<? extends CharSequence, V> m) {
			V v = m.get(name);
			if (v==null) return Collections.emptySet();
			else return Collections.singleton(v);
		}

		public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m) {
			C c = m.get(name);
			if (c==null) return Collections.emptySet();
			else return Collections.singleton(c);
		}

		@SuppressWarnings("unchecked")
		public <V extends CharSequence> Iterable<V> matches(Set<V> values) {
			if (values.contains(name)) return Collections.singleton((V)name);
			else return Collections.emptySet();
		}
	}
	
	public static class PatternNameMatcher extends AbstractNameMatcher {
		protected Matcher matcher;
		protected boolean anchored;

		public PatternNameMatcher(String p, int flags, boolean anchored) {
			this(Pattern.compile(p, flags), anchored);
		}
		
		public PatternNameMatcher(Pattern p, boolean anchored) {
			this(p.matcher(""), anchored);
		}
		
		public PatternNameMatcher(Matcher matcher, boolean anchored) {
			if (matcher==null) throw new NullPointerException();
			this.matcher = matcher;
			this.anchored = anchored;
			
			if (anchored) {
				String p = matcher.pattern().pattern();
				if (p.startsWith("^") || p.endsWith("$")) {
					logger.warn("Anchored pattern contains anchor mark: "+p);
				}
			}
		}

		public String getRegularExpression() {
			return WikiTextAnalyzer.getRegularExpression(matcher.pattern(), anchored);
		}

		public boolean matchesLine(String s) {
			if ((matcher.pattern().flags() & Pattern.MULTILINE)==0) throw new UnsupportedOperationException();
			return matches(s);
		}
		
		public boolean matches(CharSequence s) {
			matcher.reset(s);
			
			if (anchored) return matcher.matches();
			else return matcher.find();
		}
	}
	
	/**
	 * Sensor to detect if a WikiPage matches some condition. 
	 * In addition, a Sensor defines a value that is associated
	 * with matching the condition.
	 * For example, if the Sensor matches a pattern in the page title, 
	 * which indicates the type of page, then the value would represent
	 * that type of page.
	 */
	public interface Sensor<V> {
		
		/** returns true iff the given WikiPage matches the condition
		 * this sensor represents.
		 **/
		public boolean sense(WikiPage page);
		
		/**
		 * returns the value of this sensor, that is, the value that is 
		 * associated with matching the condition this Sensor represents.
		 */
		public V getValue();
	}
	
	public static abstract class AbstractSensor<V> implements Sensor<V> {
		protected V value;
		
		public AbstractSensor(V value) {
			super();
			this.value = value;
		}

		public abstract boolean sense(WikiPage page);
		
		public V getValue() {
			return value;
		}
		
		/*
		public AbstractSensor setValue(Object v) {
			if (value!=null) throw new IllegalStateException("value already set");
			value = v;
			return this;
		}
		*/
	}
	
	public static class EntityDecodeMangler implements Mangler {
		public CharSequence mangle(CharSequence text) {
			return HtmlEntities.decodeEntities(text);
		}
	}
	
	public static class StripMarkupMangler implements Mangler {
		private WikiTextAnalyzer analyzer;

		public StripMarkupMangler(WikiTextAnalyzer analyzer) {
			if (analyzer==null) throw new NullPointerException();
			this.analyzer = analyzer;
		}
		
		public CharSequence mangle(CharSequence text) {
			return this.analyzer.stripMarkup(text);
		}
	}
	
	public static class BoxStripMangler implements Mangler, SuccessiveMangler {
		protected Matcher matcher;
		protected String[] beginnings;
		protected String[] ends;
		
		public BoxStripMangler(String[] beginnings, String[] ends, String stop) {
			this.beginnings = beginnings;
			this.ends = ends;
			
			if (beginnings.length!=ends.length) throw new IllegalArgumentException("must have the same number of ends and beginnings");
			
			StringBuilder regex = new StringBuilder();
			for (int i = 0; i < beginnings.length; i++) {
				if (i>0) regex.append('|');
				regex.append('(');
				regex.append(beginnings[i]);
				regex.append(')');
			}
			for (int i = 0; i < ends.length; i++) {
				regex.append('|');
				regex.append('(');
				regex.append(ends[i]);
				regex.append(')');
			}
			
			if (stop!=null) {
				regex.append('|');
				regex.append('(');
				regex.append(stop);
				regex.append(')');
			}
			
			Pattern pattern = Pattern.compile(regex.toString(), Pattern.CASE_INSENSITIVE | Pattern.MULTILINE);
			matcher = pattern.matcher("");
		}
		
		public CharSequence mangle(CharSequence text) {
			return mangle(text, null);
		}
		
		public CharSequence mangle(CharSequence text, ParsePosition pp) {
			StringBuilder out = new StringBuilder(text.length());
			
			int[] stack = new int[100];
			int level = 0;
			int last = pp == null ? 0 : pp.getIndex();
			
			//System.out.print("."); System.out.flush();
			
			matcher.reset(text);
			if (pp!=null) matcher.region(pp.getIndex(), text.length());
			while (matcher.find()) {
				if (level==0) out.append(text, last, matcher.start());
				if (pp!=null) pp.setIndex(matcher.start());
				
				int marker = getMarkerId(matcher);
				//System.out.println(marker+": "+m.group(0));
				
				if (marker==0) { //end
					if (level==0) {
						if (trim(out).length()>0 || matcher.group().trim().length()>0) {
							return out; //NOTE: hefty hack //TODO: min length?
						}
					}
				}
				else if (marker>0) { //push
					if (level<stack.length) {
						stack[level] = -marker;
						level++;
					}
				}
				else { //pop
					if (level>0) {
						if (marker==stack[level-1]) {
							level--;
						}
						else { //mismatching closing thingy!
							//scan up the stack for matching open block
							//XXX: maybe use precedence order (level ordinals?)
							int lvl = level;
							while (lvl>0 && marker!=stack[lvl-1]) {
								lvl--;
							}
							
							if (lvl>0) {
								level = lvl -1;
							}
						}
					}
				}
				
				last = matcher.end();
				if (pp!=null) pp.setIndex(last);
			}
			
			if (level==0) {
				out.append(text, last, text.length());
				if (pp!=null) pp.setIndex(text.length());
			}
			
			return out;
		}
		
		protected int getMarkerId(Matcher m) {
			for (int i=1; i<=beginnings.length*2; i++) {
				if (m.group(i)!=null) {
					if (i<=beginnings.length) return i;
					else return -i +beginnings.length;
				}
			}
			
			if (m.group(beginnings.length*2+1)!=null) {
				return 0;
			}
			
			throw new RuntimeException("no group matched (can't happen!)");
		}
		
	}
	
	public static class MultiSensor<V> extends AbstractSensor<V> {
		protected Sensor<V>[] sensors;

		public MultiSensor(V value, Sensor<V>... sensors) {
			super(value);
			this.sensors = sensors;
		}
		
		@Override
		public boolean sense(WikiPage page) {
			for(Sensor<V> s: sensors) {
				if (!s.sense(page)) return false;
			}
			
			return true;
		}
	}
	
	public static class CleanedTextSensor<V> extends AbstractSensor<V> {
		protected NameMatcher matcher;
		
		public CleanedTextSensor(V value, String pattern, int flags) {
			this(value, new PatternNameMatcher(pattern, flags, false));
		}
		
		public CleanedTextSensor(V value, NameMatcher matcher) {
			super(value);
			this.matcher = matcher;
		}

		@Override
		public boolean sense(WikiPage page) {
			return matcher.matches( page.getCleanedText(true) );
		}
	}
	
	public static class TitleSensor<V> extends AbstractSensor<V> {
		protected NameMatcher matcher;
		protected int namespace;
		
		public TitleSensor(V value, String pattern, int flags) {
			this(value, Namespace.NONE, pattern, flags);
		}
		
		public TitleSensor(V value, NameMatcher matcher) {
			this(value, Namespace.NONE, matcher);
		}
		
		public TitleSensor(V value, int ns, String pattern, int flags) {
			this(value, ns, new PatternNameMatcher(pattern, flags, true));
		}
		
		public TitleSensor(V value, int ns, NameMatcher matcher) {
			super(value);
			this.matcher = matcher;
			this.namespace = ns;
		}

		@Override
		public boolean sense(WikiPage page) {
			if (namespace!=Namespace.NONE && namespace!=page.getNamespace()) return false;
			return matcher.matches(page.getName());
		}
	}
	
	public static class PropertyValueExtractor implements ValueExtractor {
		protected String property;
		private String prefix;
		private String suffix;
		private boolean normalize = true;
		
		public PropertyValueExtractor(String property) {
			super();
			this.property = property;
		}

		public PropertyValueExtractor setPrefix(String prefix) {
			this.prefix = prefix;
			return this;
		}

		public PropertyValueExtractor setSuffix(String suffix) {
			this.suffix = suffix;
			return this;
		}
		
		public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
			Set<CharSequence> vv = page.getProperties().get(property);
			
			if (vv!=null && !vv.isEmpty()) {
				if (into==null) into = new HashSet<CharSequence>();
				
				for(CharSequence v: vv) {
					//TODO: use builder?!
					if (prefix!=null) v = prefix + v;
					if (suffix!=null) v = v + suffix;
					
					if (normalize) v = page.getAnalyzer().normalizeTitle(v);
					into.add(v);
				}
			}
			
			return into;
		}
		
	}
	
	public static class TitlePartExtractor implements ValueExtractor {
		protected Matcher matcher;
		protected int namespace;
		protected String replacement;
		protected List<Sensor<?>> conditions = null;
		protected boolean normalize= true;
		
		public TitlePartExtractor(String pattern, int flags, String replacement) {
			this(Namespace.NONE, pattern, flags, replacement);
		}
		
		public TitlePartExtractor(Pattern pattern, String replacement) {
			this(Namespace.NONE, pattern, replacement);
		}
		
		public TitlePartExtractor(int ns, String pattern, int flags, String replacement) {
			this(ns, Pattern.compile(pattern, flags), replacement);
		}
		
		public TitlePartExtractor(int ns, Pattern pattern, String replacement) {
			this.matcher = pattern.matcher("");
			this.namespace = ns;
			this.replacement = replacement;
		}

		public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
			if (namespace!=Namespace.NONE && namespace!=page.getNamespace()) return into;
			
			if (conditions!=null) {
				for(Sensor<?> sensor: conditions) {
					if (!sensor.sense(page)) return into;
				}
			}
			
			matcher.reset(page.getName());
			if (!matcher.matches()) return into;

			CharSequence v = matcher.replaceFirst(replacement);
			if (normalize) v = page.getAnalyzer().normalizeTitle(v);
			return addToSet(into, v);
		}
		
		public TitlePartExtractor addCondition(Sensor<?> sensor) { 
			if (conditions==null) conditions = new ArrayList<Sensor<?>>();
			conditions.add(sensor);
			return this;
		}
	}
	
	
	protected static <V> Map<String, V> paramKeyMap(String[] params) {
		if (params==null) return null;
		
		HashMap<String, V> m = new HashMap<String, V>();
		for (String p: params) {
			m.put(p, null);
		}
		
		return m;
	}
	
	public static class HasTemplateLikeSensor<V> extends AbstractSensor<V> implements TemplateUser {
		protected NameMatcher matcher;
		protected Map<String, NameMatcher> params;
		
		//TODO: provide an OR mode, so this triggers if *any* param matches
		
		public HasTemplateLikeSensor(V value, String pattern, int flags) {
			this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false), null);
		}
		
		public HasTemplateLikeSensor(V value, String pattern, int flags, String[] params) {
			this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false), WikiTextAnalyzer.<NameMatcher>paramKeyMap(params));
		}
		
		public HasTemplateLikeSensor(V value, NameMatcher matcher, Map<String, NameMatcher> params) {
			super(value);
			this.matcher = matcher;
		}

		@Override
		public boolean sense(WikiPage page) {
			if (params==null) {
				CharSequence t = page.getTemplatesString();
				return matcher.matchesLine(t.toString());
			}
			
			MultiMap<String, TemplateData, List<TemplateData>> templates = page.getTemplates();
			if (templates.size()==0) return false;
			
			templateLoop : for (List<TemplateExtractor.TemplateData> ll: matcher.matches(templates)) {
					if (params==null) return true;
				
					for (TemplateExtractor.TemplateData tpl: ll) {
						for (Map.Entry<String, NameMatcher> f: params.entrySet()) {
							String key = f.getKey();
							NameMatcher valueMatcher = f.getValue();
							
							CharSequence value = tpl.getParameter(key);
							if (value!=null) {
								if (valueMatcher==null) ;
								else {
									if (valueMatcher.matches(value)) ;
									else continue templateLoop;
								}
							}
							else continue templateLoop;
						}
						
						return true;
					}
			}
			
			return false;
		}

		public String getTemplateNamePattern() {
			return matcher.getRegularExpression();
		}
	}
	
	public static class HasTemplateSensor<V> extends HasTemplateLikeSensor<V> {
		
		public HasTemplateSensor(V value, String pattern) {
			this(value, new ExactNameMatcher(pattern), null);
		}
		
		public HasTemplateSensor(V value, String pattern, String[] params) {
			this(value, new ExactNameMatcher(pattern), WikiTextAnalyzer.<NameMatcher>paramKeyMap(params));
		}
		
		private HasTemplateSensor(V value, NameMatcher matcher, Map<String, NameMatcher> params) {
			super(value, matcher, params);
		}
	}
	
	public static class HasPropertySensor<V> extends AbstractSensor<V> {
		protected String name; 
		protected NameMatcher matcher; 
		
		public HasPropertySensor(V value, String name) {
			this(value, name, null);
		}
		
		public HasPropertySensor(V value, String name, String pattern, int flags) {
			this(value, name, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false));
		}
		
		public HasPropertySensor(V value, String name, NameMatcher matcher) {
			super(value);
			this.matcher = matcher;
		}

		@Override
		public boolean sense(WikiPage page) {
			Set<CharSequence> vv = page.getProperties().get(name);
			
			if (matcher==null) {
				return vv!=null && !vv.isEmpty();
			}
			else {
				return matcher.matches(vv).iterator().hasNext();
			}
		}
	}
	
	public static class HasCategoryLikeSensor<V> extends AbstractSensor<V> {
		//protected Pattern pattern;
		protected NameMatcher matcher; 
		
		public HasCategoryLikeSensor(V value, String pattern, int flags) {
			this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false));
		}
		
		public HasCategoryLikeSensor(V value, NameMatcher matcher) {
			super(value);
			this.matcher = matcher;
		}

		@Override
		public boolean sense(WikiPage page) {
			String categories = page.getCategoriesString();
			return matcher.matchesLine(categories);
		}
	}
	
	public static class HasCategorySensor<V> extends HasCategoryLikeSensor<V> {
		
		public HasCategorySensor(V value, String category) {
			super(value, new ExactNameMatcher(category));
		}
	}
	
	public static class HasSectionLikeSensor<V> extends AbstractSensor<V> {
		protected NameMatcher matcher;
		
		public HasSectionLikeSensor(V value, String pattern, int flags) {
			this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false));
		}
		
		public HasSectionLikeSensor(V value, NameMatcher matcher) {
			super(value);
			this.matcher = matcher;
		}

		@Override
		public boolean sense(WikiPage page) {
			String sections = page.getSectionsString();
			return matcher.matchesLine(sections);
		}
	}
	
	public static class HasSectionSensor<V> extends HasSectionLikeSensor<V> {
		
		public HasSectionSensor(V value, String section) {
			super(value, new ExactNameMatcher(section));
		}

	}
	
	/**
	 * Extractor to extract some value from a WikiPage. 
	 */
	public interface ValueExtractor {
		
		public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into);
	}
	
	public static class PagePropertyValueExtractor implements ValueExtractor {
		protected String name;

		public PagePropertyValueExtractor(String name) {
			this.name = name;
		}

		public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
			Set<CharSequence> vv = page.getProperties().get(name);
			if (vv!=null && vv.size()>0) {
				if (into==null) into = new HashSet<CharSequence>();
				into.addAll(vv);
			}
			
			return into;
		}
		
	}
	
	/**
	 * Extractor to extract some value from a WikiPage. 
	 */
	public interface PropertyExtractor {
		
		/** extracts properties and returns them as a map. If a map instance is provided,
		 * that instance will be used to store the properties. Otherwise, a new instance
		 * will be created if any properties have been found. If no map was provided and no
		 * properties have been found, this method returns null. 
		 **/
		public MultiMap<String, CharSequence, Set<CharSequence>> extract(WikiPage page, MultiMap<String, CharSequence, Set<CharSequence>> into);
	}

	/**
	 * Property specification for properties derived from template parameters 
	 */
	public interface TemplateParameterPropertySpec {
		
		/** determins a property value from a map of template parameters.
		 **/
		public Set<CharSequence> getPropertyValues(WikiPage page, TemplateExtractor.TemplateData params, Set<CharSequence> values);
		
		public String getPropertyName();
	}
	
	public static abstract class AbstractTemplateParameterPropertySpec implements TemplateParameterPropertySpec {
		protected String propertyName;
		
		public AbstractTemplateParameterPropertySpec(String name) {
			if (name==null) throw new NullPointerException();
			this.propertyName = name;
		}
		
		public String getPropertyName() {
			return propertyName;
		}

		public Set<CharSequence> getPropertyValues(WikiPage page, TemplateExtractor.TemplateData params, Set<CharSequence> values) {
			CharSequence v = getPropertyValue(page, params); 
			if (v==null) return values;
			
			if (values==null) values = new HashSet<CharSequence>();
			values.add(v);
			
			return values;
		}

		protected abstract CharSequence getPropertyValue(WikiPage page, TemplateExtractor.TemplateData params);
		
	}
	
	protected static <V> Set<V> addToSet(Set<V> set, V... values) {
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
	protected static <V> V[] append(V[] arr, V v, Class<V> c) {
		V[] a = (V[])Array.newInstance(c, arr==null ? 1 : arr.length+1);
		
		if (arr!=null) System.arraycopy(arr, 0, a, 0, arr.length);
		arr = a;
		arr[ arr.length -1 ] = v;

		return arr;
	}
	
	public static class DefaultTemplateParameterPropertySpec implements TemplateParameterPropertySpec {
		protected Mangler[] norm = null;
		protected Mangler[] clean = null;
		protected Matcher find = null;
		protected Matcher split = null;
		
		protected NameMatcher cond = null;
		
		protected boolean upper = false;
		protected boolean strip = false;
		
		protected String property;
		protected String parameter;
		
		protected String prefix = null;
		protected String suffix = null;
		
		public DefaultTemplateParameterPropertySpec(String name, String prop) {
			if (name==null) throw new NullPointerException();
			if (prop==null) throw new NullPointerException();
			
			this.parameter = name;
			this.property = prop;
		}
		
		public DefaultTemplateParameterPropertySpec addCleanup(Pattern pattern, String rep) {
			return addCleanup(new RegularExpressionMangler(pattern, rep));
		}
		
		public DefaultTemplateParameterPropertySpec addCleanup(Mangler clean) {
			if (clean==null) return this;
			
			this.clean = append(this.clean, clean, Mangler.class);
			return this;
		}
		
		public DefaultTemplateParameterPropertySpec setCondition(NameMatcher cond) {
			this.cond = cond;
			return this;
		}
		
		public DefaultTemplateParameterPropertySpec setCondition(Pattern p, boolean anchored) {
			return setCondition( new PatternNameMatcher(p, anchored));
		}
		
		public DefaultTemplateParameterPropertySpec setCondition(String p, int flags, boolean anchored) {
			return setCondition( new PatternNameMatcher(p, flags, anchored));
		}
		
		public DefaultTemplateParameterPropertySpec addNormalizer(Pattern pattern, String rep) {
			return addNormalizer(new RegularExpressionMangler(pattern, rep));
		}
		
		public DefaultTemplateParameterPropertySpec addNormalizer(Mangler norm) {
			if (norm==null) return this;
			
			this.norm = append(this.norm, norm, Mangler.class);
			return this;
		}

		public DefaultTemplateParameterPropertySpec setSplitPattern(Pattern split) {
			this.split = split==null ? null : split.matcher("");
			return this;
		}

		public DefaultTemplateParameterPropertySpec setFindPattern(Pattern find) {
			this.find = find==null ? null : find.matcher("");
			return this;
		}

		public DefaultTemplateParameterPropertySpec setToUpperCase(boolean upper) {
			this.upper = upper;
			return this;
		}

		public DefaultTemplateParameterPropertySpec setStripMarkup(boolean strip) {
			this.strip = strip;
			return this;
		}

		public DefaultTemplateParameterPropertySpec setPrefix(String prefix) {
			this.prefix = prefix;
			return this;
		}

		public DefaultTemplateParameterPropertySpec setSuffix(String suffix) {
			this.suffix = suffix;
			return this;
		}

		public Set<CharSequence> getPropertyValues(WikiPage page, TemplateExtractor.TemplateData params, Set<CharSequence> values) {
			CharSequence v = params.getParameter(parameter);
			if (v==null) return values;
			if (v.length()==0) return values;
			
			if (clean!=null) {
				for (Mangler m: clean) v = m.mangle(v);
			}
			
			if (cond!=null) {
				if (!cond.matches(v)) return values;
			}
			
			if (split!=null) {
				int i = 0;
				int j = 0;
				
				split.reset(v);
				boolean done = false;
				
				while (i<v.length()) {
					if (split.find(i)) {
						j = split.start();
					}
					else {
						j = v.length();
						done = true;
					}
					
					CharSequence w = v.subSequence(i, j);
					
					if (done) i = j;
					else i = split.end();
					
					values = addValue(w, page, values);
				}
			}
			else if (find!=null) {
				find.reset(v);
				while (find.find()) {
					CharSequence w = find.groupCount() > 0 ? find.group(1) : find.group();
					
					values = addValue(w, page, values);
				}
			}
			else if (split==null) {
				values = addValue(v, page, values);
			}
			
			return values;
		}
		
		protected Set<CharSequence> addValue(CharSequence w, WikiPage page, Set<CharSequence> values) {
			if (w==null || w.length()==0) return values;
			
			w = trim(w);
			if (w.length()==0) return values;
			
			w = mangle(page, w);
			if (w.length()==0) return values;
			
			values = addToSet(values, w);
			return values;
		}
		
		protected CharSequence mangle(WikiPage page, CharSequence v) {
			if (strip) {
				WikiTextAnalyzer analyzer = page.getAnalyzer(); 
				/*if (analyzer.smellsLikeWikiText(v))*/ v = analyzer.stripMarkup(v);
			}
			
			if (norm!=null) {
				for (Mangler m: norm) v = m.mangle(v);
			}
			
			if (upper) v = v.toString().toUpperCase();
			
			//TODO: use builder!
			if (prefix!=null) v = prefix + v;
			if (suffix!=null) v = v + suffix;
			
			return v;
		}

		public String getPropertyName() {
			return property;
		}
		
	}
	
	public static class TemplateParameterExtractor implements PropertyExtractor, TemplateUser {
		protected NameMatcher template;
		protected TemplateParameterPropertySpec[] properties;
		
		public TemplateParameterExtractor(String template, int flags, TemplateParameterPropertySpec... properties) {
			this(new ExactNameMatcher(template), properties);
		}
		
		public TemplateParameterExtractor(Pattern template, TemplateParameterPropertySpec... properties) {
			this(new PatternNameMatcher(template, true), properties);
		}
		
		public TemplateParameterExtractor(NameMatcher template, TemplateParameterPropertySpec... properties) {
			if (template==null) throw new NullPointerException();
			if (properties==null) throw new NullPointerException();
			
			this.template = template;
			this.properties = properties;
		}

		public MultiMap<String, CharSequence, Set<CharSequence>> extract(WikiPage page, MultiMap<String, CharSequence, Set<CharSequence>> into) {
			MultiMap<String, TemplateExtractor.TemplateData, List<TemplateExtractor.TemplateData>> tpl = page.getTemplates();
			
			for (Iterable<TemplateExtractor.TemplateData> list : template.matches(tpl)) {
				for (TemplateExtractor.TemplateData m: list) {
					for (TemplateParameterPropertySpec prop: properties) {

						Set<CharSequence> set = null;
						set = prop.getPropertyValues(page, m, set);

						if (set!=null) {
							if (into==null) into = new ValueSetMultiMap<String, CharSequence>();
							into.putAll(prop.getPropertyName(), set);
						}
					}
				}
			}
			
			return into;
		}

		public String getTemplateNamePattern() {
			return template.getRegularExpression();
		}
		
	}
	
	protected static String getRegularExpression(Pattern pattern, boolean anchored) {
		int f = pattern.flags();
		String p = pattern.pattern();
		
		if ((f & Pattern.LITERAL) > 0) return "(" + Pattern.quote(p) + ")";
		
		StringBuilder s = new StringBuilder();
		
		if ((f & Pattern.CASE_INSENSITIVE) > 0) s.append('i');
		if ((f & Pattern.UNIX_LINES) > 0) s.append('d');
		if ((f & Pattern.MULTILINE) > 0) s.append('m');
		if ((f & Pattern.DOTALL) > 0) s.append('s');
		if ((f & Pattern.UNICODE_CASE) > 0) s.append('u');
		if ((f & Pattern.COMMENTS) > 0) s.append('x');
		
		if (s.length()>0) s.insert(0, "(?").append(':').append(p).append(')');
		else s.append(p);
			
		if (anchored) s.insert(0, "^").append("$");
		
		return s.toString();
	}
	
	public static enum LinkMagic {
		NONE, 
		CATEGORY,
		IMAGE,
		LANGUAGE,
		REDIRECT
	}
	
	public static final Filter<WikiLink> articleLinks = new Filter<WikiLink>() {

		public boolean matches(WikiLink link) {
			if (link.getMagic()!=LinkMagic.NONE) return false;
			if (link.getNamespace()!=Namespace.MAIN) return false;
			if (link.getInterwiki()!=null) return false;
			return true;
		}
		
	};
	
	public interface LinkSimilarityMeasureFactory {
		public Measure<WikiLink> newLinkSimilarityMeasure(CharSequence basename, WikiTextAnalyzer analyzer);
	}
	

	public static class DefaultLinkSimilarityMeasure implements Measure<WikiLink> {
		protected CharSequence basename;
		protected WikiTextAnalyzer analyzer;

		protected static final LevenshteinDistance levenshteinDistance = new LevenshteinDistance(true, 255);
		
		protected List<String> basewords;
		
		public DefaultLinkSimilarityMeasure(CharSequence basename, WikiTextAnalyzer analyzer) {
			this.analyzer = analyzer;
			this.basename = toLowerCase(analyzer.normalizeTitle(basename));
		}

		public double measure(WikiLink link) {
			double sim = 0;
			
			//NOTE: all spaces have been normalized to underscores!
			
			CharSequence p = link.getLenientPage();
			CharSequence s = link.getLenientSection();
			CharSequence t = link.getLenientText();

			if (s!=null && StringUtils.equals(s, p)) s = null;
			if (StringUtils.equals(t, p)) t = null;
			
			if (StringUtils.equals(basename, p)) return 1;
			if (s!=null && StringUtils.equals(basename, s)) return 1;
			if (t!=null && StringUtils.equals(basename, t)) return 1;
			
			double ps = measureTextMatch(p);
			if (ps>sim) sim = ps;

			if (s!=null && sim < 1) {
				double ss = measureTextMatch(s);
				if (ss>sim) sim = ss;
			}
							
			if (t!=null && sim < 1) {
				double ts = measureTextMatch(t);
				if (ts>sim) sim = ts;
			}
			
			return sim;
		}

		public double measureTextMatch(CharSequence text) {
			double sim = 0;
			
			double sssim = calculateSubstringSimilarity(basename, text);
			if (sssim>sim) sim = sssim;
			if (sim>=1) return sim;
			
			double levsim = calculateLevenshteinSimilarity(basename, text);
			if (levsim>sim) sim = levsim;
			if (sim>=1) return sim;
			
			if (basewords==null) basewords = analyzer.language.extractWords(replaceUnderscoreBySpace(basename));
			List<String> textwords = analyzer.language.extractWords(replaceUnderscoreBySpace(text));
			
			double wsim = calculateWordOverlapSimilarity(basewords, textwords);
			if (wsim>sim) sim = wsim;
			if (sim>=1) return sim;
			
			double acrosim = calculateAcronymSimilarity(basename, textwords);
			if (acrosim>sim) sim = acrosim;
			if (sim>=1) return sim;
			
			return sim;
		}

		protected double calculateAcronymSimilarity(CharSequence basename, List<String> textwords) {
			if (textwords.size() < basename.length()) return 0;
			
			StringBuilder acro = new StringBuilder();
			for (String w: textwords) {
				acro.append(w.charAt(0));
			}
			
			return calculateLevenshteinSimilarity(basename, acro);
		}

		protected double calculateWordOverlapSimilarity(List<String> basewords, List<String> textwords) {
			int c = 0;
			Collections.sort(textwords);
			for(String w: basewords) {
				if (Collections.binarySearch(textwords, w)>=0) 
					c++;
			}
			
			//TODO: divide just by basewords.size(), to make this analogous to calculateSubstringSimilarity
			return c / (double)Math.max(textwords.size(), basewords.size());
		}

		protected double calculateLevenshteinSimilarity(CharSequence basename, CharSequence text) {
			double dist = levenshteinDistance.distance(basename, text);
			int max = Math.min(255, Math.max(basename.length(), text.length()));
			double match = max - dist;
			return match/max;
		}

		protected double calculateSubstringSimilarity(CharSequence basename, CharSequence text) {
			if (StringUtils.indexOf(basename, text)>=0) return basename.length() / (double)text.length();
			else return 0;
		}
	}
	

	public class WikiLink {
		private LinkMagic magic;
		private CharSequence interwiki;
		private int namespace;
		private CharSequence target;
		private CharSequence page;
		private CharSequence section;
		private CharSequence text;
		private boolean impliedText;
		
		private CharSequence lenientTarget;
		private CharSequence lenientSection;
		private CharSequence lenientText;
		
		public WikiLink(CharSequence interwiki, int namespace, CharSequence page, CharSequence section, CharSequence text, boolean impliedText, LinkMagic magic) {
			super();
			this.magic = magic;
			this.interwiki = interwiki;
			this.namespace = namespace;
			this.page = page;
			this.section = section;
			this.text = text;
			this.impliedText = impliedText;
			this.target = page;
			if (section!=null && section.length()>0) this.target = this.target + "#" + section; 
		}

		public CharSequence getInterwiki() {
			return interwiki;
		}

		public int getNamespace() {
			return namespace;
		}

		public CharSequence getPage() {
			return page;
		}

		public CharSequence getTarget() {
			return target;
		}

		public CharSequence getSection() {
			return section;
		}

		public CharSequence getText() {
			return text;
		}
		
		public boolean isTextImplied() {
			return impliedText;
		}

		public LinkMagic getMagic() {
			return magic;
		}
		
		public CharSequence getLenientPage() {
			if (lenientTarget==null) lenientTarget = trimAndLower(determineBaseName(getTarget()));
			return lenientTarget;
		}
		
		public CharSequence getLenientSection() {
			CharSequence s = getSection();
			if (s==null) return null;
			
			if (lenientSection==null) lenientSection = trimAndLower(normalizeTitle(s));
			return lenientSection;
		}
		
		public CharSequence getLenientText() {
			if (lenientText==null && StringUtils.equals(text, page)) return getLenientPage();
			
			if (lenientText==null) lenientText = trimAndLower(normalizeTitle(getText()));
			return lenientText;
		}
		
		@Override
		public String toString() {
			StringBuilder s = new StringBuilder();
			s.append("[[");
			if (magic!=null && magic!=LinkMagic.NONE) s.append('{').append(magic).append('}');
			if (interwiki!=null) s.append('<').append(interwiki).append('>');
			if (namespace!=Namespace.MAIN) s.append(namespace).append(':');
			s.append(page);
			if (section!=null) s.append('#').append(section);
			if (text!=null) s.append('|').append(text);
			s.append("]]");
			return s.toString();
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + (impliedText ? 1231 : 1237);
			result = PRIME * result + ((interwiki == null) ? 0 : interwiki.hashCode());
			result = PRIME * result + ((magic == null) ? 0 : magic.hashCode());
			result = PRIME * result + namespace;
			result = PRIME * result + ((page == null) ? 0 : page.hashCode());
			result = PRIME * result + ((section == null) ? 0 : section.hashCode());
			result = PRIME * result + ((target == null) ? 0 : target.hashCode());
			result = PRIME * result + ((text == null) ? 0 : text.hashCode());
			return result;
		}

		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final WikiLink other = (WikiLink) obj;
			if (impliedText != other.impliedText)
				return false;
			if (interwiki == null) {
				if (other.interwiki != null)
					return false;
			} else if (!StringUtils.equals(interwiki, other.interwiki))
				return false;
			if (magic == null) {
				if (other.magic != null)
					return false;
			} else if (!magic.equals(other.magic))
				return false;
			if (namespace != other.namespace)
				return false;
			if (page == null) {
				if (other.page != null)
					return false;
			} else if (!StringUtils.equals(page, other.page))
				return false;
			if (section == null) {
				if (other.section != null)
					return false;
			} else if (!StringUtils.equals(section, other.section))
				return false;
			if (target == null) {
				if (other.target != null)
					return false;
			} else if (!StringUtils.equals(target, other.target))
				return false;
			if (text == null) {
				if (other.text != null)
					return false;
			} else if (!StringUtils.equals(text, other.text))
				return false;
			return true;
		}

		
	}
		
	public class WikiPage {
		protected int namespace;
		protected String title;
		protected String text;
		
		protected String name;
		protected String conceptName;
		protected String resourceName;
		
		protected CharSequence titleSuffix;
		protected CharSequence titlePrefix;
		protected CharSequence baseName;
		protected CharSequence displayTitle;
		protected CharSequence defaultSortKey;
		
		protected ResourceType pageType = null;
		protected ConceptType conceptType = null;

		protected Set<CharSequence> titleTerms = null;
		protected Set<CharSequence> pageTerms = null;
		protected WikiLink redirect = null;
		
		protected CharSequence cleaned = null;
		protected CharSequence flat = null;
		protected CharSequence plain = null;
		protected TextArmor armor = new TextArmor();

		protected CharSequence firstParagraph = null;
		protected CharSequence firstSentence = null;
		
		protected MultiMap<String, TemplateData, List<TemplateData>> templates = null; 
		protected MultiMap<String, CharSequence, Set<CharSequence>> properties = null;
		protected Set<CharSequence> supplementLinks = null; 
		protected Holder<CharSequence> supplementedConcept = null; 
		protected List<WikiLink> links = null; 
		protected List<WikiLink> disambig = null; 
		protected Set<String> categories = null;
		protected Set<String> sections = null;
		private String categoriesString;
		private String sectionsString;
		private String templatesString;
		
		private TemplateExtractor templateExtractor;
		
		protected WikiPage(int namespace, String title, String text, boolean forceCase) {
			super();
			this.namespace = namespace;
			this.title = title;
			this.text = text;
			this.name = normalizeTitle(title, forceCase).toString();
		}
		
		public String getResourceName() {
			if (resourceName==null) {
				if (namespace == Namespace.MAIN) {
					resourceName = name;
				}
				else {
					String ns = getCorpus().getNamespaces().getLocalName(namespace).toString();
					resourceName = ns + ":" + name;
				}
			}
			
			return resourceName;
		}
		
		public String getConceptName() {
			if (conceptName==null) {
				if (namespace == Namespace.MAIN || namespace == Namespace.CATEGORY) {
					conceptName = name;
				}
				else {
					conceptName = getResourceName();
				}
			}
			
			return conceptName;
		}
		
		public MultiMap<String, CharSequence, Set<CharSequence>> getProperties() {
			if (properties==null) properties = extractProperties(this);
			return properties;
		}
		
		public TemplateExtractor getTemplateExtractor() {
			if (templateExtractor==null) {
				templateExtractor = 
					WikiTextAnalyzer.this.config.templateExtractorFactory.newTemplateExtractor(WikiTextAnalyzer.this, armor);
			}
			
			return templateExtractor;
		}
		
		public int getNamespace() {
			return namespace;
		}
		
		public CharSequence getText() {
			return text;
		}
		
		public CharSequence getCleanedText(boolean armored) {
			if (cleaned==null) {
				cleaned = stripClutter( getText(), namespace, title, armor );
			}
			
			if (armored) return cleaned;
			else return armor.unarmor(cleaned);
		}
		
		public CharSequence getFlatText(boolean armored) {
			if (flat==null) {
				flat = stripBoxes( getCleanedText(true) );
			}
			
			if (armored) return flat;
			else return armor.unarmor(flat);
		}
		
		public CharSequence getPlainText(boolean armored) {
			if (plain==null) {
				plain = stripMarkup( getFlatText(true) );
			}
			
			if (armored) return plain;
			else return armor.unarmor(plain);
		}
		
		public CharSequence getFirstParagraph() {
			if (firstParagraph==null) {
				firstParagraph = armor.unarmor( extractFirstParagraph( getCleanedText(true) ) );
				if (firstParagraph==null) firstParagraph = "";
				
				//firstParagraph = unarmor( extractFirstParagraph( getFlatText(true) ), armor );
				
				//firstParagraph = unarmor( extractFirstParagraph( getPlainText(false) ) );
				
				/*if (plain != null) {
					firstParagraph = unarmor( extractFirstParagraph( plain ), armor );
				}
				else {
					firstParagraph = unarmor( extractFirstParagraph( getFlatText(true) ), armor );
				}*/
				
				/*
				if (plain != null) {
					firstParagraph = unarmor( extractFirstParagraph( plain ), armor );
				}
				else if (flat != null) {
					firstParagraph = unarmor( extractFirstParagraph( flat ), armor );
				}
				else {
					firstParagraph = unarmor( extractFirstParagraph( getCleanedText(true) ), armor );
				}
				*/
			}
			
			return firstParagraph;
		}
		
		public CharSequence getFirstSentence() {
			if (firstSentence==null) {
				CharSequence p = getFirstParagraph();
				firstSentence = language.extractFirstSentence( p ); 
			}
			
			return firstSentence;
		}
		
		public CharSequence getTitle() {
			return title;
		}
		
		public CharSequence getName() {
			return name;
		}
		
		public List<WikiLink> getLinks() {
			if (links==null) {
				links = Collections.unmodifiableList( extractLinks( getName(), getCleanedText(true) ) );
			}
			
			return links;
		}

		public Set<CharSequence> getSupplementLinks() {
			if (supplementLinks==null) {
				supplementLinks = Collections.unmodifiableSet( determineSupplementLinks( this ) );
			}
			
			return supplementLinks;
		}

		public CharSequence getSupplementedConcept() {
			if (supplementedConcept==null) {
				supplementedConcept = new Holder<CharSequence>(determineSupplementedConcepts( this ));
			}
			
			return supplementedConcept.getValue();
		}

		public List<WikiLink> getDisambigLinks() {
			if (disambig==null) {
				disambig = extractDisambigLinks( getName(), getFlatText(true) );
			}
			
			return disambig;
		}

		public MultiMap<String, TemplateData, List<TemplateData>> getTemplates() {
			if (templates==null) {
				CharSequence s = getCleanedText(true);
				
				templates = getTemplateExtractor().extractTemplates(s);
			}
			
			return templates;
		}
		
		public Set<String> getCategories() {
			if (categories==null) {
				Set<String> c = new HashSet<String>();
				List<WikiLink> links = getLinks();
				
				for (WikiLink link : links) {
					if (link.getMagic() == LinkMagic.CATEGORY) {
						c.add(link.getPage().toString());
					}
				}
				categories = Collections.unmodifiableSet( c );
			}
			
			return categories;
		}
		
		public Set<String> getSections() {
			if (sections==null) {
				sections = Collections.unmodifiableSet( extractSections( getCleanedText(true) ) );
			}
			
			return sections;
		}
		
		public String getSectionsString() {
			if (sectionsString == null) {
				StringBuilder s = new StringBuilder();
				Set<String> sects = getSections();
				for (CharSequence c : sects) {
					s.append(c);
					s.append('\n');
				}
				
				sectionsString = s.toString();
			}

			return sectionsString;
		}

		public String getCategoriesString() {
			if (categoriesString == null) {
				StringBuilder s = new StringBuilder();
				Set<String> cats = getCategories();
				for (CharSequence c : cats) {
					s.append(c);
					s.append('\n');
				}
				
				categoriesString = s.toString();
			}

			return categoriesString;
		}

		public String getTemplatesString() {
			if (templatesString == null) {
				StringBuilder s = new StringBuilder();
				MultiMap<String, TemplateExtractor.TemplateData, List<TemplateExtractor.TemplateData>> templates = getTemplates();
				for (CharSequence t : templates.keySet()) {
					s.append(t);
					s.append('\n');
				}
				
				templatesString = s.toString();
			}

			return templatesString;
		}

		public ResourceType getResourceType() {
			if (pageType==null) pageType = determineResourceType(this);
			return pageType;
		}

		public ConceptType getConceptType() {
			if (conceptType==null) conceptType = determineConceptType(this);
			return conceptType;
		}

		public Set<CharSequence> getTitleTerms() {
			if (titleTerms==null) {
				titleTerms = determineTitleTerms(this);
			}
			return titleTerms;
		}
		
		public Set<CharSequence> getPageTerms() {
			if (pageTerms==null) {
				pageTerms = determinePageTerms(this);
			}
			return pageTerms;
		}
		
		public WikiLink getRedirect() {
			if (redirect==null) {
				redirect = extractRedirectLink( getName(), getCleanedText(true) );
			}
			
			return redirect;
		}

		public CharSequence getTitleSuffix() {
			if (titleSuffix == null) {
				titleSuffix = determineTitleSuffix(name);
			}

			return titleSuffix;
		}
		
		public CharSequence getTitlePrefix() {
			if (titlePrefix == null) {
				titlePrefix = determineTitlePrefix(name);
			}

			return titlePrefix;
		}
		
		public CharSequence getTitleBaseName() {
			if (baseName == null) {
				baseName = determineBaseName(name);
			}

			return baseName;
		}
		
		public CharSequence getDisplayTitle() {
			if (displayTitle == null) {
				TemplateExtractor.TemplateData dt;
				MultiMap<String, TemplateExtractor.TemplateData, List<TemplateExtractor.TemplateData>> tpl = getTemplates();
				List<TemplateExtractor.TemplateData> lst = tpl.get("DISPLAYTITLE");
				dt = lst==null || lst.size()==0 ? null : lst.get(0);
				
				if (dt!=null) displayTitle = dt.getParameter("0");
				//if (displayTitle==null) displayTitle = replaceUnderscoreBySpace( getTitle() );
				//TODO: avoid re-trying
			}
			
			return displayTitle;
		}
		
		public CharSequence getDefaultSortKey() {
			if (defaultSortKey == null) {
				TemplateExtractor.TemplateData dst;
				MultiMap<String, TemplateExtractor.TemplateData, List<TemplateExtractor.TemplateData>> tpl = getTemplates();
				List<TemplateExtractor.TemplateData> lst = tpl.get("DEFAULTSORT");
				dst = lst==null || lst.size()==0 ? null : lst.get(0);
				
				if (dst!=null) defaultSortKey = dst.getParameter("0");
				//if (defaultSortKey==null) defaultSortKey = replaceUnderscoreBySpace( getTitle() );
				//TODO: avoid re-trying
			}
			
			return defaultSortKey;
		}
		
		public WikiTextAnalyzer getAnalyzer() {
			return WikiTextAnalyzer.this;
		}
	}

	private Corpus corpus;
	private NamespaceSet namespaces;
	private ConceptTypeSet conceptTypes;

	private WikiConfiguration config;
	protected PlainTextAnalyzer language;
	
	private Matcher interwikiMatcher = Pattern.compile("^[a-z]+(-[a-z]+)*$", Pattern.CASE_INSENSITIVE).matcher("");

	private Matcher defaultSortKeyMatcher; //set up in initialize
	private Matcher displayTitleMatcher; //set up in initialize
	private Matcher magicMatcher; //set up in initialize
	private Matcher redirectMatcher; //set up in initialize
	private Matcher linkMatcher; //set up in initialize()
	private boolean titleCase; //set up in initialize()
	
	private Matcher sectionMatcher = WikiConfiguration.sectionPattern(".+?", 0).matcher("");
	private boolean initialized;
	
	private Matcher titleSuffixMatcher;
	private Matcher titlePrefixMatcher;
	private Matcher badLinkMatcher;
	private Matcher badTitleMatcher;
	private Matcher disambigLineMatcher;
	private List<Matcher> disambigLinkMatchers;
	private Matcher mainArtikeMarkerMatcher;
	private Matcher disambigStripSectionMatcher;
	
	private Matcher relevantTemplateMatcher;
	private List<TemplateUser> extraTemplateUsers = new ArrayList<TemplateUser>();
	
	private WikiTextSniffer sniffer = new WikiTextSniffer();
	private Map<String, String> languageNames;
	
	public WikiTextAnalyzer(PlainTextAnalyzer language) {
		this.language = language;
		this.corpus = language.getCorpus();
		this.namespaces = corpus.getNamespaces();
		this.conceptTypes = corpus.getConceptTypes();
		
		config = new WikiConfiguration();
		config.defaults();
	}
	
	public void addExtraTemplateUser(Pattern p, boolean anchored) {
		addExtraTemplateUser(new DummyTemplateUser(p, anchored));
	}
	
	public void addExtraTemplateUser(TemplateUser u) {
		extraTemplateUsers.add(u);
	}
	
	public boolean smellsLikeWikiText(CharSequence v) {
		return sniffer.sniffWikiText(v);
	}

	public boolean isInitialized() {
		return initialized;
	}
	
	public void configure(WikiConfiguration config, TweakSet tweaks) {
		if (isInitialized()) throw new IllegalStateException("already initialized");
		if (tweaks==null) throw new NullPointerException();
		
		this.tweaks = tweaks;
		
		config.prepareFor(this);
		this.config.merge(config);
	}
	
	protected Pattern buildTemplatePattern(Collection<? extends Object>... lists) {
		StringBuilder pattern = new StringBuilder();
		
		outer: 
		for (Collection<? extends Object> list: lists) {
			for (Object obj: list) {
				if (obj==null) continue;
				if (!(obj instanceof TemplateUser)) continue;
				TemplateUser tu = (TemplateUser)obj;
				
				String p = tu.getTemplateNamePattern();
				if (p==null) continue;
				
				if (p.equals("") || p.equals(".*")  || p.equals("^.*$")) {
					pattern.setLength(0);
					pattern.append(".*");
					break outer;
				}
				
				if (pattern.length()>0) pattern.append('|');
				pattern.append('(').append(p).append(')');
			}
		}
		
		if (pattern.length()==0) return null;
		
		return Pattern.compile( pattern.toString() );
	}
	
	@SuppressWarnings("unchecked")
	public void initialize(NamespaceSet dumpedNamespaces, boolean titleCase) {
		if (isInitialized()) throw new IllegalStateException("already initialized");
		
		this.namespaces.addAll(dumpedNamespaces);
		this.titleCase = titleCase;
		
		for (Sensor sensor: config.conceptTypeSensors) {
			ConceptType type = (ConceptType)sensor.getValue();
			if (type!=null) conceptTypes.addType(type);
		}
		
		String img =  StringUtils.join("|", namespaces.getNamespace(Namespace.IMAGE).getNames());
		Pattern imagePattern = Pattern.compile("\\[\\[ *("+img+") *:(?>[^\\|\\]]+)(\\|((?>[^\\[\\]]+)|\\[\\[(?>[^\\]]+)\\]\\]|\\[(?>[^\\]]+)\\])*)?\\]\\]", Pattern.CASE_INSENSITIVE | Pattern.DOTALL );
		config.stripClutterManglers.add( new RegularExpressionMangler(imagePattern, "") ); //XXX: weould be nice to *replace* the default image pattern... 
		
		linkMatcher = Pattern.compile("\\[\\[([^\r\n]+?)(\\|([^\r\n]*?))?\\]\\]("+config.linkTrail+")").matcher(""); 

		defaultSortKeyMatcher = config.defaultSortKeyPattern.matcher("");
		displayTitleMatcher = config.displayTitlePattern.matcher("");
		magicMatcher = config.magicPattern.matcher("");
		redirectMatcher = config.redirectPattern.matcher("");
		titleSuffixMatcher = config.titleSuffixPattern.matcher("");
		titlePrefixMatcher = config.titlePrefixPattern.matcher("");
		badLinkMatcher = config.badLinkPattern.matcher("");
		badTitleMatcher = config.badTitlePattern.matcher("");
		disambigLineMatcher = config.disambigLinePattern.matcher("");
		mainArtikeMarkerMatcher = config.mainArtikeMarkerPattern == null ? null : config.mainArtikeMarkerPattern.matcher("");
		disambigStripSectionMatcher = config.disambigStripSectionPattern == null ? null : config.disambigStripSectionPattern.matcher("");
		
		disambigLinkMatchers = new ArrayList<Matcher>();
		for (Pattern p: config.disambigLinkPatterns) {
			Matcher m = p.matcher("");
			disambigLinkMatchers.add(m);
		}
		
		extraTemplateUsers.add( new DummyTemplateUser(displayTitleMatcher, true) );
		extraTemplateUsers.add( new DummyTemplateUser(defaultSortKeyMatcher, true) );
		
		for (Pattern p : config.extraTemplatePatterns) {
			extraTemplateUsers.add( new DummyTemplateUser(p, true) );
		}
		
		Pattern relevantTemplatePattern = buildTemplatePattern(
					config.resourceTypeSensors,
					config.conceptTypeSensors,
					config.propertyExtractors,
					config.pageTermExtractors,
					extraTemplateUsers
				);
		
		relevantTemplateMatcher = relevantTemplatePattern.matcher("");
		
		initialized = true;
	}
	
	public boolean isRelevantTemplate(CharSequence name) {
		if (relevantTemplateMatcher==null) return false;
		
		relevantTemplateMatcher.reset(name);
		boolean relevant = relevantTemplateMatcher.find();
		
		return relevant;
	}

	
	public Corpus getCorpus() {
		return corpus;
	}
	
	public WikiPage makePage(int namespace, String title, String text, boolean forceCase) {
		if (!initialized) throw new IllegalStateException("call initialize() first");
		return new WikiPage(namespace, title, text, forceCase);
	}
	
	protected static <V> V evalSensors(Collection<Sensor<V>> sensors, WikiPage page, V defValue) {
		if (sensors==null) return null;
		
		for (Sensor<V> sensor : sensors) {
			if (sensor.sense(page)) return sensor.getValue();
		}
		
		return defValue;
	}
	
	protected static MultiMap<String, CharSequence, Set<CharSequence>> evalPropertyExtractors(Collection<PropertyExtractor> extractors, WikiPage page) {
		if (extractors==null) return null;
		
		MultiMap<String, CharSequence, Set<CharSequence>> m = null;
		
		for (PropertyExtractor extractor : extractors) {
			m = extractor.extract(page, m);
		}
		
		if (m==null) m = ValueSetMultiMap.empty();
		return m;
	}
	
	protected MultiMap<String, CharSequence, Set<CharSequence>> extractProperties(WikiPage page) {
		MultiMap<String, CharSequence, Set<CharSequence>> m = evalPropertyExtractors(config.propertyExtractors, page);
		if (m==null) m = ValueSetMultiMap.empty();
		return m;
	}
	
	protected CharSequence resolveMagic(CharSequence text, int namespace, CharSequence title) {
		magicMatcher.reset(text);
		
		StringBuffer s = new StringBuffer(text.length());
		while(magicMatcher.find()) {
			CharSequence value = evaluateMagic(magicMatcher, namespace, title);
			
			magicMatcher.appendReplacement(s, Matcher.quoteReplacement(value.toString()));
		}
		
		magicMatcher.appendTail(s);
		return s;
		
	}
	
	protected static final Matcher subpageMatcher = Pattern.compile("^(.+)/([^/]+)$").matcher("");
	
	private CharSequence evaluateMagic(Matcher matcher, int namespace, CharSequence title) {
		String name = matcher.group(1).toUpperCase();
		
		if (name.equals("PAGENAME")) {
			return replaceUnderscoreBySpace(title);
		}
		else if (name.equals("PAGENAMEE")) {
			return replaceSpaceByUnderscore(title);
		}
		else if (name.equals("SUBPAGENAME")) {
			subpageMatcher.reset(title);
			return replaceUnderscoreBySpace(subpageMatcher.replaceAll("$2"));
		}
		else if (name.equals("SUBPAGENAMEE")) {
			subpageMatcher.reset(title);
			return replaceSpaceByUnderscore(subpageMatcher.replaceAll("$2"));
		}
		else if (name.equals("BASEPAGENAME")) {
			subpageMatcher.reset(title);
			return replaceUnderscoreBySpace(subpageMatcher.replaceAll("$1"));
		}
		else if (name.equals("BASEPAGENAMEE")) {
			subpageMatcher.reset(title);
			return replaceSpaceByUnderscore(subpageMatcher.replaceAll("$1"));
		}
		else if (name.equals("NAMESPACE")) {
			return replaceUnderscoreBySpace(namespaces.getNamespace(namespace).getLocalName());
		}
		else if (name.equals("NAMESPACEE")) {
			return replaceUnderscoreBySpace(namespaces.getNamespace(namespace).getLocalName());
		}
		else if (name.equals("FULLPAGENAME")) {
			if (namespace!=0) title = namespaces.getNamespace(namespace).getLocalName() + ":" + title;  
			return replaceUnderscoreBySpace(title);
		}
		else if (name.equals("FULLPAGENAMEE")) {
			if (namespace!=0) title = namespaces.getNamespace(namespace).getLocalName() + ":" + title;  
			return replaceSpaceByUnderscore(title);
		}
		else {
			return matcher.group(0);
		}
	}

	protected CharSequence stripClutter(CharSequence text, int namespace, CharSequence title, TextArmor armor) {
		//XXX: currently, a lot of template resulotion goes on here. 
		//     that might not be needed for that full text. Or... maybe it is, for link text?
		text = applyArmorers(config.stripClutterArmorers, text, armor);
		text = resolveMagic(text, namespace, title);
		
		return applyManglers(config.stripClutterManglers, text);
	}

	protected CharSequence stripBoxes(CharSequence text) {
		return applyManglers(config.stripBoxesManglers, text);
	}

	public CharSequence stripMarkup(CharSequence text) {
		//NOTE: if one of the mangers is a StripMarkupMangler, we got regress.
		return applyManglers(config.stripMarkupManglers, text);
	}

	protected ResourceType determineResourceType(WikiPage page) {
		if (page.namespace == Namespace.CATEGORY) return ResourceType.CATEGORY;
		
		ResourceType t = evalSensors(config.supplementSensors, page, null);
		if (t != null) return t;
		
		if (page.getSupplementedConcept()!=null) return ResourceType.SUPPLEMENT;
		
		if (page.namespace != Namespace.MAIN) return ResourceType.OTHER;
		
		redirectMatcher.reset(page.getCleanedText(true));
		if (redirectMatcher.find()) return ResourceType.REDIRECT;
		
		return (ResourceType)evalSensors(config.resourceTypeSensors, page, ResourceType.ARTICLE);
	}

	protected ConceptType determineConceptType(WikiPage page) {
		return (ConceptType)evalSensors(config.conceptTypeSensors, page, ConceptType.OTHER);
	}

	protected CharSequence determineTitleSuffix(CharSequence title) {
		titleSuffixMatcher.reset(title);
		
		if (titleSuffixMatcher.matches()) {
			CharSequence t = titleSuffixMatcher.group(2);
			t = normalizeTitle(t); //XXX: too early?!
			if (t.length()==0) t = null;
			return t;
		}
		
		return null;
	}
	
	protected CharSequence determineTitlePrefix(CharSequence title) {
		titlePrefixMatcher.reset(title);
		
		if (titlePrefixMatcher.matches()) {
			CharSequence t = titlePrefixMatcher.group(1);
			t = normalizeTitle(t); //XXX: too early?! or redundant?
			if (t.length()==0) t = null;
			return t;
		}
		
		return null;
	}
	
	public Set<CharSequence> determinePageTerms(WikiPage page) {
		return evalExtractors(config.pageTermExtractors, page);
	}
	
	public Set<CharSequence> determineTitleTerms(WikiPage page) {
		Set<CharSequence> terms = determineTitleTerms(page.getTitle());
		
		CharSequence displayTitle = page.getDisplayTitle();
		if (displayTitle!=null && displayTitle.length()>0) {
			terms.add(displayTitle.toString());
		}
		
		return terms;
	}

	public Set<CharSequence> determineTitleTerms(CharSequence title) {
		Set<CharSequence> terms = new HashSet<CharSequence>();
		
		//XXX: should take the cached one from a WikiPage, if possible. 
		//XXX: make another Title class?
		CharSequence name = trim(replaceUnderscoreBySpace(determineBaseName(title))); 
		terms.add( name.toString() );
		
		//TODO: use subpage-titles
		
		return terms;
	}

	public Set<CharSequence> evalExtractors(Collection<ValueExtractor> extractors, WikiPage page) {
		Set<CharSequence> terms = null;
		
		if (extractors==null) return Collections.emptySet();
		
		for (ValueExtractor extractor : extractors) {
			terms = extractor.extract(page, terms);
		}
		
		if (terms==null) return Collections.emptySet();
		return terms;
	}

	protected WikiLink extractRedirectLink(CharSequence title, CharSequence text) {
		redirectMatcher.reset(text);
		if (!redirectMatcher.find()) return null;
		
		return makeLink(title, redirectMatcher.group(1), null, null);
	}
	
	/** Link targets in MediaWiki may be given in url-encoded form, that is,
	 * using codes like %3A for : (Colon). 
	 ***/
	protected CharSequence decodeLinkTarget(CharSequence name) {
		if (name == null || name.length()<3) return name; //short-cirquit
		
		if (name.length()>3 && StringUtils.indexOf('&', name)>=0) {
			name = HtmlEntities.decodeEntities(name);
		}

		if (StringUtils.indexOf('%', name)>=0) {
			name = UrlUtils.decode(name.toString(), false); //XXX: rough toString
		}
		
		return name;
	}
	
	/** Sections in MediaWiki may be addressed in encoded form, that is,
	 * using codes like .C3.9C for &Uuml; (U-Umlaut). 
	 ***/
	protected CharSequence decodeSectionName(CharSequence name) {
		if (name == null || name.length()<3 || StringUtils.indexOf('.', name)<0) return name; //short-cirquit
		
		//XXX: this is nearly exactly the same code as UrlUtils.decode !
		
		try {
			byte[] a = name.toString().getBytes("UTF-8"); //original bytes //XXX: rough toString
			byte[] b = new byte[a.length];     //target buffer 
			byte x = 0; //accumulator for encoded byte
			int i = 0;  //index on original bytes
			int c = 0;  //size/index on target buffer
			int st = 0; //parser state (bytes into encoded section)
			
			for (i=0; i<a.length; i++) {
				if (st==0) { //normal bytes
					if (a[i] == '.') st = 1; //start of potential code section 
					else b[c++] = a[i];
				}
				else if (st==1) { //first digit
					if (a[i] >= '0' && a[i] <= '9') {
						st = 2;
						x = (byte)((a[i] - '0') << 4); 
					}
					else if (a[i] >= 'A' && a[i] <= 'F') {
						st = 2;
						x = (byte)((a[i] - 'A' + 10) << 4); 
					}
					else {
						x = 0;
						b[c++] = a[i - st--];
						b[c++] = a[i];
					}
				}
				else if (st==2) { //second digit
					if (a[i] >= '0' && a[i] <= '9') {
						st = 0;
						x += (a[i] - '0'); 
						if (x>=32 || x<0) b[c++] = x; //NOTE: 0 <= codes < 32 are control chars (also true in utf-8)
					}
					else if (a[i] >= 'A' && a[i] <= 'F') {
						st = 0;
						x += (a[i] - 'A' + 10); 
						if (x>=32 || x<0) b[c++] = x; //NOTE: 0 <= codes < 32 are control chars (also true in utf-8)
					}
					else {
						b[c++] = a[i - st--];
						b[c++] = a[i - st--];
						b[c++] = a[i];
					}
					
					x = 0;
				}
				else {
					throw new Error("oops");
				}
			}
			
			while (st>0) {
				b[c++] = a[i - st--];
			}
			
			return new String(b, 0, c, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			throw new Error("UTF-8 not supported", e);
		}
	}
	
	@SuppressWarnings("null")
	public WikiLink makeLink(CharSequence context, CharSequence target, CharSequence text, CharSequence tail) {
		if (!initialized) throw new IllegalStateException("call initialize() first");
		
		//FIXME: decode entities HERE?! That MAY be once too often...
		
		target = trim(target);
		if (target.length()<config.minNameLength || target.length()>config.maxNameLength) return null;
		
		target = decodeLinkTarget(target);
		
		LinkMagic magic = LinkMagic.NONE;
		CharSequence interwiki = null;
		int namespace = Namespace.MAIN;
		CharSequence page = target;
		CharSequence section = null;
		boolean esc = false;
		
		while (page.length()>0 && page.charAt(0)==':') {
			page = page.subSequence(1, page.length());
			esc = true;
		}
		
		if (page.length()==0) return null;
		
		//handle section links ------------------------
		int idx = StringUtils.indexOf('#', page);
		if (idx==page.length()-1) {
			page = page.subSequence(0, page.length()-1);
			section = null;
		}
		else if (idx==0) {
			section = page.subSequence(1, page.length());
			page = context;
		}
		else if (idx>0) {
			section = page.subSequence(idx+1, page.length());
			page = target.subSequence(0, idx);
		}
		
		if (section!=null) { //handle special encoded chars in section ref
			section = decodeSectionName(trim(section));
			section = replaceSpaceByUnderscore(section);
		}
		
		//handle qualifiers ------------------------
		idx = StringUtils.indexOf(':', page);
		if (idx>=0) {
			CharSequence pre = trim(page.subSequence(0, idx));
			pre = normalizeTitle(pre);
			int ns = getNamespaceId(pre);
			if (ns!=Namespace.NONE) {
				namespace = ns;
				page = page.subSequence(idx+1, page.length());
				
				if (!esc) {
					if (ns==Namespace.IMAGE) magic = LinkMagic.IMAGE;
					else if (ns==Namespace.CATEGORY) magic = LinkMagic.CATEGORY;
				}
			}
			else if (isInterwikiPrefix(pre)) {
				page = page.subSequence(idx+1, page.length());
				interwiki = toLowerCase(pre);

				if (isInterlanguagePrefix(pre) && !esc) {
					magic = LinkMagic.LANGUAGE;
				}
			}
			/*else {
				int dummy = 0; //breakpoint dummy
			}*/
		}
		
		boolean implied = false;
		
		if (text==null) {
			implied = true;
			
			if (magic == LinkMagic.CATEGORY) {
				text = context; //sort key defaults to local page
			}
			else {
				text = target;
				if (text.charAt(0)==':') text = text.subSequence(1, text.length());
			}
		}
		
		if (tail!=null && magic != LinkMagic.CATEGORY) text = text.toString() + tail;
		if (!implied) text = stripMarkup(text); //XXX: this can get pretty expensive...
		text = HtmlEntities.decodeEntities(text);
		
		if (page.length()==0) return null;
		
		page = normalizeTitle(page);
		return new WikiLink(interwiki, namespace, page, section, text, implied, magic); 
	}

	public boolean isInterlanguagePrefix(CharSequence pre) {
		pre = trimAndLower(pre);
		return getLanguageNames().containsKey(pre);
	}
	
	protected Map<String, String> getLanguageNames() {
		if (this.languageNames==null) {
			this.languageNames = Languages.load(this.tweaks);
		}
		
		return this.languageNames;
	}

	public boolean isInterwikiPrefix(CharSequence pre) {
		interwikiMatcher.reset(pre);
		return interwikiMatcher.matches();
	}

	public int getNamespaceId(CharSequence name) {
		if (name.length()==0) return Namespace.MAIN;
		name = normalizeTitle(name);
		if (name.length()==0) return Namespace.MAIN;
		
		return namespaces.getNumber(name.toString());
	}
	
	public CharSequence normalizeTitle(CharSequence title) {
		return normalizeTitle(title, true);
	}
	
	public CharSequence normalizeTitle(CharSequence title, boolean forceCase) {
		title = trim(title);
		if (title.length()==0) return title;
		
		title = replaceSpaceByUnderscore(title);
		
		if (titleCase && forceCase && title.charAt(0)>'Z') { //fast check for ascii caps first
			int ch = Character.codePointAt(title, 0);
			if (Character.isLowerCase(ch)) {
				int uch = Character.toUpperCase(ch);
				if (uch!=ch) {
					int w = Character.charCount(ch);
					int len= title.length();
					
					char[] uchars = Character.toChars(uch);
					char[] chars = new char[uchars.length + len - w];
					System.arraycopy(uchars, 0, chars, 0, uchars.length);
					
					if (title instanceof String) {
						((String)title).getChars(w, len, chars, uchars.length);
					}
					else if (title instanceof StringBuilder) {
						((StringBuilder)title).getChars(w, len, chars, uchars.length);
					}
					else {
						for (int i = 0; i<len-w; i++) {
							chars[i+uchars.length] = title.charAt(i);
						}
					}
					
					title = new String(chars);
				}
			}
		}
		
		return title;
	}
	
	public boolean isBadTerm(CharSequence term) {
		if (term.length()<config.minTermLength || term.length()>config.maxTermLength) return true;
		if (StringUtils.containsControlChar(term, true)) return true; 
		return false; //TODO: pattern?
	}
	
	public boolean isBadLinkTarget(CharSequence tgt) {
		if (isBadTitle(tgt)) return true;

		badLinkMatcher.reset(tgt);
		return badLinkMatcher.find();
	}

	public boolean isBadTitle(CharSequence ttl) {
		if (ttl.length()<config.minNameLength || ttl.length()>config.maxNameLength) return true;
		if (StringUtils.containsControlChar(ttl, true)) return true; 

		badTitleMatcher.reset(ttl);
		return badTitleMatcher.find();
	}

	protected CharSequence extractFirstParagraph(CharSequence text) {
		ParsePosition pp = new ParsePosition(0);
		CharSequence s;
		int c = 0;
		
		do {
			if (c++ > 255) { //NOTE: sanity check, should never trigger. but if it happens, be sure to fail and supply a stack trace.
				throw new RuntimeException("oops! runaway loop in extractFirstParagraph()");
			}
				
			s = config.extractParagraphMangler.mangle(text, pp);
			if (s==null || s.length()==0) return s;
			s = trim(stripMarkup(s));
		} while (s.length()==0 && pp.getIndex()<text.length());
		
		return s.length()==0 ? null : s;
	}

	protected CharSequence determineBaseName(CharSequence title) {
		titleSuffixMatcher.reset(title);
		title = titleSuffixMatcher.replaceAll("$1");
		
		titlePrefixMatcher.reset(title);
		title = titlePrefixMatcher.replaceAll("$2");
		
		return title;
	}

	protected List<WikiLink> extractDisambigLinks(CharSequence title, CharSequence text) {
		List<WikiLink> links = new ArrayList<WikiLink>();
		
		if (disambigStripSectionMatcher!=null) {
			text = stripSections(text, disambigStripSectionMatcher);
		}
		
		//TODO: "see also" without section
		
		//get base name from title, i.e. strip suffix; "Foo_(Bar)" becomes "Foo"
		final CharSequence name = determineBaseName(title);
		
		//construct a measure for the similarity of links to the disabig-pages basename
		Measure<WikiLink> linkMeasure = config.linkSimilarityMeasureFactory.newLinkSimilarityMeasure(title, this);		

		//extract one link for each disambig-line
		disambigLineMatcher.reset(text);
		while (disambigLineMatcher.find()) {
			String line = disambigLineMatcher.group();
			WikiLink link = determineDisambigTarget(title, name, line, linkMeasure);
			if (link!=null) links.add(link);
			
			//XXX: provide for cases where there is more than one good choice in a line?
			//     is that impossibly by convention in ALL wiki projects?
		}
		
		return links;
	}
	
	public CharSequence stripSections(CharSequence text, Matcher m) {
		StringBuilder buff = null;
		
		sectionMatcher.reset(text);
		m.reset(text);
		
		int idx = 0;
		
		while (idx < text.length() && m.find()) { //find next match
			if (buff==null) buff = new StringBuilder();
			buff.append(text, idx, m.start());
			
			String h = m.group(1); //the (=+) bit
			int j = text.length();
			
			sectionMatcher.region(m.end(), text.length());
			while (sectionMatcher.find()) { //find next section
				String hh = sectionMatcher.group(1); //the (=+) bit
				if (hh.length()<=h.length()) { //if level <=, end section to strip
					j = sectionMatcher.start();
					break;
				}
			}
			
			m.region(j, text.length());
			idx = j;
		}
				
		if (buff==null) {
			return text;
		}
		else {
			buff.append(text, idx, text.length());		
			return buff;
		}
	}
	
	private WikiLink determineDisambigTarget(CharSequence title, CharSequence name, CharSequence line, final Measure<WikiLink> linkMeasure) {
		List<WikiLink> ll = null; //links from the line
		
		//NOTE: is we find a pattern like ", see [[(.*?)]]$", use the link from that!
		for( Matcher m : disambigLinkMatchers) {
			m.reset(line);
			
			if (m.find()) {
				//extract links from first group if groups are captured. Else use entire match.
				ll = extractLinks(title, m.groupCount() > 0 ? m.group(1) : m.group(0));
				Filters.retain(ll, articleLinks); //filter links
				
				//there should be only one link, otherwise the pattern didn't really work
				if (ll.size()==1) { 
					return ll.get(0); 
				}
			}
		}
		
		//look at all links in the line, since we didn't find a match for the patterns above
		ll = extractLinks(title, line);
		
		if (ll.size()==0) { //no links, forget it
			return null;
		}
		
		Filters.retain(ll, articleLinks); //throw away non-article links
		
		if (ll.size()==0) { //no good links, forget it
			return null;
		}
		
		if (ll.size()==1) { //there's only one link, use it
			//TODO: this is problematic, the link often goes to a broader concept!
			//      Use config.minDisambigLinkSimilarity here?
			return ll.get(0);
		}

		//compare links to base name, keep only the links that contain the basename
		List<WikiLink> fll = new ArrayList<WikiLink>(ll);
		Filters.retain(fll, new SubStringLinkFilter(name)); //filter links //TODO: check if this is worth the trouble. Maybe just use the similarity
		
		if (fll.size()==1) { //found one good looking link, use it
			//Note: config.minDisambigLinkSimilarity doesn't make sense here... if the name is contained, the similarity is 1.
			return ll.get(0);
		}
		else if (fll.size()>0) { 
			//if there are good looking links, only consider those further.
			//otherwise, consider all
			ll = fll;
		}
		
		if (ll.isEmpty()) { 
			//sanity assert. ll should always contain elements at this point (otherwise, we would have aborted earlier)
			throw new RuntimeException("oops! this should not happen!");
		}
		
		//we still have multiple candidates. roll out the big guns.
		//Construct a list of candidate links, sorted by their similarity to
		//the disambig's base name, as determined by calculateLinkSimilarity().
		//use bubble-sort because of quick setup (no more than 4 links are expected 
		//on a line, more than 16 are considered an error)

		class CandidateLinkEntry {
			public final double similarity;
			public final WikiLink link;
			
			public CandidateLinkEntry(WikiLink link) {
				super();
				this.similarity = linkMeasure.measure(link);
				this.link = link;
			}
		}
		
		CandidateLinkEntry[] lla = new CandidateLinkEntry[ll.size()];
		Iterator<WikiLink> it = ll.iterator();
		int c = 0;
		
		//candidates:
		while (it.hasNext()) {
			CandidateLinkEntry e = new CandidateLinkEntry(it.next());
			
			/*
			//scan upward through the array, see if we already have a link with that target
			for (int i=0; i<c; i++) {
				//if the link target (page#section) matches:
				if (lla[i].link.getPage().equals(e.link.getPage()) 
						&& lla[i].link.getSection().equals(e.link.getSection())) {
					
					//remove old candidate entry if the new one is better
					if (lla[i].similarity < e.similarity) {
						c--;
						for (int j = i; j<c-1; j++) {
							lla[j] = lla[j+1];
						}
					}
					else {
						//continue outer loop with next link
						continue candidates;
					}
				}
			}
			*/
			
			//append new element, then bubble up
			//there's rarely more than three links, so qsort etc would be massive overhead
			lla[c] = e;
			for (int i=c; i>0; i--) { //TODO: unit test!
				if (lla[i].similarity > lla[i-1].similarity) {
					//swap
					CandidateLinkEntry tmp = lla[i];
					lla[i] = lla[i-1];
					lla[i-1] = tmp;
				}
				else {
					break;
				}
			}
			
			c++;
			
			//safeguard, bail out if there are too many links on the line
			if (c>config.maxDisambigLinksPerLine) {
				//NOTE: 16 links in one line of a disambig is way too much. something bad has happened. 
				//TODO: issue a warning somehow!
				break; 
			}
		}
		
		if (c<1) { 
			//sanity assert. lla should always contain elements at this point 
			throw new RuntimeException("oops! this should not happen!");
		}

		if (lla[c-1]==null) { 
			//sanity assert. lla should always contain elements at this point 
			throw new RuntimeException("oops! this should not happen!");
		}
		
		CandidateLinkEntry best = lla[0];
		
		//best match is too bad
		if (best.similarity <= config.minDisambigLinkSimilarity) {
			return null; //TODO: perhaps simply pick one? first? last?
		}
		
		WikiLink link = best.link;
		return link;
	}

	public int getMaxNameLength() {
		return config.maxNameLength;
	}
	
	public int getMinNameLength() {
		return config.minNameLength;
	}
	
	public int getMaxTermLength() {
		return config.maxNameLength;
	}
	
	public int getMinTermLength() {
		return config.minNameLength;
	}
	
	public boolean useCategoryAliases() {
		return config.useCategoryAliases;
	}
	
	protected Set<CharSequence> determineSupplementLinks(WikiPage page) {
		return evalExtractors(config.supplementNameExtractors, page);
	}
	
	protected CharSequence determineSupplementedConcepts(WikiPage page) {
		Set<CharSequence> s = evalExtractors(config.supplementedConceptExtractors, page);
		return s.isEmpty() ? null : s.iterator().next();
	}
	
	protected List<WikiLink> extractLinks(CharSequence title, CharSequence text) {
		List<WikiLink> links = new ArrayList<WikiLink>();
		
		//FIXME: make sure link-text gets trimmed. or not? beware sort-keys. 
		
		linkMatcher.reset(text);
		while (linkMatcher.find()) {
			WikiLink link = makeLink(title, linkMatcher.group(1), linkMatcher.group(3), linkMatcher.group(4));
			if (link==null) continue;
			if (isBadLinkTarget(link.getPage())) continue; 
				
			links.add(link);
		}
		
		return links;
	}

	protected Set<String> extractSections(CharSequence text) {
		sectionMatcher.reset(text);
		Set<String> sections = new HashSet<String>();
		
		while (sectionMatcher.find()) {
			sections.add(sectionMatcher.group(2));
		}
		
		return sections;
	}
	
	public String getMagicTemplateId(CharSequence name) {
		displayTitleMatcher.reset(name);
		if (displayTitleMatcher.matches()) return "DISPLAYTITLE";
		
		defaultSortKeyMatcher.reset(name);
		if (defaultSortKeyMatcher.matches()) return "DEFAULTSORT";
		
		return null;
	}

	/**
	 * returns true of the given string is conventionally used as a magic sort key
	 * in category links, to indicate that the page containing the link should be
	 * considered the "main article" for the category the link points to.
	 * For example, if "!" is used to indicate the "main page", the page "Book"
	 * may contain a categorization link like [[Category:Books|!]].   
	 */
	public boolean isMainArticleMarker(String sortKey) {
		if (mainArtikeMarkerMatcher==null) return false;
		
		//TODO: main article may not use plain "!", but "!foo".
		//XXX: make this work for leading spaces too: " foo";
		mainArtikeMarkerMatcher.reset(sortKey);
		return mainArtikeMarkerMatcher.matches();
	}
	
	public boolean mayBeFormOf(CharSequence form, CharSequence base) {
		form = toLowerCase(form);
		base = toLowerCase(base);
		
		if (config.maxWordFormDistance <= 0) return StringUtils.equals(form, base);
		
		double d = LevenshteinDistance.instance.distance(form, base);
		d /= Math.max(form.length(), base.length());
		return d <= config.maxWordFormDistance;
	}

	public WikiLink newLink(String interwiki, int namespace, String page, String section, String text, boolean impliedText, LinkMagic magic) {
		return new WikiLink(interwiki, namespace, page, section, text, impliedText, magic);
	}

	public static WikiTextAnalyzer getWikiTextAnalyzer(Corpus corpus, TweakSet tweaks) throws InstantiationException {
		PlainTextAnalyzer language = PlainTextAnalyzer.getPlainTextAnalyzer(corpus, tweaks);
		language.initialize();
		
		return getWikiTextAnalyzer(language);
	}
	
	@SuppressWarnings({ "null", "unchecked" })
	public static WikiTextAnalyzer getWikiTextAnalyzer(PlainTextAnalyzer language) throws InstantiationException {
		Class[] acc = getSpecializedClasses(language.getCorpus(), WikiTextAnalyzer.class, "WikiTextAnalyzer");
		Class[] ccc = getSpecializedClasses(language.getCorpus(), WikiConfiguration.class, "WikiConfiguration", language.getCorpus().getConfigPackages());
		
		logger.debug("getWikiTextAnalyzer uses implementation "+acc[acc.length-1]);
		logger.debug("getWikiTextAnalyzer uses configurations "+Arrays.toString(ccc));
		
		try {
			Constructor ctor = acc[acc.length-1].getConstructor(new Class[] { PlainTextAnalyzer.class });
			WikiTextAnalyzer analyzer = (WikiTextAnalyzer)ctor.newInstance(new Object[] { language } );
			
			Set<Class> stop = new HashSet<Class>();
			
			for (int i = ccc.length-1; i >= 0; i--) { //NOTE: most specific last, because last write wins.
				if (!stop.add(ccc[i])) continue;
				
				ctor = ccc[i].getConstructor(new Class[] { });
				WikiConfiguration conf = (WikiConfiguration)ctor.newInstance(new Object[] { } );
				analyzer.configure(conf, language.tweaks);
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
		
	public static void main(String[] args) throws InstantiationException, IOException {
		String lang = args[0];
		String name = args[1];
		String file = args[2];
		
		String text = IOUtil.slurp(new File(file), "UTF-8");
		
		TweakSet tweaks = new TweakSet();
		
		Corpus corpus = Corpus.forName("TEST", lang, (String[])null);
		WikiTextAnalyzer analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(corpus, tweaks);
		
		NamespaceSet namespaces = Namespace.getNamespaces(null);
		analyzer.initialize(namespaces, true);
		
		WikiLink t = analyzer.makeLink("TEST", name, null, null);
		
		WikiPage page = analyzer.makePage(t.getNamespace(), t.getTarget().toString(), text, true);

		String plain = page.getPlainText(false).toString();
		System.out.println(plain);
		System.out.println();
	}

}
