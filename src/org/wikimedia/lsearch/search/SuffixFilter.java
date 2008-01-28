package org.wikimedia.lsearch.search;

import java.io.Serializable;

public class SuffixFilter implements Serializable {
	protected String excludeSuffix;

	public SuffixFilter(String excludeSuffix) {
		this.excludeSuffix = excludeSuffix;
	}

	public String getExcludeSuffix() {
		return excludeSuffix;
	}

	public void setExcludeSuffix(String excludeSuffix) {
		this.excludeSuffix = excludeSuffix;
	}
	
	public String toString(){
		return "exclude:"+excludeSuffix;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((excludeSuffix == null) ? 0 : excludeSuffix.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final SuffixFilter other = (SuffixFilter) obj;
		if (excludeSuffix == null) {
			if (other.excludeSuffix != null)
				return false;
		} else if (!excludeSuffix.equals(other.excludeSuffix))
			return false;
		return true;
	}
	
}
