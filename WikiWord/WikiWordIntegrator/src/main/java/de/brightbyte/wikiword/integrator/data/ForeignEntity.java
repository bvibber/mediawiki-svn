package de.brightbyte.wikiword.integrator.data;

public interface ForeignEntity {
	public String getAuthority();
	public String getID();
	public String getName();
	public FeatureSet getProperties();
}
