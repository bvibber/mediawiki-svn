package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;
import java.util.TimeZone;
import java.util.WeakHashMap;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.store.Directory;

/**
 * Article meta information fetched from multiple fields, like
 * how old the page is, if it's a subpage, etc.. 
 * 
 * @author rainman
 *
 */
public class ArticleMeta {
	static Logger log = Logger.getLogger(AggregateMetaField.class);
	/** directory -> cache */
	protected static WeakHashMap<Directory,ArticleMetaSource> cache = new WeakHashMap<Directory,ArticleMetaSource>();
	protected static Object lock = new Object();
	/** directory -> fields */
	protected static WeakHashMap<Directory,Boolean> cachingInProgress = new WeakHashMap<Directory,Boolean>();
	
	/** Check if there is a current background caching on a reader */
	public static boolean isBeingCached(IndexReader reader){
		synchronized(cachingInProgress){
			return cachingInProgress.containsKey(reader.directory());
		}
	}
	
	public static void invalidateCache(IndexReader reader){
		synchronized (lock) {
			cache.remove(reader.directory());
		}
	}
	public static CacheBuilder getCacherBuilder(IndexReader reader, NamespaceFilter subpages) throws IOException {
		synchronized (lock) {
			ArticleMetaSource src = cache.get(reader.directory());
			if(src != null)
				return null; // already cached 
			src = new ArticleMetaSource(reader,subpages);
			cache.put(reader.directory(),src);
			return src;			
		}
	}
	
	public static ArticleMetaSource getCachedSource(IndexReader reader) throws IOException{
		synchronized(lock) {
			return cache.get(reader.directory());
		}
	}

	public static class ArticleMetaSource implements CacheBuilder {
		protected boolean[] subpage = null;
		protected float[] daysOld = null;
		protected IndexReader reader = null;
		protected boolean finishedCaching = false;
		protected SimpleDateFormat isoDate;
		protected long now = 0;
		protected NamespaceFilter subpages;
		protected boolean isOptimized;
		
		public void init() {
			subpage = new boolean[reader.maxDoc()];
			daysOld = new float[reader.maxDoc()];
			
			synchronized (cachingInProgress) {
				cachingInProgress.put(reader.directory(),true);
			}
			
		}
		
		public void cache(int i, Document doc) throws IOException {
			try{
				if(!isOptimized && reader.isDeleted(i))
					return;
					Document d = reader.document(i);
					subpage[i] = resolveSubpage(d);	
					daysOld[i] = resolveDaysOld(d);
			} catch(Exception e){
				String ext = "";
				if(doc != null)
					ext = ", ns="+doc.get("namespace")+", title="+doc.get("title");
				log.error("Exception during caching of article info for docid="+i+ext);
				e.printStackTrace();
				throw new IOException(e);
			}
			
		}
		public void end() {
			synchronized (cachingInProgress) {
				cachingInProgress.remove(reader.directory());
			}
			finishedCaching = true;
			
		}
		
		/** See if article is a subpage 
		 * @throws IOException 
		 * @throws CorruptIndexException */
		protected final boolean resolveSubpage(Document d) throws IOException{			
			String ns = d.get("namespace");
			if(ns == null)
				return false;
			if(!subpages.contains(Integer.parseInt(ns)))
				return false;
			else{
				String title = d.get("title");
				if(title.contains("/"))
					return true;
			}
			return false;
		}
		/** Calculate how old the indexed article is */
		protected final float resolveDaysOld(Document d) throws IOException {
			String dateStr = d.get("date");
			if(dateStr == null)
				return 0;
			try {
				Date date = null;
				float parsed = 0;
				float diff = 0;
				synchronized(isoDate){
					date = isoDate.parse(dateStr);
					parsed = date.getTime();
				}
				diff = (float)(now - parsed) / (float)(1000*60*60*24);
				if(diff < 0){
					log.warn("Got diff<0 on "+dateStr+" now="+now+", parsed="+parsed);
					return 0;
				}
				return diff;
			} catch (ParseException e) {
				e.printStackTrace();
				log.error("Error parsing date "+dateStr+" : "+e.getMessage());
			}
			return 0;
		}
		
		public ArticleMetaSource(IndexReader reader, NamespaceFilter subpages){
			this.reader = reader;
			this.now = System.currentTimeMillis();
			this.subpages = subpages;
			isoDate = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
			isoDate.setTimeZone(TimeZone.getTimeZone("GMT"));
			this.isOptimized = reader.isOptimized();
			
		}

		public final boolean isSubpage(int docid) throws IOException {
			if(!finishedCaching)
				return resolveSubpage(reader.document(docid));
			
			return subpage[docid];
		}

		public float daysOld(int docid) throws IOException {
			if(!finishedCaching)
				return resolveDaysOld(reader.document(docid));
			
			return daysOld[docid];
		}

		
	}
}
