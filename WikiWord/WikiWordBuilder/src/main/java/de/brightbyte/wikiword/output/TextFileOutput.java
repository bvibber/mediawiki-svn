package de.brightbyte.wikiword.output;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;

import de.brightbyte.io.IOUtil;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ResourceType;

public class TextFileOutput extends AbstractOutput implements TextOutput {
	
	protected String encoding;
	protected File outputDir;
	private boolean doHash;
	
	public TextFileOutput(DatasetIdentifier dataset, File outputDir, String enc, boolean doHash) {
		super(dataset);
		
		if (outputDir==null) throw new NullPointerException();
		if (enc==null) throw new NullPointerException();
		
		if (!outputDir.exists()) throw new IllegalArgumentException("output directory "+outputDir+" does not exist");
		if (!outputDir.isDirectory()) throw new IllegalArgumentException(outputDir+" is not a directory");
		if (!outputDir.canWrite()) throw new IllegalArgumentException("can't write to "+outputDir);
		
		this.encoding = enc;
		this.outputDir = outputDir;
		this.doHash = doHash;
	}

	public void storeDefinitionText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeFile(name, "def.txt", text);
	}

	public void storeSynopsisText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeFile(name, "syn.txt", text);
	}

	public void storePlainText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeFile(name, "txt", text);
	}

	public void storeRawText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		writeFile(name, "wiki", text);
	}

	protected void writeFile(String name, String ext, String text) throws PersistenceException {
		File f = getFilePath(name, ext);
		
		if (doHash) {
			File d = f.getParentFile();
			
			if (!d.exists()) d.mkdirs();
		}
		
		try {
			IOUtil.spit(f, text, this.encoding, false);
		} catch (IOException e) {
			throw new PersistenceException(e);
		}
	}

	private File getFilePath(String name, String ext) {
		File d = this.outputDir;

		if (this.doHash) { 
			String md5 = StringUtils.hex( StringUtils.md5(name) );
			d = new File(d, md5.substring(0, 1) + "/" +  md5.substring(0, 2)); 
		}
		
		return new File(d, sanatizeFileName(name) + "." +ext);
	}

	protected String sanatizeFileName(String name) {
		try {
			name = URLEncoder.encode(name, "UTF-8");
			name = name.replace('%', '^');
			
			return name;
		} catch (UnsupportedEncodingException e) {
			throw new RuntimeException(e);
		}
	}

	@Override
	public void close() throws PersistenceException {
		//noop
	}

	@Override
	public void flush() throws PersistenceException {
		//noop
	}
}
