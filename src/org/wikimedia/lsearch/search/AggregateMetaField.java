package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.Collection;
import java.util.HashMap;
import java.util.StringTokenizer;
import java.util.WeakHashMap;

import org.apache.log4j.Logger;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexReader.FieldOption;

public class AggregateMetaField {
	static Logger log = Logger.getLogger(RankField.class);
	protected static WeakHashMap<IndexReader,HashMap<String,AggregateMetaFieldSource>> cache = new WeakHashMap<IndexReader,HashMap<String,AggregateMetaFieldSource>>();
	protected static Object lock = new Object();
	
	/** Get a cached field source 
	 * @throws IOException */
	public static AggregateMetaFieldSource getCachedSource(IndexReader reader, String field) throws IOException{
		synchronized(lock){
			HashMap<String,AggregateMetaFieldSource> fields = cache.get(reader);
			if(fields == null){
				fields = new HashMap<String,AggregateMetaFieldSource>();
				cache.put(reader,fields);
			}
			AggregateMetaFieldSource s = fields.get(field);
			if(s != null)
				return s;
			else{
				s = new AggregateMetaFieldSource(reader,field);
				fields.put(field,s);
				return s;
			}		
		}
	}
	
	static public class AggregateMetaFieldSource {
		protected int[] index = null;
		protected byte[] length  = null;
		protected float[] boost  = null;
		protected IndexReader reader = null;
		
		protected AggregateMetaFieldSource(IndexReader reader, String fieldBase) throws IOException{
			this.reader = reader;
			String field = fieldBase+"_meta";
			Collection fields = reader.getFieldNames(FieldOption.ALL);
			if(!fields.contains(field))
				return; // index doesn't have ranking info
			
			log.info("Caching aggregate meta field source for "+reader.directory());
			int maxdoc = reader.maxDoc();
			index = new int[maxdoc];
			int count = 0;
			length = new byte[maxdoc]; // estimate
			boost = new float[maxdoc]; // estimate
			for(int i=0;i<maxdoc;i++){
				String stored = null;
				try{
					stored = reader.document(i).get(field);
					index[i] = count;
					if(stored == null)
						continue;
					int last = 0;
					int cur;					
					int j=0;
					while(true){
						cur = stored.indexOf(' ',last);
						if(cur == -1){
							if(last == 0)
								break;
							else
								cur = stored.length();
						}
						if(j % 2 == 0){ // every even values is length
							if(count == length.length){ // extend array
								byte[] t = new byte[length.length*2];
								System.arraycopy(length,0,t,0,length.length);
								length = t;
							}
							length[count] = (byte)Integer.parseInt(stored.substring(last,cur)); // treat byte values as unsigned
						} else{ // odd value is boost
							if(count == boost.length){ // extend array
								float[] t = new float[boost.length*2];
								System.arraycopy(boost,0,t,0,boost.length);
								boost = t;
							}
							boost[count] = Float.parseFloat(stored.substring(last,cur));							
							count++; // advance to next
						}
						last = cur + 1;
						j++;
						if(cur == stored.length())
							break;
					}
					if(j!=0 && j % 2 != 0){
						log.warn("Inconsistent meta field in document "+i+" : "+stored);
					}
				} catch(NumberFormatException e){
					log.error("NumberFormatException for docid = "+i+", stored="+stored);
					e.printStackTrace();
				} catch(Exception e){
					log.error("Exception during processing stored_field="+field+" on docid="+i+", with stored="+stored+" : "+e.getMessage());
					e.printStackTrace();
				}
			}
			// compact arrays
			if(count < length.length - 1){
				byte[] bt = new byte[count];
				System.arraycopy(length,0,bt,0,count);
				length = bt;
				float[] ft = new float[count];
				System.arraycopy(boost,0,ft,0,count);
				boost = ft;
			}
			log.info("Finished caching aggregate meta source for "+reader);
		}

		protected int getValueIndex(int docid, int position){
			int start = index[docid];
			int end = (docid == index.length-1)? length.length : index[docid+1];
			if(position >= end-start)
				try {
					throw new ArrayIndexOutOfBoundsException("Requestion position "+position+" for "+docid+" ["+reader.document(docid).get("title")+"], but last valid index is "+(end-start-1));
				} catch (IOException e) {
					e.printStackTrace();
					throw new ArrayIndexOutOfBoundsException("Requestion position "+position+" unavailable");
				}
			return start+position;
		}
		
		/** Get length for position */
		public int getLength(int docid, int position){
			return length[getValueIndex(docid,position)];
		}
		/** Get boost for position */
		public float getBoost(int docid, int position){
			return boost[getValueIndex(docid,position)];
		}
	}
}
