package org.wikimedia.lsearch.oai;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.Authenticator;
import java.net.URL;
import java.util.ArrayList;

import org.apache.log4j.Logger;
import org.apache.lucene.store.BufferedIndexInput;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;

/**
 * OAI Client. Contacts OAI repo and returns a list of index
 * update records.   
 * 
 * @author rainman
 *
 */
public class OAIHarvester {
	static Logger log = Logger.getLogger(OAIHarvester.class);
	protected String urlbase;
	protected OAIParser parser;
	protected IndexUpdatesCollector collector;
	protected IndexId iid;
	protected String resumptionToken, responseDate;
	
	public OAIHarvester(IndexId iid, String url, Authenticator auth){
		this.urlbase = url;
		this.iid = iid;
		Authenticator.setDefault(auth); 
	}
	
	/** Invoke ListRecords from a certain timestamp */
	public ArrayList<IndexUpdateRecord> getRecords(String from){
		try{
			read(new URL(urlbase+"?verb=ListRecords&metadataPrefix=mediawiki&from="+from));
			return collector.getRecords();
		} catch(IOException e){
			log.warn("I/O exception listing records: "+e.getMessage());
			return null;
		}
	}
	
	protected void read(URL url) throws IOException {
		collector = new IndexUpdatesCollector(iid);
		InputStream in = new BufferedInputStream(url.openStream());
		parser = new OAIParser(in,collector);
		parser.parse();
		resumptionToken = parser.getResumptionToken();
		responseDate = parser.getResponseDate();
		in.close();
	}

	/** Invoke ListRecords using the last resumption token */
	public ArrayList<IndexUpdateRecord> getMoreRecords(){
		try{
			read(new URL(urlbase+"?verb=ListRecords&metadataPrefix=mediawiki&resumptionToken="+resumptionToken));
			return collector.getRecords();
		} catch(IOException e){
			log.warn("I/O exception listing records: "+e.getMessage());
			return null;
		}
	}
	
	public boolean hasMore(){
		return !resumptionToken.equals("");
	}
	
	public String getResponseDate(){
		return responseDate;
	}

}
