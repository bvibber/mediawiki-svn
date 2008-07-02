package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.util.BitSet;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.index.IndexThread;
/**
 * Implements a simplistic unicode decomposer. By default will use 
 * unicode data from lib/UnicodeData.txt. The decomposer attempts
 * to decompose every character into compatible letters, for instance
 * š will be decomposed to s. Wile &#64257 will be decomposed into f and i.
 * 
 * @author rainman
 *
 */
public class UnicodeDecomposer {
	class Buffer {
		char[] buffer;
		int len;
		public Buffer(char[] buffer, int len) {
			this.buffer = buffer;
			this.len = len;
		}
		public void add(char ch){
			if(len<buffer.length)
				buffer[len++] = ch;
		}
		
	}
	static org.apache.log4j.Logger log = Logger.getLogger(UnicodeDecomposer.class);
	final protected static char[][] decomposition = new char[65536][];
	final protected static boolean[] combining = new boolean[65536];
	protected static UnicodeDecomposer instance = null;
	
	/**
	 * Get decomposing <b>letter</b> characters
	 * @param ch
	 * @return array of characters from unicode class L, null if no decomposition
	 */
	public char[] decompose(char ch){
		return decomposition[ch];
	}
	
	protected UnicodeDecomposer(String path){
		initFromFile(path);
		log.debug("Loaded unicode decomposer");
	}
	
	public boolean isCombiningChar(char ch){
		return combining[ch];
	}
	
	/**
	 * Get singleton instance of the Unicode decomposer class.
	 * Loads lib/UnicodeData.txt on first call
	 * @return
	 */
	synchronized public static UnicodeDecomposer getInstance(){
		if(instance == null){
			String lib = Configuration.open().getString("MWConfig","lib","./lib");
			instance = new UnicodeDecomposer(lib+"/UnicodeData.txt");
		}
		
		return instance;
	}
		
	/**
	 * Read unicode data from the UnicodeData.txt file
	 * @param path
	 */
	protected void initFromFile(String path){
		BitSet letters = new BitSet(65536);
		try {
			BufferedReader in = new BufferedReader(new FileReader(path));
			String line;
			char ch,chd;
			int chVal;
			char buf[] = new char[20];
			int len = 0, i;
			// first pass, get only the letter chars
			while((line = in.readLine()) != null){
				String[] parts = line.split(";");
				chVal =  Integer.parseInt(parts[0],16);
				if(chVal > 0xFFFF)
					continue; // ignore any additional chars
				if(parts[2].charAt(0) == 'L')
					letters.set(chVal);
				
				if(parts[2].charAt(0) == 'M')
					combining[chVal] = true;
				else
					combining[chVal] = false;
			}
			in.close();
			
			// decomposition table
			char[][] table = new char[65536][];
			
			// default for all chars: no decomposition
			for(int ich = 0; ich <= 0xFFFF; ich++){
				decomposition[ich]=null;
				table[ich]=null;
			}
			
			// second pass, make the decomposition table
			in = new BufferedReader(new FileReader(path));
			while((line = in.readLine()) != null){
				String[] parts = line.split(";");
				chVal =  Integer.parseInt(parts[0],16);
				if(chVal > 0xFFFF)
					continue; // ignore any additional chars
				ch = (char) chVal;
				if(letters.get(ch)){
					String[] decomp = parts[5].split(" ");
					if(decomp.length == 1)
						continue;

					len = 0;
					for(String d : decomp){
						if(d.startsWith("<"))
							continue; // special markup like <super>
						chd = (char) Integer.parseInt(d,16);
						if(letters.get(chd))
							buf[len++] = chd;
					}
					if( len != 0 ){
						table[ch]= new char[len];
						for(i=0;i<len;i++)
							table[ch][i] = buf[i];
					} 
				} 				
			}
			// using decomposition table recursively decompose characters
			for(int ich = 0; ich <= 0xFFFF; ich++){
				if(table[ich]==null)
					continue;
				Buffer buffer = new Buffer(buf,0);
				recursiveDecompose(buffer,table,letters,(char)ich);
				if(buffer.len != 0){
					decomposition[ich]= new char[buffer.len];
					for(i=0;i<buffer.len;i++)
						decomposition[ich][i] = buffer.buffer[i];
				}					
			}
			in.close();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
			log.error("Cannot find file "+path);
		} catch (IOException e) {
			e.printStackTrace();
			log.error("Error reading unicode data file from "+path);
		} catch (Exception e){
			e.printStackTrace();
			log.error("Error in unicode data file at "+path+" : "+e.getMessage());
		}
	}

	/**
	 * Depth-first recursion, gradually decompose characters (if it has many diacritics)
	 * 
	 * @param buf - buffer where to write resulting decompositions
	 * @param table - mapping char -> decomposing letters
	 * @param letters - bitset of letter characters
	 * @param c - char to decompose
	 */
	protected void recursiveDecompose(Buffer buf, char[][] table, BitSet letters, char c) {
		// terminal
		if(table[c]==null && letters.get(c)){
			buf.add(c);
		} else if(table[c]!=null && letters.get(c)){
			// depth-first recursion
			for(char ch : table[c]){
				recursiveDecompose(buf,table,letters,ch);
			}
		}
	}
}
