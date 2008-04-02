package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.io.Serializable;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.ArticleInfo;
import org.wikimedia.lsearch.search.ArticleMeta.ArticleMetaSource;

public class ArticleInfoImpl implements ArticleInfo, Serializable {
	protected transient ArticleMetaSource src = null;
	

	public final void init(IndexReader reader, NamespaceFilter subpages) throws IOException {
		src = ArticleMeta.getCachedSource(reader,subpages);
	}
	
	public final boolean isSubpage(int docid) throws IOException {
		return src.isSubpage(docid);
	}

	public final float daysOld(int docid) throws IOException {
		return src.daysOld(docid);
	}

}
