package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.Association;

public interface ConceptAssociationFilter {
	public boolean acceptAssociation(Association assoc);
}
