package de.brightbyte.wikiword.integrator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.FilteredAssociationCursor;
import de.brightbyte.wikiword.integrator.data.filter.ConceptAssociationFilter;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;

public class FilterConceptAssociations extends BuildConceptAssociations {
	
	@Override
	protected void run() throws Exception {
		AssociationFeatureStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		DataCursor<Association> cursor = new FilteredAssociationCursor(asc, createAssociationFilter(sourceDescriptor));
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processAssociations(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	
	protected ConceptAssociationFilter createAssociationFilter(FeatureSetSourceDescriptor sourceDescriptor) {
		throw new UnsupportedOperationException("no implementations of ConceptAssociationFilter are implemented yet."); //TODO: implement filters
	}


	public static void main(String[] argv) throws Exception {
		FilterConceptAssociations app = new FilterConceptAssociations();
		app.launch(argv);
	}
}