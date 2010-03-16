package org.wikimedia.lsearch.benchmark;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.zip.GZIPInputStream;

/** Reads terms from an endless stream of terms */
public class StreamTerms implements Terms {
	BufferedReader in = null;
	String path;

	public StreamTerms(String path){
		this.path = path;
		open();
	}
	
	protected void open(){
		try{
			if(in != null)
				in.close();
			if(path.endsWith(".gz"))
				in = new BufferedReader(
						new InputStreamReader(
								new GZIPInputStream(
										new FileInputStream(path))));
			else 
				in = new BufferedReader(
						new InputStreamReader(
								new FileInputStream(path)));
			} catch(IOException e){
				e.printStackTrace();
			}
	}

	public String next() {
		try {
			return in.readLine();
		} catch (IOException e) {
			// try reopening the stream
			open();
			try {
				return in.readLine();
			} catch (IOException e1) {
				e1.printStackTrace();
				return null;
			}
		}
	}

	public int termCount() {
		// TODO Auto-generated method stub
		return 0;
	}
	
}
