package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordReference;

public class ConceptDescriptionStoreSchema extends WikiWordStoreSchema {
	protected EntityTable conceptInfoTable;
	protected Corpus corpus;

	public ConceptDescriptionStoreSchema(Corpus corpus, Connection connection, TweakSet tweaks, boolean useFlushQueue) {
		super(corpus.getDbPrefix(), connection, tweaks, useFlushQueue );
		init(corpus, tweaks);
	}

	public ConceptDescriptionStoreSchema(Corpus corpus, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) {
		super(corpus.getDbPrefix(), connectionInfo, tweaks, useFlushQueue);
		init(corpus, tweaks);
	}
	
	private void init(Corpus corpus, TweakSet tweaks) {
		this.corpus = corpus;
	}
	
	@Override
	protected ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(conceptInfoTable, "concept", false);   
		checkReferentialIntegrity(conceptDescriptionTable, "concept", false);   
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
}
