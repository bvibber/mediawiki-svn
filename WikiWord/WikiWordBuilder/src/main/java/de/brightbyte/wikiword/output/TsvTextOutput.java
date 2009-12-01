package de.brightbyte.wikiword.output;

import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;

public class TsvTextOutput extends AbstractTsvOutput implements TextOutput {
	
	public TsvTextOutput(Corpus corpus, OutputStream out, String enc) throws UnsupportedEncodingException {
		this( corpus, new OutputStreamWriter(out, enc));
	}

	public TsvTextOutput(Corpus corpus, Writer out) {
		super(corpus, out);
	}
	
	public void storeDefinitionText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("definition", name, text);
	}

	public void storeSynopsisText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("synopsis", name, text);
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.output.TextOutput#storePlainText(int, java.lang.String, de.brightbyte.wikiword.ResourceType, java.lang.String)
	 */
	public void storePlainText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("plain", name, text);
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.output.TextOutput#storeRawText(int, java.lang.String, de.brightbyte.wikiword.ResourceType, java.lang.String)
	 */
	public void storeRawText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeRow("raw", name, text);
	}

}
