package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


public class FeatureSetMultiMangler implements FeatureSetMangler {

	protected List<FeatureSetMangler> manglers;
	
	public FeatureSetMultiMangler(FeatureSetMangler... manglers) {
		this(new ArrayList<FeatureSetMangler>(Arrays.asList(manglers))); //NOTE: must be a modifyable list
	}
	
	public FeatureSetMultiMangler(List<FeatureSetMangler> manglers) {
		this.manglers = manglers;
	}
	
	public void addMangler(FeatureSetMangler m) {
		manglers.add(m);
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((manglers == null) ? 0 : manglers.hashCode());
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
		final FeatureSetMultiMangler other = (FeatureSetMultiMangler) obj;
		if (manglers == null) {
			if (other.manglers != null)
				return false;
		} else if (!manglers.equals(other.manglers))
			return false;
		return true;
	}

	public FeatureSet apply(FeatureSet fts) {
		for (FeatureSetMangler m: manglers) {
			fts = m.apply(fts);
		}
		
		return fts;
	}

}
