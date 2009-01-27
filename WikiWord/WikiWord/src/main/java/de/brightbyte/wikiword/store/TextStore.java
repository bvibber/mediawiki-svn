package de.brightbyte.wikiword.store;

import de.brightbyte.util.PersistenceException;

public interface TextStore extends WikiWordStore {
	public String getWikiText(int resourceId) throws PersistenceException;
	public String getWikiText(String resourceName) throws PersistenceException;

	public String getPlainText(int resourceId) throws PersistenceException;
	public String getPlainText(String resourceName) throws PersistenceException;
}
