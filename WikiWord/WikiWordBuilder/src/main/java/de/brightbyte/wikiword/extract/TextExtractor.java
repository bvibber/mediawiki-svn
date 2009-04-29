package de.brightbyte.wikiword.extract;

import java.util.Date;

import de.brightbyte.application.Arguments;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.builder.AbstractImporter;
import de.brightbyte.wikiword.output.TextOutput;

public class TextExtractor extends AbstractExtractor {
	
	private TextOutput textOutput;
	//private LocalConceptStore localConceptStore;

	//private boolean storeDefinitions = true;
	private boolean storeWikiText;
	private boolean storePlainText;
	
	private int textId = 0;
	
	public TextExtractor(WikiTextAnalyzer analyzer, TextOutput output, TweakSet tweaks) {
		super(analyzer, output, tweaks);
		
		this.output = output;
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
			int namespace = analyzerPage.getNamespace();
			//CharSequence title = analyzerPage.getTitle();
			ResourceType type = analyzerPage.getResourceType();
			
			if (namespace!=Namespace.MAIN) {
				out.trace("bad namespace "+namespace); 
				return false;
			}
			
			if (!storeWikiText && type!=ResourceType.ARTICLE) {
				out.trace("bad type "+type); 
				return false;
			}
			
			return super.isRelevant(analyzerPage);
	}	
	
	@Override
	public int importPage(WikiPage analyzerPage, Date timestamp) throws PersistenceException {
		String text = analyzerPage.getText().toString();

		ResourceType ptype = analyzerPage.getResourceType();
		String name = analyzerPage.getName().toString();
		
		//TODO: check if page is stored. if up to date, skip. if older, update. if missing, create. optionally force update.

		textId ++;
		
		if (storeWikiText) { //TODO: separate access path... 
			textOutput.storeRawText(textId, name, ptype, text);
		}
		
		if (storePlainText) { //TODO: separate access path... 
			String plain = analyzerPage.getPlainText(false).toString();
			checkSmellsLikeWiki(0, plain, "plain text: "+name+" (id={0})", textId);
			textOutput.storePlainText(textId, name, ptype, plain);
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
		
		args.declare("wiki", null, true, String.class, "store raw wiki text");
		args.declare("plain", null, true, String.class, "store stripped plain text");
		//args.declare("defs", null, true, String.class, "extract and store definitions");
	}

	@Override
	public void configure(Arguments args) throws Exception {
		super.configure(args);
		
		//this.storeDefinitions = !args.isSet("defs");
		this.storeWikiText = !args.isSet("wiki");
		this.storePlainText = !args.isSet("plain");
	}

}
