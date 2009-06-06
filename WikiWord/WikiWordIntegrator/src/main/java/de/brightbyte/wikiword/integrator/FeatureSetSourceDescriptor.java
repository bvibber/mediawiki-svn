package de.brightbyte.wikiword.integrator;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.List;
import java.util.Map;

import de.brightbyte.text.Chunker;
import de.brightbyte.wikiword.TweakSet;

public class FeatureSetSourceDescriptor extends TweakSet {
	
	public FeatureSetSourceDescriptor() {
		this(null, null);
	}

	public FeatureSetSourceDescriptor(String prefix, TweakSet parent) {
		super(prefix, parent);
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
		return getTweak("query", null);
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
	

	public Map<String, Chunker> getDataFieldChunkers() { //FIXME: factory/parser!
		return getTweak("foreign.chunkers", (Map<String, Chunker>)null);
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

	
}
