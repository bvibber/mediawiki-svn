package org.wikimedia.lsearch.highlight;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.UnknownHostException;
import java.util.ArrayList;

import org.wikimedia.lsearch.test.ArticlesParser;
import org.wikimedia.lsearch.test.TestArticle;

public class Benchmark {

	public static void test(ArrayList<TestArticle> articles) throws UnknownHostException, IOException{
		Socket socket = new Socket("localhost",8333);
		
		PrintWriter out = new PrintWriter(socket.getOutputStream());
		BufferedReader in = new BufferedReader(new InputStreamReader(socket.getInputStream()));
		
		out.println("QUERY wikilucene douglas adams");
		main: for(int i=0;i<20;){
			for(TestArticle ar : articles){
				if(i >= 20)
					break main;
				out.println("HIGHLIGHT "+ar.content.length()+" "+ar.namespace+" "+ar.title);
				out.print(ar.content);
				i++;
			}
		}
		out.println("FETCH 2");
		out.flush();
		String line;
		while((line = in.readLine()) != null);
		out.close();
		in.close();
		socket.close();
	}
	
	/**
	 * @param args
	 * @throws IOException 
	 * @throws UnknownHostException 
	 */
	public static void main(String[] args) throws UnknownHostException, IOException {
		ArticlesParser ap = new ArticlesParser("./test-data/highlight.articles");
		ArrayList<TestArticle> articles = ap.getArticles();
		int runs = 50;
		System.out.println("Running "+runs+" 20-article higlights");
		long start = System.currentTimeMillis();
		for(int i=0;i<runs;i++){
			test(articles);
		}
		long delta = System.currentTimeMillis()-start;
		System.out.println("Finished in "+delta+" ms ("+delta/runs+" ms / req)");
	}

}
