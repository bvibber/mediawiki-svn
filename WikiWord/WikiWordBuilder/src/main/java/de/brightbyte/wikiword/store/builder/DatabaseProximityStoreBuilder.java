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
import de.brightbyte.wikiword.processor.ImportProgressTracker;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

public class DatabaseProximityStoreBuilder 
				extends DatabaseWikiWordStoreBuilder 
				implements ProximityStoreBuilder {
	
	   protected FeatureVectorFactors featureVectorFactors;
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected RelationTable proximityTable;
		protected RelationTable featureTable;
		protected EntityTable featureMagnitudeTable;
		
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		private double proximityThreshold;
		private int proximityExpansionPasses;
		
		protected DatabaseProximityStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ProximityStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
		    this.featureVectorFactors = new FeatureVectorFactors(tweaks);
			
			Inserter featureInserter = configureTable("feature", 8*1024, 32);
			featureTable = (RelationTable)featureInserter.getTable();
			
			Inserter featureMagnitudeInserter = configureTable("feature_magnitude", 8*1024, 32);
			featureMagnitudeTable = (EntityTable)featureMagnitudeInserter.getTable();

			Inserter proximityInserter = configureTable("proximity", 8*1024, 32);
			proximityTable = (RelationTable)proximityInserter.getTable();
			
			proximityThreshold = tweaks.getTweak("proximity.threshold", 0.15);
			proximityExpansionPasses = tweaks.getTweak("proximity.expansion.passes", 3);
		}

		private static String getBiasFormula(String biasField, double biasCoef) {
			if ( biasField == null || biasCoef <= 0) return "1";
			else if (biasCoef==1) return biasField;
			else if (biasCoef>1) throw new IllegalArgumentException("biasCoef must not be greater than 1");
			else return "( 1 - ( ( 1 - "+biasField+" ) * "+biasCoef+" ) ) ";
		}
		
		/**
		 * Builds feature vectors. For a specification, refer to ProximityStoreSchema
		 */
		protected int buildFeatures(DatabaseTable t, String conceptField, String featureField, String suffix, double w, String baseBiasField, double baseBiasCoef, String targetBiasField, double targetBiasCoef) throws PersistenceException {
			if (!conceptStore.areStatsComplete()) throw new IllegalStateException("statistics need to be built before concept infos!");

			String v = ""+w;
			if (baseBiasField!=null && baseBiasCoef>0) v = getBiasFormula("B."+baseBiasField, baseBiasCoef) + " * "  + v;
			if (targetBiasField!=null && targetBiasCoef>0) v = getBiasFormula("D."+targetBiasField, targetBiasCoef) + " * "  + v;
			
			DatabaseTable degreeTable = conceptStore.getStatisticsStoreBuilder().getDatabaseAccess().getTable("degree");
			
			String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, total_weight) ";
			sql += " SELECT T."+conceptField+", T."+featureField+", "+v+" FROM "+t.getSQLName()+" as T ";
			if (baseBiasField!=null && baseBiasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as B ON T."+conceptField+" = B.concept ";
			if (targetBiasField!=null && targetBiasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as D ON T."+featureField+" = D.concept ";
			
			if (suffix!=null) sql += " "+suffix+" ";
			
			String update = " ON DUPLICATE KEY UPDATE total_weight = total_weight + VALUES(total_weight)";
			
			int n = executeChunkedUpdate("buildFeatures", "feature#"+t.getName()+"."+featureField, sql, update, t, conceptField);
			return n;
		}
		
		/**
		 * Builds feature vectors. For a specification, refer to ProximityStoreSchema
		 */
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
				int n = buildFeatures(broaderTable, "broad", "narrow", null, featureVectorFactors.downWeight, "down_bias", featureVectorFactors.downBiasCoef, "up_bias", featureVectorFactors.upBiasCoef);
				endTask("buildFeatures", "feature#down", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#up")) {
				int n = buildFeatures(broaderTable, "narrow", "broad", null, featureVectorFactors.upWeight, "up_bias", featureVectorFactors.upBiasCoef, "down_bias", featureVectorFactors.downBiasCoef);
				endTask("buildFeatures", "feature#up", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#out")) {
				int n = buildFeatures(linkTable, "anchor", "target", null, featureVectorFactors.outWeight, "out_bias", featureVectorFactors.outBiasCoef, "in_bias", featureVectorFactors.inBiasCoef);
				endTask("buildFeatures", "feature#out", n+" entries");
		    }
		
			if (beginTask("buildFeatures", "feature#in")) {
				int n = buildFeatures(linkTable, "target", "anchor", null, featureVectorFactors.inWeight, "in_bias", featureVectorFactors.inBiasCoef, "out_bias", featureVectorFactors.outBiasCoef);
				endTask("buildFeatures", "feature#in", n+" entries");
		    }
		
			/*if (beginTask("buildFeatures", "feature#offset")) {
					// apply offset
					String sql = "UPDATE "+featureTable.getSQLName()+" ";
					sql += " SET total_weight = total_weight + "+featureVectorFactors.weightOffset;
					
					int n = executeChunkedUpdate("buildFeatures", "feature#offset", sql, null, featureTable, "concept");
					endTask("buildFeatures", "feature#offset", n+" entries");
			}*/

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

		protected int insertProximity(int concept, int level) throws PersistenceException {
			String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity)";
			sql += " SELECT A.concept, B.concept, "+level+", sum( A.normal_weight * B.normal_weight ) as proximity ";
			sql += " FROM "+featureTable.getSQLName()+" as A ";
			sql += " JOIN "+featureTable.getSQLName()+" as B ON A.feature = B.feature ";
			sql += " WHERE A.concept = "+concept+" ";
			sql += " AND A.concept > B.concept"; //NOTE: only calculate half of the matrix, the other half is added by complementProximity
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
			protected int level;

			protected ImportProgressTracker conceptTracker;
			protected ImportProgressTracker featureTracker;
			
			public CollectProximityQuery(String context, String name, int level) {
				super();
				this.context = context;
				this.name = name;
				this.level = level;
				this.conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
				this.conceptTracker = new ImportProgressTracker("concepts");
				this.featureTracker = new ImportProgressTracker("features");
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
				int i = 0;
				try {
					ResultSet res = DatabaseProximityStoreBuilder.this.executeQuery(context+"::"+name+"#chunk"+chunk, sql);
					while (res.next()) {
						lastId = res.getInt(1);
						
						int c = insertProximity(lastId, level); //TODO: progress tracker!
						n*= c;
						i+= 1;
						
						conceptTracker.step();
						featureTracker.step(c);
						
						if ( (i % 1000) == 0 ) {
							conceptTracker.chunk();
							featureTracker.chunk();
							log("- "+conceptTracker);
							log("- "+featureTracker);
						}
					}
					
					res.close();
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
				
				conceptTracker.chunk();
				featureTracker.chunk();
				log("- "+conceptTracker);
				log("- "+featureTracker);

				flush();
				return n;
			}

			public ResultSet executeQuery(int chunk, long first, long end) throws Exception {
				throw new UnsupportedOperationException();
			}

		}
		
		/**
		 * Builds concept proximity information. For a specification, refer to ProximityStoreSchema
		 */
		public void buildProximity() throws PersistenceException {
			DatabaseTable conceptTable = conceptStore.getDatabaseAccess().getTable("concept");

			if (beginTask("buildProximity", "proximity#self")) {
				// add self-proximity
				String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity) ";
				sql += "SELECT id, id, 0, 1 FROM "+conceptTable.getSQLName();
				
				int n = executeChunkedUpdate("buildProximity", "proximity#self", sql, null, conceptTable, "id");
				endTask("buildProximity", "proximity#self", n+" entries");
			}

			if (beginTask("buildProximity", "collect")) {
				// calculate base-proximity from feature vectors
				CollectProximityQuery query = new CollectProximityQuery("buildProximity", "collectBaseProximity", 0);
				int n = executeChunkedUpdate(query, 1);
				endTask("buildProximity", "collect", n+" entries");
			}
			
			if (beginTask("buildProximity", "collect.complement#level0")) {
				int n = complementProximity(0);
				endTask("buildProximity", "collect.complement#level0", n+" entries");
			}
			
			int level= 0;
			while (true) {
				level++;
				if (level>proximityExpansionPasses) break;
				
				int n = 0;
				if (beginTask("buildProximity", "distribute.left#level"+level)) {
					n += expandProximity(level, true); //top half, left filter
					endTask("buildProximity", "distribute.left#level"+level, n+" entries");
				}

				if (beginTask("buildProximity", "distribute.right#level"+level)) {
					n = expandProximity(level, false); //top half, right filter
					endTask("buildProximity", "distribute.right#level"+level, n+" entries");
					if (n==0) break; //shorten out - if nothing changed, nothing will change
				}

				if (beginTask("buildProximity", "distribute.complement#level"+level)) {
					n = complementProximity(level); //generate bottom half
					endTask("buildProximity", "distribute.complement#level"+level, n+" entries");
				}
			}
		}

		protected int complementProximity(int level) throws PersistenceException {
				String sql = "INSERT IGNORE INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity)";
				sql += " SELECT concept2, concept1, level, proximity ";
				sql += " FROM "+proximityTable.getSQLName()+" as P ";
				
				String where  = " WHERE level = "+level;        //filter by level to avoid overhead
				where  += " AND concept1 > concept2 "; //look at upper half of matrix to generate the lower half.
				
				return executeChunkedUpdate("complementProximity", "complement#level"+level, sql, where, proximityTable, "P.concept1", -10);
		}	
		
		protected int expandProximity(int level, boolean leftFilter) throws PersistenceException {
			//NOTE: calculate transitiv proximity: prox(A,C) = prox(A,B) *  prox(B,C)
			//to make better use of indexes, calculate as prox(A,C) = prox(B,A) *  prox(B,C),
			//i.e. join on P.concept1 = Q.concept1
			
			String sql = "INSERT IGNORE INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity)";
			sql += " SELECT P.concept2, Q.concept2, "+level+", P.proximity * Q.proximity ";
			sql += " FROM "+proximityTable.getSQLName()+" as P ";
			sql += " JOIN "+proximityTable.getSQLName()+" as Q ON P.concept1 = Q.concept1 ";
			
			String where  = " WHERE P.proximity * Q.proximity > "+proximityThreshold+" "; //filter by threshold
			where  += " AND P.concept2 != Q.concept2 "; //no circular proximity
			where  += " AND P.concept2 != P.concept1 "; //no self-proximity 
			where  += " AND P.concept2 > Q.concept2 "; //only calculate half of the values, the complementary entries are added by complementProximity
			
			//if none of the values was added in the previous pass, the value would have been added earlier
			//NOTE: condition: P.level = level-1 OR Q.level = level-1; to make better use of indexes,
			//      we first filter for one (left), then for the other (right) condition.
			if (leftFilter) where  += " AND P.level = "+(level-1); 
			else where  += " AND Q.level = "+(level-1); 
			
			return executeChunkedUpdate("expandProximity", "expand#level"+level, sql, where, proximityTable, leftFilter ? "P.concept1" : "Q.concept1", 3);
	}	
		
}