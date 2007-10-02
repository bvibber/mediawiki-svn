package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import org.apache.log4j.Logger;

/**
 * Send a command to OS, handle errors, and properly close streams 
 * 
 * @author rainman
 *
 */
public class Command {
	static Logger log = Logger.getLogger(Command.class);
	
	public static void closeStreams(Process p) throws IOException {
		if(p != null){
			p.getInputStream().close();
			p.getOutputStream().close();
			p.getErrorStream().close();
		}
	}
	
	public static void exec(String command) throws IOException {
		exec(new String[] {command});
	}
	
	public static void exec(String[] command) throws IOException {
		Process p = null;
		log.debug("Executing shell command "+command);		
		try {
			if(command.length == 1)
				p = Runtime.getRuntime().exec(command[0]);
			else
				p = Runtime.getRuntime().exec(command);
			p.waitFor();		
			if(p.exitValue()!=0){
				log.warn("Got exit value "+p.exitValue()+" while executing "+command);
				String line;
				StringBuilder sb = new StringBuilder();
				BufferedReader r = new BufferedReader(new InputStreamReader(p.getErrorStream()));
				while((line = r.readLine()) != null)
					sb.append(line);
				throw new IOException("Error executing command: "+sb);
			}
		} catch (InterruptedException e) {
			e.printStackTrace();
			throw new IOException("Interrupted");
		} finally {
			closeStreams(p);
			if(p != null)
				p.destroy();
		}
	}

}
