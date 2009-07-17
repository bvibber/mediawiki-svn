package de.brightbyte.wikiword.integrator.processor;

import java.util.List;
import java.util.Set;
import java.util.Map.Entry;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;
import de.brightbyte.wikiword.integrator.store.ForeignPropertyStoreBuilder;

public class ForeignPropertyPassThrough extends AbstractForeignEntityProcessor {
	protected ForeignPropertyStoreBuilder store;
	protected String qualifier;
	
	public ForeignPropertyPassThrough(ForeignPropertyStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	@Override
	protected  void processForeignEntity(ForeignEntity e) throws PersistenceException {
		Set<Entry<String, List<Object>>> entries = e.getProperties().entrySet();
		for (Entry<String, List<Object>> p: entries) {
			String prop = p.getKey();
			List<Object> vv = p.getValue();
			
			for (Object v: vv) {
				store.storeProperty(e.getAuthority(), e.getID(), prop, String.valueOf(v), qualifier);
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
