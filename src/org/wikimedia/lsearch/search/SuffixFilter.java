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
	
}
