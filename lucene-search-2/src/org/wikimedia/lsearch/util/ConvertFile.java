package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;

/**
 * Convert some file from latin1 to utf8
 *  
 * @author rainman
 *
 */
public class ConvertFile {
	public static void main(String[] args) throws IOException {
		String from = "ISO-8859-1";
		String to = "UTF-8";
		String path = args[0];
		BufferedReader in = new BufferedReader(
				new InputStreamReader(
						new FileInputStream(path),from));
		
		PrintWriter out = new PrintWriter(new OutputStreamWriter(new FileOutputStream(path+".out",false),to));
		String line = null;
		while((line = in.readLine()) != null){
			out.println(line);
		}
		out.close();
	}
}
