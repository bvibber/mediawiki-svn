package de.brightbyte.wikiword.analyzer;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.net.URLEncoder;
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

import de.brightbyte.abstraction.BeanPropertyAbstractor;
import de.brightbyte.application.Arguments;
import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.data.filter.Filters;
import de.brightbyte.data.measure.LevenshteinDistance;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.IOUtil;
import de.brightbyte.net.UrlUtils;
import de.brightbyte.util.Holder;
import de.brightbyte.util.Logger;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ConceptTypeSet;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.Interwiki;
import de.brightbyte.wikiword.Languages;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.extractor.ValueExtractor;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;
import de.brightbyte.wikiword.analyzer.sensor.Sensor;
import de.brightbyte.wikiword.analyzer.template.DeepTemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.DummyTemplateUser;
import de.brightbyte.wikiword.analyzer.template.FlatTemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;
import de.brightbyte.wikiword.builder.InputFileHelper;
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
	
	static final Logger logger = Logger.getLogger(WikiTextAnalyzer.class); 
	
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
			this.basename = AnalyzerUtils.toLowerCase(analyzer.normalizeTitle(basename));
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
			
			if (basewords==null) basewords = analyzer.language.extractWords(AnalyzerUtils.replaceUnderscoreBySpace(basename));
			List<String> textwords = analyzer.language.extractWords(AnalyzerUtils.replaceUnderscoreBySpace(text));
			
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
		private CharSequence targetConcept;
		private CharSequence targetConceptPage;
		private CharSequence title;
		private CharSequence section;
		private CharSequence text;
		private boolean impliedText;
		
		private CharSequence lenientTarget;
		private CharSequence lenientSection;
		private CharSequence lenientText;
		
		public WikiLink(CharSequence interwiki, CharSequence target, int namespace, CharSequence title, CharSequence section, CharSequence text, boolean impliedText, LinkMagic magic) {
			super();
			this.magic = magic;
			this.interwiki = interwiki;
			this.namespace = namespace;
			this.title = title;
			this.section = section;
			this.text = text;
			this.impliedText = impliedText;
			this.target = target;
		}

		public CharSequence getInterwiki() {
			return interwiki;
		}

		public int getNamespace() {
			return namespace;
		}

		public CharSequence getTitle() {
			return title;
		}

		public CharSequence getTarget() {
			return target;
		}


		public CharSequence getTargetPage() {
			CharSequence t = getTarget();
			if (section==null) return t;
				
			int idx = StringUtils.indexOf('#', t);
			if (idx<0) return t;
			
			return t.subSequence(idx+1, t.length());
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
			if (lenientTarget==null) lenientTarget = AnalyzerUtils.trimAndLower(determineBaseName(getTarget()));
			return lenientTarget;
		}
		
		public CharSequence getLenientSection() {
			CharSequence s = getSection();
			if (s==null) return null;
			
			if (lenientSection==null) lenientSection = AnalyzerUtils.trimAndLower(normalizeTitle(s));
			return lenientSection;
		}
		
		public CharSequence getLenientText() {
			if (lenientText==null && StringUtils.equals(text, title)) return getLenientPage();
			
			if (lenientText==null) lenientText = AnalyzerUtils.trimAndLower(normalizeTitle(getText()));
			return lenientText;
		}
		
		@Override
		public String toString() {
			StringBuilder s = new StringBuilder();
			s.append("[[");
			if (magic!=null && magic!=LinkMagic.NONE) s.append('{').append(magic).append('}');
			if (interwiki!=null) s.append('<').append(interwiki).append('>');
			if (namespace!=Namespace.MAIN) s.append('(').append(namespace).append(')');
			s.append(target);
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
			result = PRIME * result + ((title == null) ? 0 : title.hashCode());
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

		public CharSequence getTargetConcept() {
			if (targetConcept==null) {
				targetConcept = getTargetConceptPage();
				if (section!=null) targetConcept = targetConcept + "#" + section; 
			}
			
			return targetConcept;
		}

		public CharSequence getTargetConceptPage() {
			if (targetConceptPage==null) {
				if (namespace!=Namespace.MAIN && !isConceptNamespace(namespace)) {
					targetConceptPage = target;
					int idx = StringUtils.indexOf('#', targetConceptPage);
					if (idx>=0) targetConceptPage= targetConceptPage.subSequence(0, idx); 
				} else {
					targetConceptPage = title;
				}
			}
			
			return targetConceptPage;
		}
	}

	protected class Page implements WikiPage {
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
		protected boolean redirectKnown = false;
		protected WikiLink aliasFor = null;
		protected boolean aliasForKnown = false;
		
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
		
		protected Page(int namespace, String title, String text, boolean forceCase) {
			this.namespace = namespace;
			this.title = title;
			this.text = text;
			this.name = normalizeTitle(title, forceCase).toString();
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getResourceName()
		 */
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
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getConceptName()
		 */
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
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getProperties()
		 */
		public MultiMap<String, CharSequence, Set<CharSequence>> getProperties() {
			if (properties==null) properties = extractProperties(this);
			return properties;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTemplateExtractor()
		 */
		public TemplateExtractor getTemplateExtractor() {
			if (templateExtractor==null) {
				if ( WikiTextAnalyzer.this.config.nestedTemplateFields==null || WikiTextAnalyzer.this.config.nestedTemplateFields.isEmpty() ) {
					templateExtractor = new FlatTemplateExtractor(WikiTextAnalyzer.this, armor);
				} else {
					templateExtractor = new DeepTemplateExtractor(WikiTextAnalyzer.this, armor);
					((DeepTemplateExtractor)templateExtractor).addContainerFields(WikiTextAnalyzer.this.config.nestedTemplateFields);
				}
			}
			
			return templateExtractor;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getNamespace()
		 */
		public int getNamespace() {
			return namespace;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getText()
		 */
		public CharSequence getText() {
			return text;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getCleanedText(boolean)
		 */
		public CharSequence getCleanedText(boolean armored) {
			if (cleaned==null) {
				cleaned = stripClutter( getText(), namespace, title, armor );
			}
			
			if (armored) return cleaned;
			else return armor.unarmor(cleaned);
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getFlatText(boolean)
		 */
		public CharSequence getFlatText(boolean armored) {
			if (flat==null) {
				flat = stripBoxes( getCleanedText(true) );
			}
			
			if (armored) return flat;
			else return armor.unarmor(flat);
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getPlainText(boolean)
		 */
		public CharSequence getPlainText(boolean armored) {
			if (plain==null) {
				CharSequence flatText = getFlatText(true);
				plain = stripMarkup(  flatText  );
			}
			
			if (armored) return plain;
			else return armor.unarmor(plain);
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getFirstParagraph()
		 */
		public CharSequence getFirstParagraph() {
			if (firstParagraph==null) {
				//CharSequence text = getCleanedText(true); //XXX: why did we try this??
				CharSequence text = getFlatText(true);
				
				CharSequence p = extractFirstParagraph( text );
				
				if (p==null) {
					firstParagraph = "";
				} else {
					p = stripMarkup(  p );
					firstParagraph = armor.unarmor( p ); 
				}
			}
			
			return firstParagraph;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getFirstSentence()
		 */
		public CharSequence getFirstSentence() {
			if (firstSentence==null) {
				CharSequence p = getFirstParagraph();
				firstSentence = language.extractFirstSentence( p );
			}
			
			return firstSentence;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTitle()
		 */
		public CharSequence getTitle() {
			return title;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getName()
		 */
		public CharSequence getName() {
			return name;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getLinks()
		 */
		public List<WikiLink> getLinks() {
			if (links==null) {
				links = Collections.unmodifiableList( extractLinks( getName(), getCleanedText(true) ) );
			}
			
			return links;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getSupplementLinks()
		 */
		public Set<CharSequence> getSupplementLinks() {
			if (supplementLinks==null) {
				supplementLinks = Collections.unmodifiableSet( determineSupplementLinks( this ) );
			}
			
			return supplementLinks;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getSupplementedConcept()
		 */
		public CharSequence getSupplementedConcept() {
			if (supplementedConcept==null) {
				supplementedConcept = new Holder<CharSequence>(determineSupplementedConcepts( this ));
			}
			
			return supplementedConcept.getValue();
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getDisambigLinks()
		 */
		public List<WikiLink> getDisambigLinks() {
			if (disambig==null) {
				disambig = extractDisambigLinks( getName(), getFlatText(true) );
			}
			
			return disambig;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTemplates()
		 */
		public MultiMap<String, TemplateData, List<TemplateData>> getTemplates() {
			if (templates==null) {
				CharSequence s = getCleanedText(true);
				
				templates = getTemplateExtractor().extractTemplates(s);
			}
			
			return templates;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getCategories()
		 */
		public Set<String> getCategories() {
			if (categories==null) {
				Set<String> c = new HashSet<String>();
				List<WikiLink> links = getLinks();
				
				for (WikiLink link : links) {
					if (link.getMagic() == LinkMagic.CATEGORY) {
						c.add(link.getTitle().toString());
					}
				}
				categories = Collections.unmodifiableSet( c );
			}
			
			return categories;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getSections()
		 */
		public Set<String> getSections() {
			if (sections==null) {
				sections = Collections.unmodifiableSet( extractSections( getCleanedText(true) ) );
			}
			
			return sections;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getSectionsString()
		 */
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

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getCategoriesString()
		 */
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

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTemplatesString()
		 */
		public String getTemplatesString() {
			if (templatesString == null) {
				StringBuilder s = new StringBuilder();
				MultiMap<String, TemplateData, List<TemplateData>> templates = getTemplates();
				for (CharSequence t : templates.keySet()) {
					s.append(t);
					s.append('\n');
				}
				
				templatesString = s.toString();
			}

			return templatesString;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getResourceType()
		 */
		public ResourceType getResourceType() {
			if (pageType==null) pageType = determineResourceType(this);
			return pageType;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getConceptType()
		 */
		public ConceptType getConceptType() {
			if (conceptType==null) {
				conceptType = determineConceptType(this);
				if (conceptType==null) throw new RuntimeException("failed to determin concept type for "+getResourceName()+" (rcType: "+getResourceType()+")");
			}
			return conceptType;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTitleTerms()
		 */
		public Set<CharSequence> getTitleTerms() {
			if (titleTerms==null) {
				titleTerms = determineTitleTerms(this);
			}
			return titleTerms;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getPageTerms()
		 */
		public Set<CharSequence> getPageTerms() {
			if (pageTerms==null) {
				pageTerms = determinePageTerms(this);
			}
			return pageTerms;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getRedirect()
		 */
		public WikiLink getRedirect() {
			if (!redirectKnown) {
				redirect = extractRedirectLink( this );
				redirectKnown = true;
			}
			
			return redirect;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getAliasFor()
		 */
		public WikiLink getAliasFor() {
			if (!aliasForKnown) {
				aliasFor = extractAliasLink( this );
				aliasForKnown = true;
			}
			
			return aliasFor;
		}

		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTitleSuffix()
		 */
		public CharSequence getTitleSuffix() {
			if (titleSuffix == null) {
				titleSuffix = determineTitleSuffix(name);
			}

			return titleSuffix;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTitlePrefix()
		 */
		public CharSequence getTitlePrefix() {
			if (titlePrefix == null) {
				titlePrefix = determineTitlePrefix(name);
			}

			return titlePrefix;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getTitleBaseName()
		 */
		public CharSequence getTitleBaseName() {
			if (baseName == null) {
				baseName = determineBaseName(name);
			}

			return baseName;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getDisplayTitle()
		 */
		public CharSequence getDisplayTitle() {
			if (displayTitle == null) {
				TemplateData dt;
				MultiMap<String, TemplateData, List<TemplateData>> tpl = getTemplates();
				List<TemplateData> lst = tpl.get("DISPLAYTITLE");
				dt = lst==null || lst.size()==0 ? null : lst.get(0);
				
				if (dt!=null) displayTitle = dt.getParameter("0");
				//if (displayTitle==null) displayTitle = replaceUnderscoreBySpace( getTitle() );
				//TODO: avoid re-trying
			}
			
			return displayTitle;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getDefaultSortKey()
		 */
		public CharSequence getDefaultSortKey() {
			if (defaultSortKey == null) {
				TemplateData dst;
				MultiMap<String, TemplateData, List<TemplateData>> tpl = getTemplates();
				List<TemplateData> lst = tpl.get("DEFAULTSORT");
				dst = lst==null || lst.size()==0 ? null : lst.get(0);
				
				if (dst!=null) defaultSortKey = dst.getParameter("0");
				//if (defaultSortKey==null) defaultSortKey = replaceUnderscoreBySpace( getTitle() );
				//TODO: avoid re-trying
			}
			
			return defaultSortKey;
		}
		
		/* (non-Javadoc)
		 * @see de.brightbyte.wikiword.analyzer.WikiPage#getAnalyzer()
		 */
		public WikiTextAnalyzer getAnalyzer() {
			return WikiTextAnalyzer.this;
		}

		public CharSequence getCleanedText() {
			return getCleanedText(false);
		}

		public CharSequence getFlatText() {
			return getFlatText(false);
		}

		public CharSequence getPlainText() {
			return getPlainText(false);
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
	private Map<String, Matcher> magicMatchers; //set up in initialize
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
	private Map<String, Interwiki> interwikiMap;

	public WikiTextAnalyzer(PlainTextAnalyzer language) throws IOException {
		this.language = language;
		this.corpus = language.getCorpus();
		this.namespaces = corpus.getNamespaces();
		this.interwikiMap = corpus.getInterwikiMap();
		this.conceptTypes = corpus.getConceptTypes();
		
		config = new WikiConfiguration( language.getCorpus().getWikiName() );
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
		
		config.attach(this);
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
		titleSuffixMatcher = config.titleSuffixPattern.matcher("");
		titlePrefixMatcher = config.titlePrefixPattern.matcher("");
		badLinkMatcher = config.badLinkPattern.matcher("");
		badTitleMatcher = config.badTitlePattern.matcher("");
		disambigLineMatcher = config.disambigLinePattern.matcher("");
		mainArtikeMarkerMatcher = config.mainArtikeMarkerPattern == null ? null : config.mainArtikeMarkerPattern.matcher("");
		disambigStripSectionMatcher = config.disambigStripSectionPattern == null ? null : config.disambigStripSectionPattern.matcher("");

		magicMatchers = new HashMap<String, Matcher>(config.magicPatterns.size());
		for (Map.Entry<String, Pattern> e: config.magicPatterns.entrySet()) {
				magicMatchers.put(e.getKey(), e.getValue().matcher(""));
		}

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
					config.redirectExtractors,
					config.aliasExtractors,
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
		return new Page(namespace, title, text, forceCase);
	}
	
	protected MultiMap<String, CharSequence, Set<CharSequence>> extractProperties(WikiPage page) {
		MultiMap<String, CharSequence, Set<CharSequence>> m = AnalyzerUtils.evalPropertyExtractors(config.propertyExtractors, page);
		if (m==null) m = ValueSetMultiMap.empty();
		return m;
	}
	
	protected CharSequence resolveMagic(CharSequence text, int namespace, CharSequence title) {
		for(Map.Entry<String, Matcher> e: magicMatchers.entrySet()) {
			text = resolveMagic(e.getKey(), e.getValue(), text, namespace, title);
		}
		
		return text;
	}
	
	protected CharSequence resolveMagic(String name, Matcher m, CharSequence text, int namespace, CharSequence title) {
		m.reset(text);
		
		StringBuffer s =  null;
		while(m.find()) {
			CharSequence value = evaluateMagic(name, namespace, title);
			
			if (s==null) s = new StringBuffer(text.length());
			
			if (value==null) m.appendReplacement(s, Matcher.quoteReplacement(m.group(0)));
			else m.appendReplacement(s, Matcher.quoteReplacement(value.toString()));
		}
		
		if (s==null) {
			return text;
		} else {
			m.appendTail(s);
			return s;
		}
	}
	
	protected static final Matcher subpageMatcher = Pattern.compile("^(.+)/([^/]+)$").matcher("");
	
	private CharSequence evaluateMagic(String name, int namespace, CharSequence title) {
		name = name.toUpperCase(); //XXX: ugly...
		
		if (name.equals("PAGENAME")) {
			return AnalyzerUtils.replaceUnderscoreBySpace(title);
		}
		else if (name.equals("PAGENAMEE")) {
			return AnalyzerUtils.replaceSpaceByUnderscore(title);
		}
		else if (name.equals("SUBPAGENAME")) {
			subpageMatcher.reset(title);
			return AnalyzerUtils.replaceUnderscoreBySpace(subpageMatcher.replaceAll("$2"));
		}
		else if (name.equals("SUBPAGENAMEE")) {
			subpageMatcher.reset(title);
			return AnalyzerUtils.replaceSpaceByUnderscore(subpageMatcher.replaceAll("$2"));
		}
		else if (name.equals("BASEPAGENAME")) {
			subpageMatcher.reset(title);
			return AnalyzerUtils.replaceUnderscoreBySpace(subpageMatcher.replaceAll("$1"));
		}
		else if (name.equals("BASEPAGENAMEE")) {
			subpageMatcher.reset(title);
			return AnalyzerUtils.replaceSpaceByUnderscore(subpageMatcher.replaceAll("$1"));
		}
		else if (name.equals("NAMESPACE")) {
			return AnalyzerUtils.replaceUnderscoreBySpace(namespaces.getNamespace(namespace).getLocalName());
		}
		else if (name.equals("NAMESPACEE")) {
			return AnalyzerUtils.replaceUnderscoreBySpace(namespaces.getNamespace(namespace).getLocalName());
		}
		else if (name.equals("FULLPAGENAME")) {
			if (namespace!=0) title = namespaces.getNamespace(namespace).getLocalName() + ":" + title;  
			return AnalyzerUtils.replaceUnderscoreBySpace(title);
		}
		else if (name.equals("FULLPAGENAMEE")) {
			if (namespace!=0) title = namespaces.getNamespace(namespace).getLocalName() + ":" + title;  
			return AnalyzerUtils.replaceSpaceByUnderscore(title);
		}
		else {
			return null;
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
		text = applyManglers(config.stripBoxesManglers, text);
		text = StringUtils.trim(text); //TODO: consolidate multiple blank lines //TODO: strip empty parents ()
		return text;
	}

	public CharSequence stripMarkup(CharSequence text) {
		//NOTE: if one of the mangers is a StripMarkupMangler, we got regress.
		text  = applyManglers(config.stripMarkupManglers, text);
		text = StringUtils.trim(text); //TODO: consolidate multiple blank lines //TODO: strip empty parents ()
		return text;
	}

	protected ResourceType determineResourceType(WikiPage page) {
		if (page.getNamespace() == Namespace.CATEGORY) return ResourceType.CATEGORY;
		
		ResourceType t = AnalyzerUtils.evalSensors(config.supplementSensors, page, null);
		if (t != null) return t;
		
		if (page.getSupplementedConcept()!=null) return ResourceType.SUPPLEMENT;
		
		if (page.getNamespace() != Namespace.MAIN) return ResourceType.OTHER;
		
		WikiLink redirect = page.getRedirect();
		if (redirect!=null) return ResourceType.REDIRECT;
		
		return (ResourceType)AnalyzerUtils.evalSensors(config.resourceTypeSensors, page, ResourceType.ARTICLE);
	}

	protected ConceptType determineConceptType(WikiPage page) {
		return (ConceptType)AnalyzerUtils.evalSensors(config.conceptTypeSensors, page, ConceptType.OTHER);
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
		//TODO: extract bold parts of first sentence!
		
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
		CharSequence name = AnalyzerUtils.trim(AnalyzerUtils.replaceUnderscoreBySpace(determineBaseName(title))); 
		terms.add( name.toString() );
		
		//TODO: use subpage-titles
		
		return terms;
	}

	public Set<CharSequence> evalExtractors(Collection<ValueExtractor> extractors, WikiPage page) {
		Set<CharSequence> values = null;
		
		if (extractors==null) return Collections.emptySet();
		
		for (ValueExtractor extractor : extractors) {
			values = extractor.extract(page, values);
		}
		
		if (values==null) return Collections.emptySet();
		return values;
	}

	protected WikiLink extractRedirectLink(WikiPage page) {
		Set<CharSequence> t = evalExtractors(config.redirectExtractors, page);
		if (t==null || t.isEmpty()) return null;
		
		CharSequence target = t.iterator().next(); //first item
		
		return makeLink(page.getName(), target, null, null);
	}

	protected WikiLink extractAliasLink(WikiPage page) {
		Set<CharSequence> t = evalExtractors(config.aliasExtractors, page);
		if (t==null || t.isEmpty()) return null;
		
		CharSequence target = t.iterator().next(); //first item
		
		return makeLink(page.getName(), target, null, null);
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
		
		target = AnalyzerUtils.trim(target);
		if (target.length()<config.minNameLength || target.length()>config.maxNameLength) return null;
		
		target = decodeLinkTarget(target);
		
		LinkMagic magic = LinkMagic.NONE;
		CharSequence interwiki = null;
		int namespace = Namespace.MAIN;
		CharSequence section = null;
		boolean esc = false;
		
		while (target.length()>0 && target.charAt(0)==':') {
			target = target.subSequence(1, target.length());
			esc = true;
		}
		
		if (target.length()==0) return null;
		
		boolean implied = false;
		
		if (text==null) {
			implied = true;
			text = target;
		}
		
		boolean setTargetToTitle = true;
		CharSequence title = target;
		
		//handle section links ------------------------
		int idx = StringUtils.indexOf('#', title);
		if (idx==title.length()-1) {
			title = title.subSequence(0, title.length()-1);
			target = title;
			section = null;
		}
		else if (idx==0) {
			section = title.subSequence(1, title.length());
			title = context;
			target = null; //restored later
		}
		else if (idx>0) {
			section = title.subSequence(idx+1, title.length());
			title = title.subSequence(0, idx);
		}
		
		//TODO: subpages starting with "/"...
		
		if (section!=null) { //handle special encoded chars in section ref
			section = decodeSectionName(AnalyzerUtils.trim(section));
			section = AnalyzerUtils.replaceSpaceByUnderscore(section);
			if (target==null) {
				target = context + "#" + section;
				setTargetToTitle = false;
			}
		}
		
		//handle qualifiers ------------------------
		idx = StringUtils.indexOf(':', title);
		if (idx>=0) {
			CharSequence pre = AnalyzerUtils.trim(title.subSequence(0, idx));
			pre = normalizeTitle(pre);
			int ns = getNamespaceId(pre);
			if (ns!=Namespace.NONE) {
				namespace = ns;
				title = title.subSequence(idx+1, title.length());
				target = target.subSequence(idx+1, target.length());
				target = getNamespaceName(ns) + ":" + normalizeTitle(target);
				setTargetToTitle = false;
				
				if (!esc) {
					if (ns==Namespace.IMAGE) magic = LinkMagic.IMAGE;
					else if (ns==Namespace.CATEGORY) magic = LinkMagic.CATEGORY;
				}
			}
			else if (isInterwikiPrefix(pre)) {
				if (target!=title) setTargetToTitle = false;
				title = title.subSequence(idx+1, title.length());
				
				if (!setTargetToTitle) {
						idx = StringUtils.indexOf(':', target);
						target = target.subSequence(idx+1, target.length());
						target = normalizeTitle(target);
				}
				
				//FIXME: normalize target title *namespace*, so it can be joined against the about table!
				
				interwiki = AnalyzerUtils.toLowerCase(pre);
				
				if (isInterlanguagePrefix(pre) && !esc) {
					magic = LinkMagic.LANGUAGE;
				}
			}
			/*else {
				int dummy = 0; //breakpoint dummy
			}*/
		}
		
		if (implied && magic == LinkMagic.CATEGORY) {
			text = context; //sort key defaults to local page
		}
		
		if (tail!=null && magic == LinkMagic.NONE) text = text.toString() + tail;
		if (!implied) text = stripMarkup(text); //XXX: this can get pretty expensive...
		text = HtmlEntities.decodeEntities(text);
		
		if (title.length()==0) return null;
		
		if (target==title) setTargetToTitle = true;
		title = normalizeTitle(title);
		if (setTargetToTitle) 
			target = title;
			
		return new WikiLink(interwiki, target, namespace, title, section, text, implied, magic); 
	}

	public boolean isInterlanguagePrefix(CharSequence pre) {
		pre = AnalyzerUtils.trimAndLower(pre);
		return getLanguageNames().containsKey(pre);
	}
	
	protected Map<String, String> getLanguageNames() {
		if (this.languageNames==null) {
			this.languageNames = Languages.load(this.tweaks);
		}
		
		return this.languageNames;
	}

	public boolean isInterwikiPrefix(CharSequence pre) {
		if (interwikiMap!=null && !interwikiMap.isEmpty()) {
			pre =  AnalyzerUtils.trimAndLower(pre);
			return interwikiMap.containsKey(pre);
		} else {
			interwikiMatcher.reset(pre);
			return interwikiMatcher.matches();
		}
	}

	public int getNamespaceId(CharSequence name) {
		if (name.length()==0) return Namespace.MAIN;
		name = normalizeTitle(name);
		if (name.length()==0) return Namespace.MAIN;
		
		return namespaces.getNumber(name.toString());
	}
	
	public String getNamespaceName(int id) {
		if (id==0) return "";
		
		return namespaces.getNamespace(id).getLocalName();
	}
	
	public CharSequence normalizeTitle(CharSequence title) {
		return normalizeTitle(title, true);
	}
	
	public CharSequence normalizeTitle(CharSequence title, boolean forceCase) {
		title = AnalyzerUtils.trim(title);
		if (title.length()==0) return title;
		
		title = AnalyzerUtils.replaceSpaceByUnderscore(title);
		
		if (titleCase && forceCase) {
			title = AnalyzerUtils.titleCase(title);
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
			s = AnalyzerUtils.trim(stripMarkup(s));
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

	public boolean useSuffixAsCategory() {
		return config.useSuffixAsCategory;
	}

	public boolean flatTextSupported() {
		return config.flatTextSupported;
	}

	public boolean definitionsSupported() {
		return config.definitionsSupported;
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
		
		//TODO: make special LinkExtractor interface
		
		linkMatcher.reset(text);
		while (linkMatcher.find()) {
			String target = linkMatcher.group(1);
			String label = linkMatcher.group(3);
			String trail = linkMatcher.group(4);
			
			WikiLink link = makeLink(title, target, label, trail);
			if (link==null) continue;
			if (isBadLinkTarget(link.getTarget())) continue; 
				
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
	
	/**
	 * returns true of the given namespace ID identifies a namespace
	 * that contains pages about concepts. This is usually the main namespace
	 * and the category namespace.
	 */
	public boolean isConceptNamespace(int ns) {
		return config.conceptNamespacecs.contains(ns);
	}
	
	public boolean mayBeFormOf(CharSequence form, CharSequence base) {
		form = AnalyzerUtils.toLowerCase(form);
		base = AnalyzerUtils.toLowerCase(base);
		
		if (config.maxWordFormDistance <= 0) return StringUtils.equals(form, base);
		
		double d = LevenshteinDistance.instance.distance(form, base);
		d /= Math.max(form.length(), base.length());
		return d <= config.maxWordFormDistance;
	}

	public WikiLink newLink(String interwiki, String target, int namespace, String title, String section, String text, boolean impliedText, LinkMagic magic) {
		return new WikiLink(interwiki, target, namespace, title, section, text, impliedText, magic);
	}

	public static WikiTextAnalyzer getWikiTextAnalyzer(Corpus corpus, TweakSet tweaks) throws InstantiationException {
		PlainTextAnalyzer language = PlainTextAnalyzer.getPlainTextAnalyzer(corpus, tweaks);
		language.initialize();
		
		return getWikiTextAnalyzer(language);
	}
	
	@SuppressWarnings({ "null", "unchecked" })
	public static WikiTextAnalyzer getWikiTextAnalyzer(PlainTextAnalyzer language) throws InstantiationException {
		String[] pkgs = language.getCorpus().getConfigPackages();
		Class[] acc = getSpecializedClasses(language.getCorpus(), WikiTextAnalyzer.class, "WikiTextAnalyzer");
		Class[] ccc = getSpecializedClasses(language.getCorpus(), WikiConfiguration.class, "WikiConfiguration", pkgs);
		
		logger.debug("getWikiTextAnalyzer uses implementation "+acc[acc.length-1]);
		logger.debug("getWikiTextAnalyzer uses configurations "+Arrays.toString(ccc));
		
		try {
			Constructor ctor = acc[acc.length-1].getConstructor(new Class[] { PlainTextAnalyzer.class });
			WikiTextAnalyzer analyzer = (WikiTextAnalyzer)ctor.newInstance(new Object[] { language } );
			
			Set<Class> stop = new HashSet<Class>();
			
			for (int i = ccc.length-1; i >= 0; i--) { //NOTE: most specific last, because last write wins.
				if (!stop.add(ccc[i])) continue;
				WikiConfiguration conf;
				
				try {
					ctor = ccc[i].getConstructor(new Class[] { });
					conf = (WikiConfiguration)ctor.newInstance(new Object[] { } );
				} 
				catch (NoSuchMethodException ex) {
					ctor = ccc[i].getConstructor(new Class[] { String.class });
					conf = (WikiConfiguration)ctor.newInstance(new Object[] { language.getCorpus().getLanguage() } );
				}
				
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
	
	public static void main(String[] argv) throws InstantiationException, IOException {
		Arguments args = new Arguments();
		args.declare("tweaks", null, true, String.class, "tweak file");
		
		args.parse(argv);
		
		String lang = args.getParameter(0);
		String name = args.getParameter(1);
		String file = args.getParameter(2);
		
		if (file.endsWith("/") || file.endsWith("=")) {
			if (file.indexOf("://")>0) file = file + URLEncoder.encode(name, "UTF-8");
			else file = file + name + ".wiki";
		}
		
		TweakSet tweaks = new TweakSet();

		String tf = args.getStringOption("tweaks", null);
		if (tf!=null) tweaks.loadTweaks(new File(tf));

		
		tweaks.setTweaks(System.getProperties(), "wikiword.tweak."); //XXX: doc
		tweaks.setTweaks(args, "tweak."); //XXX: doc
		
		InputFileHelper input = new InputFileHelper(tweaks);
		String text = IOUtil.slurp(input.open(file), "UTF-8");
		
		Corpus corpus = Corpus.forName("TEST", lang, tweaks);
		WikiTextAnalyzer analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(corpus, tweaks);
		
		NamespaceSet namespaces = Namespace.getNamespaces(null);
		analyzer.initialize(namespaces, true);
		
		WikiLink t = analyzer.makeLink("TEST", name, null, null);
		
		WikiPage page = analyzer.makePage(t.getNamespace(), t.getTarget().toString(), text, true);

		for (int i=3; i<args.getParameterCount(); i++) {
				String attr = args.getParameter(i);
				System.out.println("==== "+attr+" =====================================");
			
				Object v= BeanPropertyAbstractor.instance.getProperty(page, attr);
				
				DebugUtil.dump("", v, ConsoleIO.output);
				System.out.println();
		}
	}

}
