package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.application.Agenda;
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
		protected RelationTable featureProductTable;
		
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		
		protected DatabaseProximityStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ProximityStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
			
			Inserter featureInserter = configureTable("feature", 8*1024, 32);
			featureTable = (RelationTable)featureInserter.getTable();
			
			Inserter featureMagnitudeInserter = configureTable("feature_magnitude", 8*1024, 32);
			featureMagnitudeTable = (EntityTable)featureMagnitudeInserter.getTable();

			Inserter featureProductInserter = configureTable("feature_product", 8*1024, 32);
			featureProductTable = (RelationTable)featureProductInserter.getTable();

			Inserter proximityInserter = configureTable("proximity", 8*1024, 32);
			proximityTable = (RelationTable)proximityInserter.getTable();
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
			
			String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, weight) ";
			sql += " SELECT T."+conceptField+", T."+featureField+", "+v+" FROM "+t.getSQLName()+" as T ";
			if (biasField!=null && biasCoef!=0) sql += " JOIN "+degreeTable.getSQLName()+" as D ON T."+featureField+" = D.concept ";
			
			if (suffix!=null) sql += " "+suffix+" ";
			
			String update = " ON DUPLICATE KEY UPDATE weight = weight + VALUES(weight)";
			
			int n = executeChunkedUpdate("buildFeatures", "feature#"+t.getName()+"."+featureField, sql, update, t, conceptField);
			return n;
		}
		
		public void buildFeatures() throws PersistenceException {
			DatabaseTable conceptTable = conceptStore.getDatabaseAccess().getTable("concept");
			DatabaseTable broaderTable = conceptStore.getDatabaseAccess().getTable("broader");
			DatabaseTable linkTable = conceptStore.getDatabaseAccess().getTable("link");
			
			if (beginTask("buildFeatures", "feature#self")) {
					// add self-references
					String sql = "INSERT INTO "+featureTable.getSQLName()+" (concept, feature, weight) ";
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
					sql += " SET weight = weight + "+featureVectorFactors.weightOffset;
					
					int n = executeChunkedUpdate("buildFeatures", "feature#offset", sql, null, featureTable, "concept");
					endTask("buildFeatures", "feature#offset", n+" entries");
			}

			if (beginTask("buildFeatures", "featureMagnitude")) {
				// apply offset
				String sql = "INSERT INTO "+featureMagnitudeTable.getSQLName()+" (concept, magnitude) ";
				sql += " SELECT concept, SQRT( SUM( weight * weight ) ) FROM "+featureTable.getSQLName()+" ";
				String group = " GROUP BY concept";
				
				int n = executeChunkedUpdate("buildFeatures", "featureMagnitude", sql, group, featureTable, "concept");
				endTask("buildFeatures", "featureMagnitude", n+" entries");
			}
		}

		public void buildProximity() throws PersistenceException {
			if (beginTask("buildProximity", "featureProduct")) {
				String sql = "INSERT INTO "+featureProductTable.getSQLName()+" (concept1, concept2, feature, weight) ";
				sql += " SELECT A.concept, B.concept, A.feature, A.weight * B.weight ";
				sql += " FROM "+featureTable.getSQLName()+" as A ";
				sql += " JOIN "+featureTable.getSQLName()+" as B ON A.feature = B.feature ";
	
				int n = executeChunkedUpdate("buildProximity", "featureProduct", sql, null, featureTable, "A.concept");
				endTask("buildProximity", "featureProduct", n+" entries");
			}
			
			if (beginTask("buildProximity", "vectorProduct")) {
				String sql = "INSERT INTO "+proximityTable.getSQLName()+" (concept1, concept2, proximity) ";
				sql += " SELECT concept1, concept2, SUM( weight ) ";
				sql += " FROM "+featureProductTable.getSQLName()+" ";
				String group = " GROUP BY concept2, concept1 ";
	
				int n = executeChunkedUpdate("buildProximity", "vectorProduct", sql, group, featureProductTable, "concept1");
				endTask("buildProximity", "vectorProduct", n+" entries");
			}
			
			if (beginTask("buildProximity", "scale1")) {
				String sql = "UPDATE "+proximityTable.getSQLName()+" as P ";
				sql += " JOIN "+featureMagnitudeTable.getSQLName()+" as M ON M.concept = P.concept1 ";
				sql += " SET proximity = proximity / magnitude ";
	
				int n = executeChunkedUpdate("buildProximity", "scale1", sql, null, proximityTable, "concept1");
				endTask("buildProximity", "scale1", n+" entries");
			}
			
			if (beginTask("buildProximity", "scale2")) {
				String sql = "UPDATE "+proximityTable.getSQLName()+" as P ";
				sql += " JOIN "+featureMagnitudeTable.getSQLName()+" as M ON M.concept = P.concept2 ";
				sql += " SET proximity = proximity / magnitude ";
	
				int n = executeChunkedUpdate("buildProximity", "scale2", sql, null, proximityTable, "concept1");
				endTask("buildProximity", "scale2", n+" entries");
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