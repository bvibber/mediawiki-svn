package de.brightbyte.wikiword.rdf;

import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLEncoder;
import java.text.MessageFormat;

import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;

public class WikiWordIdentifiers {
	
	public static final URI base;
	
	static {
		try {
			base = new URI("http://brightbyte.de/vocab/wikiword");
		} catch (URISyntaxException e) {
			throw new Error("failed to create http-URI");
		}
	}
	
	private String localDatasetQualifier;
	
	public WikiWordIdentifiers(TweakSet tweaks) {
		this.localDatasetQualifier = tweaks.getTweak("rdf.dataset.qualifier", "*");
	}

	public String getLocalDatasetQualifier() {
		return localDatasetQualifier;
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

	public static String conceptTypeURI(String name) {
		return conceptTypeBaseURI()+name;
	}
	
	public static String resourceTypeURI(String name) {
		return resourceTypeBaseURI()+name;
	}
	
	public static String conceptTypeBaseURI() {
		return base + "/concept-type#";
	}
	
	public static String resourceTypeBaseURI() {
		return base + "/resource-type#";
	}
	
	public String localConceptURI(Corpus corpus, String concept) {
		return localConceptBaseURI(corpus) + localConceptID(concept);
	}
	
	public String globalConceptURI(DatasetIdentifier ds, int concept) {
		return globalConceptBaseURI(ds) + globalConceptID(concept);
	}
	
	public String localConceptBaseURI(Corpus corpus) {
		String u = corpus.getURL().toString();
		if (!u.endsWith("/") && !u.endsWith("#")) u += "/";
		return base + "/concept/from/" + u;
	}
	
	public String globalConceptBaseURI(DatasetIdentifier ds) {
		return base + "/concept/in/" + datasetURI(ds.getCollection(), ds.getName()) + "/";
	}
		
	public static String localConceptID(String concept) {
		return concept;
	}
	
	public static String globalConceptID(int id) {
		return "x" + Integer.toHexString(id);
	}	
	
	/*
	public static URI globalConceptURI(String names) {
		return makeURI(namespace, globalConceptLName(names));
	}
	*/
	public static URL corpusURL(String domain) {
		try {
			return new URL("http://"+domain+"/wiki/");
		} catch (MalformedURLException e) {
			throw new Error("failed to create http-URI for domain "+domain);
		}
	}

	/*
	public static String globalConceptLName(String names) {
		return "concept/"+StringUtils.hex(StringUtils.md5(names));
	}
	*/
	
	public String datasetLName(DatasetIdentifier ds) {
		return datasetLName(ds.getCollection(), ds.getName());
	}
	
	public String datasetURI(DatasetIdentifier ds) {
		return datasetURI(ds.getCollection(), ds.getName());
	}
	
	public String datasetLName(String collection, String name) {
		if (collection==null) collection= "";
		if (name==null || name.length()==0) throw new IllegalArgumentException("bad name: "+name);
		
		String s = "/dataset/";
		s += localDatasetQualifier + "/";
		
		s += collection+":"+name;
		return s; 
	}
	
	public String datasetURI(String collection, String name) {
		return base + datasetLName(collection, name);
	}
		
}
