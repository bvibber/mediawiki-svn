package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ConceptTypeSet;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.Languages;
import de.brightbyte.wikiword.TweakSet;

public class GlobalConceptStoreSchema extends WikiWordConceptStoreSchema {
	protected RelationTable originTable;
	protected RelationTable mergeTable;
	protected RelationTable langprepTable;
	protected RelationTable meaningTable;
	protected RelationTable aboutTable;

	private ConceptTypeSet conceptTypes;
	protected TweakSet tweaks;
	
	public GlobalConceptStoreSchema(DatasetIdentifier dataset, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(tweaks);
	}

	public GlobalConceptStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(tweaks);
	}
	
	private Corpus[] languages;
	
	private void init(TweakSet tweaks) throws SQLException {
		int nameSize = 32*255+32*16; //TODO: from tweaks!
		
		this.tweaks = tweaks;

		//make references mendator
		linkTable.addField( new ReferenceField(this, "anchor", "INT", null, true, null, "concept", "id", null ) );
		linkTable.addField( new ReferenceField(this, "target", "INT", null, true, null, "concept", "id", null ) );

		//make keys unique
		linkTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "anchor_target", new String[] {"anchor", "target"}) );
		linkTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "target_anchor", new String[] {"target", "anchor"}) );

		//langlinkTable.addField( new DatabaseField(this, "language_bits", "INT", null, true, null ) );

		conceptTable.addField( new DatabaseField(this, "language_bits", "INT", null, true, null ) );
		conceptTable.addField( new DatabaseField(this, "language_count", "INT", null, true, KeyType.INDEX ) ); //index for fast clustering for statistics
		//NOTE: replace field defined by WikiWordConceptStoreSchema!
		conceptTable.addField( new DatabaseField(this, "name", getTextType(nameSize), null, true, null ) ); //XXX: remove this?! use preferred label!
		conceptTable.removeKey( "name" );

		groupStats.add( new GroupStatsSpec("concept", "language_count", null));
		
		relationTable.addField( new DatabaseField(this, "langref", "INT", "DEFAULT 0", true, KeyType.INDEX ) );
		
		definitionTable.addField( new DatabaseField(this, "lang", getTextType(10), null, true, null ) );
		definitionTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_lang", new String[] {"concept", "lang"}) );

		//meaningTable.addField( new DatabaseField(this, "lang", getTextType(10), null, true, null) );
		//NOTE: replace key defined by WikiWordConceptStoreSchema!
		//meaningTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "term_concept", new String[] {"lang", "term_text", "concept"}) );		

		originTable = new RelationTable(this, "origin", getDefaultTableAttributes());
		originTable.addField( new ReferenceField(this, "global_concept", "INT", null, true, null, "concept", "id", null ) );
		originTable.addField( new DatabaseField(this, "local_concept", "INT", null, true, null ) );
		//originTable.addField( new DatabaseField(this, "global_concept_name", getTextType(nameSize), "DEFAULT NULL", false, null ) );
		originTable.addField( new DatabaseField(this, "local_concept_name", getTextType(255), null, true, null ) );
		originTable.addField( new DatabaseField(this, "lang", getTextType(10), null, true, null ) );
		originTable.addField( new DatabaseField(this, "lang_bit", "INT", null, true, null ) );
		originTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "lang_concept", new String[] {"lang", "local_concept"}) );		
		originTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "lang_name", new String[] {"lang", "local_concept_name"}) );		
		originTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "concept_lang", new String[] {"global_concept", "lang"}) );		
		addTable(originTable);

		/*
		langprepTable = new RelationTable(this, "langprep", defaultTableAttributes);
		langprepTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ) );
		langprepTable.addField( new DatabaseField(this, "concept_bits", "INT", null, true, null) );
		langprepTable.addField( new ReferenceField(this, "target", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		langprepTable.addField( new DatabaseField(this, "target_bits", "INT", null, true, null ) );
		langprepTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_target", new String[] {"concept", "target"}) );		
		addTable(langprepTable);
		*/
		
		mergeTable = new RelationTable(this, "merge", getDefaultTableAttributes());
		mergeTable.addField( new ReferenceField(this, "old", "INT", null, true, KeyType.PRIMARY, "concept", "id", null ) );
		mergeTable.addField( new DatabaseField(this, "new", "INT", null, true, KeyType.INDEX ) );
		addTable(mergeTable);
		
		
		meaningTable = new RelationTable(this, "meaning", getDefaultTableAttributes());
		meaningTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ) );
		meaningTable.addField( new DatabaseField(this, "lang", getTextType(10), null, true, null ) );
		meaningTable.addField( new DatabaseField(this, "rule", "INT", null, true, KeyType.INDEX) );
		meaningTable.addField( new DatabaseField(this, "freq", "INT", null, true, null ) );
		meaningTable.addField( new DatabaseField(this, "term_text", getTextType(255), null, true, null) );
		meaningTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "lang_term_concept", new String[] {"lang", "term_text", "concept"}) );
		meaningTable.addKey( new DatabaseKey(this, KeyType.INDEX, "term_concept", new String[] {"term_text", "concept"}) );
		meaningTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept_lang", new String[] {"concept", "lang"}) );
		addTable(meaningTable);
		
		aboutTable = new RelationTable(this, "about", getDefaultTableAttributes());
		//aliasTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		aboutTable.addField( new DatabaseField(this, "lang", getTextType(10), null, true, null ) );
		aboutTable.addField( new ReferenceField(this, "local_resource", "INT", null, true, null, "resource", "id", null ) );
		aboutTable.addField( new ReferenceField(this, "local_resource_name", getTextType(255), null, true, null, "resource", "name", null ) );
		aboutTable.addField( new ReferenceField(this, "type", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		aboutTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ) );
		aboutTable.addKey( new DatabaseKey(this, KeyType.INDEX, "by_concept", new String[] {"concept", "lang"}) );
		aboutTable.addKey( new DatabaseKey(this, KeyType.INDEX, "by_resource_name", new String[] {"lang", "local_resource_name"}) );
		aboutTable.addKey( new DatabaseKey(this, KeyType.INDEX, "by_resource_id", new String[] {"lang", "local_resource"}) );
		addTable(aboutTable);
		
		//getLanguages(); //initialize knownlanguages, corpuses and content types#
	}
	
	/**
	 * @see de.brightbyte.wikiword.LocalConceptQuerior#checkConsistency()
	 */
	@Override
	public void checkConsistency() throws SQLException {
		super.checkConsistency();
		
		checkIdSanity(originTable, "local_concept");
		checkReferentialIntegrity(originTable, "global_concept", false);   
		checkReferentialIntegrity(meaningTable, "concept", false);   
		checkReferentialIntegrity(aboutTable, "about", false);   
		
		checkReferentialIntegrity(relationTable, "concept1", false);   
		checkReferentialIntegrity(relationTable, "concept2", false);   
		
		//TODO: check origin: (lang,local_concept)
	}
	
	public int getLanguageBit(String lang) throws SQLException {
		Corpus[] languages = getLanguages();
		
		int bit = 1;
		for (int i=0; i<languages.length; i++) {
			if (lang.equals(languages[i].getLanguage())) return bit;
			bit = bit << 1;
		}
		
		return 0;
	}

	public int getLanguageBits(String[] langs) throws SQLException {
		int bits = 0;
		for (int i=0; i<langs.length; i++) {
			bits |= getLanguageBit(langs[i]);
		}
		
		return bits;
	}

	public Corpus[] getLanguages(int bits) throws SQLException {
		Corpus[] languages = getLanguages();
		
		ArrayList<Corpus> langs = new ArrayList<Corpus>(languages.length);
		
		int bit = 1;
		for (int i=0; i<languages.length; i++) {
			if ((bits & bit)>0) langs.add(languages[i]); 
			bit = bit << 1;
		}
	
		return (Corpus[]) langs.toArray(new Corpus[langs.size()]);
	}
	
	public Corpus getLanguage(int bit) throws SQLException {
		Corpus[] languages = getLanguages();
		
		int b = 1;
		for (int i=0; i<languages.length; i++) {
			if ((bit & b)>0) return languages[i]; 
			bit = b << 1;
		}
		
		return null;
	}
	
	protected Corpus[] getCorpuses(String[] languages) throws SQLException {
		Corpus[] cc = new Corpus[languages.length];
		
		int i = 0;
		for (String l: languages) {
			if (!getLanguageNames().containsKey(l)) {
				throw new SQLException("bad corpus prefix: "+l+" is not a language name. Hint: check tweaks languages.*AsLanguage"); 
			}
			
			cc[i++] = Corpus.forName(getCollectionName(), l, tweaks);
		}
		
		return cc;
	}
	
	public Corpus[] setLanguages(String[] languages) throws SQLException {
		Corpus[] cc = getCorpuses(languages);
		setLanguages(cc);
		return cc;
	}
	
	public void setLanguages(Corpus[] languages) {
		if (languages.length>32) throw new IllegalArgumentException("only up to 32 languages are supported!");

		this.languages = languages;
				
		conceptTypes = new ConceptTypeSet();
		conceptTypes.addAll(ConceptType.canonicalConceptTypes);

		for (Corpus lang: languages) {
			conceptTypes.addAll(lang.getConceptTypes());
		}
	}
	
	public DatasetIdentifier[] getThesauri() throws SQLException {
		String[] ll = listPrefixes("origin");
		DatasetIdentifier[] cc = new Corpus[ll.length];
		
		int i = 0;
		for (String l: ll) {
			cc[i++] = DatasetIdentifier.forName(getCollectionName(), l, tweaks);
		}
		
		return cc;
	}
	
	private Map<String, String> languageNames;
	
	protected Map<String, String> getLanguageNames() {
		if (this.languageNames==null) {
			this.languageNames = Languages.load(this.tweaks);
		}
		
		return this.languageNames;
	}
	
	public Corpus[] getLanguages() throws SQLException {
		if (languages!=null) return languages;
		setLanguages( detectLanguages() );
		return languages;
	}
	
	private Corpus[] allLanguages = null;
	public Corpus[] detectLanguages() throws SQLException {
		if (allLanguages!=null) return allLanguages;
		
		String[] ll = listPrefixes("resource");
		Arrays.sort(ll); //FIXME: sort by size!
		
		allLanguages = getCorpuses(ll);
		return allLanguages;
	}
	
	@Override
	public ConceptType getConceptType(int type) throws SQLException {
		if (conceptTypes==null) getLanguages(); //init on demand
		return conceptTypes.getType(type);
	}
	
}
