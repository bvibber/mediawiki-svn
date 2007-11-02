package org.wikimedia.lsearch.util;

import java.io.IOException;
import java.io.UnsupportedEncodingException;

import org.wikimedia.lsearch.analyzers.Aggregate;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer;

public class Buffer {
	public byte[] buf = new byte[256];
	public int len=0;
	
	public byte[] getBytes(){
		byte[] ret = new byte[len];
		System.arraycopy(buf,0,ret,0,len);
		return ret;
	}
	
	/** write some control sequence, uses invalid utf-8 chars as prefixes */
	public final void writeControl(int val){
		write((byte)(0xff));
		write((byte)(val&0xff));
	}
	
	/** convenience method for write(byte) */
	public final void write(int c){
		write((byte)c);
	}
	
	/** write an array of bytes, with first 4 bytes representing the length of the array */
	public final void writeBytesWithLength(byte[] bytes){
		writeInt(bytes.length);
		for(byte b : bytes)
			write(b);
	}
	
	/** write an array of bytes */
	public final void writeBytes(byte[] bytes){
		for(byte b : bytes)
			write(b);
	}
	
	/** write a single byte */
	public final void write(byte c){
		if(len >= buf.length){
			byte[] t = new byte[buf.length*2];
			System.arraycopy(buf,0,t,0,buf.length);
			buf = t;
		}
		buf[len++] = c;
	}
	
	public final void writeInt(int v){
      write((v >>> 24) & 0xFF);
      write((v >>> 16) & 0xFF);
      write((v >>>  8) & 0xFF);
      write((v >>>  0) & 0xFF);
	}
	
	public final void writeString(String s){
		try {
			for(byte c : s.getBytes("utf-8")){
				write(c);
			}
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
	}
	
	public final void writeStringWithLength(String s){
		try {
			byte[] bytes = s.getBytes("utf-8");
			write((byte)bytes.length);
			for(byte c : bytes){
				write(c);
			}
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
	}
	
	/** Format: type (1b), rank (4b), text (string), size of serialized (4b), serialized (bytes) 
	 * @throws IOException */ 
	public final void writeAggregate(String text, Aggregate a, int type) throws IOException{
		write(type);
		writeInt((int)a.boost());
		writeStringWithLength(text);
		byte[] serialized = ExtToken.serialize(new LanguageAnalyzer.ArrayTokens(a.getTokens()));		
		writeBytesWithLength(serialized);	
	}
}
