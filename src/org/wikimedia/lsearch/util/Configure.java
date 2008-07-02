package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.Inet4Address;
import java.net.UnknownHostException;
import java.util.HashMap;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.BasicConfigurator;
import org.apache.log4j.Level;
import org.apache.log4j.Logger;

/**
 * Make a generic single-host, single-index configuration 
 * 
 * @author rainman
 *
 */
public class Configure {
	
	static HashMap<String,String> vars = new HashMap<String,String>();

	public static void main(String[] args) throws Exception {
		BasicConfigurator.configure();
		Logger.getRootLogger().setLevel(Level.INFO);
		if(args.length != 1){
			System.out.println("Generate configuration from template/simple.");
			System.out.println("Usage: Configure <path to mediawiki root directory>");
			return;
		}
		String mediawiki = args[0];
		String dbname = getVariable(mediawiki,"wgDBname");
		String scriptPath = getVariable(mediawiki,"wgScriptPath");
		String server = getVariable(mediawiki,"wgServer");
		String hostname = Inet4Address.getLocalHost().getHostName();
		String base = System.getProperty("user.dir");
		
		System.out.println("Generating configuration files for "+dbname+" ... ");
		
		vars.put("$mediawiki",mediawiki);
		vars.put("$base",base);
		vars.put("$dbname",dbname);
		vars.put("$hostname",hostname);
		vars.put("$indexes",FSUtils.format(new String[]{base,"indexes"}));
		vars.put("$wgScriptPath",scriptPath);
		vars.put("$wgServer",server);
		
		String[] templates = new String[] {"lsearch.conf", "lsearch-global.conf", "lsearch.log4j"};
		
		for(String file : templates){
			System.out.println("Making "+file);
			copy(FSUtils.format(new String[]{base,"template","simple",file}),
					FSUtils.format(new String[] {base,file}));		
		}
		
		System.out.println("Making config.inc");
		PrintWriter out = new PrintWriter(new FileOutputStream(FSUtils.format(new String[] {base,"config.inc"}),false)); // overwrite
		for(String var : vars.keySet()){
			out.println(var.substring(1)+"="+vars.get(var));
		}
		out.close();
				
	}
	
	/** Use maintenance/eval.php to get medawiki variables */
	public static String getVariable(String mediawiki, String var) throws IOException{
		return Command.exec(new String[] { 
				"/bin/bash", 
				"-c", 
				"cd "+mediawiki+" && (echo \"return \\$"+var+"\" | php maintenance/eval.php)"}).trim();
	}
	
	/** create config file from template, replacing variables 
	 * @throws IOException */ 
	public static void copy(String from, String to) throws IOException{
		BufferedReader in = new BufferedReader(new FileReader(from));
		PrintWriter out = new PrintWriter(new FileOutputStream(to,false)); // overwrite
		
		String line = null;
		Set<Entry<String,String>> replace = vars.entrySet();
		while((line = in.readLine()) != null){
			// replace vars
			for(Entry<String,String> r : replace){
				line = line.replace(r.getKey(),r.getValue());
			}
			out.println(line);
		}
		in.close();
		out.close();
	}
}
