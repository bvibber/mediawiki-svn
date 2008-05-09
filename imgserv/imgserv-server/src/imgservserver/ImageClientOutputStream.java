/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.io.BufferedOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.Socket;

public class ImageClientOutputStream extends OutputStream {
	static final int BUFFER_SIZE = 8192;
	static final String END_OF_CHUNKED_DATA_MARKER = "0000";
	
	BufferedOutputStream strm;
	boolean chunked = false;
	byte[] buffer = new byte[BUFFER_SIZE];
	int bufpos = 0;
	String header = null;
	
	public ImageClientOutputStream(Socket client) throws IOException {
		strm = new BufferedOutputStream(client.getOutputStream());
	}
	
	public void cancel() throws IOException {
		bufpos = 0;
		chunked = false;
		header = null;
	}
	
	public void setChunked(boolean c) throws IOException {
		flushBuffer();
		if (chunked)
			strm.write(END_OF_CHUNKED_DATA_MARKER.getBytes());
		chunked = c;
	}
	
	@Override
	public void close() throws IOException {
		flush();
		if (chunked)
			strm.write(END_OF_CHUNKED_DATA_MARKER.getBytes());
		strm.close();
	}
	
	@Override
	public void flush() throws IOException {
		flushBuffer();
		strm.flush();
	}
	
	@Override
	public void write(byte[] b) throws IOException {
		if (b.length == 0)
			return;
		addToBuffer(b, 0, b.length);
	}

	@Override
	public void write(byte[] b, int off, int len) throws IOException {
		if (len == 0)
			return;
		
		addToBuffer(b, off, len);
	}
	
	public void write(int b) throws IOException {
		byte[] a = new byte[1];
		a[0] = (byte) b;
		addToBuffer(a, 0, 1);
	}
	
	private void addToBuffer(byte[] data, int offs, int len) 
	throws IOException {
		for (int i = offs; i < len + offs; ++i) {
			buffer[bufpos++] = data[i];
			if (bufpos == BUFFER_SIZE)
				flushBuffer();
		}
	}
	
	public void setHeader(String header) {
		this.header = header;
	}
	
	private void flushBuffer() throws IOException {
		if (header != null) {
			strm.write(header.getBytes());
			header = null;
		}
		
		if (bufpos == 0)
			return;
		
		if (chunked) {
			String len = String.format("%04x", bufpos);
			strm.write(len.getBytes());
		}
		strm.write(buffer, 0, bufpos);
		bufpos = 0;
	}
}
