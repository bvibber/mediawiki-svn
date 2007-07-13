package org.wikimedia.lsearch.beans;

import java.io.Serializable;

/**
 * The report card distributed indexer sends back to the main indexer.
 * It keeps information if the index update operation has succeeded, etc.. 
 * 
 * @author rainman
 *
 */
public class IndexReportCard implements Serializable {
	protected boolean succDelete;
	protected boolean succAdd;
	protected ReportId id;
	/** host of origin */
	public String host;
	public String senderDbRole;
	
	public IndexReportCard(ReportId id, String host) {
		this.id = id;
		this.host = host;
		this.senderDbRole = id.getIndexId().toString();
	}
	
	public IndexReportCard(ReportId id, String host, String senderDbRole) {
		this.id = id;
		this.host = host;
		this.senderDbRole = senderDbRole;
	}
	
	protected final String bv(boolean value){
		return value? "YES" : "NO"; 
	}
	
	@Override
	public String toString() {
		return "ReportCard (from "+senderDbRole+"@"+host+"): Add:"+bv(succAdd)+", Del: "+bv(succDelete)+" for "+id; 
	}

	public String getHost() {
		return host;
	}
	public void setHost(String host) {
		this.host = host;
	}
	public void setSuccessfulDelete(){
		succDelete = true;
	}
	public void setSuccessfulAdd(){
		succAdd = true;
	}	
	public void setFailedDelete(){
		succDelete = false;
	}
	public void setFailedAdd(){
		succAdd = false;
	}

	public boolean isSuccAdd() {
		return succAdd;
	}

	public boolean isSuccDelete() {
		return succDelete;
	}

	public ReportId getId() {
		return id;
	}
}
