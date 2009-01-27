/**
 * 
 */
package de.brightbyte.wikiword.store;

import de.brightbyte.util.PersistenceException;

public interface GroupNameTranslator {
	public String translate(String s) throws PersistenceException;
}