package de.brightbyte.wikiword.integrator;

public class BuildConceptMappingsTest extends IntegratorAppTestBase<BuildConceptMappings> {
	
	public BuildConceptMappingsTest() {
		super("BuildConceptMappingsTest");
		
		dumpActual = true;
		dumpExpected = true;
	}

	//-----------------------------------------------------------------------------------------------------
	public void testMatchTerms() throws Exception {
		runApp("selectOptimum");
	}

	@Override
	protected BuildConceptMappings createApp() {
		return new BuildConceptMappings();
	}

}
