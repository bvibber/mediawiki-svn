package de.brightbyte.wikiword.output;

import java.io.Writer;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class AbstractTsvOutput extends AbstractWriterOutput {
	
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
