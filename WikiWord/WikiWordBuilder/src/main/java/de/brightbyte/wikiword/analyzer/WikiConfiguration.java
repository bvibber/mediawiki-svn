package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Properties;
import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.DefaultLinkSimilarityMeasure;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.LinkSimilarityMeasureFactory;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiLink;
import de.brightbyte.wikiword.analyzer.extractor.PropertyExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TextPatternExtractor;
import de.brightbyte.wikiword.analyzer.extractor.ValueExtractor;
import de.brightbyte.wikiword.analyzer.mangler.Armorer;
import de.brightbyte.wikiword.analyzer.mangler.BoxStripMangler;
import de.brightbyte.wikiword.analyzer.mangler.EntityDecodeMangler;
import de.brightbyte.wikiword.analyzer.mangler.Mangler;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionArmorer;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.mangler.SuccessiveMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.Sensor;

/**
 * A WikiConfiguration represents knowledge about language specific and project specific
 * patterns. It provides sets of patterns, manglers and sensors for use by WikiTextAnalyzer.
 */
public class WikiConfiguration {
	/**
	 * A relaxed link matching pattern for detecting links on redirect pages,
	 * For use in redirectPattern. 
	 * Taken from MediaWiki.
	 */ 
	public static final String REDIRECT_LINK = ".*?\\[{2}(.*?)(?:\\||\\]{2})";
	
	/**
	 *  List of Mangler objects for stripping all remaingin wiki markup from a text. To be applied after 
	 *  stripClutterManglers, stripClutterArmorers, and stripBoxesManglers. Used to provide "plain" text.
	 */
	public List<Mangler> stripMarkupManglers = new ArrayList<Mangler>();
	
	/**
	 * List of Mangler objects for stripping box structures from wiki-text, like templates, tables, 
	 * and blocks enclosed by some XML-style tags. Used to generate a version of the text suitable 
	 * for identifying paragraphs and sentences.
	 */ 
	public List<Mangler> stripBoxesManglers = new ArrayList<Mangler>();
	
	/**
	 * List of Mangler objects for stripping unwanted markup elements, like images and galleries; 
	 * also used to replace templates that are required for accuratly parsing the page structure later,
	 * especially those starting or ending tables or blocks.
	 */ 
	public List<Mangler> stripClutterManglers = new ArrayList<Mangler>();
	
	/**
	 * List of Armorer objects for subsituting parts of the text for which wiki-text markup should not apply.
	 * This includes especially comments, &lt;nowiki&gt;-blocks and &lt;pre&gt;-blocks. 
	 */ 
	public List<Armorer> stripClutterArmorers = new ArrayList<Armorer>();
	
	/**
	 * List of sensors for determining the page's resource type, applicable
	 * for the main namespace.
	 */
	public List<Sensor<ResourceType>> resourceTypeSensors = new ArrayList<Sensor<ResourceType>>();
		
	/**
	 * List of sensors for determining the page's resource type, prior to namespace considerations.
	 */
	public List<Sensor<ResourceType>> supplementSensors = new ArrayList<Sensor<ResourceType>>();

	/**
	 * List of sensors for determining an article's concept type, that is, 
	 * assigning a ConceptTypes object. 
	 */
	public List<Sensor<ConceptType>> conceptTypeSensors = new ArrayList<Sensor<ConceptType>>();
	
	/**
	 * List of extractors for determining an article's properties. 
	 */
	public List<PropertyExtractor> propertyExtractors = new ArrayList<PropertyExtractor>();
	
	/**
	 * List of extractors for determining terms for an article. 
	 */
	public List<ValueExtractor> pageTermExtractors = new ArrayList<ValueExtractor>();
	
	/**
	 * List of extractors for determining redirect targets. 
	 */
	public List<ValueExtractor> aliasExtractors = new ArrayList<ValueExtractor>();
	
	/**
	 * List of extractors for determining redirect targets. 
	 */
	public List<ValueExtractor> redirectExtractors = new ArrayList<ValueExtractor>();
	
	/**
	 * List of extractors for determining the name of supplement pages. 
	 */
	public List<ValueExtractor> supplementNameExtractors = new ArrayList<ValueExtractor>();

	/**
	 * List of extractors for determining the name the supplemented page. 
	 */
	public List<ValueExtractor> supplementedConceptExtractors = new ArrayList<ValueExtractor>();
	
	/**
	 * Pattern for matching the name of the DISPLAYNAME magic word, to be used 
	 * for localizing that name.
	 */
	public Pattern displayTitlePattern = null;

	/**
	 * Pattern for matching the name of the DEFAULTSORT magic word, to be used 
	 * for localizing that name.
	 */
	public Pattern defaultSortKeyPattern = null;

	/**
	 * Maximum distance between to strings for being considered "forms" of the same word.
	 * This is a value between 0 (no difference allowed, identical strings only) 
	 * and 1 (any pair of strings matches). The value is compared to the normalized Levenshtein
	 * distance, that is, lev(a, b) / max(a.length(), b.length()). This is used when detecting
	 * "main articles" for categories.
	 */ 
	public double maxWordFormDistance; 
	
	/**
	 * Pattern for magic variables that need to be replaced early by concrete values.
	 * Only few magic variables are handled by this, mostly those that evaulate to some
	 * form or part of the page's name. Other magic variables are stripped or handeled
	 * as templates.
	 */ 
	public Map<String, Pattern> magicPatterns = new HashMap<String, Pattern>();
	
	/**
	 * Pattern for a sequence if characters that, if it immediatly follows a link,
	 * is appended to the link text; This allows liks of the form "[[dog]]s".
	 * Per default, this pattern matches all letters. 
	 */ 
	public String linkTrail = "[\\p{IsL}]*";
	
	/**
	 * Pattern for illegal page titles. This matches any characters that are illegal
	 * in MediaWiki page titles, like curly braces, square brackets, etc. 
	 */
	public Pattern badTitlePattern = null;

	/**
	 * Pattern for illegal link targets. This is considered inaddition to  badTitlePattern
	 * when deciding which links to skip.
	 */
	public Pattern badLinkPattern = null;

	
	/**
	 * A mengler that extracts the first paragraph from a given text. This task is
	 * rather complex, because it invloves stripping all recursive box-like structures first, 
	 * which includes tables, templates, and some blocks formed with html-tags. This task is
	 * usually performed mainly by an instance of BoxStripMangler. 
	 */
	public SuccessiveMangler extractParagraphMangler = null;
	
	/**
	 * Pattern matching conventional title suffixes (qualifiers). On most wikis, 
	 * that means a part in parentacies at the end of the title, as in "Earth_(Planet)".
	 * The first capturing group of the pattern must be the title without the suffix,
	 * the second capturing group must be the suffix, without the parentacies.
	 */ 
	public Pattern titleSuffixPattern = null;
	
	/**
	 * Pattern matching section title prefixes, that is, for splitting a fragment identifier
	 * from the name of a page. This means partitionling a title that contains a "#" (hash). 
	 * The first capturing group of the pattern must be the page title before the hash,
	 * the second capturing group must be section (fragment) title after the hash.
	 */ 
	public Pattern titlePrefixPattern = null;

	/**
	 * Pattern matching lines that are considered "relevant" on a disambiguation page.
	 * Usually, these are lines starting with one or more "*" and not ending with ":".
	 */ 
	public Pattern disambigLinePattern;

	/**
	 * Patterns matching parts of lines from disambiguation patterns that are known 
	 * to provide the disambiguation link. This may be used to bypass heuristics for picking 
	 * the right link from a disambiguation line, in case there is more than one.
	 * Per default this is not used, a typical application would be patterns that 
	 * match ", see [[xyz]]".
	 */ 
	public List<Pattern> disambigLinkPatterns = new ArrayList<Pattern>();

	/**
	 * Patterns for extra templates to be analyzed.
	 */
	public List<Pattern> extraTemplatePatterns = new ArrayList<Pattern>();;
	
	
	/**
	 * Maximum number of links to consider per line on a disambiguation page. This is a 
	 * sanity check used as a fail safe for the heuristics for picking the right link from the
	 * line. There will be rarely more than three links per line, so the default of 16 here
	 * should be more than sufficient. 
	 */
	public int maxDisambigLinksPerLine = 16;
	
	/**
	 * Minimum similarity between thedisambiguation link and the disambiguation page's title
	 * required for a link to be considered for being selected as the disambiguation link from
	 * a given line on the disambiguation page. This is initialized to 0.2 per default. The 
	 * similarity value this is compared against is determiend by the Measure provided by
	 * linkSimilarityMeasureFactory.
	 */ 
	public double minDisambigLinkSimilarity = 0;

	/**
	 * Factory for generating Measure objects that determine the similarity of links with
	 * a given page title. This is used to check links on disambiguation pages against the
	 * title of said page, to determine if thei are condidates for pointing to a meaning
	 * of the disambiguated term. The default implementation returns an instance of
	 * WikiTextAnalyzer.DefaultLinkSimilarityMeasure. 
	 */ 
	public WikiTextAnalyzer.LinkSimilarityMeasureFactory linkSimilarityMeasureFactory;

	/**
	 * Switch to enable or disable automatic detection of "main articles" of categories;
	 * Categories may be considered aliases for their main article, because they represent
	 * the same concept.
	 */ 
	public boolean useCategoryAliases = true; //use main articles as alias to resolve plural category names
	
	/**
	 * Pattern matching the special sort keys used for placing "main articles" in "their" 
	 * categories. Sort keys used for this purpose are generally single space or punctuation
	 * characters that cause the article to be listed at the very top of the category page.
	 */ 
	public Pattern mainArtikeMarkerPattern;
	
	/**
	 * Pattern matching the name of sections that should be stripped from disambiguation 
	 * pages before looking for disambiguation lines and links. This should especially
	 * match sectiopns for cross-references, i.e. "see also".
	 */ 
	public Pattern disambigStripSectionPattern;

	/** The maximum number of characters allowed for page titles and concept names */
	public int maxNameLength = 128;
	
	/** The maximum number of characters allowed for terms */
	public int maxTermLength = 128;
	
	/** The minimum number of characters required for page titles and concept names */
	public int minNameLength = 1;
	
	/** The minimum number of characters required for terms */
	public int minTermLength = 1;

	/**
	 * Switch to enable or disable the use of title suffixes as categories.
	 */ 
	public boolean useSuffixAsCategory = false; 

	/** Flag indication wether extraction of plain prose text is supported for this wiki **/
	public boolean flatTextSupported = true; 
	
	/** Flag indication wether extraction of definitions is supported for this wiki **/
	public boolean definitionsSupported = true; 
	
	protected WikiTextAnalyzer analyzer;
	
	protected String wikiName;

	public Set<Integer> conceptNamespacecs  = new HashSet<Integer>();
	
	protected MultiMap<CharSequence, CharSequence, Set<CharSequence>> nestedTemplateFields = new ValueSetMultiMap<CharSequence, CharSequence>();

	protected WikiConfiguration() {
		this(null);
	}
	
	public WikiConfiguration(String wikiName) {
		if (wikiName==null) {
			wikiName = AnalyzerUtils.getClassNameSuffix(getClass());
		}
		
		this.wikiName = wikiName;
	}

	public void defaults() throws IOException {
		String img =  StringUtils.join("|", Namespace.canonicalNamespaces.getNamespace(Namespace.IMAGE).getNames());
		Pattern imagePattern = Pattern.compile("\\[\\[ *("+img+") *:(?>[^\\|\\]]+)(\\|((?>[^\\[\\]]+)|\\[\\[(?>[^\\]]+)\\]\\]|\\[(?>[^\\]]+)\\])*)?\\]\\]", Pattern.CASE_INSENSITIVE | Pattern.DOTALL );
		
		this.stripClutterArmorers.add( new RegularExpressionArmorer("comment", Pattern.compile("<!--.*?-->", Pattern.CASE_INSENSITIVE | Pattern.DOTALL), -1) );
		this.stripClutterArmorers.add( new RegularExpressionArmorer("nowiki", tagBlockPattern("nowiki"), 1) );
		this.stripClutterArmorers.add( new RegularExpressionArmorer("pre", tagBlockPattern("pre"), 1) );
		
		this.stripClutterManglers.add( new RegularExpressionMangler(tagBlockPattern("gallery"), "") );
		
		this.stripClutterManglers.add( new RegularExpressionMangler(imagePattern, "") );
		this.stripClutterManglers.add( new RegularExpressionMangler("__\\w+__", "", Pattern.CASE_INSENSITIVE) );
		this.stripClutterManglers.add( new RegularExpressionMangler("[\u00AD\u200B\u200C\u200D\uFEFF]", "", 0) ); //strip soft hyphens and zero width space
		
		String[] boxBeginnings = new String[] {
				"\\{\\{",
				"^\\{\\|",
				"<div(?:\\s+[^>]*)?>",
				"<center(?:\\s+[^>]*)?>",
		};
		
		String[] boxEnds = new String[] {
				"\\|?\\}\\}",
				"^\\|\\}",
				"</div\\s*>",
				"</center\\s*>",
		};
		
		this.stripBoxesManglers.add( new RegularExpressionMangler(tagBlockPattern("ref"), "") );
		this.stripBoxesManglers.add( new BoxStripMangler(boxBeginnings, boxEnds, null) );
		this.stripBoxesManglers.add( new RegularExpressionMangler("<br[^<>]*/?>", "\n", Pattern.CASE_INSENSITIVE | Pattern.DOTALL) );

		if (this.extractParagraphMangler==null) {
			/*
			boxBeginnings = new String[] {
					"^\\{\\|",
					"\\{\\{",
					"<div(?:\\s+[^>]*)?>",
					"<center(?:\\s+[^>]*)?>",
			};
			
			boxEnds = new String[] {
					"^\\|\\}",
					"\\}\\}",
					"</div\\s*>",
					"</center\\s*>",
			};
			*/
			this.extractParagraphMangler = new BoxStripMangler(boxBeginnings, boxEnds, "(\r\n|\r|\n){2,}|^=+.+=+\\s*$"); //TODO: also break on tables, etc
		}
		
		this.stripMarkupManglers.add( new RegularExpressionMangler( tagPattern("br"), "\n") );
		this.stripMarkupManglers.add( new RegularExpressionMangler("^---+", "", Pattern.MULTILINE  ) );
		this.stripMarkupManglers.add( new RegularExpressionMangler("\\[\\[([^\\]|:]+:)([^ _][^\\]]+)\\]\\]", "", Pattern.CASE_INSENSITIVE) ); //XXX: assume all qualified links are interlanguage or categorizations 
		this.stripMarkupManglers.add( new RegularExpressionMangler("\\[\\[([^|\\]|]+)\\|([^\\]]+)\\]\\]", "$2", Pattern.CASE_INSENSITIVE) ); //pipied links
		this.stripMarkupManglers.add( new RegularExpressionMangler("\\[\\[([^\\]|]+)\\]\\]", "$1", Pattern.CASE_INSENSITIVE) ); //unpiped links
		this.stripMarkupManglers.add( new RegularExpressionMangler("\\[\\w+:[^\\s]+\\]", "", Pattern.CASE_INSENSITIVE) );
		this.stripMarkupManglers.add( new RegularExpressionMangler("\\[\\w+:[^\\s]+\\s+([^]]+)\\]", "$1", Pattern.CASE_INSENSITIVE) ); //XXX: unite with above
		//this.stripMarkupManglers.add( new RegularExpressionMangler("^=+\\s*(.*?)\\s*=+\\s*$", "\n\\$ $1\n", Pattern.CASE_INSENSITIVE | Pattern.MULTILINE | Pattern.DOTALL) );
		this.stripMarkupManglers.add( new RegularExpressionMangler("^(:+)", "\t", Pattern.MULTILINE ) );
		//this.stripMarkupManglers.add( new RegularExpressionMangler("(?<!\n)\n(?![\n:*#<])", " ", Pattern.MULTILINE ) );

		this.stripMarkupManglers.add( new RegularExpressionMangler("'''?|<[\\w\\d]+.*?/?>|</[\\w\\d]+>"  //TODO: (some?) unicode punctuation: \p{Punctuation}
																	+"|^[\\p{IsZ}]+|[\\p{IsZ}]+$"
																	//+"|^[\\s\"\\'\\]\\[<>,;.:?\\{\\}]+" 
																	//+"|[\\s\"\\'\\]\\[<>,;.:?\\{\\}]+$"
																	//+"|^(\\s*\\[\\[\\s*[-\\w]+\\s*:\\s*[^\\|\\]]*?\\]\\]\\s*)+" //langlinks (kills raw interwiki-links too)
																	+"$", "", Pattern.CASE_INSENSITIVE | Pattern.MULTILINE ) );
		
		this.stripMarkupManglers.add( new EntityDecodeMangler() ); //NOTE: must be last, entities may be used to escape markup
		
		this.maxWordFormDistance = 1.0/3.0; 
		
		this.badTitlePattern = Pattern.compile("^$|''|[|{}<>\\]\\[]|^\\w+://");
		//this.badLinkPattern = Pattern.compile("^[^\\d]+:[^ _]|^\\.\\.?$"); //disallow namespace/interlang
		this.badLinkPattern = Pattern.compile("^\\.\\.?$"); 
		this.titleSuffixPattern = Pattern.compile("^(.*)[ _]\\((.*?)\\)$");
		this.titlePrefixPattern = Pattern.compile("^(.*?)#(.+)$");
		this.disambigStripSectionPattern = null; 
		
		this.disambigLinePattern = Pattern.compile("^:*[*#]+(.+[^:\\s])\\s*$", Pattern.MULTILINE );
		//this.disambigLinkPatterns.add( ... );
		
		this.linkTrail = "[\\p{IsL}]*";
		
		this.maxDisambigLinksPerLine = 16;
		this.minDisambigLinkSimilarity = 0.2; 
		
		this.maxNameLength = 128;
		this.maxTermLength = 128;
		
		this.minNameLength = 1;
		this.minTermLength = 1;
		
		this.linkSimilarityMeasureFactory = new LinkSimilarityMeasureFactory() {
			public Measure<WikiLink> newLinkSimilarityMeasure(CharSequence basename, WikiTextAnalyzer analyzer) {
				return new DefaultLinkSimilarityMeasure(basename, analyzer);
			}
		};
		
		useCategoryAliases = true;
		mainArtikeMarkerPattern = Pattern.compile("^[- !_*$@#+~/%]?"); //use "category main articles" to resolve plural names
		
		this.useSuffixAsCategory = false;
		this.definitionsSupported = true;
		this.flatTextSupported = true;
		
		//defaults from external knowledge....
		
		Properties pyBotFamily = AuxilliaryWikiProperties.loadProperties("PyBotFamily", wikiName);
		Properties messages = AuxilliaryWikiProperties.loadProperties("Messages", wikiName);
		Properties magicWords = AuxilliaryWikiProperties.loadProperties("MagicWords", wikiName);

		List<String> disambig = AuxilliaryWikiProperties.loadList("DisambiguationTemplates", wikiName);
		
		String disambiguationTemplates = pyBotFamily.getProperty("disambiguationTemplates");
		if (disambiguationTemplates!=null) resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.DISAMBIG, "^("+disambiguationTemplates.replace(' ', '_')+")$", 0) );
		
		if (disambig!=null && disambig.size()>0) {
			String d = StringUtils.join("|", disambig);
			d = d.replace(' ', '_');
			resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.DISAMBIG, "^("+d+")$", 0) );
		}
		
		String disambcatname = pyBotFamily.getProperty("disambcatname");
		if (disambcatname!=null) resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.DISAMBIG, "^"+disambcatname.replace(' ', '_')+"$", 0) );

		String linktrail = messages.getProperty("linktrail");
		if (linktrail==null) linktrail = pyBotFamily.getProperty("linktrails");
		if (linktrail!=null) this.linkTrail = linktrail;
		
		//XXX: msg,msgnw,int,raw...
		//TODO: formatnum, formatdate, padleft, padright: pass through
		
		String displaytitle = magicWords.getProperty("displaytitle", "DISPLAYTITLE");
		this.displayTitlePattern = Pattern.compile(displaytitle, Pattern.CASE_INSENSITIVE);
		
		String defaultsort = magicWords.getProperty("defaultsort", "DEFAULT(SORT(KEY)?|CATEGORYSORT)");
		defaultsort = defaultsort.replace(":", ""); //XXX: nasty hack
		this.defaultSortKeyPattern = Pattern.compile(defaultsort, Pattern.CASE_INSENSITIVE);
		
		String[] magicNames = { 
				"pagename", "pagenamee", 
				"fullpagename", "fullpagenamee", 
				"subpagename", "subpagenamee", 
				"basepagename", "basepagenamee", 
				"namespace", "namespacee", 
				};
		
		for (String n: magicNames) {
			Pattern p = Pattern.compile("\\{\\{\\s*("+magicWords.getProperty(n)+")\\s*\\}\\}", Pattern.CASE_INSENSITIVE); 
			magicPatterns.put(n, p);
		}
		
		String redirect = magicWords.getProperty("redirect", "#REDIRECT(?:ION)?");
		this.redirectExtractors.add( new TextPatternExtractor("^(?:"+redirect+")"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE | Pattern.MULTILINE, 1) );
		
		String category_redirect_templates = pyBotFamily.getProperty("category_redirect_templates");
		if (category_redirect_templates!=null) this.redirectExtractors.add( new TemplateParameterValueExtractor(category_redirect_templates.replace(' ', '_'), Pattern.CASE_INSENSITIVE, "1").setPrefix("Category:") );
		
		conceptNamespacecs.add(Namespace.MAIN);
		conceptNamespacecs.add(Namespace.CATEGORY);
	}
	
	public void attach(WikiTextAnalyzer analyzer) {
		if (this.analyzer!=null) {
			if (this.analyzer==analyzer) return;
			else throw new IllegalStateException("WikiConfiguration already attached to a WikiTextAnalyzer");
		}
		
		this.analyzer = analyzer;
		//noop
	}
	
	public void merge(WikiConfiguration with) {
		stripMarkupManglers.addAll(with.stripMarkupManglers);
		stripBoxesManglers.addAll(with.stripBoxesManglers);
		stripClutterManglers.addAll(with.stripClutterManglers);
		stripClutterArmorers.addAll(with.stripClutterArmorers);
		
		resourceTypeSensors.addAll(with.resourceTypeSensors);
		supplementSensors.addAll(with.supplementSensors);
		conceptTypeSensors.addAll(with.conceptTypeSensors);
		propertyExtractors.addAll(with.propertyExtractors);
		pageTermExtractors.addAll(with.pageTermExtractors);
		redirectExtractors.addAll(with.redirectExtractors);
		aliasExtractors.addAll(with.aliasExtractors);
		supplementNameExtractors.addAll(with.supplementNameExtractors);
		supplementedConceptExtractors.addAll(with.supplementedConceptExtractors);
		
		extraTemplatePatterns.addAll(with.extraTemplatePatterns);
		nestedTemplateFields.putAll(with.nestedTemplateFields);
		
		//if (with.language!=null) language = with.language;
		
		if (with.linkTrail!=null) linkTrail = with.linkTrail;
		if (with.badLinkPattern!=null) badLinkPattern = with.badLinkPattern;

		if (with.displayTitlePattern!=null) displayTitlePattern = with.displayTitlePattern;
		if (with.defaultSortKeyPattern!=null) defaultSortKeyPattern = with.defaultSortKeyPattern;
		if (with.magicPatterns!=null) magicPatterns.putAll(with.magicPatterns);

		if (with.extractParagraphMangler!=null) extractParagraphMangler = with.extractParagraphMangler;
		if (with.titleSuffixPattern!=null) titleSuffixPattern = with.titleSuffixPattern;
		if (with.titlePrefixPattern!=null) titlePrefixPattern = with.titlePrefixPattern;

		if (with.disambigLinePattern!=null) disambigLinePattern = with.disambigLinePattern;
		if (with.disambigLinkPatterns!=null) disambigLinkPatterns = with.disambigLinkPatterns;

		if (with.linkSimilarityMeasureFactory!=null) linkSimilarityMeasureFactory = with.linkSimilarityMeasureFactory;

		maxDisambigLinksPerLine = Math.min(maxDisambigLinksPerLine, with.maxDisambigLinksPerLine);
		minDisambigLinkSimilarity = Math.max(minDisambigLinkSimilarity, with.minDisambigLinkSimilarity);

		useCategoryAliases = useCategoryAliases || with.useCategoryAliases; 
		if (with.mainArtikeMarkerPattern!=null) mainArtikeMarkerPattern = with.mainArtikeMarkerPattern;
		if (with.disambigStripSectionPattern!=null) disambigStripSectionPattern = with.disambigStripSectionPattern;

		if (with.maxWordFormDistance>0) maxWordFormDistance = with.maxWordFormDistance;
		
		useSuffixAsCategory = useSuffixAsCategory || with.useSuffixAsCategory;
		definitionsSupported = definitionsSupported && with.definitionsSupported;
		flatTextSupported = flatTextSupported && with.flatTextSupported;
	}
	
	/**
	 * generates a pattern for matching section headers of sections with names matching the given pattern.
	 * 
	 * @param pattern a regular expression matching the name of the section 
	 * @param flags Flags to be used when compiling the pattern; use the constants provided by the Pattern class.
	 */
	public static Pattern sectionPattern(String pattern, int flags) {
		return Pattern.compile( "^\\s*(=+)\\s*(" + pattern + ")\\s*\\1\\s*$", flags | Pattern.MULTILINE );
	}

	/**
	 * Generates a pattern that matches blocks enclosed by XML-style tags with the given tag name.
	 * Also matches empty tags. Nested tags with the same name are not supported correctly. 
	 * The text between the matching pair of tags is captured by the first group. 
	 * 
	 * @param tag a regular expression matching the name of the tag 
	 */
	public static Pattern tagBlockPattern(String tag) {
		return Pattern.compile("< *"+tag+"(?: [^>\\n]*)? */>|< *"+tag+"(?: [^>\\n]*)? *>(.*?)</ *"+tag+" *>", Pattern.CASE_INSENSITIVE | Pattern.DOTALL);
	}

	public static Pattern tagPattern(String tag) {
		return Pattern.compile("< *"+tag+"(?: [^>\\n]*)? */?>", Pattern.CASE_INSENSITIVE | Pattern.DOTALL);
	}

	/**
	 * Compiles a pattern that matches templates with the given name and number of parameters.
	 * The template's name is captured by the first group, the first parameter by the seconds group,
	 * and so on. 
	 * 
	 * @param name a regular expression matching the name of the tag 
	 * @param params Flags to be used when compiling the pattern; use the constants provided by the Pattern class.
	 * @param more if true, the template is allowed to have more parameters, which are hoewever not captured.
	 */
	public static Pattern templatePattern(String name, int params, boolean more) {
		return templatePattern(name, params, more, false);
	}
	
	public static Pattern templatePattern(String name, int params, boolean more, boolean stripNamedParams) {
		String s = templatePatternString(name, params, more, stripNamedParams);
		return Pattern.compile(s, Pattern.CASE_INSENSITIVE | Pattern.DOTALL);
	}
	
	/**
	 * Composes a pattern that matches templates with the given name and number of parameters.
	 * The template's name is captured by the first group, the first parameter by the seconds group,
	 * and so on. 
	 * 
	 * @param name a regular expression matching the name of the tag 
	 * @param params Flags to be used when compiling the pattern; use the constants provided by the Pattern class.
	 * @param more if true, the template is allowed to have more parameters, which are hoewever not captured.
	 */
	public static String templatePatternString(String name, int params, boolean more) {
		return templatePatternString(name, params, more, false);
	}
	
	public static String templatePatternString(String name, int params, boolean more, boolean stripNamedParams) {
		String s = "\\{\\{\\s*";
		s+= "#?("+name+")\\s*";
		
		if (stripNamedParams) s+= "(?:\\s*\\|[^|={}]*=[^|{}]*\\s*)*";
		
		for (int i=0; i<params; i++){
			if (i>0) s+= "\\|";
			else s+= "[:|]";
			
			s+= "([^|={}]*)\\s*";
		}
		
		if (more) {
			if (params==0) s+= "(\\s*[|:].*?)?";
			else s+= "(\\s*\\|.*?)?";
		}
		
		s+= "\\s*\\}\\}";

		return s;
	}
	
}
