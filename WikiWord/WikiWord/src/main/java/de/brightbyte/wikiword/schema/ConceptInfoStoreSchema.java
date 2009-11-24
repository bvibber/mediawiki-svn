package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordReference;

public class ConceptInfoStoreSchema extends WikiWordStoreSchema {
	public final String referenceSeparator; //info record separator
	public final String referenceFieldSeparator; //info field separator
	
	public final Pattern referenceSeparatorPattern; //info record separator
	public final Pattern referenceFieldSeparatorPattern; //info field separator

	public class ReferenceListEntrySpec extends WikiWordReference.ListFormatSpec {

		public final String joinField;
		public final String valueExpression;

		public ReferenceListEntrySpec(String field, String expression, boolean useId, boolean useName, boolean useCardinality, boolean useRelevance) {
			super(referenceSeparatorPattern, referenceFieldSeparatorPattern, useId, useName,
					useCardinality, useRelevance);
			
			this.joinField = field;
			this.valueExpression = expression;
		}
		
	}
	
	public final ReferenceListEntrySpec langlinkReferenceListEntry;

	public final ReferenceListEntrySpec termReferenceListEntry;
	public final ReferenceListEntrySpec broaderReferenceListEntry;
	public final ReferenceListEntrySpec narrowerReferenceListEntry;
	public final ReferenceListEntrySpec inLinksReferenceListEntry;
	public final ReferenceListEntrySpec outLinksReferenceListEntry;
	public final ReferenceListEntrySpec similarReferenceListEntry;
	public final ReferenceListEntrySpec relatedReferenceListEntry;
	public final ReferenceListEntrySpec related2ReferenceListEntry;
	
	protected EntityTable conceptInfoTable;
	protected EntityTable conceptDescriptionTable;
	protected EntityTable conceptFeaturesTable;
	
	private String fields(String... f) {
		if (f.length==0) return null;
		if (f.length==1) return f[0];
		
		String s = StringUtils.join(", '"+referenceFieldSeparator+"', ", f);
		return "concat("+s+")";
	}

	public ConceptInfoStoreSchema(DatasetIdentifier dataset, Connection connection, boolean description, TweakSet tweaks, boolean useFlushQueue, boolean cacheNames) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		
		referenceSeparator = tweaks.getTweak("dbstore.cacheReferenceSeparator", "\u001E"); //ASCII Record Separator
		referenceFieldSeparator = tweaks.getTweak("dbstore.cacheReferenceFieldSeparator", "\u001F"); //ASCII Field Separator
		referenceSeparatorPattern = Pattern.compile(referenceSeparator.replaceAll("[^$(){}\\[\\]\\\\]", "\\\\$0")); 
		referenceFieldSeparatorPattern = Pattern.compile(referenceFieldSeparator.replaceAll("[^$(){}\\[\\]\\\\]", "\\\\$0")); 

		langlinkReferenceListEntry = 
			new ReferenceListEntrySpec("language, target", "concat(language, ':', target)", 
					false, true, false, false); 
				
		termReferenceListEntry = 
			new ReferenceListEntrySpec("term_text", fields("term_text", "freq"), 
					false, true, true, false);

		broaderReferenceListEntry = 
			new ReferenceListEntrySpec("broad", cacheNames ? fields("broad", "broad_name", "if (lhs is null or lhs = 0, 0, 1/lhs)") : fields("broad", "if (lhs is null or lhs = 0, 0, 1/lhs)"), 
					true, cacheNames, false, true);
		
		narrowerReferenceListEntry = 
			new ReferenceListEntrySpec("narrow", cacheNames ? fields("narrow", "narrow_name", "if (lhs is null or lhs = 0, 0, 1/lhs)") : fields("narrow", "if (lhs is null or lhs = 0, 0, 1/lhs)"), 
					true, cacheNames, false, true);
		
		inLinksReferenceListEntry = 
			new ReferenceListEntrySpec("anchor", cacheNames ? fields("anchor", "anchor_name", "idf") : fields("anchor", "idf"), 
					true, cacheNames, false, true);
		
		outLinksReferenceListEntry = 
			new ReferenceListEntrySpec("target", cacheNames ? fields("target", "target_name", "idf") : fields("target", "idf"), 
					true, cacheNames, false, true);
			
		similarReferenceListEntry = 
			new ReferenceListEntrySpec("concept2", fields("concept2", "langmatch"), //TODO: frequency for similar from langref(!)
					true, false, true, false); //TODO: name?... in relation table?... //XXX: why no score
		
		relatedReferenceListEntry = 
			new ReferenceListEntrySpec("concept2", "concept2", 
					true, false, false, false); //TODO: name?... in relation table?... //XXX: why no score
				
		related2ReferenceListEntry = 
			new ReferenceListEntrySpec("concept1","concept1", 
					true, false, false, false); //TODO: name?... in relation table?... //XXX: why no score
				
		init(tweaks, description);
	}

	public ConceptInfoStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, boolean description, TweakSet tweaks, boolean useFlushQueue, boolean cacheNames) throws SQLException {
		this(dataset, connectionInfo.getConnection(), description, tweaks, useFlushQueue, cacheNames);
	}
	
	private void init(TweakSet tweaks, boolean description) throws SQLException {
		
		int listBlobSize = tweaks.getTweak("dbstore.listBlobSize", 255*255); 
		setGroupConcatMaxLen(listBlobSize); //TODO: if it's larger currently, don't shrink!
		
		conceptInfoTable = new EntityTable(this, "concept_info", getDefaultTableAttributes());
		conceptInfoTable.addField( new DatabaseField(this, "concept", "INT", null, true, KeyType.PRIMARY ) );
		conceptInfoTable.addField( new DatabaseField(this, "inlinks", getTextType(listBlobSize), null, false, null ) );
		conceptInfoTable.addField( new DatabaseField(this, "outlinks", getTextType(listBlobSize), null, false, null ) );
		conceptInfoTable.addField( new DatabaseField(this, "narrower", getTextType(listBlobSize), null, false, null ) );
		conceptInfoTable.addField( new DatabaseField(this, "broader", getTextType(listBlobSize), null, false, null ) );
		conceptInfoTable.addField( new DatabaseField(this, "langlinks", getTextType(listBlobSize), null, false, null ) );
		//TODO: inlinks, outlinks, coocc, co-coocc
		conceptInfoTable.setAutomaticField(null);
		conceptInfoTable.addField( new DatabaseField(this, "similar", getTextType(listBlobSize), null, false, null ) );
		conceptInfoTable.addField( new DatabaseField(this, "related", getTextType(listBlobSize), null, false, null ) );
		addTable(conceptInfoTable);

		if (description) {
			conceptDescriptionTable = new EntityTable(this, "concept_description", getDefaultTableAttributes());
			conceptDescriptionTable.addField( new DatabaseField(this, "concept", "INT", null, true, KeyType.PRIMARY ) );
			conceptDescriptionTable.addField( new DatabaseField(this, "terms", getTextType(listBlobSize), null, false, null ) );
			conceptDescriptionTable.setAutomaticField(null);
			addTable(conceptDescriptionTable);
		}

		conceptFeaturesTable = new EntityTable(this, "concept_features", getDefaultTableAttributes());
		conceptFeaturesTable.addField( new DatabaseField(this, "concept", "INT", null, true, KeyType.PRIMARY ) );
		conceptFeaturesTable.addField( new DatabaseField(this, "features", getFieldType(byte[].class), null, false, null ) );
		conceptFeaturesTable.setAutomaticField(null);
		addTable(conceptFeaturesTable);
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		//FIXME: "concept" is not declared to be a reference field, and references an unknown bit
		//checkReferentialIntegrity(conceptInfoTable, "concept", false);   
		//checkReferentialIntegrity(conceptDescriptionTable, "concept", false);   
	}
	
	public boolean hasDescriptions() {
		return conceptDescriptionTable != null;
	}
	
	
	@Override
	public boolean isComplete() throws SQLException {
			if (!super.isComplete()) return false;
			if (!this.tableExists("concept_info")) return false;
			
			String sql = "select count(*) from "+this.getSQLTableName("concept_info");
			int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
			if (c == 0) return false; //XXX: hack
			
			return true;
	}
		
}
