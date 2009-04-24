package de.brightbyte.wikiword.store.dumper;

import java.io.IOException;
import java.io.Writer;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.store.builder.FauxStoreBuilder;

public class AbstractTsvOutput extends FauxStoreBuilder {
	
	protected Writer out;
	
	private CharSequence terminator = "\r\n";
	private CharSequence separator = "\t";
	private Matcher mangler = Pattern.compile("[\r\n\t]").matcher("");

	protected String mangle(String s) {
		mangler.reset(s);
		return mangler.replaceAll(" ");
	}
	
	public AbstractTsvOutput(DatasetIdentifier dataset, Writer out) {
		super(dataset);
		this.out = out;
	}


	public void open() throws PersistenceException {
		//noop
	}

	protected StringBuilder buffer = new StringBuilder();
	
	protected void writeRow(String... values) throws PersistenceException {
		buffer.setLength(0);
		boolean first = true;
		
		for (String v: values) {
			v = mangle(v);
		
			if (first) first = false;
			else buffer.append(separator);
			
			buffer.append(v);
		}
		
		buffer.append(terminator);
		
		try {
			out.write(buffer.toString());
		} catch (IOException e) {
			throw new PersistenceException();
		}
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

}
