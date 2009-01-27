package de.brightbyte.wikiword.rdf;

import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.RdfNamespace;
import de.brightbyte.wikiword.rdf.WikiWordIdentifiers;

/**
 * Dublin Core vocabulary, as defined by http://dublincore.org/documents/dcmi-terms/
 */
public class WW<V, R extends V> implements RdfNamespace<V, R> {
	public static final String namespace = WikiWordIdentifiers.base+"#";
	
	public final String prefix;
 
	public final R type;
	
	public final R similar;
	public final R assoc;

	public final R displayLabel;

	public final R score;
	
	public final R idfScore;
	public final R lhsScore;
	public final R inDegree;
	public final R outDegree;
	public final R linkDegree;

	public final R idfRank;
	public final R lhsRank;
	public final R inRank;
	public final R outRank;
	public final R linkRank;
	
    public WW(RdfPlatform<V, R, ?, ?> platform) throws RdfException {
		this(platform, "ww");
	}
	
	public WW(RdfPlatform<V, R, ?, ?> platform, String prefix) throws RdfException {
		this.prefix = prefix;
		
		type = platform.newResource(namespace, "type");
		similar = platform.newResource(namespace, "similar");
		assoc = platform.newResource(namespace, "assoc");
		displayLabel = platform.newResource(namespace, "displayLabel");
		score = platform.newResource(namespace, "score");
		idfScore = platform.newResource(namespace, "idfScore");
		lhsScore = platform.newResource(namespace, "lhsScore");
		inDegree = platform.newResource(namespace, "inDegree");
		outDegree = platform.newResource(namespace, "outDegree");
		linkDegree = platform.newResource(namespace, "linkDegree");
		idfRank = platform.newResource(namespace, "idfRank");
		lhsRank = platform.newResource(namespace, "lhsRank");
		inRank = platform.newResource(namespace, "inRank");
		outRank = platform.newResource(namespace, "outRank");
		linkRank = platform.newResource(namespace, "linkRank");
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
