package de.brightbyte.wikiword.integrator.processor;

import java.util.Collection;
import java.util.List;
import java.util.Set;
import java.util.Map.Entry;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntityFeatureSet;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.FeatureSet.Feature;
import de.brightbyte.wikiword.integrator.store.ForeignPropertyStoreBuilder;

public class ForeignPropertyPassThrough extends AbstractProcessor<ForeignEntityFeatureSet> implements ForeignEntityProcessor<ForeignEntityFeatureSet> {
	protected ForeignPropertyStoreBuilder store;
	protected String qualifier;
	
	public ForeignPropertyPassThrough(ForeignPropertyStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	
	public void processEntites(DataCursor<ForeignEntityFeatureSet> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(ForeignEntityFeatureSet e) throws PersistenceException {
		processForeignEntity(e);
	}
	
	protected  void processForeignEntity(ForeignEntityFeatureSet e) throws PersistenceException {
		for (String prop: e.keys()) {
			Collection<? extends Feature<? extends Object>> features = e.getFeatures(prop);
			
			for ( Feature<? extends Object> feature: features) {
				store.storeProperty(e.getAuthority(), e.getID(), prop, feature.getValue(), feature.getQualifiers());
			}
		}
	}

	public String getQualifier() {
		return qualifier;
	}

	public void setQualifier(String qualifier) {
		this.qualifier = qualifier;
	}

}
