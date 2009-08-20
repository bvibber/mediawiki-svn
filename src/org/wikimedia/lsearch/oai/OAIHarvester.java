package org.wikimedia.lsearch.oai;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.Authenticator;
import java.net.MalformedURLException;
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
	protected String host;
	/** number of retries before giving up, useful when there are broken servers in the cluster */
	protected int retries = 5;
	
	public OAIHarvester(IndexId iid, String url, Authenticator auth) throws MalformedURLException{
		this.urlbase = url;
		this.iid = iid;
		URL base = new URL(url);
		this.host = base.getHost();
		log.info(iid+" using base url: "+url);
		Authenticator.setDefault(auth); 
	}
	
	/** Invoke ListRecords from a certain timestamp, fetching at least records..  */
	public ArrayList<IndexUpdateRecord> getRecords(String from, int atLeast) throws IOException {
		ArrayList<IndexUpdateRecord> ret = new ArrayList<IndexUpdateRecord>();
		read(new URL(urlbase+"&verb=ListRecords&metadataPrefix=mediawiki&from="+from));
		ret.addAll(collector.getRecords());
		if(ret.size() < atLeast && hasMore())
			ret.addAll( getMoreRecords(atLeast - ret.size()) );
		
		return ret;
	}		
	
	/** Get single record */
	public ArrayList<IndexUpdateRecord> getRecord(String key) throws IOException {
		// sample key: oai:localhost:wikilucene:25139
		String id = "oai:"+host+":"+iid.getDBname()+":"+key;
		read(new URL(urlbase+"&verb=GetRecord&metadataPrefix=mediawiki&identifier="+id));
		return collector.getRecords();
	}
	
	protected void read(URL url) throws IOException {
		log.info("Reading records from "+url);
		// try reading from url a number of times before giving up
		for(int tryNum = 1; tryNum <= this.retries; tryNum++){
			try{
				collector = new IndexUpdatesCollector(iid);
				InputStream in = new BufferedInputStream(url.openStream());
				parser = new OAIParser(in,collector);
				parser.parse();
				resumptionToken = parser.getResumptionToken();
				responseDate = parser.getResponseDate();
				in.close();
				break;
			} catch(IOException e){				
				if(tryNum == this.retries)
					throw e;
				else
					log.warn("Error reading from url (will retry): "+url);
			}
		}
	}

	/** Invoke ListRecords using the last resumption token, get atLeast num of records */
	public ArrayList<IndexUpdateRecord> getMoreRecords(int atLeast){
		ArrayList<IndexUpdateRecord> ret = new ArrayList<IndexUpdateRecord>();
		try{			
			do{
				read(new URL(urlbase+"&verb=ListRecords&metadataPrefix=mediawiki&resumptionToken="+resumptionToken));
				ret.addAll(collector.getRecords());
			} while(hasMore() && ret.size() < atLeast);
		} catch(IOException e){
			log.warn("I/O exception listing records: "+e.getMessage(),e);
			return null;
		}
		return ret;
	}
	
	public boolean hasMore(){
		return !resumptionToken.equals("");
	}
	
	public String getResponseDate(){
		return responseDate;
	}

}
