package org.wikimedia.lsearch.util;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;

/**
 * Extract pairs of rank,title from a lucene index
 * 
 * @author rainman
 *
 */
public class ExtractTitles {
	static Logger log = Logger.getLogger(ExtractTitles.class);
	
	/**
	 * Syntax: 
	 * ExtractTitles <dbname>
	 * 
	 * @throws IOException 
	 */
	public static void main(String[] args) throws IOException {
		Configuration.setVerbose(false);
		GlobalConfiguration.setVerbose(false);
		Configuration.open();
		if(args.length != 1){
			System.out.println("Tool to extract a list of titles and article ranks from latest snapshot");
			System.out.println("Syntax: ExtractTitles <dbname>");
			return;
		}
		String dbname = args[0];
		IndexId iid = IndexId.get(dbname);
		if(iid == null){
			System.out.println("Bad index name: "+dbname);
			return;
		}
		IndexRegistry registry = IndexRegistry.getInstance();
		for(IndexId part : iid.getPhysicalIndexIds()){
			IndexReader ir = IndexReader.open(registry.getLatestSnapshot(part).getPath()); 
			for(int i=0;i<ir.maxDoc();i++){
				if(ir.isDeleted(i))
					continue; // unoptimized index might have deleted docs
				Document d = ir.document(i);
				String ns = d.get("namespace");
				String title = d.get("title");
				String rank = d.get("rank");
				System.out.println(rank+" "+ns+" "+title.replace(" ","_"));
			}
		}

	}

}
