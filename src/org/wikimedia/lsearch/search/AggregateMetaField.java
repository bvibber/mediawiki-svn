package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;
import java.util.StringTokenizer;
import java.util.WeakHashMap;

import javax.print.attribute.standard.Finishings;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexReader.FieldOption;
import org.apache.lucene.store.Directory;
import org.wikimedia.lsearch.analyzers.Aggregate.Flags;

/**
 * Local cache of aggregate field meta informations
 * 
 * @author rainman
 *
 */
public class AggregateMetaField {
	static Logger log = Logger.getLogger(AggregateMetaField.class);
	/** reader -> field -> meta (tie cache to directory, since lucene ensures there is one directory instance per path) */
	protected static WeakHashMap<Directory,HashMap<String,AggregateMetaFieldSource>> cache = new WeakHashMap<Directory,HashMap<String,AggregateMetaFieldSource>>();
	protected static Object lock = new Object();
	/** directory -> fields */
	protected static WeakHashMap<Directory,Set<String>> cachingInProgress = new WeakHashMap<Directory,Set<String>>();
	
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
	
	/** Get a meta cacher, return null if field is already cached or being cached */
	public static CacheBuilder getCacherBuilder(IndexReader reader, String field) throws IOException {
		synchronized(lock){
			HashMap<String,AggregateMetaFieldSource> fields = cache.get(reader.directory());
			if(fields == null){
				fields = new HashMap<String,AggregateMetaFieldSource>();
				cache.put(reader.directory(),fields);
			}
			AggregateMetaFieldSource s = fields.get(field);
			if(s != null) // already cached
				return null;
			else{
				s = new AggregateMetaFieldSource(reader,field);
				fields.put(field,s);
				return s;
			}		
		}
	}
	
	/** Get a cached meta source to use in queries */
	public static AggregateMetaFieldSource getCachedSource(IndexReader reader, String field) {
		synchronized(lock){
			// this will produce exception if stuff is not properly cached at index deployment
			return cache.get(reader.directory()).get(field);
		}
	}

	
	/**
	 * Cached meta aggregate info 
	 * 
	 * @author rainman
	 *
	 */
	static public class AggregateMetaFieldSource implements CacheBuilder {
		protected int[] index = null;
		protected byte[] length  = null;
		protected byte[] lengthNoStopWords = null;
		protected byte[] lengthComplete = null;
		protected float[] boost  = null;
		protected byte[] flags = null;
		protected byte[] namespaces = null;
		protected IndexReader reader = null;
		protected String field;
		protected boolean cachingFinished = false;
		protected boolean isOptimized;
		// temporary: 
		protected int count = 0;
		protected int maxdoc = 0;
		
		public void init() {
			synchronized(cachingInProgress){
				Set<String> set = cachingInProgress.get(reader.directory());
				if(set == null){
					set = new HashSet<String>();
					cachingInProgress.put(reader.directory(),set);
				}
				if(set.contains(field))
					return;
				set.add(field);
			}
			maxdoc = reader.maxDoc();
			index = new int[maxdoc];
			length = new byte[maxdoc]; // estimate maxdoc values
			lengthNoStopWords = new byte[maxdoc];
			lengthComplete = new byte[maxdoc];			
			boost = new float[maxdoc];
			flags = new byte[maxdoc];
			namespaces = new byte[maxdoc];
		}

		public void cache(int i, Document doc) throws IOException {
			byte[] stored = null;
			try{
				if(!isOptimized && reader.isDeleted(i))
					return;
				stored = doc.getBinaryValue(field);
				namespaces[i] = (byte)Integer.parseInt(doc.get("namespace"));
				index[i] = count;
				if(stored == null)
					return;
				for(int j=0;j<stored.length/8;j++){
					if(count >= length.length){
						length = extendBytes(length);
						lengthNoStopWords = extendBytes(lengthNoStopWords);
						lengthComplete = extendBytes(lengthComplete);						
						boost = extendFloats(boost);
						flags = extendBytes(flags);
					}						
					length[count] = stored[j*8];
					if(length[count] == 0){
						log.debug("Broken length=0 for docid="+i+", at position "+j);
					}
					lengthNoStopWords[count] = stored[j*8+1];
					int boostInt = (((stored[j*8+2]&0xff) << 24) + ((stored[j*8+3]&0xff) << 16) + ((stored[j*8+4]&0xff) << 8) + ((stored[j*8+5]&0xff) << 0));
					boost[count] = Float.intBitsToFloat(boostInt);
					lengthComplete[count] = stored[j*8+6];
					flags[count] = stored[j*8+7];
					count++;
				}										
			} catch(Exception e){
				log.error("Exception during processing stored_field="+field+" on docid="+i+", with stored="+stored+" : "+e.getMessage());
				e.printStackTrace();
				throw new IOException(e);
			}
		}
		
		public void end(){
			if(count < length.length - 1){
				length = resizeBytes(length,count);
				lengthNoStopWords = resizeBytes(lengthNoStopWords,count);
				boost = resizeFloats(boost,count);
				lengthComplete = resizeBytes(lengthComplete,count);
				flags = resizeBytes(flags,count);
			}
			cachingFinished = true;
			
			synchronized(cachingInProgress){
				Set<String> set = cachingInProgress.get(reader.directory());
				set.remove(field);
				if(set.size() == 0)
					cachingInProgress.remove(reader.directory());
			}
		}
		
		protected byte[] extendBytes(byte[] array){
			return resizeBytes(array,array.length*2);
		}
		protected byte[] resizeBytes(byte[] array, int size){
			byte[] t = new byte[size];
			System.arraycopy(array,0,t,0,Math.min(array.length,size));
			return t;
		}
		protected float[] extendFloats(float[] array){
			return resizeFloats(array,array.length*2);
		}		
		protected float[] resizeFloats(float[] array, int size){
			float[] t = new float[size];
			System.arraycopy(array,0,t,0,Math.min(array.length,size));
			return t;
		}
		
		protected AggregateMetaFieldSource(IndexReader reader, String fieldBase) throws IOException{
			this.reader = reader;
			this.field = fieldBase+"_meta";
			this.isOptimized = reader.isOptimized();
		}
		protected int getValueIndex(int docid, int position){
			return getValueIndex(docid,position,false);
		}
		protected int getValueIndex(int docid, int position, boolean checkExists){
			int start = index[docid];
			int end = (docid == index.length-1)? length.length : index[docid+1];
			if(position >= end-start){
				if(checkExists) // if true this is not an error
					return -1; 
				else
					throwException(docid,position,end-start-1);
			}
			return start+position;
		}
		
		private void throwException(int docid, int position, int lastValid){
			try {
				// first try to give more detailed error
				throw new ArrayIndexOutOfBoundsException("Requestion position "+position+" on field "+field+" for "+docid+" ["+reader.document(docid).get("namespace")+":"+reader.document(docid).get("title")+"], but last valid index is "+lastValid);
			} catch (IOException e) {
				e.printStackTrace();
				throw new ArrayIndexOutOfBoundsException("Requestion position "+position+" on field "+field+" unavailable");
			}			
		}
		
		protected byte[] getStored(int docid) throws CorruptIndexException, IOException{
			return reader.document(docid).getBinaryValue(field);
		}
		
		/** Get length of nonalias tokens */ 
		public int getLength(int docid, int position) throws CorruptIndexException, IOException{
			if(!cachingFinished) // still caching in background
				return getStored(docid)[position*8];
			return length[getValueIndex(docid,position)];
		}		
		/** Get length without stop words */ 
		public int getLengthNoStopWords(int docid, int position) throws CorruptIndexException, IOException{
			if(!cachingFinished) 
				return getStored(docid)[position*8+1];
			return lengthNoStopWords[getValueIndex(docid,position)];
		}
		/** Get length with all the aliases */
		public int getLengthComplete(int docid, int position) throws CorruptIndexException, IOException{
			if(!cachingFinished)
				return getStored(docid)[position*8+6];
			return lengthComplete[getValueIndex(docid,position)];
		}
		
		/** generic function to get boost value at some position, if checkExists=true won't die on error */
		private float getBoost(int docid, int position, boolean checkExists) throws CorruptIndexException, IOException{
			if(!cachingFinished){
				byte[] stored = getStored(docid);
				if(stored == null || (position*8+5)>=stored.length){
					if(checkExists)
						return 1;
					else
						throwException(docid,position,(stored==null)? 0 : (stored.length/8));
				}
				int boostInt = (((stored[position*8+2]&0xff) << 24) + ((stored[position*8+3]&0xff) << 16) + ((stored[position*8+4]&0xff) << 8) + ((stored[position*8+5]&0xff) << 0));
				return Float.intBitsToFloat(boostInt);
			}
			int inx = getValueIndex(docid,position,checkExists);
			if(inx == -1) // value not found, fine ... (were looking for boost)
				return 1;
			return boost[inx];
		}
		
		/** Get boost for position */ 
		public float getBoost(int docid, int position) throws CorruptIndexException, IOException{
			return getBoost(docid,position,false);
		}
		
		/** Get rank (boost at position 0) */
		public float getRank(int docid) throws CorruptIndexException, IOException{
			return getBoost(docid,0,true);
		}
		
		/** Get namespace of the document */
		public int getNamespace(int docid) throws CorruptIndexException, IOException{
			if(!cachingFinished){
				return Integer.parseInt(reader.document(docid).get("namespace"));
			} 
			return namespaces[docid];
		}
		
		/** Get flag values for docid at position */
		public Flags getFlags(int docid, int position) throws CorruptIndexException, IOException{
			int ord = 0;
			if(!cachingFinished)
				ord = getStored(docid)[position*8+7];
			else
				ord = flags[getValueIndex(docid,position)];

			return Flags.values()[ord];
		}
		
		
	}

}
