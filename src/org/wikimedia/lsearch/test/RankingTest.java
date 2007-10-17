package org.wikimedia.lsearch.test;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLDecoder;
import java.util.ArrayList;

import org.wikimedia.lsearch.beans.ResultSet;

/** 
 * Test cases for search result ranking. 
 * 
 * @author rainman
 *
 */
public class RankingTest {
	static String db = "enwiki";
	static String host = "localhost";
	static int port = 8123;
	
	public static ArrayList<ResultSet> getResults(String query) throws IOException{
		query = query.replace(" ","%20"); 
		String urlString = "http://"+host+":"+port+"/search/"+db+"/"+query+"?case=ignore&limit=20&namespaces=0&offset=0";
		URL url = new URL(urlString);
		BufferedReader br = new BufferedReader(new InputStreamReader(url.openStream()));
		String line;
		int lineNum = 0;
		ArrayList<ResultSet> results = new ArrayList<ResultSet>();
		while ( (line = br.readLine()) != null ) {
			if(lineNum > 1){
				String[] parts = line.split(" ",3);
				String title = URLDecoder.decode(parts[2]).replace("_"," ");
				results.add(new ResultSet(Double.parseDouble(parts[0]),parts[1],title));
			}
			lineNum ++ ;
		}
		br.close();
		return results;
	}

	public static void printResults(ArrayList<ResultSet> res, int pointer){
		String gap;
		for(int i=0;i<10 && i<res.size();i++){
			if(i == pointer)
				gap = " -> ";
			else
				gap = "    ";
			System.out.println(gap+res.get(i));
		}
	}
	
	public static void assertHit(int index, String key, ArrayList<ResultSet> res, String query){
		String info = "hit=["+index+"] key=["+key+"] on ["+query+"]";
		if(res.size() > index){
			if(key.equals(res.get(index).getKey())){
				System.out.println("PASSED "+info);
				printResults(res,index);
				return;
			} else{
				System.out.println("FAILED "+info+" : ");
				printResults(res,index);
			}
		} else{
			System.out.println("FAILED "+info+" : NO RESULT");
			printResults(res,index);
		}		
	}
	
	public static void assertHits(String query, String[] hits) throws IOException {
		ArrayList<ResultSet> res = getResults(query);
		for(int i=0;i<hits.length;i++){
			assertHit(i,hits[i],res,query);
		}
	}
	
	public static void main(String[] args) throws IOException{
		
		assertHits("douglas adams",new String[] {
				"0:Douglas Adams",
				"0:The Hitchhiker's Guide to the Galaxy",
		});
		
		assertHits("douglas adams book",new String[] {
				"0:The Hitchhiker's Guide to the Galaxy",
		});
		
		assertHits("call me ishmael",new String[]{
				"0:Moby Dick"
		});
		
		assertHits("moon radius",new String[]{
				"0:Moon"
		});
		
		assertHits("radius of the moon",new String[]{
				"0:Moon"
		});
		
		assertHits("http",new String[]{
				"0:Hypertext Transfer Protocol"
		});
		
		assertHits("argentina climate",new String[]{
				"0:Geography of Argentina"
		});
		
		assertHits("good thomas",new String[]{
				"0:Prime-factor FFT algorithm"
		});
		
		assertHits("3.14",new String[]{
				"0:Pi"
		});
		
		assertHits("middle east conflict",new String[]{
				"0:Arab-Israeli conflict"
		});
		
		assertHits("houston we have a problem",new String[]{
				"0:Apollo 13 (film)"
		});
		
		assertHits("balkan music",new String[]{
				"0:Music of Southeastern Europe"
		});
		
		assertHits("music of balkan",new String[]{
				"0:Music of Southeastern Europe"
		});
		
		assertHits("balkan brass",new String[]{
				"0:Balkan Brass Band"
		});
		
		
	}
	
}
