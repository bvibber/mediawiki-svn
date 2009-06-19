package de.brightbyte.wikiword.integrator;


public class LoadForeignPropertiesTest extends IntegratorAppTestBase<LoadForeignProperties> {
	
	public LoadForeignPropertiesTest() {
		super("LoadForeignPropertiesTest");
	}

	/*
	protected String[] getSetUpStatements() {
		return new String[] { "CREATE TABLE QUUXBASE ( foo INT NOT NULL, bar VARCHAR(32) )",
				"CREATE TABLE QUUX ( foo INT NOT NULL, bar VARCHAR(32) )" };
	}

	protected String[] getTearDownStatements() {
		return new String[] { "DROP TABLE QUUXBASE", "DROP TABLE QUUX" };
	}
	*/
	
	//-----------------------------------------------------------------------------------------------------
	public void testTableImport() throws Exception {
		runApp("tableImport", "external_authority", "external_id", "property", "value", "qualifier");
	}

	public void testTripleImport() throws Exception {
		runApp("tripleImport", "external_authority", "external_id", "property", "value", "qualifier");
	}

	@Override
	protected LoadForeignProperties createApp() {
		return new LoadForeignProperties();
	}

}
