package de.brightbyte.wikiword;

import java.net.URL;
import java.util.Date;

public class RevisionInfo {
	private final int pageId;
	private final int revisionId;
	private final String pageTitle;
	private final Date revisionTimestamp;
	private final int namespace;
	private final Corpus corpus;
	
	public RevisionInfo(final Corpus corpus, final int pageId, final int revisionId, final Date revisionTimestamp, final String pageTitle, final int namespace) {
		super();
		this.pageId = pageId;
		this.revisionId = revisionId;
		this.pageTitle = pageTitle;
		this.revisionTimestamp = revisionTimestamp;
		this.corpus = corpus;
		this.namespace = namespace;
	}

	public Corpus getCorpus() {
		return corpus;
	}

	public int getPageId() {
		return pageId;
	}
	
	public String getPageTitle() {
		return pageTitle;
	}
	
	public int getRevisionId() {
		return revisionId;
	}
	
	public Date getRevisionTimestamp() {
		return revisionTimestamp;
	}
	
	public int getNamespace() {
		return namespace;
	}

	public String toString() {
		return getPageTitle(); 
	}
	
	public URL getPageURL() {
		return corpus.getResourceURL(getPageTitle()); 
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((corpus == null) ? 0 : corpus.hashCode());
		result = PRIME * result + pageId;
		result = PRIME * result + revisionId;
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
		final RevisionInfo other = (RevisionInfo) obj;
		if (corpus == null) {
			if (other.corpus != null)
				return false;
		} else if (!corpus.equals(other.corpus))
			return false;
		if (pageId != other.pageId)
			return false;
		if (revisionId != other.revisionId)
			return false;
		return true;
	}
	
	
}
