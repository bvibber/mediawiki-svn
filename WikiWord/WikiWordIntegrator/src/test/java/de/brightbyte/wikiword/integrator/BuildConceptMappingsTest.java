package de.brightbyte.wikiword.integrator;

public class BuildConceptMappingsTest extends IntegratorAppTestBase<BuildConceptMappings> {
	
	public BuildConceptMappingsTest() {
		super("BuildConceptMappingsTest");
		
		dumpActual = true;
		dumpExpected = true;
	}

	//-----------------------------------------------------------------------------------------------------
	public void testPassThrough() throws Exception {
		runApp("passThrough");
	}

	@Override
	protected BuildConceptMappings createApp() {
		return new BuildConceptMappings();
	}

}
