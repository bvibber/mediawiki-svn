package de.brightbyte.wikiword.output;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ResourceType;

public interface TextOutput extends DataOutput {

	public void storePlainText(int textId, String name, ResourceType ptype,
			String text) throws PersistenceException;

	public void storeRawText(int textId, String name, ResourceType ptype,
			String text) throws PersistenceException;

	public void storeSynopsisText(int textId, String name, ResourceType ptype, 
			String text) throws PersistenceException;

	public void storeDefinitionText(int textId, String name, ResourceType ptype, 
			String text) throws PersistenceException;
}