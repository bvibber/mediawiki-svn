package org.wikimedia.lsearch.storage;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;

public class LuceneStorage {
	protected IndexId iid;
	protected IndexWriter writer = null;
	protected IndexReader reader = null;
	protected IndexRegistry registry = IndexRegistry.getInstance();
	protected String path = null; 

	public LuceneStorage(IndexId iid, String path){
		this.path = path;
		this.iid = iid;
	}
	
	public LuceneStorage(IndexId iid){
		this.iid = iid;
	}
	protected void ensureWrite() throws IOException{
		if(writer == null){
			writer = new IndexWriter(iid.getImportPath(), new SplitAnalyzer(1,false), true);
			writer.setMaxBufferedDocs(300);
			writer.setMergeFactor(20);
		}
	}
	
	protected void ensureRead() throws IOException{
		if(reader == null){
			String p = null;
			if(path != null)
				p = path;
			else{
				LocalIndex li = registry.getLatestSnapshot(iid);
				if(li == null)
					throw new IOException("There are no snapshots for "+iid);
				p = li.getPath();
			}			
			reader = IndexReader.open(p); 
		}
	}
	
	public boolean canRead(){
		if(reader != null)
			return true;
		else{
			try{
				ensureRead();
				return true;
			} catch(IOException e){
				return false;
			}
		}
	}
	
	public void snapshot() throws IOException{
		if(writer != null){
			writer.optimize();
			writer.close();
			writer = null;
			IndexThread.makeIndexSnapshot(iid,iid.getImportPath());
			registry.refreshSnapshots(iid);
		}
	}
}
