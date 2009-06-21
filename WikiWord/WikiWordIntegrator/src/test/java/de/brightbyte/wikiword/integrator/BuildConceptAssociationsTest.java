package de.brightbyte.wikiword.integrator;


public class BuildConceptAssociationsTest extends IntegratorAppTestBase<BuildConceptAssociations> {
	
	public BuildConceptAssociationsTest() {
		super("BildConceptAssociations");
	}

	//-----------------------------------------------------------------------------------------------------
	public void testTableImport() throws Exception {
		runApp("tableImport");
	}

	@Override
	protected BuildConceptAssociations createApp() {
		return new BuildConceptAssociations();
	}

}
