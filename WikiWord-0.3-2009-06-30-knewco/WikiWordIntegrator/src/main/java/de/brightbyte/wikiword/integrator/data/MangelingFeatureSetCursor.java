package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.ConvertingCursor;
import de.brightbyte.data.cursor.DataCursor;

public class MangelingFeatureSetCursor extends ConvertingCursor<FeatureSet, FeatureSet> {

	public MangelingFeatureSetCursor(DataCursor<FeatureSet> source, FeatureSetMangler mangler) {
		super(source, mangler);
	}
	
	public MangelingFeatureSetCursor(DataCursor<FeatureSet> source, FeatureSetMangler... mangler) {
		super(source, new FeatureSetMultiMangler(mangler));
	}
	
}
