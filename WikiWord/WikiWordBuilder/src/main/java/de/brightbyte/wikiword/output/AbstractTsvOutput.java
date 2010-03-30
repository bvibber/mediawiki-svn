package de.brightbyte.wikiword.output;

import java.io.Writer;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class AbstractTsvOutput extends AbstractWriterOutput implements DataSink<String[]> {
	
	private CharSequence terminator = "\r\n";
	private CharSequence separator = "\t";
	private Matcher mangler = Pattern.compile("[\r\n\t]").matcher("");

	protected String mangle(String s) {
		mangler.reset(s);
		return mangler.replaceAll(" ");
	}
	
	public Corpus getCorpus() {
		return (Corpus)getDatasetIdentifier();
	}
	
	public AbstractTsvOutput(DatasetIdentifier dataset, Writer out) {
		super(dataset, out);
	}


	public void open() throws PersistenceException {
		//noop
	}

	protected StringBuilder buffer = new StringBuilder();
	

	public int transfer(DataCursor<String[]> cursor) throws PersistenceException {
		String[] rec;
		int c = 0;
		while ((rec = cursor.next()) != null) {
			commit(rec);
			c++;
		}
		
		return c;
	}

	public void commit(String[] values) throws PersistenceException {
		writeRow(values);
	}
	
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
		
		write(buffer.toString());
	}
	
}
