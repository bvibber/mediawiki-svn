/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.io.IOException;
import java.net.InetAddress;
import java.net.ServerSocket;
import java.net.Socket;

public class RequestListener {
	ServerSocket sock;
	Configuration config;
	
	public RequestListener(Configuration c) throws IOException {
		config = c;
		InetAddress bind = config.getBindAddress();
		if (bind == null)
			sock = new ServerSocket(c.getPort(), 10);
		else
			sock = new ServerSocket(c.getPort(), 10, bind);
	}
	
	public void run() throws IOException {
		for (;;) {
			Socket newclient;
			newclient = sock.accept();
			
			ImageClient client = new ImageClient(newclient, config);
			client.start();
		}
	}
}
