package org.wikimedia.lsearch.ranks;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.EOFException;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.nio.ByteBuffer;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

public class StringMap {
	protected static final int BUFFER_SIZE = 300;
	protected char[] buf = new char[BUFFER_SIZE];
	protected int len = 0, pos = 0;
	protected HashMap<String,ArrayList<String>> map = null;
	protected HashMap<Integer,ArrayList<Integer>> hashMap = null;
	protected byte[] serialized = null;
	public static final char DELIMITER = '\0';
	protected final int INT_SIZE = Integer.SIZE / 8;
	
	public StringMap(HashMap<String,ArrayList<String>> map){
		this.map = map;
	}
	
	public StringMap(byte[] serialized) throws IOException{
		this.serialized = serialized;
		readHash();
	}
	
	/** initialize the small hashmap at the beggining of the stream */
	private void readHash() throws IOException {
		hashMap = new HashMap<Integer,ArrayList<Integer>>();
		ByteArrayInputStream ba = new ByteArrayInputStream(serialized);
		DataInputStream di = new DataInputStream(ba);
		int size = di.readInt();
		for(int i=0;i<size;i++){
			int hash = di.readInt();
			ArrayList<Integer> pos = hashMap.get(hash);
			if(pos == null){
				pos = new ArrayList<Integer>();
				hashMap.put(hash,pos);
			}
			pos.add(di.readInt());
		}		
	}

	protected int encLen(String str) throws UnsupportedEncodingException{
		return str.getBytes("utf-8").length;
	}
	
	public byte[] serialize() throws IOException{
		if(serialized != null)
			return serialized;
		// unique string, string -> index (within string segment)
		HashMap<String,Integer> strings = new HashMap<String,Integer>();
		// hash -> list of keys
		HashMap<Integer,ArrayList<String>> hashs = new HashMap<Integer,ArrayList<String>>();
		// contexts, key -> index of string (from strings)
		HashMap<String,ArrayList<Integer>> contexts = new HashMap<String,ArrayList<Integer>>();
		// keys in some order
		ArrayList<String> keys = new ArrayList<String>();
		keys.addAll(map.keySet());
		int offset = 0;
		for(String key : keys){
			// mapping hash -> keys
			int hash = key.hashCode();
			ArrayList<String> hk = hashs.get(hash);
			if(hk == null){
				hk = new ArrayList<String>();
				hashs.put(hash,hk);
			}
			hk.add(key);
			// contexts
			ArrayList<Integer> cc = new ArrayList<Integer>();
			contexts.put(key,cc);			
			for(String s : map.get(key)){
				// identifier 
				Integer i = strings.get(s);
				if(i == null){
					i = offset;
					strings.put(s,i);
					offset += encLen(s) + INT_SIZE;
				}				
				cc.add(i);	
			}			
		}
		int keyOffset = INT_SIZE+2*INT_SIZE*map.size();
		int stringOffset = keyOffset;
		// key -> offset
		HashMap<String,Integer> keyOffsets = new HashMap<String,Integer>();
		for(String key : keys){
			keyOffsets.put(key,stringOffset);
			stringOffset += INT_SIZE+encLen(key)+INT_SIZE+contexts.get(key).size()*INT_SIZE;
		}
		// serialize!		
		ByteArrayOutputStream ba = new ByteArrayOutputStream();
		DataOutputStream ds = new DataOutputStream(ba);
		ds.writeInt(hashs.size());
		// write out the hashmap
		ArrayList<Entry<Integer,ArrayList<String>>> sortedHash = new ArrayList<Entry<Integer,ArrayList<String>>>();
		sortedHash.addAll(hashs.entrySet());
		Collections.sort(sortedHash,new Comparator<Entry<Integer,ArrayList<String>>>(){
			public int compare(Entry<Integer, ArrayList<String>> o1, Entry<Integer, ArrayList<String>> o2) {
				return o1.getKey() - o2.getKey();
			}			
		});
		// write pairs [ hash] [ position of key ]
		for(Entry<Integer,ArrayList<String>> e : sortedHash){
			int hash = e.getKey();
			for(String key : e.getValue()){
				ds.writeInt(hash);
				ds.writeInt(keyOffsets.get(key));
			}
		}	
		// write: [ key.length ] [ key ] [context1_pos] [context2_pos] ...
		for(String key : keys){
			byte[] b = key.getBytes("utf-8"); 
			ds.writeInt(b.length);
			ds.write(b);
			ArrayList<Integer> con = contexts.get(key);
			if(con == null || con.size()==0)
				ds.writeInt(0);
			else{
				ds.writeInt(con.size());
				for(Integer index : con){
					ds.writeInt(stringOffset+index);
				}
			}
		}
		// write string as [size] [string]
		HashSet<String> written = new HashSet<String>(); 
		for(String key : keys){
			for(String c : map.get(key)){
				if(written.contains(c))
					continue;
				byte[] b = c.getBytes("utf-8");
				ds.writeInt(b.length);
				ds.write(b);
				written.add(c);
			}
		}
		serialized = ba.toByteArray();
		return serialized;
	}
	
	private final int read(){
		return serialized[pos++] & 0xff;
	}
	
   protected int readInt() throws IOException {
      int ch1 = read();
      int ch2 = read();
      int ch3 = read();
      int ch4 = read();
      if ((ch1 | ch2 | ch3 | ch4) < 0)
      	throw new EOFException();
      return ((ch1 << 24) + (ch2 << 16) + (ch3 << 8) + (ch4 << 0));
  }
   
   protected String readString() throws IOException{
   	int len = readInt();
   	int start = pos;
   	pos+=len;
   	return new String(serialized,start,len,"utf-8");
   }
	
	/** Get an array of string for a key 
	 * @throws IOException */
	public ArrayList<String> get(String key) throws IOException{
		ArrayList<String> ret = new ArrayList<String>();
		if(!hashMap.containsKey(key.hashCode()))
			return ret;
		for(Integer p : hashMap.get(key.hashCode())){
			pos = p;
			String k = readString();
			if(key.equals(k)){
				// found key, read context
				int num = readInt();
				int[] strings = new int[num];
				for(int i=0;i<num;i++){
					strings[i] = readInt();
				}
				for(int strpos : strings){
					pos = strpos;
					ret.add(readString());
				}
			}
		}
		return ret;
	}
}
