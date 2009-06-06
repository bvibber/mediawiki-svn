package de.brightbyte.wikiword.integrator;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.List;
import java.util.Map;

import de.brightbyte.wikiword.TweakSet;

public class ForeignEntityStoreDescriptor extends TweakSet {
	
	public ForeignEntityStoreDescriptor() {
		super();
	}

	public ForeignEntityStoreDescriptor(TweakSet parent) {
		super(parent);
	}

	public String getDataEncoding() {
		return getTweak("foreign.encoding", "UTF-8");
	}

	public String getSqlQuery() {
		return getTweak("foreign.query", null);
	}

	public String getSourceFileName() { //FIXME
		return getTweak("foreign.file", null);
	}

	public String[] getDataFields() {
		List<String> v = getTweak("foreign.fields", (List<String>)null);
		if (v==null) return null;
		return (String[]) v.toArray(new String[v.size()]);
	}

	public Map<String, String> getSplitExpressions() { //FIXME:!
		return getTweak("split", (Map<String, String>)null);
	}

	public String getPropertyValueField() {
		return getTweak("foreign.property-value-field", null);
	}

	public String getPropertyNameField() {
		return getTweak("foreign.property-name-field", null);
	}

	public String getConceptIdField() {
		return getTweak("foreign.concept-id-field", null);
	}

	public String getConceptNameField() {
		return getTweak("foreign.concept-name-field", "name");
	}

	public String getAuthorityName() {
		String name = getTweak("foreign.authority", null);
		if (name==null) throw new RuntimeException("authority name not specified!");
		return name;
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
	
}
