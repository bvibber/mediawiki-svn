package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.DefaultFeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import junit.framework.TestCase;

public class AssociationTest extends TestCase {

	public void testMerge() {
		FeatureSet aSource = new DefaultFeatureSet("name");
		FeatureSet aProps =  new DefaultFeatureSet("name");
		FeatureSet aTarget = new DefaultFeatureSet("name");
		
		aSource.put("name", "aSource");
		aProps.put("name", "aProps");
		aTarget.put("name", "aTarget");
		
		Association a = new Association(aSource, aProps, aTarget);

		FeatureSet bSource = new DefaultFeatureSet("name");
		FeatureSet bProps =  new DefaultFeatureSet("name");
		FeatureSet bTarget = new DefaultFeatureSet("name");
		
		bSource.put("name", "bSource");
		bProps.put("name", "bProps");
		bTarget.put("name", "bTarget");
		
		Association b = new Association(bSource, bProps, bTarget);
		
		FeatureSet cSource = new DefaultFeatureSet("name");
		FeatureSet cProps =  new DefaultFeatureSet("name");
		FeatureSet cTarget = new DefaultFeatureSet("name");
		
		cSource.put("name", "cSource");
		cProps.put("name", "cProps");
		cTarget.put("name", "cTarget");
		
		Association c = new Association(cSource, cProps, cTarget);

		//---------------------------------------------------
		
		FeatureSet abSource = new DefaultFeatureSet("name");
		FeatureSet abProps =  new DefaultFeatureSet("name");
		FeatureSet abTarget = new DefaultFeatureSet("name");
		
		abSource.put("name", "aSource");
		abProps.put("name", "aProps");
		abTarget.put("name", "aTarget");

		abSource.put("name", "bSource");
		abProps.put("name", "bProps");
		abTarget.put("name", "bTarget");
		
		Association ab = new Association(abSource, abProps, abTarget);
		
		assertEquals(ab, Association.merge(a, b));

		//---------------------------------------------------

		FeatureSet abcSource = new DefaultFeatureSet("name");
		FeatureSet abcProps =  new DefaultFeatureSet("name");
		FeatureSet abcTarget = new DefaultFeatureSet("name");
		
		abcSource.put("name", "aSource");
		abcProps.put("name", "aProps");
		abcTarget.put("name", "aTarget");
		
		abcSource.put("name", "bSource");
		abcProps.put("name", "bProps");
		abcTarget.put("name", "bTarget");
		
		abcSource.put("name", "cSource");
		abcProps.put("name", "cProps");
		abcTarget.put("name", "cTarget");
		
		Association abc = new Association(abcSource, abcProps, abcTarget);
		
		assertEquals(abc, Association.merge(a, b, c));
	}

}
