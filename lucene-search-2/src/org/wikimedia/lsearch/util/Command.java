package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Arrays;

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
	
	/** Execute command, @return stdout of process */
	public static String exec(String command) throws IOException {
		return exec(new String[] {command});
	}
	
	/** Execute command, @return stdout of process */
	public static String exec(String[] command) throws IOException {
		Process p = null;
		log.debug("Executing shell command "+Arrays.toString(command));		
		try {
			if(command.length == 1)
				p = Runtime.getRuntime().exec(command[0]);
			else
				p = Runtime.getRuntime().exec(command);
			p.waitFor();		
			if(p.exitValue()!=0){
				log.warn("Got exit value "+p.exitValue()+" while executing "+Arrays.toString(command));
				throw new IOException("Error executing command: "+readStream(p.getErrorStream()));
			}
			return readStream(p.getInputStream());
		} catch (InterruptedException e) {
			e.printStackTrace();
			throw new IOException("Interrupted");
		} finally {
			closeStreams(p);
			if(p != null)
				p.destroy();
		}
	}	
		
	protected static String readStream(InputStream in) throws IOException {
		String line;
		StringBuilder sb = new StringBuilder();
		BufferedReader r = new BufferedReader(new InputStreamReader(in));
		while((line = r.readLine()) != null)
			sb.append(line);
		return sb.toString();
	}
}
