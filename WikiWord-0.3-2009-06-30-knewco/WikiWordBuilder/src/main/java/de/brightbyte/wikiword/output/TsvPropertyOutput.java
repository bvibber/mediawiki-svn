package de.brightbyte.wikiword.output;

import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;

public class TsvPropertyOutput extends AbstractTsvOutput {
	
	public TsvPropertyOutput(Corpus corpus, OutputStream out, String enc) throws UnsupportedEncodingException {
		this( corpus, new OutputStreamWriter(out, enc));
	}

	public TsvPropertyOutput(Corpus corpus, Writer out) {
		super(corpus, out);
	}

	public void storeProperty(int rcId, int conceptId, String concept, String property, String value) throws PersistenceException {
		writeRow(concept, property, value);
	}

}
