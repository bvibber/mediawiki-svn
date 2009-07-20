package de.brightbyte.wikiword.integrator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.FilteredAssociationCursor;
import de.brightbyte.wikiword.integrator.store.ConceptAssociationStoreBuilder;

public class FilterConceptAssociations extends BuildConceptAssociations {
	
	@Override
	protected void run() throws Exception {
		ConceptAssociationStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		//FIXME: wia mapping cursor, for filtering!
		
		DataCursor<Association> cursor = new FilteredAssociationCursor(asc, createAssociationFilter(sourceDescriptor));
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processAssociations(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	
	protected Filter<Association> createAssociationFilter(FeatureSetSourceDescriptor sourceDescriptor) {
		throw new UnsupportedOperationException("no implementations of ConceptAssociationFilter are implemented yet."); //TODO: implement filters
	}


	public static void main(String[] argv) throws Exception {
		FilterConceptAssociations app = new FilterConceptAssociations();
		app.launch(argv);
	}
}