package de.brightbyte.wikiword.integrator.data;

public interface ForeignEntityRecord extends Record, ForeignEntity {
	public String getAuthorityField();
	public String getIDField();
	public String getNameField();
}
