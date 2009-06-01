package de.brightbyte.wikiword.integrator.data;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.TsvCursor;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.util.PersistenceException;

public class TsvFeatureSetCursor implements DataCursor<FeatureSet> {
	protected DataCursor<List<String>> source;
	private String[] fields;
	
	public TsvFeatureSetCursor(InputStream in, String enc) throws UnsupportedEncodingException {
		this( new TsvCursor(in, enc) );
	}

	public TsvFeatureSetCursor(Reader rd) {
		this(new TsvCursor(rd));
	}
	
	public TsvFeatureSetCursor(BufferedReader reader) {
		this(new TsvCursor(reader));
	}

	public TsvFeatureSetCursor(LineCursor lines) {
		this(new TsvCursor(lines));
	}

	public TsvFeatureSetCursor(LineCursor lines, CsvLineChunker chunker) {
		this(new TsvCursor(lines, chunker));
	}

	public TsvFeatureSetCursor(DataCursor<List<String>> source) {
		if (source==null) throw new NullPointerException();
		this.source = source;
	}
	
	public void setFields(String[] fields) {
		if (fields==null) throw new NullPointerException();
		this.fields = fields;
	}
	
	public void readFields() throws PersistenceException {
		List<String> s = source.next();
		if (s!=null) {
			String[] f = (String[]) s.toArray(new String[s.size()]);
			setFields(f);
		}
	}

	public String[] getFields() {
		return fields;
	}
	
	public void close() {
			source.close();
	}

	public FeatureSet next() throws PersistenceException {
		List<String> s = source.next();
		if (s==null) return null;
		return record(s);
	}

	protected FeatureSet record(List<String> s) {
		if (fields==null) throw new IllegalStateException("call setFields() or readFields() first!");
		
		FeatureSet ft = new DefaultFeatureSet();
		
		for (int i=0; i<fields.length; i++) {
			String f = fields[i];
			String v = s.get(i);
			ft.put(f, v);
		}

		return ft;
	}

	protected void finalize() {
		close();
	}

}
