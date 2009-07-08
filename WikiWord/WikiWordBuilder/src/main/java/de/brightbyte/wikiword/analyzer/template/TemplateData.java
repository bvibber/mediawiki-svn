/**
 * 
 */
package de.brightbyte.wikiword.analyzer.template;

import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

public class TemplateData {
	protected Map<CharSequence, CharSequence> params;
	protected int paramCounter = 0;
	
	protected String name;
	
	public TemplateData(String name) {
		this.name = name;
	}
	
	public String getName() {
		return name;
	}
	
	public Map<CharSequence, CharSequence> getParameters() {
		if (params==null) return Collections.emptyMap();
		else return params; //XXX: unmodifiable?...
	}
	
	public CharSequence getParameter(CharSequence key) {
		if (params == null) return null;
		else return params.get(key);
	}
	
	public void setParameter(CharSequence key, CharSequence value) {
		if (params==null) params = new HashMap<CharSequence, CharSequence>();
		params.put(key, value);
	}
	
	public void addParameter(CharSequence value) {
		paramCounter++;
		setParameter(String.valueOf(paramCounter), value);
	}
	
	@Override
	public String toString() {
		return getName() + ": " + (params==null ? Collections.emptyMap().toString() : params.toString());
	}
	
	public CharSequence nextParameterName() {
		return String.valueOf(paramCounter+1);
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((params == null) ? 0 : params.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (!(obj instanceof TemplateData))
			return false;
		final TemplateData other = (TemplateData) obj;
		if (params == null) {
			if (other.params != null)
				return other.params.size()==0;
		} else if (!params.equals(other.params))
			return false;
		return true;
	}
	
	
}