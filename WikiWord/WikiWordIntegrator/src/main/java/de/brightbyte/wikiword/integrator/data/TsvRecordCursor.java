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

public class TsvRecordCursor implements DataCursor<Record> {
	protected DataCursor<List<String>> source;
	private String[] fields;
	
	public TsvRecordCursor(InputStream in, String enc) throws UnsupportedEncodingException {
		this( new ChunkingCursor(in, enc) );
	}

	public TsvRecordCursor(Reader rd) {
		this(new ChunkingCursor(rd));
	}
	
	public TsvRecordCursor(BufferedReader reader) {
		this(new ChunkingCursor(reader));
	}

	public TsvRecordCursor(LineCursor lines) {
		this(new ChunkingCursor(lines));
	}

	public TsvRecordCursor(LineCursor lines, char separator, boolean esc) {
		this(new ChunkingCursor(lines, new CsvLineChunker(separator, esc)));
	}

	public TsvRecordCursor(LineCursor lines, Chunker chunker) {
		this(new ChunkingCursor(lines, chunker));
	}

	public TsvRecordCursor(DataCursor<List<String>> source) {
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

	public Record next() throws PersistenceException {
		List<String> s = source.next();
		if (s==null) return null;
		return record(s);
	}

	protected Record record(List<String> s) {
		if (fields==null) throw new IllegalStateException("call setFields() or readFields() first!");
		
		Record rec = new DefaultRecord();
		
		for (int i=0; i<fields.length; i++) {
			String f = fields[i];
			String v = s.get(i);
			rec.add(f, v);
		}

		return rec;
	}

	protected void finalize() {
		close();
	}

}
