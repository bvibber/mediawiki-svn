package org.wikimedia.lsearch.test;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;

/**
 * Remotely test a spell-checker host
 * 
 * @author rainman
 *
 */
public class SpellCheckTest {
	static String host = "localhost";
	static int port = 8123;
	static String db = "enwiki";
	
	public static String getSuggestion(String query) throws IOException{
		query = query.replace(" ","%20"); 
		String urlString = "http://"+host+":"+port+"/search/"+db+"/"+query+"?case=ignore&limit=20&namespaces=0&offset=0";
		URL url = new URL(urlString);
		BufferedReader br = new BufferedReader(new InputStreamReader(url.openStream()));
		String line;
		int lineNum = 0;
		while ( (line = br.readLine()) != null ) {
			if(lineNum == 1){
				if(line.startsWith("#suggest")){
					br.close();
					return line.substring(9).replaceAll("<[^>]+>","");
				}
			}
			lineNum ++ ;
		}
		br.close();
		return "";
	}
	
	/**
	 * @param args
	 * @throws IOException 
	 */
	public static void main(String[] args) throws IOException {
		int len = CHECK.length;
		System.out.println("Running "+len+" tests");
		int good = 0, failed = 0;
		int count = 1;
		for(String[] c : CHECK){
			String sug = getSuggestion(c[0]);
			if(!sug.equals(c[1])){
				System.out.println("["+count+"/"+len+"] FAILED {"+sug+"} EXPECTED ["+c[1]+"] FOR ["+c[0]+"]");
				failed++;
			} else{
				System.out.println("["+count+"/"+len+"] OK");
				good++;
			}
			count ++;
		}
		System.out.println("Good tests: "+good+", failed tests: "+failed);
	}
	
	// wrong -> right
   private static final String[][] CHECK = { 
   	{"annul of improbably research", "annals of improbable research" },
   	{"los angles", "los angeles" },
   	{"what is the type of engineers thats deal with various depth of the eart crust", "what is the type of engineers thats deal with various depths of the earth crust"},
   	{"argentina cilmage", "argentina climate"},
   	{"Vista Compatibly", "Vista Compatible"},
   	{"sarah thomson", "sarah thompson"},
   	{"attribution (finance)", ""},
   	{"SOUTH PARK EPISDOE LIST", "SOUTH PARK EPISODE LIST"},
   	{"the grnd canyon", "the grand canyon"},
   	{"ron burgand","ron burgundy"},
   	{"fullmetal achemist ep 1","fullmetal alchemist ep 1"},
   	{"fullmetal alchemist ep 1",""},
   	{"enerst shackleton", "ernest shackleton"},
   	{"los angles lakers", "los angeles lakers"},
   	{"crab fisher","crab fishing"},
   	{"discovery channe;", "discovery channel"},
   	{"Young Cuties", ""},
   	{"fire australia", ""},
   	{"platoon film", ""},
   	{"basillar artery","basilar artery"},
   	{"franki vallie","frankie valli"},
   	{"cuties",""},
   	{"teh",""},
   	{"21st ammendment", "21st amendment"},
   	{"stargate junior",""},
   	{"fire australia",""},
   	{"ISO crack", ""},
   	{"The James Gang (band)",""},
   	{"cource", "course"},
   	{"carolene products",""},
   	{"orvileWright","overnight"},
   	
   };

}
