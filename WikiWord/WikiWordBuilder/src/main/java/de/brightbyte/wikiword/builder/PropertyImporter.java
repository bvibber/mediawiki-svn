package de.brightbyte.wikiword.builder;

import java.util.Date;
import java.util.Map;
import java.util.Set;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.MultiMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiPage;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.PropertyStoreBuilder;

public class PropertyImporter extends ConceptImporter {
	
	public PropertyImporter(WikiTextAnalyzer analyzer, LocalConceptStoreBuilder store, TweakSet tweaks) throws PersistenceException {
		super(analyzer, store, tweaks);
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
	
	@Override
	public int importPage(int namespace, String title, String text, Date timestamp) throws PersistenceException {
		WikiTextAnalyzer.WikiPage analyzerPage = analyzer.makePage(namespace, title, text, forceTitleCase);
		
		if (!isRelevant(analyzerPage)) {
			out.trace("ignored page "+title+" in namespace "+namespace); //XXX: trace only!
			return -1;
		}
		
		String name = analyzerPage.getConceptName();
		String rcName = analyzerPage.getResourceName();
		
		int rcId = storeResource(rcName, analyzerPage.getResourceType(), timestamp);
		
		ConceptType ctype = analyzerPage.getConceptType();
		int cid = storeConcept(rcId, name, ctype);
		
		//storeProperty(rcId, cid, name, "__TYPE__", analyzerPage.getConceptType().getName()); //FIXME: remove me!
		
		MultiMap<String, CharSequence, Set<CharSequence>> properties = analyzerPage.getProperties();
		for (Map.Entry<String, Set<CharSequence>> e: properties.entrySet()) {
			String property = e.getKey();
			
			for (CharSequence v: e.getValue()) {
				storeProperty(rcId, cid, name, property, v.toString());
			}
		}
		
		storeSupplements(rcId, cid, analyzerPage);
		
		return cid;
	}
	
	private boolean isRelevant(WikiPage analyzerPage) {
		ResourceType t = analyzerPage.getResourceType();
		
		if (t!=ResourceType.ARTICLE 
				&& t!=ResourceType.CATEGORY 
				&& t!=ResourceType.SUPPLEMENT) return false;
		
		if (t==ResourceType.SUPPLEMENT) {
			return true;
		}
		
		if ( analyzerPage.getProperties().isEmpty()
				&& analyzerPage.getSupplementedConcept()==null
				&& analyzerPage.getSupplementLinks().isEmpty()  ) {
			return false;
		}
		
		//TODO: some concept types are always relevent. how to configure?!
		
		return true;
	}

	public static void declareOptions(Arguments args) {
		AbstractImporter.declareOptions(args);
	}

	@Override
	public void configure(Arguments args) {
		super.configure(args);
	}

}
