package de.brightbyte.wikiword.integrator;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.List;
import java.util.Map;

import de.brightbyte.text.Chunker;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;

public class FeatureSetSourceDescriptor extends TweakSet {
	
	public interface SqlQueryGenerator {
		public String makewQuery(FeatureSetSourceDescriptor fsd);
	}

	public FeatureSetSourceDescriptor() {
		this(null, null);
	}

	public FeatureSetSourceDescriptor(String prefix, TweakSet parent) {
		super(prefix, parent);
	}

	public void loadTweaks(URL u) throws IOException {
		super.loadTweaks(u);
		setBaseURL(u); //XXX: always?!
	}
	
	public String getAuthorityName() {
		String name = getTweak("authority", null);
		if (name==null) throw new RuntimeException("authority name not specified!");
		return name;
	}
	
	public String getDataEncoding() {
		return getTweak("encoding", "UTF-8");
	}

	public String getSqlQuery() {
		String sql = getTweak("query", null);
		
		if (sql==null) {
			SqlQueryGenerator generator = getTweak("queryGenerator", null);
			if (generator!=null) sql = generator.makewQuery(this); 
		}
		
		return sql;
	}

	public String getSourceFileName() { //FIXME
		return getTweak("file", null);
	}

	
	public void setBaseURL(URL baseURL) {
		parameters.put(".baseURL", baseURL);
	}
	
	public URL getBaseURL() {
		try {
			URL u = getTweak(".baseURL", (URL)null);
			if (u==null) u = new File(".").toURI().toURL();
			return u;
		} catch (MalformedURLException e) {
			return null;
		}
	}
	
	
	public String[] getDataFields() {
		List<String> v = getTweak("fields", (List<String>)null);
		if (v==null) return null;
		return (String[]) v.toArray(new String[v.size()]);
	}
	

	public Map<String, Chunker> getDataFieldChunkers() { 
		return getTweak("field-chunkers", (Map<String, Chunker>)null);
	}
	
	public String getPropertyValueField() {
		return getTweak("property-value-field", null);
	}

	public String getPropertyNameField() {
		return getTweak("property-name-field", null);
	}

	public String getPropertySubjectField() {
		return getTweak("property-subject-field", null);
	}

	public String getPropertySubjectNameField() {
		return getTweak("property-subject-name-field", null);
	}

	public boolean getSkipHeader() {
		return getTweak("skip-header", false);
	}

	public String getSourceFileFormat() {
		String format = getTweak("file-format", null);
		
		if (format==null) {
			String n = getSourceFileName();
			if (n!=null) format = InputFileHelper.getFormat(n);
		}
		
		return format;
	}
	
	public Chunker getCsvLineChunker() {
		Chunker chunker = getTweak("csv-chunker", null);
		
		if (chunker==null) {
			char ch = getTweak("csv-separator", '\u008F');
			if (ch!='\u008F') chunker = new CsvLineChunker(ch, getTweak("csv-backslash-escape", false));
		}

		if (chunker==null) {
			if (getTweak("file-format", "tsv").equals("csv"))
				chunker = CsvLineChunker.csv;
		}
		
		if (chunker==null) chunker = CsvLineChunker.tsv;
		return chunker;
	}

	
}
