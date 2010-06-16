package de.brightbyte.wikiword.builder;

import java.util.Map;
import java.util.Set;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.MultiMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.RevisionInfo;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.builder.ConceptBasedStoreBuilder;
import de.brightbyte.wikiword.store.builder.IncrementalStoreBuilder;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;

public class PropertyImporter extends ConceptImporter {
	
	boolean buildConcepts = true;
	
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
	public int importPage(WikiPage analyzerPage, RevisionInfo revision) throws PersistenceException {
		String name = analyzerPage.getConceptName();
		String rcName = analyzerPage.getResourceName();
		
		int rcId  = 0;
		int cid = 0;
		
		ResourceType rcType = analyzerPage.getResourceType();
		ConceptType ctype = analyzerPage.getConceptType();
		
		if (buildConcepts) {
			rcId = storeResource(revision.getPageId(), revision.getRevisionId(), rcName, rcType, revision.getRevisionTimestamp());	
			
			if (rcType == ResourceType.REDIRECT) {
				storeAlias(analyzerPage, rcId);
				cid = -1; //XXX: hack!
			} else {
				cid = storeConcept(rcId, name, ctype);
			}
			
			storeSuffixInfo(analyzerPage, rcId, cid, name);
		}  else if (rcType == ResourceType.ARTICLE || rcType == ResourceType.SUPPLEMENT) {
			conceptTracker.step();

			MultiMap<String, CharSequence, Set<CharSequence>> properties = analyzerPage.getProperties();
			for (Map.Entry<String, Set<CharSequence>> e: properties.entrySet()) {
				String property = e.getKey();
				
				for (CharSequence v: e.getValue()) {
					storeProperty(rcId, cid, name, property, v.toString());
				}
			}
			
			storeProperty(rcId, cid, name, "is-a", ctype.getName());
			
			if (buildConcepts) {
				storeSupplements(rcId, cid, analyzerPage);
			}
		}
		
		return cid;
	}
	
	protected void deleteDataAfter(int delAfter) throws PersistenceException {
		if (buildConcepts) super.deleteDataAfter(delAfter);
		else {
			((IncrementalStoreBuilder)propertyStore).prepareMassProcessing(); 
			((IncrementalStoreBuilder)propertyStore).deleteDataAfter(delAfter, false); 
			((IncrementalStoreBuilder)propertyStore).prepareMassInsert(); 
		}
	}
		
	@Override 
	protected boolean isRelevant(WikiPage analyzerPage) {
		ResourceType t = analyzerPage.getResourceType();
		
		if (t!=ResourceType.ARTICLE 
				&& t!=ResourceType.CATEGORY 
				&& t!=ResourceType.SUPPLEMENT) {
			return false;
		}
		
		if (t==ResourceType.SUPPLEMENT) {
			return true;
		}
		
		if (t==ResourceType.REDIRECT) {
			return buildConcepts;
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

		args.declare("attach", null, false, Boolean.class, "attach properties to existing thesaurus");
	}

	@Override
	public void configure(Arguments args) throws Exception  {
		super.configure(args);
		
		if (args.isSet("attach")) buildConcepts = false;
		else buildConcepts = true;
		
		setStoreProperties(true);
	}

	protected boolean getPurgeData() {
		return buildConcepts;
	}
	
	@Override
	public void finish() throws PersistenceException {
		ConceptBasedStoreBuilder store = buildConcepts ? this.store : this.propertyStore;
		
		store.prepareMassProcessing(); //NOTE: always make sure the DB is ready for mass processing
		
		if (beginTask("PropertyImporter.finish", "finishIdReferences#1")) {
			store.finishIdReferences();
			endTask("PropertyImporter.finish", "finishIdReferences#1");
		}
		
		if (beginTask("PropertyImporter.finish", "finishAliases")) {
			store.finishAliases();
			endTask("PropertyImporter.finish", "finishAliases");
		}
		
		if (beginTask("PropertyImporter.finish", "finishIdReferences#2")) { //NOTE: resolve IDs again, some may only be known know, after resolving aliases.
			store.finishIdReferences();
			endTask("PropertyImporter.finish", "finishIdReferences#2");
		}
	}	
}
