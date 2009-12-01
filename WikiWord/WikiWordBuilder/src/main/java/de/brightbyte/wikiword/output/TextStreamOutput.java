package de.brightbyte.wikiword.output;

import java.io.IOException;
import java.io.OutputStream;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ResourceType;

public class TextStreamOutput extends AbstractStreamOutput implements TextOutput {
	
	private static final String MARKER = "[\u0001\u0002\u0003\u0004] (binary marker)";
	protected String encoding;
	
	public TextStreamOutput(DatasetIdentifier dataset, OutputStream out, String enc) {
		super(dataset, out);
		
		if (enc==null) throw new NullPointerException();
		
		this.encoding = enc;
	}

	public void storeDefinitionText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeBlock(name, "definition", "text/plain", ptype, text);
	}

	public void storeSynopsisText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeBlock(name, "synopsis", "text/plain", ptype, text);
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
			
			s.append("Marker: "); s.append(MARKER); s.append(sep);
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
			
			write(b);
			write(data);
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}
}
