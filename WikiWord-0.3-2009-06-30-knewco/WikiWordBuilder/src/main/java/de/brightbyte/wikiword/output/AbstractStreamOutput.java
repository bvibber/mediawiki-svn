package de.brightbyte.wikiword.output;

import java.io.IOException;
import java.io.OutputStream;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class AbstractStreamOutput extends AbstractOutput {
	
	private OutputStream out;
	
	public AbstractStreamOutput(DatasetIdentifier dataset, OutputStream out) {
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

	protected void write(byte[] buffer) throws PersistenceException {
		write(buffer, 0, buffer.length);
	}
	
	protected void write(byte[] buffer, int ofs, int len) throws PersistenceException {
		try {
			out.write(buffer, ofs, len);
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}
}
