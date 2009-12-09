package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

/**
 *  Defines the database schema for storing feature vectors and proximity information. 
 *  
 * <h4>Table <tt>feature</tt></h4>
 * <p>Holds feature vectors used to compare features and determine their proximity. Each feature is a weighted association with another concept, 
 * i.e. the dimensions of the vector space are given by the set of all concept.</p>
 * <dl>
 * 		<dt>concept</dt><dd>The ID of the concept this entry refers to. Reference to concept.id. Unique key together with the feature field.
 * 												All entries with the same value in the concept field make up the feature vector for that concept.</dd>
 * 		<dt>feature</dt><dd>The ID of the feature (dimension) for describing the concept. Reference to concept.id. Unique key together with the concept field.</dd>
 * 		<dt>total_weight</dt><dd>The total weight of the association of the concept subject of this record to the concept indicated by the feature field. 
 * 												The calculation of this value is described below. </dd>
 * 		<dt>normal_weight</dt><dd>The normalized weight, that is, total_weight devided by the vector's norm (length) 
 * 														given by the square root of the sum of the squares of the total_weight field of all feature entries belonging to a given concept.</dd>
 * </dl>
 * 
 *  <p>The total_weight field of the feature entries contains the "raw" weight of the association between two concepts, namely 
 *  the concept that is the subject of the feature vector, and the "target" concept that constitutes the individual feature in the vector.
 *  This weight, w(A, B), is influenced by different relations that may exist between two concepts, which are: in, out, up, and down. 
 *  in and out are inverse of each other, as are up and down: in(A, B) iff out(B, A) and up(A, B) iff down(B, A). in(A, B) applies if the 
 *  link table has an entry with link.anchor = B and link.target = A; conversely, out(A, B) applies if   link.anchor = A and link.target = B. 
 *  Similarly up(A, B) applies if the broader table has an entry with broader.narrow = A and broader.broad = B and down(A, B) applies for 
 *  broader.narrow = B and broader.broad = A.</p>
 *  
 *   <p>For each of these relations, there's a bias value defined in the degree table (see StatisticsStoreSchema). 
 *   The bias for some relations's degree is given by <tt>1 - ( log(degree(B)) / log(number_of_concepts) )</tt>, where the degree is the number of 
 *   concepts the relation applies to. That is, A's in_degree is the size of the set of all Bs for which in(A, B) applies.
 *   This bias is combined with the bias coefficient for a given relation to form the effective bias for that relation, e.g.:
 *   <tt>in_effective_bias(B) = 1 - ( ( 1 - in_bias(B) ) * in_bias_coef )</tt> which amounts to <tt>1 - ( ( log(in_degree(B)) / log(number_of_concepts) ) * in_bias_coef )</tt>.
 *   For each relation, there's also weight factor provided, which is applied to complement's bias. So if in(A, B) applies, in_w(A, B) is given by:
 *   <tt>in_weight_factor * out_effective_bias(B) </tt>; the feature vector for A is then calculated for each feature B as follows:
 *   <tt>A[B] = w(A,B) =  in_w(A, B) + out_w(A, B) + up_w(A, B) + down_w(A, B)</tt>. Note that A[A] = c, where c is the "self-weight", which usually equals 1. 
 *   </p>
 *   
 *   <p>The self, weight, the four bias-coeficients and the four weight-factors are the parameters for the feature vector calculation.
 *   They can be tweaked to adjust the relative weight given to the different types of relations in the thesaurus with respect to determining the semantic proximity,
 *   that is, the thematic similarity, of concepts. E.g. having similar incoming links (i.e. frequent co-occurrance of references) is a stringer indicator
 *   of similarity than common outgoing links.
 *   </p>
 *  
 *  <p>Note that as a result of the rules above, the weight of the association is not symmetrical: w(A, B) may be different from w(B,A)</p> 
 * 
 * <h4>Table <tt>proximity</tt></h4>
 * <p>Holds statistical figures relating to the entire thesaurus.</p>
 * <dl>
 * 		<dt>concept1</dt><dd>The first concept. Comprises a unique key together with concept2.</dd>
 * 		<dt>concept2</dt><dd>The second concept. Comprises a unique key together with concept1.</dd>
 * 		<dt>proximity</dt><dd>The semantic proximity of concept1 and concept2. This is given by the scalar products
 * 												of the normalized feature vectors of concept1 and concept2, as stored in the feature table.
 * 												Entries with a low proximity value may be omitted (subject to tweak value <tt>proximity.threshold</tt>).</dd>
 * </dl>
 * 
 * @author daniel
 */
public class ProximityStoreSchema extends WikiWordStoreSchema {
	
	protected RelationTable featureTable;
	protected EntityTable featureMagnitudeTable;
	protected RelationTable featureProductTable;
	protected RelationTable proximityTable;


	public ProximityStoreSchema(DatasetIdentifier dataset, Connection connection, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(global, tweaks);
	}

	public ProximityStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(global, tweaks);
	}
	
	private void init(boolean global, TweakSet tweaks) {
		featureTable = new RelationTable(this, "feature", getDefaultTableAttributes());
		featureTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ));
		featureTable.addField( new ReferenceField(this, "feature", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		featureTable.addField( new DatabaseField(this, "total_weight", "REAL", "DEFAULT 0", true, null ) );
		featureTable.addField( new DatabaseField(this, "normal_weight", "REAL", "DEFAULT 0", true, null ) );
		featureTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_feature", new String[] {"concept", "feature"}) );
		featureTable.setAutomaticField(null);
		addTable(featureTable);

		/*
		 featureProductTable = new RelationTable(this, "feature_product", getDefaultTableAttributes());
		featureProductTable.addField( new ReferenceField(this, "concept1", "INT", null, true, null, "concept", "id", null ));
		featureProductTable.addField( new ReferenceField(this, "concept2", "INT", null, true, null, "concept", "id", null ) );
		featureProductTable.addField( new ReferenceField(this, "feature", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		featureProductTable.addField( new DatabaseField(this, "total_weight", "REAL", "DEFAULT 0", true, null ) );
		featureProductTable.addField( new DatabaseField(this, "normal_weight", "REAL", "DEFAULT 0", true, null ) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept1_concept2_feature", new String[] {"concept1", "concept2", "feature"}) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept1_feature", new String[] {"concept1", "feature"}) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept2_feature", new String[] {"concept2", "feature"}) );
		featureProductTable.setAutomaticField(null);
		addTable(featureProductTable);*/

		featureMagnitudeTable = new EntityTable(this, "feature_magnitude", getDefaultTableAttributes());
		featureMagnitudeTable.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.PRIMARY, "concept", "id", null ));
		featureMagnitudeTable.addField( new DatabaseField(this, "magnitude", "REAL", "DEFAULT 0", true, null ) );
		featureMagnitudeTable.setAutomaticField(null);
		addTable(featureMagnitudeTable);

		proximityTable = new RelationTable(this, "proximity", getDefaultTableAttributes());
		proximityTable.addField( new ReferenceField(this, "concept1", "INT", null, true, null, "concept", "id", null ));
		proximityTable.addField( new ReferenceField(this, "concept2", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		proximityTable.addField( new DatabaseField(this, "proximity", "REAL", "DEFAULT 0", true, null ) );
		proximityTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concepts", new String[] {"concept1", "concept2"}) );
		proximityTable.setAutomaticField(null);
		addTable(proximityTable);
	}

	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(featureTable, "concept", false);
		checkReferencePairConsistency(featureTable, "concept", "concept_name");
	}
	
	@Override
	public boolean isComplete() throws SQLException {
		if (!super.isComplete()) return false;
		if (!this.tableExists("feature")) return false;
		if (!this.tableExists("proximity")) return false;
		
		String sql = "select count(*) from "+this.getSQLTableName("feature");
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		sql = "select count(*) from "+this.getSQLTableName("proximity");
		c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		return true;
	}
	
}
