package de.brightbyte.wikiword.store.builder;

import java.io.IOException;
import java.io.OutputStream;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ResourceType;

public class PlainTextOutput extends FauxStoreBuilder implements TextStoreBuilder {
	
	protected OutputStream out;
	protected String encoding;
	
	public PlainTextOutput(DatasetIdentifier dataset, OutputStream out, String enc) {
		super(dataset);
		
		if (out==null) throw new NullPointerException();
		if (enc==null) throw new NullPointerException();
		
		this.out = out;
		this.encoding = enc;
	}

	public int storeDefinition(int rcId, String name, int conceptId, ResourceType ptype, String text) throws PersistenceException {
		writeBlock(name, "definition", "text/plain", ptype, text);
		return 0;
	}

	public void storePlainText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeBlock(name, "plain", "text/plain", ptype, text);
	}

	public void storeRawText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeBlock(name, "raw", "text/x-wiki", ptype, text);
	}

	protected void writeBlock(String name, String aspect, String format, ResourceType ptype, String text) throws PersistenceException {
		String sep = "\r\n";

		format += "; charset="+encoding.toLowerCase();
		
		try {
			StringBuilder s = new StringBuilder();
			text = text.trim()+"\r\n";
			byte[] data = text.getBytes(encoding);
			
			s.append("Page: "); s.append(name); s.append(sep);
			s.append("Aspect:"); s.append(aspect); s.append(sep);
			s.append("Page-Type:"); s.append(ptype.name()); s.append(sep);
			s.append("Content-Type: "); s.append(format); s.append(sep);
			s.append("Content-Length: "); s.append(data.length); s.append(sep);
				s.append("; chars="); s.append(text.length()); 
				s.append("; codepoints="); s.append(Character.codePointCount(text, 0, text.length())); 
				s.append(sep);
			s.append(sep);

			byte[] b = s.toString().getBytes(encoding);
			
			out.write(b);
			out.write(data);
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

}
