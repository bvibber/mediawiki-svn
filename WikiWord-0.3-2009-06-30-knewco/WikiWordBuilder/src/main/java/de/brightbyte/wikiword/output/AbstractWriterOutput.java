package de.brightbyte.wikiword.output;

import java.io.IOException;
import java.io.Writer;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class AbstractWriterOutput extends AbstractOutput {
	
	private Writer out;
	
	public AbstractWriterOutput(DatasetIdentifier dataset, Writer out) {
		super(dataset);
		
		if (out==null) throw new NullPointerException();
		this.out = out;
	}

	public void close() throws PersistenceException {
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

	protected void write(String s) throws PersistenceException {
		try {
			out.write(s);
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}
}
