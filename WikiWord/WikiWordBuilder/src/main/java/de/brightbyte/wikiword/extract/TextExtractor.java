package de.brightbyte.wikiword.extract;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.application.Arguments;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.RevisionInfo;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.builder.AbstractImporter;
import de.brightbyte.wikiword.output.TextOutput;

public class TextExtractor extends AbstractExtractor<TextOutput> {
	
	//private LocalConceptStore localConceptStore;

	private boolean storeDefinitions;
	private boolean storeSynopsis;
	private boolean storeWikiText;
	private boolean storePlainText;
	
	private int textId = 0;
	private Set<Namespace> allowdNamespaces;
	
	public TextExtractor(WikiTextAnalyzer analyzer, TextOutput output, TweakSet tweaks) {
		super(analyzer, output, tweaks);
	}
	
	/*
	protected int getResourceId(String name) throws PersistenceException {
		if (localConceptStore==null) {
			if (!(store instanceof DatabaseTextStore)) return 0;
			
			try {
				localConceptStore = new DatabaseLocalConceptStore(
						((DatabaseTextStore)store).getCorpus(), 
						((DatabaseTextStore)store).getDatabaseAccess().getConnection(), 
						tweaks);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		return localConceptStore.getResourceId(name);
	}
	*/
	
	protected boolean isRelevant(WikiPage analyzerPage) {
			Namespace namespace =   analyzer.getCorpus().getNamespaces().getNamespace( analyzerPage.getNamespace() );
			
			if (allowdNamespaces != null && !allowdNamespaces.contains(namespace)) {
				out.trace("skipping page from namespace "+namespace); 
				return false;
			}
			
			//CharSequence title = analyzerPage.getTitle();
			ResourceType type = analyzerPage.getResourceType();
			
			if (!storeWikiText && type!=ResourceType.ARTICLE) {
				out.trace("skipping non-article page with type "+type); 
				return false;
			}
			
			return super.isRelevant(analyzerPage);
	}	
	
	@Override
	public int importPage(WikiPage analyzerPage, RevisionInfo revision) throws PersistenceException {

		ResourceType ptype = analyzerPage.getResourceType();
		String name = analyzerPage.getName().toString();
		
		//TODO: check if page is stored. if up to date, skip. if older, update. if missing, create. optionally force update.

		textId ++;
				
		if (storeWikiText) { //TODO: separate access path... 
			String text = analyzerPage.getText().toString().trim();
			output.storeRawText(textId, name, ptype, text);
		}
		
		//CharSequence title = analyzerPage.getTitle();
		ResourceType type = analyzerPage.getResourceType();

		if (storePlainText && type==ResourceType.ARTICLE) { //TODO: separate access path... 
			String plain = analyzerPage.getPlainText(false).toString().trim();
			
			if (plain!=null && plain.length()>0) {
					checkSmellsLikeWiki(0, plain, "plain text: "+name+" (id={0})", textId);
					output.storePlainText(textId, name, ptype, plain);
			}
		}
		
		if (storeSynopsis && type==ResourceType.ARTICLE) { //TODO: separate access path... 
			String syn = analyzerPage.getFirstParagraph().toString().trim();
			
			if (syn!=null && syn.length()>0) {
					checkSmellsLikeWiki(0, syn, "definition text: "+name+" (id={0})", textId);
					output.storeSynopsisText(textId, name, ptype, syn);
			}
		}
		
		if (storeDefinitions && type==ResourceType.ARTICLE) { //TODO: separate access path... 
			String def = analyzerPage.getFirstSentence().toString().trim();
			
			if (def!=null && def.length()>0) {
					checkSmellsLikeWiki(0, def, "definition text: "+name+" (id={0})", textId);
					output.storeDefinitionText(textId, name, ptype, def);
			}
		}
		
		/*
		if (ptype == ResourceType.ARTICLE && storeDefinitions) {
			String definition = analyzerPage.getFirstSentence();
			if (definition!=null && definition.length()>0) {
				int conceptId = getConceptId(name);
				store.storeDefinition(rcId, name, conceptId, ptype, definition);
			}
		}	
		*/	
		
		return textId;
	}

	public static void declareOptions(Arguments args) {
		AbstractImporter.declareOptions(args);
		
		args.declare("namespaces", null, true, String.class, "list of namespaces to process. if omitted, all are processed.");
		args.declare("extract", null, true, String.class, "What to extract. One or more of raw, plain, or definition. Default is raw,plain");
	}

	@Override
	public void configure(Arguments args) throws Exception {
		super.configure(args);
		
		setNamespaceFilter( getNamespaces(args) );
		
		String ext = args.getOption("extract", "raw,plain").toLowerCase();
		String[]ee = ext.split("[,;/|:+]");
		
		for (String e: ee) {
			e = e.toLowerCase();
			if (e.equals("def") || e.equals("definition")) storeDefinitions = true;
			else if (e.equals("synopsis") || e.equals("intro")) storeSynopsis = true;
			else if (e.equals("raw") || e.equals("wiki") || e.equals("wikitext")) storeWikiText= true;
			else if (e.equals("plain") || e.equals("flat")) storePlainText = true;
			else throw new IllegalArgumentException("unknown extraction aspect: "+e);
		}
		
	}


	protected Set<Namespace> getNamespaces(Arguments args) {
		if (!args.isSet("namespaces")) return null;
		
		String s = args.getOption("namespaces", "");
		String[] nn = s.split("[\\s,;:/|+]+");
		if (nn.length==0) return null;
		
		NamespaceSet namespaces = analyzer.getCorpus().getNamespaces();
		Set<Namespace> result = new HashSet<Namespace>(); 
		
		for (String n: nn) {
			Namespace ns;
			if (n.equals("") || n.equals("*") || n.equalsIgnoreCase("main")) ns = namespaces.getNamespace(Namespace.MAIN); 
			else ns = namespaces.getNamespace(n);
			
			result.add(ns);
		}
		
		return result;
	}
	
	public void setNamespaceFilter(Set<Namespace> namespaces) {
		this.allowdNamespaces = namespaces;
	}

}
