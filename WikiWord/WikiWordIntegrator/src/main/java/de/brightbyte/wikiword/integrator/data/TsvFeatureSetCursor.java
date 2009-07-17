package de.brightbyte.wikiword.integrator.data;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.text.ParseException;
import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.text.Chunker;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.util.ErrorHandler;
import de.brightbyte.util.PersistenceException;

public class TsvFeatureSetCursor implements DataCursor<FeatureSet> {
	protected DataCursor<List<String>> source;
	private String[] fields;
	
	public TsvFeatureSetCursor(InputStream in, String enc) throws UnsupportedEncodingException {
		this( new ChunkingCursor(in, enc) );
	}

	public TsvFeatureSetCursor(Reader rd) {
		this(new ChunkingCursor(rd));
	}
	
	public TsvFeatureSetCursor(BufferedReader reader) {
		this(new ChunkingCursor(reader));
	}

	public TsvFeatureSetCursor(LineCursor lines) {
		this(new ChunkingCursor(lines));
	}

	public TsvFeatureSetCursor(LineCursor lines, char separator, boolean esc) {
		this(new ChunkingCursor(lines, new CsvLineChunker(separator, esc)));
	}

	public TsvFeatureSetCursor(LineCursor lines, Chunker chunker) {
		this(new ChunkingCursor(lines, chunker));
	}

	public TsvFeatureSetCursor(DataCursor<List<String>> source) {
		if (source==null) throw new NullPointerException();
		this.source = source;
	}
	
	public void setParseErrorHandler(ErrorHandler<ChunkingCursor, ParseException, PersistenceException> errorHandler) {
		if (source instanceof ChunkingCursor) {
			((ChunkingCursor)source).setParseErrorHandler(errorHandler);
		} else {
			throw new IllegalStateException("source is not a ChunkingCursor, can't set error handler");
		}
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
