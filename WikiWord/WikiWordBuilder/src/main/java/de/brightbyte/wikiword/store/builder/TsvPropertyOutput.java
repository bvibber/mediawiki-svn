package de.brightbyte.wikiword.store.builder;

import java.io.IOException;
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

public class TsvPropertyOutput extends FauxStoreBuilder implements PropertyStoreBuilder {
	
	protected Writer out;
	
	private CharSequence terminator = "\r\n";
	private CharSequence separator = "\t";

	private Corpus corpus;
	
	//FIXME: use multiple TsvWriters
	
	public TsvPropertyOutput(Corpus corpus, OutputStream out, String enc) throws UnsupportedEncodingException {
		this(corpus, new OutputStreamWriter(out, enc));
	}

	public TsvPropertyOutput(Corpus corpus, Writer out) {
		this.corpus = corpus;
		this.out = out;
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
		StringBuilder s = new StringBuilder();
		
		s.append(concept);
		s.append(separator);

		s.append(property);
		s.append(separator);
		
		s.append(value);
		s.append(terminator);
		
		try {
			out.write(s.toString());
		} catch (IOException e) {
			throw new PersistenceException();
		}
	}

	public void open() throws PersistenceException {
		//noop
	}

	public void close(boolean flush) throws PersistenceException {
		try {
			out.close();
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}

	public void flush() throws PersistenceException {
		try {
			out.flush();
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}

	public Corpus getCorpus() {
		return corpus;
	}

}
