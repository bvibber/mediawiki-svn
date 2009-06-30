package de.brightbyte.wikiword.integrator;

public class FilterConceptMappingsTest extends IntegratorAppTestBase<FilterConceptMappings> {
	
	public FilterConceptMappingsTest() {
		super("FilterConceptMappingsTest");
		
		dumpActual = true;
		dumpExpected = true;
	}

	//-----------------------------------------------------------------------------------------------------
	public void testSelectOptimum() throws Exception {
		runApp("selectOptimum");
	}

	@Override
	protected FilterConceptMappings createApp() {
		return new FilterConceptMappings();
	}

}
