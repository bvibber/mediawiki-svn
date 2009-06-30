package de.brightbyte.wikiword;

import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URLEncoder;
import java.text.MessageFormat;

/**
 * Utility class, representing the WikiWord RDF vocabulary.
 */
public class RdfEntities {
	
	public static final URI wikiwordBase;
	public static final URI resourceTypeBase;
	public static final URI conceptTypeBase;

	public static final URI concept;
	public static final URI resource;
	
	/*
	public static final URI termRefersTo;
	public static final URI conceptReferences;
	public static final URI conceptIsBroader;
	public static final URI conceptIsEquivalent;
	public static final URI describedIn;
	*/
	
	public static final String corpusPattern = "http://{0}";
	public static final String resourcePattern = "http://{0}/wiki/{1}";
	public static final String localConceptPattern = "http://brightbyte.de/vocab/wikiword/concept/local-{0}/{1}";
	public static final String globalConceptPattern = "http://brightbyte.de/vocab/wikiword/concept/global/{0}";
	
	
	static {
		try {
			wikiwordBase = new URI("http://brightbyte.de/vocab/wikiword#");
			resourceTypeBase = new URI(wikiwordBase+"ResourceType");
			conceptTypeBase = new URI(wikiwordBase+"ConceptType");

			concept = new URI(wikiwordBase+"Concept");
			resource = new URI(wikiwordBase+"Resource");

			/*
			termRefersTo = new URI(wikiwordBase+"termRefersTo");
			conceptReferences = new URI(wikiwordBase+"conceptReferences");
			conceptIsBroader = new URI(wikiwordBase+"conceptIsBroader");
			conceptIsEquivalent = new URI(wikiwordBase+"conceptIsEquivalent");
			describedIn = new URI(wikiwordBase+"describedIn");
			*/
		}
		catch (URISyntaxException ex) {
			throw new Error(ex);
		}
	}

	public static URI makeURI(URI base, String name) {
		try {
			return base == null ? new URI(name) : new URI(base+name);
		} catch (URISyntaxException e) {
			throw new IllegalArgumentException("bad uri: "+base+name, e);
		}
	}

	public static URI makeURI(String pattern, String... args) {
		for (int i= 0; i<args.length; i++) {
			try {
				args[i] = URLEncoder.encode(args[i], "UTF-8");
			} catch (UnsupportedEncodingException e) {
				throw new Error("UTF-8 not supported");
			}
		}
		
		String u = MessageFormat.format(pattern, (Object[])args);
		
		try {
			return new URI( u );
		} catch (URISyntaxException e) {
			throw new IllegalArgumentException("bad uri: "+u, e);
		}
	}

	public static URI makeCorpusURI(String corpus) {
		return makeURI(corpusPattern, corpus);
	}

	public static URI makeResourceURI(String corpus, String resource) {
		return makeURI(resourcePattern, corpus, resource);
	}

	public static URI makeLocalConceptURI(String corpus, String concept) {
		return makeURI(localConceptPattern, corpus, concept);
	}

	public static URI makeGlobalConceptURI(int concept) { //FIXME: id sucks, but we don't have a good name either!
		return makeURI(globalConceptPattern, String.valueOf(concept));
	}

}
