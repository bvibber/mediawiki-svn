/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.net.InetAddress;
import java.util.Properties;

public class Configuration {
	static final int DEFAULT_PORT = 8765;

	boolean usepngds = false;
	String tmpdir = null;
	int port = DEFAULT_PORT;
	InetAddress address;
	
	public Configuration(Properties p) {
		String x;
		
		if ((x = p.getProperty("tmpdir")) != null)
			tmpdir = x;
		
		if ((x = p.getProperty("usepngds")) != null)
			usepngds = x.equals("true");

		if ((x = p.getProperty("port")) != null)
			port = Integer.parseInt(x);
	
		if ((x = p.getProperty("bind")) != null) {
			try {
				address = InetAddress.getByName(x);
			} catch (Exception e) {
				System.err.printf("%% Invalid bind address \"%s\": %s.\n",
						x);
				System.exit(1);
			}
		}
		
		if (tmpdir == null && usepngds) {
			System.err.printf("%% tmpdir must be set when using pngds.\n");
			System.exit(1);
		}
	}
	
	public boolean getUsepngds() {
		return usepngds;
	}
	
	public int getPort() {
		return port;
	}
	
	public String getTmpdir() {
		return tmpdir;
	}
	
	public InetAddress getBindAddress() {
		return address;
	}
}
