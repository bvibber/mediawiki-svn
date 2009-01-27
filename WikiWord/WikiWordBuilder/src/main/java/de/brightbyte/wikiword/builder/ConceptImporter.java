package de.brightbyte.wikiword.builder;

import java.util.Date;
import java.util.List;
import java.util.Set;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.model.LocalConceptReference;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;

public class ConceptImporter extends AbstractImporter {
	protected static final boolean useSuffixAsCategory = false; //NOTE: leads to inconsistencies if used...
	
	private boolean storeDefinitions = true;
	
	private Tracker conceptTracker;
	private Tracker linkTracker;
	
	private LocalConceptStoreBuilder store;

	
	public ConceptImporter(WikiTextAnalyzer analyzer, LocalConceptStoreBuilder store, TweakSet tweaks) {
		super(analyzer, store, tweaks);
		
		this.store = store;
	}
	
	@Override
	public void prepare() throws PersistenceException {
		store.prepare();
	}
	
	@Override
	public void finish() throws PersistenceException {
		if (beginTask("ConceptImporter.finish", "finishImport")) {
			store.finishImport();
			endTask("ConceptImporter.finish", "finishImport");
		}
		
		if (beginTask("ConceptImporter.finish", "finishBadLinks")) {
			store.finishBadLinks();
			endTask("ConceptImporter.finish", "finishBadLinks");
		}
		
		if (beginTask("ConceptImporter.finish", "finishSections")) {
			store.finishSections(); //XXX: does this after id-links? copy ids directly? building narrow from narrow_name would no longer be needed.
			endTask("ConceptImporter.finish", "finishSections");
		}
				
		if (beginTask("ConceptImporter.finish", "finishMissingConcpets")) {
			store.finishMissingConcepts();
			endTask("ConceptImporter.finish", "finishMissingConcpets");
		}
		
		//XXX: maybe run finishMissingConcpets AGAIN: buildTermsForMissingConcepts
		//     may created unresolved pointers in broader.boroader field.
		
		if (beginTask("ConceptImporter.finish", "finishIdReferences")) {
			store.finishIdReferences();
			endTask("ConceptImporter.finish", "finishIdReferences");
		}
		
		if (beginTask("ConceptImporter.finish", "finishAliases")) {
			store.finishAliases();
			endTask("ConceptImporter.finish", "finishAliases");
		}
		
		if (beginTask("ConceptImporter.finish", "finishRelations")) {
			store.finishRelations();
			endTask("ConceptImporter.finish", "finishRelations");
		}
				
		if (beginTask("ConceptImporter.finish", "buildTermsForMissingConcepts")) {
			if (getAgenda().isTaskDirty()) resetTermsForMissingConcepts();
			buildTermsForMissingConcepts();
			endTask("ConceptImporter.finish", "buildTermsForMissingConcepts");
		}
		
		if (beginTask("ConceptImporter.finish", "finishMeanings")) {
			store.finishMeanings();
			endTask("ConceptImporter.finish", "finishMeanings");
		}
		
		/*
		if (beginTask("ConceptImporter.finish", "finishConceptInfo")) {
			store.finishConceptInfo();
			endTask("ConceptImporter.finish", "finishConceptInfo");
		}
		*/
		
		if (beginTask("ConceptImporter.finish", "finishFinish")) {
			store.finishFinish();
			endTask("ConceptImporter.finish", "finishFinish");
		}
		
		store.flush();
	}
	
	protected void buildTermsForMissingConcepts() throws PersistenceException {
		if (!analyzer.isInitialized()) { //XXX: ugly hack!
			analyzer.initialize(Namespace.canonicalNamespaces, true);
		}
		
		CursorProcessor<LocalConceptReference> p = new CursorProcessor<LocalConceptReference>() {
		
			public void process(DataCursor<LocalConceptReference> c) throws Exception {
				
				LocalConceptReference r;
				while ((r = c.next())!=null) {
					WikiTextAnalyzer.WikiPage analyzerPage = analyzer.makePage(0, r.getName(), "", false); //XXX: bypass analyzer page? 
					storePageTerms(-1, r.getId(), analyzerPage); //FIXME: skip sections, foo#bar is not a good term!
		
					if (useSuffixAsCategory) {
						CharSequence sfx = analyzerPage.getTitleSuffix();
						if (sfx!=null) storeConceptBroader(-1, r.getId(), r.getName(), analyzer.normalizeTitle(sfx).toString(), ExtractionRule.BROADER_FROM_SUFFIX);
					}
					
					//NOTE: covered by DatabaseLocalConceptStoreBuilder.buildSectionBroader
					//CharSequence pfx = analyzerPage.getTitlePrefix();
					//if (pfx!=null) storeConceptBroader(-1, r.getId(), r.getName(), analyzer.normalizeTitle(pfx).toString(), ExtractionRule.BROADER_FROM_SUFFIX); 			
				}
				
				store.flush();
			}
		
		};
		
		store.processUnknownConcepts(p);
	}

	protected void resetTermsForMissingConcepts() throws PersistenceException {
		store.resetTermsForUnknownConcepts();
	}

	@Override
	public void reset() {
		super.reset();
		conceptTracker = new Tracker("concepts");
		linkTracker = new Tracker("links");
	}
	
	@Override
	public void trackerChunk() {
		super.trackerChunk();
		conceptTracker.chunk();
		linkTracker.chunk();
		
		out.info("- "+conceptTracker);
		out.info("- "+linkTracker);
	}
	
	protected void storeReferences(int rcId, List<WikiTextAnalyzer.WikiLink> links) throws PersistenceException {
		for (WikiTextAnalyzer.WikiLink link : links) {
			WikiTextAnalyzer.LinkMagic m = link.getMagic();
			
			if (m==WikiTextAnalyzer.LinkMagic.NONE) {
				if (link.getNamespace()!=Namespace.MAIN) continue;
				if (link.getInterwiki()!=null) continue;
			
				storeReference(rcId, link.getText().toString(), -1, link.getTarget().toString(), ExtractionRule.TERM_FROM_LINK);
				if (link.getSection()!=null) storeSection(rcId, link.getTarget().toString(), link.getPage().toString());
			}
		}
	}
	
	protected void storeLinks(int rcId, int conceptId, String conceptName, List<WikiTextAnalyzer.WikiLink> links) throws PersistenceException {
		for (WikiTextAnalyzer.WikiLink link : links) {
			WikiTextAnalyzer.LinkMagic m = link.getMagic();
			
			if (m==WikiTextAnalyzer.LinkMagic.NONE) {
				if (link.getNamespace()!=Namespace.MAIN) continue;
				if (link.getInterwiki()!=null) continue;
			
				storeLink(rcId, conceptId, conceptName, link.getText().toString(), link.getTarget().toString(), ExtractionRule.TERM_FROM_LINK);
				if (link.getSection()!=null) storeSection(rcId, link.getTarget().toString(), link.getPage().toString());
			}
		}
	}
	
	protected void storePageTerms(int rcId, int conceptId, WikiTextAnalyzer.WikiPage analyzerPage) throws PersistenceException {
		storePageTerms(rcId, analyzerPage.getTitleTerms(), conceptId, analyzerPage.getName().toString(), ExtractionRule.TERM_FROM_TITLE);
		storePageTerms(rcId, analyzerPage.getPageTerms(), conceptId, analyzerPage.getName().toString(), ExtractionRule.TERM_FROM_IDENTIFIER);
		
		CharSequence defaultSortKey = analyzerPage.getDefaultSortKey();
		if (defaultSortKey!=null && defaultSortKey.length()>0) {
			storeReference(rcId, defaultSortKey.toString(), conceptId, analyzerPage.getName().toString(), ExtractionRule.TERM_FROM_SORTKEY);
		}
	}
	
	protected void storePageTerms(int rcId, Set<? extends CharSequence> terms, int targetId, String targetName, ExtractionRule rule) throws PersistenceException {
		for (CharSequence term : terms) {
			storeReference(rcId, term.toString(), targetId, targetName, rule);
		}
	}
	
	@Override
	public int importPage(int namespace, String title, String text, Date timestamp) throws PersistenceException {
		if (text.length()==0) {
			out.warn("WARNING: ignored blank page "+title); 
			return -1;
		}
		
		WikiTextAnalyzer.WikiPage analyzerPage = analyzer.makePage(namespace, title, text, forceTitleCase); 
		ResourceType ptype = analyzerPage.getResourceType();
		String name = analyzerPage.getConceptName();
		String rcName = analyzerPage.getResourceName();
		
		if (ptype==ResourceType.OTHER || ptype==ResourceType.UNKNOWN) {
			out.trace("ignored page "+title+" in namespace "+namespace+" with type "+ptype); 
			return -1;
		}

		//TODO: check if page is stored. if up to date, skip. if older, update. if missing, create. optionally force update.
		int rcId = storeResource(rcName, ptype, timestamp);
				
		/*
		if (storeWikiText) { //TODO: separate access path... 
			storeRawText(rcId, text);
		}
		
		if (storePlainText) { //TODO: separate access path... 
			String plain = analyzerPage.getPlainText(false);
			storePlainText(rcId, plain);
		}
		*/
		
		if (ptype == ResourceType.CATEGORY) {
			List<WikiTextAnalyzer.WikiLink> links = analyzerPage.getLinks();
			linkTracker.step(links.size());
			
			for (WikiTextAnalyzer.WikiLink link : links) {
				WikiTextAnalyzer.LinkMagic m = link.getMagic();
				
				if (m==WikiTextAnalyzer.LinkMagic.CATEGORY) {
					//FIXME: store this also as a reference to the categorie's concept under it's original title!
					storeConceptBroader(rcId, name, link.getPage().toString(), ExtractionRule.BROADER_FROM_CAT);
				}
			}
			
			
			//TODO: langlinks from category!
			//      need resolve-ids on langling, then!
			//      beware aliased categories!
		}
		else if (ptype == ResourceType.ARTICLE || ptype == ResourceType.SUPPLEMENT) {
			conceptTracker.step();
			
			//TODO: handle "other meanings" header (mini-disambig!)
			//TODO: handle "merge" header? 
			
			ConceptType ctype = analyzerPage.getConceptType();
			int conceptId = storeConcept(rcId, name, ctype);
			
			storePageTerms(rcId, conceptId, analyzerPage);
			
			//XXX: store interwiki-set inline for clustering ?
			
			if (useSuffixAsCategory) {
				CharSequence sfx = analyzerPage.getTitleSuffix();
				if (sfx!=null) storeConceptBroader(rcId, conceptId, name, analyzer.normalizeTitle(sfx).toString(), ExtractionRule.BROADER_FROM_SUFFIX);
			}
			
			//NOTE: can't really happen here (only applicable for sections)
			//CharSequence pfx = analyzerPage.getTitlePrefix();
			//if (pfx!=null) storeConceptBroader(rcId, conceptId, name, analyzer.normalizeTitle(pfx).toString(), ExtractionRule.BROADER_FROM_SUFFIX); 			
			
			//TODO: get all bold stuff from first sentence -> terms for page!
			
			if (storeDefinitions) {
				String definition = analyzerPage.getFirstSentence().toString();
				if (definition!=null && definition.length()>0) {
					storeDefinition(rcId, conceptId, definition);
				}
			}
			
			List<WikiTextAnalyzer.WikiLink> links = analyzerPage.getLinks();
			linkTracker.step(links.size());
			
			storeLinks(rcId, conceptId, name, links);
			
			for (WikiTextAnalyzer.WikiLink link : links) {
				WikiTextAnalyzer.LinkMagic m = link.getMagic();
				
				if (m==WikiTextAnalyzer.LinkMagic.NONE) {
					if (link.getNamespace()!=Namespace.MAIN) continue;
					if (link.getInterwiki()!=null) continue;
				
					//if (storeLinks) storeConceptReference(rcId, conceptId, name, link.getTarget());
				}
				else if (m==WikiTextAnalyzer.LinkMagic.CATEGORY) {
					//XXX: convert plural->singlular. XXX: do this later, and only if the plural lemma doesn't have an article!
					//      so... kep list of candidates, strip all that already have a matching concept *with* associated resource
					CharSequence sk = link.isTextImplied() ? null : link.getText();
					String sortKey = sk==null ? null : sk.toString();
					boolean categorize = true;
					
					if ( sortKey!=null && analyzer.isMainArticleMarker(sortKey) ) {
						if (analyzer.useCategoryAliases()) {
							//XXX: if there's more than one "main article", this breaks.
							String cat = link.getPage().toString();
							
							if (!cat.equals(name) &&  analyzer.mayBeFormOf(link.getLenientPage(), analyzerPage.getTitleBaseName())) {
								storePageTerms(rcId, analyzer.determineTitleTerms(link.getPage()), conceptId, name, ExtractionRule.TERM_FROM_CAT_NAME);
								
								//NOTE: the alias is preliminary: if a article with the name of the category 
								//      exists, the alias will be ignored. See DatabaseLocalConceptBuilder.finishBadLinks
								
								storeConceptAlias(rcId, -1, cat, conceptId, name, AliasScope.CATEGORY);  
								categorize = false;
							}
						}
						
						sortKey = null;
					}
					
					if (categorize) {
						if ( sortKey!=null && sortKey.length()>0 && !link.isTextImplied() ) {
							//XXX: if {{DEFAULTSORT}} is handled for PageTerms, apply for each category again? 
							storeReference(rcId, sortKey, conceptId, name, ExtractionRule.TERM_FROM_SORTKEY); //sort key is a name for this page
						}

						if ( !link.getPage().toString().equals(name) ) { //NOTE: need the toString, CharSequences doen't "equal" strings :(
							storeConceptBroader(rcId, conceptId, name, link.getPage().toString(), ExtractionRule.BROADER_FROM_CAT);
						}
					}
				}
				else if (m==WikiTextAnalyzer.LinkMagic.LANGUAGE) {
					storeLanguageLink(rcId, conceptId, name, link.getInterwiki().toString(), link.getPage().toString()); //XXX: consider target? consider both??
				}				
			}
			
			//FIXME: store supplement links
		}
		else if (ptype == ResourceType.DISAMBIG) {
			//storeConcept(rcId, name, ConceptType.NONE); 

			Set<CharSequence> terms = analyzerPage.getTitleTerms();

			List<WikiTextAnalyzer.WikiLink> links = analyzerPage.getLinks();
			linkTracker.step(links.size());

			storeReferences(rcId, links); //just word/target rel

			List<WikiTextAnalyzer.WikiLink> dlinks = analyzerPage.getDisambigLinks();
			for (WikiTextAnalyzer.WikiLink link : dlinks) {
				WikiTextAnalyzer.LinkMagic m = link.getMagic();
				
				if (m==WikiTextAnalyzer.LinkMagic.NONE) {
					if (link.getNamespace()!=Namespace.MAIN) continue;
					if (link.getInterwiki()!=null) continue;
				
					for (CharSequence term : terms) {
						storeReference(rcId, term.toString(), -1, link.getPage().toString(), ExtractionRule.TERM_FROM_DISAMBIG);
					}
				}
			}
		}
		else if (ptype == ResourceType.LIST) {
			//storeConcept(rcId, name, ConceptType.NONE);

			//FIXME: extract fewer links... use disambig-logic?
			List<WikiTextAnalyzer.WikiLink> links = analyzerPage.getLinks();
			linkTracker.step(links.size());

			storeReferences(rcId, links); //just word/target rel
			
			//TODO: extract concept name from "List of..." ?
			//FIXME: category-like interpretation!
		}
		else if (ptype == ResourceType.REDIRECT) {
			WikiTextAnalyzer.WikiLink link = analyzerPage.getRedirect();
			
			if (link==null) {
				warn(rcId, "bad redirect (no link)", "Text: "+StringUtils.clipString(text, 256, "..."), null);
			}
			else if (link.getInterwiki()!=null || link.getNamespace()!=0) {
				//redirects to other wikis or into another namespace are handeled as BAD page.
				out.info("skipped bad redirect "+rcName+" -> "+link);
			}
			else if (name.equals(link.getPage().toString())) {
				warn(rcId, "bad redirect (self-link)", "page "+name, null);
			}
			else {
				int conceptId = storeConcept(rcId, name, ConceptType.ALIAS); 
				storePageTerms(rcId, analyzerPage.getTitleTerms(), -1, link.getPage().toString(), ExtractionRule.TERM_FROM_REDIRECT );
				storeConceptAlias(rcId, conceptId, name, -1, link.getPage().toString(), AliasScope.REDIRECT); //TODO: confidence?...
				
				//FIXME: redir to section!
			}
		}
		else if (ptype == ResourceType.BAD) {
			out.info("skipped BAD page "+rcName);
		}
		else {
			out.warn("skipped page "+rcName+" ["+ptype+"]");
		}
		
		return rcId;
	}
	
	public static void declareOptions(Arguments args) {
		AbstractImporter.declareOptions(args);
		
		args.declare("nodef", null, true, String.class, "do not extract and store definitions (improves speed)");
	}

	@Override
	public void configure(Arguments args) {
		super.configure(args);
		
		this.storeDefinitions = !args.isSet("nodef");
	}

	//-----------------------------------------------------------------------------
	
	protected int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		//NOTE: trust concept name, no need to sniff it 
		return store.storeConcept(rcId, name, ctype);
	}

	protected void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		if (checkName(rcId, sourceName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, targetName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		store.storeConceptAlias(rcId, source, sourceName, target, targetName, scope);
	}

	protected void storeConceptBroader(int rcId, int narrowId, String narrowName, String broadName, ExtractionRule rule) throws PersistenceException {
		if (checkName(rcId, narrowName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, broadName,  "concept name (resource #{0}) - SKIPED", rcId)) return;
		store.storeConceptBroader(rcId, narrowId, narrowName, broadName, rule);
	}

	protected void storeConceptBroader(int rcId, String narrowName, String broadName, ExtractionRule rule) throws PersistenceException {
		if (checkName(rcId, narrowName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, broadName,  "concept name (resource #{0}) - SKIPED", rcId)) return;
		store.storeConceptBroader(rcId, narrowName, broadName, rule);
	}

	protected void storeDefinition(int rcId, int conceptId, String definition) throws PersistenceException {
		checkSmellsLikeWiki(rcId, definition, "definition of concept #{0} (storing anyway)", conceptId);
		store.storeDefinition(rcId, conceptId, definition);
	}

	protected void storeLanguageLink(int rcId, int concept, String conceptName, String lang, String target) throws PersistenceException {
		if (checkName(rcId, conceptName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, target, "external concept name (resource #{0}) - SKIPED", rcId)) return;
		store.storeLanguageLink(rcId, concept, conceptName, lang, target);
	}

	protected void storeLink(int rcId, int anchorId, String anchorName, String term, String targetName, ExtractionRule rule) throws PersistenceException {
		if (checkTerm(rcId, term, "SKIPED", -1)) return; 
		if (checkSmellsLikeWiki(rcId, term, "term - SKIPED", -1)) return;
		if (checkName(rcId, anchorName, "concept name (anchor ~ resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, targetName, "concept name (target in resource #{0}) - SKIPED", rcId)) return;
		store.storeLink(rcId, anchorId, anchorName, term, targetName, rule);
	}

	protected void storeReference(int rcId, String term, int targetId, String targetName, ExtractionRule rule) throws PersistenceException {
		if (checkTerm(rcId, term, "SKIPED", -1)) return; 
		if (checkSmellsLikeWiki(rcId, term, "term - SKIPED", -1)) return;
		if (checkName(rcId, targetName, "concept name (target in resource #{0}) - SKIPED", rcId)) return;
		store.storeReference(rcId, term, targetId, targetName, rule);
	}

	protected int storeResource(String name, ResourceType ptype, Date time) throws PersistenceException {
		//NOTE: trust name. no need to check
		return store.storeResource(name, ptype, time);
	}

	protected void storeSection(int rcId, String name, String page) throws PersistenceException {
		if (checkName(rcId, name, "section name (from section link in resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, page, "concept name (from section link in resource #{0}) - SKIPED", rcId)) return;
		store.storeSection(rcId, name, page);
	}

	
}
