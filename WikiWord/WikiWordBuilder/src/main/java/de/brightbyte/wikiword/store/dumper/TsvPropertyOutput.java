package de.brightbyte.wikiword.store.dumper;

import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;
import java.util.Date;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.store.builder.PropertyStoreBuilder;

public class TsvPropertyOutput extends AbstractTsvOutput implements PropertyStoreBuilder {
	
	public TsvPropertyOutput(Corpus corpus, OutputStream out, String enc) throws UnsupportedEncodingException {
		this( corpus, new OutputStreamWriter(out, enc));
	}

	public TsvPropertyOutput(Corpus corpus, Writer out) {
		super(corpus, out);
	}

	public int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		return -1;
	}

	public int storeResource(String name, ResourceType ctype, Date timestamp) throws PersistenceException {
		return -1;
	}

	public void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		//noop
	}
	
	public void storeProperty(int rcId, int conceptId, String concept, String property, String value) throws PersistenceException {
		writeRow(concept, property, value);
	}

	public Corpus getCorpus() {
		return (Corpus)getDatasetIdentifier();
	}

	public void finishAliases() throws PersistenceException {
		// noop
	}

}
