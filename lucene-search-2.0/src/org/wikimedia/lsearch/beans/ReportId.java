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
	public long pageId;
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
		result = PRIME * result + (int) (pageId ^ (pageId >>> 32));
		result = PRIME * result + (int) (timestamp ^ (timestamp >>> 32));
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final ReportId other = (ReportId) obj;
		if (dbrole == null) {
			if (other.dbrole != null)
				return false;
		} else if (!dbrole.equals(other.dbrole))
			return false;
		if (pageId != other.pageId)
			return false;
		if (timestamp != other.timestamp)
			return false;
		return true;
	}

	public ReportId(long pageId, long timestamp, String dbrole, IndexUpdateRecord record) {
		this.timestamp = timestamp;
		this.dbrole = dbrole;
		this.record = record;
	}

	@Override
	public String toString() {
		return pageId+" on "+dbrole+" at "+timestamp;
	}
	
	public IndexId getIndexId(){
		return IndexId.get(dbrole);
	}
	
	public String getKey(){
		return Long.toString(pageId);
	}
}
