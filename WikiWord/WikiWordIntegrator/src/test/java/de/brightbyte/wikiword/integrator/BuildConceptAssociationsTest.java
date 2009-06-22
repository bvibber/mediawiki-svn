package de.brightbyte.wikiword.integrator;

public class BuildConceptAssociationsTest extends IntegratorAppTestBase<BuildConceptAssociations> {
	
	public BuildConceptAssociationsTest() {
		super("BuildConceptAssociationsTest");
		
		dumpActual = true;
		dumpExpected = true;
	}

	//-----------------------------------------------------------------------------------------------------
	public void testMatchTerms() throws Exception {
		runApp("matchTerms");
	}

	@Override
	protected BuildConceptAssociations createApp() {
		return new BuildConceptAssociations();
	}

}
