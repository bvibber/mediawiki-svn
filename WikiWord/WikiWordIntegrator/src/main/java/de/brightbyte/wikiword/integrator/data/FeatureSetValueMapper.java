package de.brightbyte.wikiword.integrator.data;

public class FeatureSetValueMapper implements FeatureSetMangler {

	protected FeatureMapping mapping;
	
	
	public FeatureSet apply(FeatureSet features) {
		FeatureSet ft = new DefaultFeatureSet();
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(features, f, null); //XXX: extra type conversion?!
			
			ft.put(f, v);
		}
		
		return ft;
	}

}
