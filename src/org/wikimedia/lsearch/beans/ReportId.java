package org.wikimedia.lsearch.beans;

import java.io.Serializable;

import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;

/** 
 * Identifies system-wise a report about the distributed
 * index update event. 
 * 
 * @author rainman
 *
 */
public class ReportId implements Serializable {
	public int namespace;
	public String title;
	public long timestamp;
	/** string repesentation of index id */
	public String dbrole;
	/** original record for this report */
	transient public IndexUpdateRecord record;
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((dbrole == null) ? 0 : dbrole.hashCode());
		result = PRIME * result + namespace;
		result = PRIME * result + (int) (timestamp ^ (timestamp >>> 32));
		result = PRIME * result + ((title == null) ? 0 : title.hashCode());
		return result;
	}
	
	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (getClass() != obj.getClass())
			return false;
		final ReportId other = (ReportId) obj;
		if (dbrole == null) {
			if (other.dbrole != null)
				return false;
		} else if (!dbrole.equals(other.dbrole))
			return false;
		if (namespace != other.namespace)
			return false;
		if (timestamp != other.timestamp)
			return false;
		if (title == null) {
			if (other.title != null)
				return false;
		} else if (!title.equals(other.title))
			return false;
		return true;
	}
	
	

	public ReportId(int namespace, String title, long timestamp, String dbrole, IndexUpdateRecord record) {
		this.namespace = namespace;
		this.title = title;
		this.timestamp = timestamp;
		this.dbrole = dbrole;
		this.record = record;
	}

	@Override
	public String toString() {
		return namespace+":"+title+" on "+dbrole+" at "+timestamp;
	}
	
	public IndexId getIndexId(){
		return IndexId.get(dbrole);
	}
	
	public Title getTitle(){
		return new Title(namespace,title);
	}
	
	public String getKey(){
		return namespace+":"+title;
	}
	
}
