/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;

public class ImageClientInputStream extends BufferedInputStream {
	public ImageClientInputStream(InputStream in) {
		super(in);
	}
	
	public ImageClientInputStream(InputStream in, int size) {
		super(in, size);
	}
	
	public String readLine() throws IOException {
		StringBuilder b = new StringBuilder();
		int i;
		
		while ((i = read()) != -1) {
			char c = (char) i;
			if (c == '\r')
				continue;
			if (c == '\n')
				return b.toString();
			b.append(c);
		}
		
		throw new IOException("Unexpected end of stream looking for \\r\\n");
	}
}
