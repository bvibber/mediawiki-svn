package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema.ReferenceListEntrySpec;

public abstract class DatabaseConceptInfoStoreBuilder<T extends WikiWordConcept> 
				extends DatabaseWikiWordStoreBuilder 
				implements ConceptInfoStoreBuilder<T> {
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected EntityTable conceptInfoTable;
		protected Inserter conceptFeaturesInserter;
		
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		
		protected DatabaseConceptInfoStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ConceptInfoStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
			
			Inserter conceptInfoInserter = configureTable("concept_info", 64, 1024);
			conceptInfoTable = (EntityTable)conceptInfoInserter.getTable();
		}	
		
		public void buildConceptRelationCache() throws PersistenceException {
			if (!conceptStore.areStatsComplete()) throw new IllegalStateException("statistics need to be built before concept infos!");
			
			if (beginTask("buildConceptRelationCache", "prepareConceptCache:concept_info")) {
				int n = prepareConceptCache(conceptInfoTable, "concept");
				endTask("buildConceptRelationCache", "prepareConceptCache:concept_info", n+" entries");
			}
			
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,langlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "langlinks", "langlink", "concept", ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry, false, null, 1);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,langlinks", n+" entries");
			}
			
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,inlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "inlinks", "link", "target", ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry, false, null, 5);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,inlinks", n+" entries");
			}
			
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,outlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "outlinks", "link", "anchor", ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry, false, null, 5);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,outlinks", n+" entries");
			}
			
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,narrower")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "narrower", "broader", "broad", ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry, false, null, 2);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,narrower", n+" entries");
			}
			
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,broader")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "broader", "broader", "narrow", ((ConceptInfoStoreSchema)database).broaderReferenceListEntry, false, null, 2);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,broader", n+" entries");
			}
		
			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,similar")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "similar", "relation", "concept1", ((ConceptInfoStoreSchema)database).similarReferenceListEntry, false, "langmatch > 0", 1);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,similar", n+" entries");
			}
		
			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,related#1")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "related", "relation", "concept1", ((ConceptInfoStoreSchema)database).relatedReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,related#1", n+" entries");
			}
		
			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,related#2")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "related", "relation", "concept2", ((ConceptInfoStoreSchema)database).related2ReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptRelationCache", "buildConceptPropertyCache:concept_info,related#2", n+" entries");
			}
			
		}
		
		public void buildConceptFeatureCache() throws PersistenceException {
			if (beginTask("buildConceptProximityCache", "buildConceptPropertyCache:concept_info,feature")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "feature", "feature", "concept", ((ConceptInfoStoreSchema)database).featureReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptProximityCache", "buildConceptPropertyCache:concept_info,feature", n+" entries");
			}
		}
		
		public void buildConceptProximetyCache() throws PersistenceException {
			if (beginTask("buildConceptProximityCache", "buildConceptPropertyCache:concept_info,proximity")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "proximity", "proximity", "concept", ((ConceptInfoStoreSchema)database).proximityReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptProximityCache", "buildConceptPropertyCache:concept_info,proximity", n+" entries");
			}
		}
		
		protected int prepareConceptCache(DatabaseTable cacheTable, String conceptIdField) throws PersistenceException {
			DatabaseTable t = conceptStore.getDatabaseAccess().getTable("concept");
			
			String sql = "INSERT ignore INTO "+cacheTable.getSQLName()+" ( " + conceptIdField + " ) "
				+" SELECT id "
				+" FROM "+t.getSQLName();
		
			return executeChunkedUpdate("prepareConceptCache", cacheTable.getName()+"."+conceptIdField, sql, null, t, "id");
		}
		
		protected int buildConceptPropertyCache(
				final DatabaseTable cacheTable, final String cacheIdField, 
				final String propertyField, final String realtion, final String relConceptField, 
				final ReferenceListEntrySpec spec, final boolean append, final String threshold,
				final int chunkFactor) throws PersistenceException {
			
			final DatabaseTable relationTable = conceptStore.getDatabaseAccess().getTable(realtion);
			
			//XXX: if no frequency-field, evtl use inner grouping by nameField to determin frequency! (expensive, though)
		
			//XXX: for outlinks, langlinks, etc, we could exclude UNKN OWN concepts...
			
			final String v = !append ? "s" : "if ("+propertyField+" IS NOT NULL, concat("+propertyField+", '"+((ConceptInfoStoreSchema)database).referenceSeparator+"', s), s)";
			
			final String joinDistrib = !spec.useRelevance ? "" : " JOIN "+database.getSQLTableName("degree", true)+" as DT ON DT.concept = "+spec.joinField;
			final String andThreashold = threshold==null ? "" : " AND ("+threshold+")";
		
			DatabaseSchema.ChunkedQuery query = new DatabaseSchema.AbstractChunkedQuery(database, "buildConceptPropertyCache", cacheTable.getName()+"."+propertyField, cacheTable, "C."+cacheIdField) {
				
				@Override
				protected String getSQL(long first, long end) {
					String sql = "UPDATE "+cacheTable.getSQLName()+" AS C "
								+" JOIN ( SELECT "+relConceptField+", group_concat("+spec.valueExpression+" separator '"+((ConceptInfoStoreSchema)database).referenceSeparator+"' ) as s" 
								+"        FROM "+relationTable.getSQLName()
										+joinDistrib
										+" WHERE ( "+relConceptField+" >= "+first+" AND "+relConceptField+" < "+end+" )"
										+andThreashold
										+" group by "+relConceptField+" )" 
								+" AS R "
								+" ON R."+relConceptField+" = C."+cacheIdField
								+" SET "+propertyField+" = " + v
								+" WHERE ( C."+cacheIdField+" >= "+first+" AND C."+cacheIdField+" < "+end+" )";
					
					return sql;
				}
			
			};
		
			return executeChunkedUpdate(query, chunkFactor);
		}
}