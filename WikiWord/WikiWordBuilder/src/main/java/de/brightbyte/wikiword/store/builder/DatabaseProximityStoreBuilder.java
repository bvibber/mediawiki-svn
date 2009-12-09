package de.brightbyte.wikiword.store.builder;

import java.sql.ResultSet;
import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Agenda.Record;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

public class DatabaseProximityStoreBuilder 
				extends DatabaseWikiWordStoreBuilder 
				implements ProximityStoreBuilder {
	
	   protected FeatureVectorFactors featureVectorFactors = new FeatureVectorFactors();
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected RelationTable proximityTable;
		protected RelationTable featureTable;
		protected EntityTable featureMagnitudeTable;
		
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		private double proximityThreshold;
		
		protected DatabaseProximityStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ProximityStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
			
			Inserter featureInserter = configureTable("feature", 8*1024, 32);
			featureTable = (RelationTable)featureInserter.getTable();
			
			Inserter featureMagnitudeInserter = configureTable("feature_magnitude", 8*1024, 32);
			featureMagnitudeTable = (EntityTable)featureMagnitudeInserter.getTable();

			Inserter proximityInserter = configureTable("proximity", 8*1024, 32);
			proximityTable = (RelationTable)proximityInserter.getTable();
			
			proximityThreshold = tweaks.getTweak("proximity.threshold", 0.15);
		}

		protected int buildFeatures(DatabaseTable t, String conceptField, String featureField, String suffix, double w, String biasField, double biasCoef) throws PersistenceException {
			if (!conceptStore.areStatsComplete()) throw new IllegalStateException("statistics need to be built before concept infos!");

			String v = ""+w;
			
			//NOTE: conider bias of reference target
			//XXX: also consider local (outgoing) bias? feature vectors will be normalized, so that's not so relevant
			//NOTE: since there are usually more link than categories, there's a bias in favor of categories!
			//       number of links grows with article length, number of categories does not!
			if (biasField!=null && biasCoef>0) {
				if (biasCoef==1) v = "D."+biasField+" * "+w;
				else if (biasCoef>1) throw new IllegalArgumentException("biasCoef must not be greater than 1");
				else v = "( 1 - ( ( 1 - D."+biasField+" ) * "+biasCoef+" ) ) * "+w;
			}
			
			DatabaseTable degreeTable = conceptStore.getStatisticsStoreBuilder().getDatabaseAccess().getTable("degree");
			
			String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, total_weight) ";
			sql += " SELECT T."+conceptField+", T."+featureField+", "+v+" FROM "+t.getSQLName()+" as T ";
			if (biasField!=null && biasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as D ON T."+featureField+" = D.concept ";
			
			if (suffix!=null) sql += " "+suffix+" ";
			
			String update = " ON DUPLICATE KEY UPDATE total_weight = total_weight + VALUES(total_weight)";
			
			int n = executeChunkedUpdate("buildFeatures", "feature#"+t.getName()+"."+featureField, sql, update, t, conceptField);
			return n;
		}
		
		public void buildFeatures() throws PersistenceException {
			DatabaseTable conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
			DatabaseTable broaderTable = conceptStore.getDatabaseAccess().getTable("broader");
			DatabaseTable linkTable = conceptStore.getDatabaseAccess().getTable("link");
			
			if (beginTask("buildFeatures", "feature#self")) {
					// add self-references
					String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, total_weight) ";
					sql += "SELECT id, id, "+featureVectorFactors.selfWeight+" FROM "+conceptTable.getSQLName();
					
					int n = executeChunkedUpdate("buildFeatures", "feature#self", sql, null, conceptTable, "id");
					endTask("buildFeatures", "feature#self", n+" entries");
			}
			
			if (beginTask("buildFeatures", "feature#down")) {
				int n = buildFeatures(broaderTable, "broad", "narrow", null, featureVectorFactors.downWeight, "up_bias", featureVectorFactors.downBiasCoef);
				endTask("buildFeatures", "feature#down", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#up")) {
				int n = buildFeatures(broaderTable, "narrow", "broad", null, featureVectorFactors.upWeight, "down_bias", featureVectorFactors.upBiasCoef);
				endTask("buildFeatures", "feature#up", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#out")) {
				int n = buildFeatures(linkTable, "anchor", "target", null, featureVectorFactors.outWeight, "in_bias", featureVectorFactors.outBiasCoef);
				endTask("buildFeatures", "feature#out", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#in")) {
				int n = buildFeatures(linkTable, "target", "anchor", null, featureVectorFactors.inWeight, "out_bias", featureVectorFactors.inBiasCoef);
				endTask("buildFeatures", "feature#in", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#offset")) {
					// apply offset
					String sql = "UPDATE "+featureTable.getSQLName()+" ";
					sql += " SET total_weight = total_weight + "+featureVectorFactors.weightOffset;
					
					int n = executeChunkedUpdate("buildFeatures", "feature#offset", sql, null, featureTable, "concept");
					endTask("buildFeatures", "feature#offset", n+" entries");
			}

			if (beginTask("buildFeatures", "featureMagnitude")) {
				// apply offset
				String sql = "INSERT INTO "+featureMagnitudeTable.getSQLName()+" (concept, magnitude) ";
				sql += " SELECT concept, SQRT( SUM( total_weight * total_weight ) ) FROM "+featureTable.getSQLName()+" ";
				String group = " GROUP BY concept";
				
				int n = executeChunkedUpdate("buildFeatures", "featureMagnitude", sql, group, featureTable, "concept");
				endTask("buildFeatures", "featureMagnitude", n+" entries");
			}
			
			if (beginTask("buildFeatures", "scaleWeight")) {
				String sql = "UPDATE "+featureTable.getSQLName()+" as P ";
				sql += " JOIN "+featureMagnitudeTable.getSQLName()+" as M ON M.concept = P.concept ";
				sql += " SET normal_weight = total_weight / magnitude ";
	
				int n = executeChunkedUpdate("buildFeatures", "scaleWeight", sql, null, featureTable, "P.concept");
				endTask("buildFeatures", "scaleWeight", n+" entries");
			}
		}

		protected int insertProximity(int concept) throws PersistenceException {
			String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, proximity)";
			sql += " SELECT A.concept, B.concept, sum( A.normal_weight * B.normal_weight ) as proximity ";
			sql += " FROM "+featureTable.getSQLName()+" as A ";
			sql += " JOIN "+featureTable.getSQLName()+" as B ON A.feature = B.feature ";
			sql += " WHERE A.concept = "+concept+" ";
			sql += " GROUP BY B.concept ";
			sql += " HAVING proximity > "+proximityThreshold;
			
			return executeUpdate("insertProximity("+concept+")", sql);
		}

		protected int deleteProximityValuesAfter(int id) throws PersistenceException {
			try {
				String sql = "DELETE FROM "+proximityTable.getSQLName()+" WHERE id > "+id;
				return database.executeUpdate("deleteProximityValuesAfter", sql);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public int getMaxProximityConceptId() throws PersistenceException {
			try {
				flush();
				
				String sql = "select max(concept) from "+proximityTable.getSQLName();
				Integer id = (Integer)database.executeSingleValueQuery("getMaxProximityConceptId", sql);
				return id == null ? 0 : id;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected class CollectProximityQuery implements DatabaseAccess.ChunkedQuery {
			protected String context;
			protected String name;
			protected DatabaseTable conceptTable;
			protected int lastId ;
			
			public CollectProximityQuery(String context, String name) {
				super();
				this.context = context;
				this.name = name;
				this.conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
			}

			public String getChunkField() {
				return "id";
			}

			public DatabaseTable getChunkTable() {
				return conceptTable;
			}

			public String getContext() {
				return context;
			}

			public String getName() {
				return name;
			}
			
			public void cleanupDirtyStep(Record rec) throws PersistenceException {
				if (rec.parameters.get("lastConceptId_")!=null) {
					lastId = (Integer)rec.parameters.get("lastConceptId_");
					log(context+"::"+name+"#CollectProximityQuery continues in dirty step, deleting pending concepts from id > "+lastId);
					deleteProximityValuesAfter(lastId);
				}  else {
					lastId = getMaxProximityConceptId();
					warning(0, context+"::"+name+"#CollectProximityQuery continues in dirty step, no lastConceptId_", "getMaxConceptId() = "+lastId, null);
				}
			}

			public String getQueryState() {
				return "lastConceptId_=I"+lastId;
			}
			
			public int executeUpdate(int chunk, long first, long end) throws PersistenceException {
				String sql = "SELECT id FROM " +conceptTable.getSQLName();
				sql += " WHERE id >= "+first+" AND id < "+end;
				sql += " ORDER BY id ASC";
				
				int n = 0;
				try {
					ResultSet res = DatabaseProximityStoreBuilder.this.executeQuery(context+"::"+name+"#chunk"+chunk, sql);
					while (res.next()) {
						lastId = res.getInt(1);
						
						n+= insertProximity(lastId); //TODO: progress tracker!
					}
					
					res.close();
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
				
				flush();
				return n;
			}

			public ResultSet executeQuery(int chunk, long first, long end) throws Exception {
				throw new UnsupportedOperationException();
			}

		}
		
		
		public void buildProximity() throws PersistenceException {
			if (beginTask("buildProximity", "collect")) {
				CollectProximityQuery query = new CollectProximityQuery("buildProximity", "collect");
				int n = executeChunkedUpdate(query, 1);
				endTask("buildProximity", "collect", n+" entries");
			}
			
			if (beginTask("buildProximity", "balance")) {
				String sql = "UPDATE "+proximityTable.getSQLName()+" as P ";
				sql += " JOIN "+proximityTable.getSQLName()+" as Q ON Q.concept1 = P.concept2  AND Q.concept2 = P.concept1 ";
				sql += " SET P.proximity = IF(P.proximity > Q.proximity, P.proximity, Q.proximity) ";
	
				int n = executeChunkedUpdate("buildProximity", "balance", sql, null, proximityTable, "P.concept1");
				endTask("buildProximity", "balance", n+" entries");
			}
		}	
		
}