package de.brightbyte.wikiword.store.dumper;

import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.store.builder.TextStoreBuilder;

public class TsvTextOutput extends AbstractTsvOutput implements TextStoreBuilder {
	
	public TsvTextOutput(Corpus corpus, OutputStream out, String enc) throws UnsupportedEncodingException {
		this( corpus, new OutputStreamWriter(out, enc));
	}

	public TsvTextOutput(Corpus corpus, Writer out) {
		super(corpus, out);
	}

	public void storeProperty(int rcId, int conceptId, String concept, String property, String value) throws PersistenceException {
		writeRow(concept, property, value);
	}

	public Corpus getCorpus() {
		return (Corpus)getDatasetIdentifier();
	}

	public void storePlainText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("plain", name, text);
	}

	public void storeRawText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("raw", name, text);
	}

}
