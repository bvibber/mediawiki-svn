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
		protected byte[] lengthNoStopWords = null;
		protected float[] boost  = null;
		protected IndexReader reader = null;
		
		protected AggregateMetaFieldSource(IndexReader reader, String fieldBase) throws IOException{
			this.reader = reader;
			String field = fieldBase+"_meta";
			Collection fields = reader.getFieldNames(FieldOption.ALL);
			if(!fields.contains(field))
				return; // index doesn't have ranking info
			
			log.info("Caching aggregate field "+field+" for "+reader.directory());
			int maxdoc = reader.maxDoc();
			index = new int[maxdoc];
			int count = 0;
			length = new byte[maxdoc]; // estimate maxdoc values
			lengthNoStopWords = new byte[maxdoc]; 
			boost = new float[maxdoc];
			for(int i=0;i<maxdoc;i++){
				byte[] stored = null;
				try{
					stored = reader.document(i).getBinaryValue(field);
					index[i] = count;
					if(stored == null)
						continue;
					for(int j=0;j<stored.length/6;j++){
						if(count >= length.length){
							length = extendBytes(length);
							lengthNoStopWords = extendBytes(lengthNoStopWords);
							boost = extendFloats(boost);
						}						
						length[count] = stored[j*6];
						if(length[count] == 0){
							log.warn("Broken length=0 for docid="+i+", at position "+j);
						}
						lengthNoStopWords[count] = stored[j*6+1];
						int boostInt = ((stored[j*6+2] << 24) + (stored[j*6+3] << 16) + (stored[j*6+4] << 8) + (stored[j*6+5] << 0));
						boost[count] = Float.intBitsToFloat(boostInt);
						
						count++;
					}										
				} catch(Exception e){
					log.error("Exception during processing stored_field="+field+" on docid="+i+", with stored="+stored+" : "+e.getMessage());
					e.printStackTrace();
				}
			}
			// compact arrays
			if(count < length.length - 1){
				length = resizeBytes(length,count);
				lengthNoStopWords = resizeBytes(lengthNoStopWords,count);
				boost = resizeFloats(boost,count);
			}
			log.info("Finished caching aggregate "+field+" for "+reader.directory());
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
		/** Get length for position */
		public int getLengthNoStopWords(int docid, int position){
			return lengthNoStopWords[getValueIndex(docid,position)];
		}
		/** Get boost for position */
		public float getBoost(int docid, int position){
			return boost[getValueIndex(docid,position)];
		}
	}
}
