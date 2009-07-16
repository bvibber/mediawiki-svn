package de.brightbyte.wikiword.analyzer;

import java.util.List;
import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiLink;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;

public interface WikiPage {

	public String getResourceName();

	public String getConceptName();

	public MultiMap<String, CharSequence, Set<CharSequence>> getProperties();

	public TemplateExtractor getTemplateExtractor();

	public int getNamespace();

	public CharSequence getText();

	public CharSequence getCleanedText(boolean armored);

	public CharSequence getFlatText(boolean armored);

	public CharSequence getPlainText(boolean armored);

	public CharSequence getCleanedText();

	public CharSequence getFlatText();

	public CharSequence getPlainText();

	public CharSequence getFirstParagraph();

	public CharSequence getFirstSentence();

	public CharSequence getTitle();

	public CharSequence getName();

	public List<WikiLink> getLinks();

	public Set<CharSequence> getSupplementLinks();

	public CharSequence getSupplementedConcept();

	public List<WikiLink> getDisambigLinks();

	public MultiMap<String, TemplateData, List<TemplateData>> getTemplates();

	public Set<String> getCategories();

	public Set<String> getSections();

	public String getSectionsString();

	public String getCategoriesString();

	public String getTemplatesString();

	public ResourceType getResourceType();

	public ConceptType getConceptType();

	public Set<CharSequence> getTitleTerms();

	public Set<CharSequence> getPageTerms();

	public WikiLink getRedirect();

	public WikiLink getAliasFor();

	public CharSequence getTitleSuffix();

	public CharSequence getTitlePrefix();

	public CharSequence getTitleBaseName();

	public CharSequence getDisplayTitle();

	public CharSequence getDefaultSortKey();

	public WikiTextAnalyzer getAnalyzer();

}