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
import de.brightbyte.job.ChunkedProgressRateTracker;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;

public class DatabaseProximityStoreBuilder 
				extends DatabaseWikiWordStoreBuilder 
				implements ProximityStoreBuilder {
	
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		private int proximityExpansionPasses;

		private FeatureBuilder featureBuilder;
		
		protected DatabaseProximityStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ProximityStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
		    
		    ProximityStoreBuilder.ProximityParameters proximityParameters = new ProximityStoreBuilder.ProximityParameters(tweaks, ProximityStoreBuilder.heuristicParameters);
			
			Inserter featureInserter = configureTable("feature", 8*1024, 32);
			RelationTable featureTable = (RelationTable)featureInserter.getTable();
			
			Inserter featureMagnitudeInserter = configureTable("feature_magnitude", 8*1024, 32);
			EntityTable featureMagnitudeTable = (EntityTable)featureMagnitudeInserter.getTable();

			Inserter proximityInserter = configureTable("proximity", 8*1024, 32);
			RelationTable proximityTable = (RelationTable)proximityInserter.getTable();
			
			featureBuilder = new FeatureBuilder(featureTable, featureMagnitudeTable, proximityTable, proximityParameters);
			
			proximityExpansionPasses = tweaks.getTweak("proximity.expansion.passes", 3);
		}

		private static String getBiasFormula(String biasField, double biasCoef) {
			if ( biasField == null || biasCoef <= 0) return "1";
			else if (biasCoef==1) return biasField;
			else if (biasCoef>1) throw new IllegalArgumentException("biasCoef must not be greater than 1");
			else return "( 1 - ( ( 1 - "+biasField+" ) * "+biasCoef+" ) ) ";
		}
		
		protected static class ConceptSetRestriction {
			private String restriction;
			private boolean isSuffix;
			private boolean doChunk;
			
			public ConceptSetRestriction (String restriction, boolean doChunk, boolean isSuffix) {
				this.restriction = restriction;
				this.doChunk = doChunk;
				this.isSuffix = doChunk;
			}
			
			public String getRestrictionExpression(DatabaseTable t, String conceptField) {
				String r = restriction;
				r = r.replace("{concept}", conceptField);
				return r;
			}
			
			public boolean doChunk() {
				return doChunk;
			}

			public boolean isSuffix() {
				return isSuffix;
			}
		}

		protected class FeatureBuilder {
			
			protected RelationTable proximityTable;
			protected RelationTable featureTable;
			protected EntityTable featureMagnitudeTable;
			protected double proximityThreshold;
			protected ProximityStoreBuilder.ProximityParameters proximityParameters;
		
			protected FeatureBuilder( ProximityStoreSchema database, String suffix, ProximityStoreBuilder.ProximityParameters proximityParameters, boolean temp) throws PersistenceException {
				this(database.derive(suffix), proximityParameters, temp) ;
			}
			
			protected FeatureBuilder( ProximityStoreSchema database, ProximityStoreBuilder.ProximityParameters proximityParameters, boolean temp) throws PersistenceException {
				this( database.makeFeatureTable(),
						database.makeFeatureMagnitudeTable(), 
						database.makeProximityTable(),  
						proximityParameters );
				
				if (database.getTableSuffix()!=null && database.getTableSuffix().length()>0)  {
					try {
						database.createTable(proximityTable, !temp, temp);
						database.createTable(featureMagnitudeTable, !temp, temp);
						database.createTable(featureTable, !temp, temp);
						
						database.truncateTable(proximityTable.getName(), false);
						database.truncateTable(featureMagnitudeTable.getName(), false);
						database.truncateTable(featureTable.getName(), false);
					} catch (SQLException e) {
						throw new PersistenceException(e);
					}
				}
			}
			
			protected FeatureBuilder( RelationTable featureTable, EntityTable featureMagnitudeTable, RelationTable proximityTable, ProximityStoreBuilder.ProximityParameters proximityParameters) {
				this.featureTable = featureTable;
				this.featureMagnitudeTable = featureMagnitudeTable;
				this.proximityTable = proximityTable;
				this.proximityParameters = proximityParameters;
			}
			
			
			/**
			 * Builds feature vectors. For a specification, refer to ProximityStoreSchema
			 */
			protected int buildFeatures(DatabaseTable t, String conceptField, String featureField, ConceptSetRestriction restriction, double w, String baseBiasField, double baseBiasCoef, String targetBiasField, double targetBiasCoef) throws PersistenceException {
				if (!conceptStore.areStatsComplete()) throw new IllegalStateException("statistics need to be built before feature sets!");

				String v = ""+w;
				if (baseBiasField!=null && baseBiasCoef>0) v = getBiasFormula("B."+baseBiasField, baseBiasCoef) + " * "  + v;
				if (targetBiasField!=null && targetBiasCoef>0) v = getBiasFormula("D."+targetBiasField, targetBiasCoef) + " * "  + v;
				
				DatabaseTable degreeTable = conceptStore.getStatisticsStoreBuilder().getDatabaseAccess().getTable("degree");
				
				String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, total_weight) ";
				sql += " SELECT T."+conceptField+", T."+featureField+", "+v+" FROM "+t.getSQLName()+" as T ";
				if (baseBiasField!=null && baseBiasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as B ON T."+conceptField+" = B.concept ";
				if (targetBiasField!=null && targetBiasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as D ON T."+featureField+" = D.concept ";
				
				if (restriction!=null) sql += " "+restriction.getRestrictionExpression(t, conceptField)+" ";
				
				String update = " ON DUPLICATE KEY UPDATE total_weight = total_weight + VALUES(total_weight)";
				
				int n = restriction==null || restriction.doChunk ()
							? executeChunkedUpdate("buildFeatures", "feature#"+t.getName()+"."+featureField, sql, update, t, conceptField, 20)
							: executeUpdate("buildFeatures::feature#"+t.getName()+"."+featureField, sql+" "+update);
							
				return n;
			}
			
			/**
			 * Builds feature vectors. For a specification, refer to ProximityStoreSchema
			 */
			public void buildFeatures(ConceptSetRestriction restriction) throws PersistenceException {
				DatabaseTable conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
				DatabaseTable broaderTable = conceptStore.getDatabaseAccess().getTable("broader");
				DatabaseTable linkTable = conceptStore.getDatabaseAccess().getTable("link");
				
				if (beginTask("buildFeatures", "feature#self")) {
						// add self-references
						String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, total_weight) ";
						sql += "SELECT id as concept, id, "+proximityParameters.selfWeight+" FROM "+conceptTable.getSQLName()+" as T ";
						
						String where = null;
						
						if (restriction!=null) {
							String r = restriction.getRestrictionExpression(conceptTable, "T.id");
							if (restriction.isSuffix()) where = r;
							else sql += " "+r;
						}
						
						int n = restriction==null || restriction.doChunk() 
										? executeChunkedUpdate("buildFeatures", "feature#self", sql, where, conceptTable, "id")
										: executeUpdate("buildFeatures::feature#self", sql);
										
						endTask("buildFeatures", "feature#self", n+" entries");
				}
				
				if (beginTask("buildFeatures", "feature#down")) {
					int n = buildFeatures(broaderTable, "broad", "narrow", restriction, proximityParameters.downWeight, "down_bias", proximityParameters.downBiasCoef, "up_bias", proximityParameters.upBiasCoef);
					endTask("buildFeatures", "feature#down", n+" entries");
			    }
			
				if (beginTask("buildFeatures", "feature#up")) {
					int n = buildFeatures(broaderTable, "narrow", "broad", restriction, proximityParameters.upWeight, "up_bias", proximityParameters.upBiasCoef, "down_bias", proximityParameters.downBiasCoef);
					endTask("buildFeatures", "feature#up", n+" entries");
			    }
			
				if (beginTask("buildFeatures", "feature#out")) {
					int n = buildFeatures(linkTable, "anchor", "target", restriction, proximityParameters.outWeight, "out_bias", proximityParameters.outBiasCoef, "in_bias", proximityParameters.inBiasCoef);
					endTask("buildFeatures", "feature#out", n+" entries");
			    }
			
				if (beginTask("buildFeatures", "feature#in")) {
					int n = buildFeatures(linkTable, "target", "anchor", restriction, proximityParameters.inWeight, "in_bias", proximityParameters.inBiasCoef, "out_bias", proximityParameters.outBiasCoef);
					endTask("buildFeatures", "feature#in", n+" entries");
			    }
			
				/*if (beginTask("buildFeatures", "feature#offset")) {
						// apply offset
						String sql = "UPDATE "+featureTable.getSQLName()+" ";
						sql += " SET total_weight = total_weight + "+proximityParameters.weightOffset;
						
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
			
			public void buildProximityAround(int concept) throws PersistenceException {
				try {
					if (beginTask("buildProximityAround", "buildFeatures(concept)")) {
						buildFeatures(new ConceptSetRestriction("WHERE {concept} = "+concept, false, true));
						endTask("buildProximityAround", "buildFeatures(concept)", "ok");
					}

					if (beginTask("buildProximityAround", "buildFeatures(join)")) {
						String t = database.createTemporaryTable("id INT, PRIMARY KEY (id)");
						
						String sql = "INSERT INTO "+t+" SELECT id FROM "+featureTable.getSQLName()+" WHERE concept = "+concept;
						database.executeUpdate("buildProximityAround::getFirstLevelConceptIds", sql);

						buildFeatures(new ConceptSetRestriction("JOIN "+t+" ON {concept} = "+t+".id "+concept, false, false));

						database.executeUpdate("buildProximityAround::dropFirstLevelConceptIds", "DROP TEMPORARY TABLE "+t);
						endTask("buildProximityAround", "buildFeatures(join)", "ok");
					}
					
					if (beginTask("buildProximityAround", "proximity#self")) {
						// add self-proximity
						String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity) ";
						sql += "VALUES "+concept+", "+concept+", "+0+", "+1;
						
						int n = executeUpdate("buildProximityAround::proximity#self", sql);
						endTask("buildProximityAround", "proximity#self", n+" entries");
					}

					if (beginTask("buildProximityAround", "proximity#collect")) {
						String sql = "SELECT DISTINCT J.concept ";
						sql += " FROM " +featureTable.getSQLName()+" as F ";
						sql += " JOIN " +featureTable.getSQLName()+" as J ON F.feature = J.feature ";
						sql += " WHERE F.concept = "+concept;
						
						ResultSet res = DatabaseProximityStoreBuilder.this.executeQuery("buildProximityAround::proximity#collect", sql);
						int n = 0;
						int i = 0;
						while (res.next()) {
							int id = res.getInt(1);
							int c = insertProximity(id, 0);
							n += c;
							i ++;
						}
						
						res.close();
						endTask("buildProximityAround", "proximity#collect", i+" concepts, "+n+" entries");
					}
					
					if (beginTask("buildProximityAround", "collect.complement#level0")) {
						int n = complementProximity(0, proximityTable);
						endTask("buildProximityAround", "collect.complement#level0", n+" entries");
					}
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
				
			}
					
			/**
			 * Builds baseic concept proximity information. For a specification, refer to ProximityStoreSchema
			 */
			public void buildBaseProximity() throws PersistenceException {
				DatabaseTable conceptTable = conceptStore.getDatabaseAccess().getTable("concept");

				if (beginTask("buildBaseProximity", "proximity#self")) {
					// add self-proximity
					String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, level, proximity) ";
					sql += "SELECT id, id, 0, 1 FROM "+conceptTable.getSQLName();
					
					int n = executeChunkedUpdate("buildBaseProximity", "proximity#self", sql, null, conceptTable, "id");
					endTask("buildBaseProximity", "proximity#self", n+" entries");
				}

				if (beginTask("buildBaseProximity", "collect")) {
					// calculate base-proximity from feature vectors
					CollectProximityQuery query = new CollectProximityQuery("buildBaseProximity", "collectBaseProximity", 0, this);
					int n = executeChunkedUpdate(query, 1);
					endTask("buildBaseProximity", "collect", n+" entries");
				}
				
				if (beginTask("buildBaseProximity", "collect.complement#level0")) {
					int n = complementProximity(0, proximityTable);
					endTask("buildBaseProximity", "collect.complement#level0", n+" entries");
				}
			}
			
			/**
			 * Builds extended concept proximity information. For a specification, refer to ProximityStoreSchema
			 */
			public void buildExtendedProximity() throws PersistenceException {
				int level= 0;
				while (true) {
					level++;
					if (level>proximityExpansionPasses) break;
					
					int n = 0;
					if (beginTask("buildExtendedProximity", "distribute.left#level"+level)) {
						n += expandProximity(level, true); //top half, left filter
						endTask("buildExtendedProximity", "distribute.left#level"+level, n+" entries");
					}

					if (beginTask("buildExtendedProximity", "distribute.right#level"+level)) {
						n = expandProximity(level, false); //top half, right filter
						endTask("buildExtendedProximity", "distribute.right#level"+level, n+" entries");
						if (n==0) break; //shorten out - if nothing changed, nothing will change
					}

					if (beginTask("buildExtendedProximity", "distribute.complement#level"+level)) {
						n = complementProximity(level, proximityTable); //generate bottom half
						endTask("buildExtendedProximity", "distribute.complement#level"+level, n+" entries");
					}
				}
			}

			protected int complementProximity(int level, DatabaseTable proximityTable) throws PersistenceException {
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
		

		protected class CollectProximityQuery implements DatabaseAccess.ChunkedQuery {
			protected String context;
			protected String name;
			protected DatabaseTable conceptTable;
			protected int lastId ;
			protected int level;

			protected ChunkedProgressRateTracker conceptTracker;
			protected ChunkedProgressRateTracker featureTracker;
			
			protected FeatureBuilder builder;
			
			public CollectProximityQuery(String context, String name, int level, FeatureBuilder builder) {
				super();
				this.context = context;
				this.name = name;
				this.level = level;
				this.conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
				this.conceptTracker = new ChunkedProgressRateTracker("concepts");
				this.featureTracker = new ChunkedProgressRateTracker("features");
				this.builder = builder;
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
					builder.deleteProximityValuesAfter(lastId);
				}  else {
					lastId = builder.getMaxProximityConceptId();
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
						
						int c = builder.insertProximity(lastId, level); 
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

		protected FeatureBuilder makeBuilder(ProximityParameters params, String suffix) throws PersistenceException {
			ProximityStoreSchema db = ((ProximityStoreSchema)database).derive(suffix);
			
			RelationTable targetFeatureTable = db.makeFeatureTable();
			EntityTable targetFeatureMagnitudeTable = db.makeFeatureMagnitudeTable();
			RelationTable targetProximityTable = db.makeProximityTable();
			
			return new FeatureBuilder(targetFeatureTable, targetFeatureMagnitudeTable, targetProximityTable, params);
		}

		public void buildBaseProximity() throws PersistenceException {
			featureBuilder.buildBaseProximity();
		}

		public void buildExtendedProximity() throws PersistenceException {
			featureBuilder.buildExtendedProximity();
		}

		public void buildFeatures() throws PersistenceException {
			featureBuilder.buildFeatures(null);
		}
		
		
}