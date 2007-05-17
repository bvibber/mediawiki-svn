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
	static org.apache.log4j.Logger log = Logger.getLogger(UnicodeDecomposer.class);
	final protected static char[][] decomposition = new char[65536][];
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
		log.info("Loaded unicode decomposer");
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
	
	protected final void nodecomp(char ch){
		//decomposition[ch] = new char[] { ch };
		decomposition[ch] = null;
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
			}
			in.close();
			
			// default for all chars: no decomposition
			for(int ich = 0; ich <= 0xFFFF; ich++)
				nodecomp((char)ich);
			
			// second pass, make the decomposition mapping
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
						decomposition[ch]= new char[len];
						for(i=0;i<len;i++)
							decomposition[ch][i] = buf[i];
					} 
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
}
