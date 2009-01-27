package de.brightbyte.wikiword.rdf;

import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfNamespace;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.wikiword.Corpus;

/**
 * Dublin Core vocabulary, as defined by http://dublincore.org/documents/dcmi-terms/
 */
public class WP<V, R extends V> implements RdfNamespace<V, R> {
	public final String namespace;
	public final String prefix;
 
	public WP(RdfPlatform<V, R, ?, ?> platform, Corpus c) throws RdfException {
		this.namespace = c.getURL().toString();
		this.prefix = c.getLanguage() + "wp";
	}

	public String getNamespace() {
		return namespace;
	}

	public String getPrefix() {
		return prefix;
	}
	
	@Override
	public String toString() {
		return getNamespace();
	}
}
