package de.brightbyte.wikiword.builder;

import java.util.Date;
import java.util.Map;
import java.util.Set;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.MultiMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiPage;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.store.builder.PropertyStoreBuilder;

public class PropertyImporter extends AbstractImporter {
	
	private PropertyStoreBuilder store;
	
	private int conceptId = 0;
	
	public PropertyImporter(WikiTextAnalyzer analyzer, PropertyStoreBuilder store, TweakSet tweaks) {
		super(analyzer, store, tweaks);
		
		this.store = store;
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
		int cid = storeConcept(rcId, analyzerPage);
		
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
	
	//TODO: duplicate from ConceptImporter! unify!
	protected void storeSupplements(int rcId, int cid, WikiPage analyzerPage) throws PersistenceException {
		CharSequence supplemented = analyzerPage.getSupplementedConcept();
		
		String name = analyzerPage.getConceptName();
		
		if (supplemented!=null) {
			storeConceptAlias(rcId, cid, name, -1, supplemented.toString(), AliasScope.SUPPLEMENT);
		}
		
		Set<CharSequence> supplementLinks = analyzerPage.getSupplementLinks();
		for (CharSequence supp: supplementLinks) {
			storeConceptAlias(rcId, -1, supp.toString(), cid, name, AliasScope.SUPPLEMENT);
		}
		
	}

	protected void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		if (checkName(rcId, sourceName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		if (checkName(rcId, targetName, "concept name (resource #{0}) - SKIPED", rcId)) return;
		store.storeConceptAlias(rcId, source, sourceName, target, targetName, scope);
	}
	
	protected void storeProperty(int rcId, int cid, String concept, String property, String value) throws PersistenceException {
		if (checkTerm(rcId, value, "value - SKIPED", cid)) return; 
		if (checkSmellsLikeWiki(rcId, value, "value - SKIPED", cid)) return;
		
		store.storeProperty(rcId, cid, concept, property, value);
	}

	protected int storeResource(String name, ResourceType ptype, Date time) throws PersistenceException {
		//NOTE: trust name. no need to check
		return store.storeResource(name, ptype, time);
	}

	private int storeConcept(int rcId, WikiPage analyzerPage) throws PersistenceException {
		//FIXME: qualified name for supplements! no concept for supplements?
		conceptId = store.storeConcept(rcId, analyzerPage.getConceptName().toString(), analyzerPage.getConceptType());
		return conceptId;
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
		
		//args.declare("rdf", null, true, String.class, "output rdf dump");
	}

	@Override
	public void configure(Arguments args) {
		super.configure(args);
		
		//if (args.isSet("rdf")) this.format = "rdf";
	}

}
