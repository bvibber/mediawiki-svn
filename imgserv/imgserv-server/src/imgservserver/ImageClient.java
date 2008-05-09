/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.Socket;
import pngds.PNGResizer;

public class ImageClient extends Thread {
	Socket	client;
	ImageClientInputStream reader;
	ImageClientOutputStream writer;
	Configuration config;
	int pngcount = 0;
	
	public ImageClient(Socket cl, Configuration c) {
		client = cl;
		config = c;
		
		try {
			handleRequest();
		} catch (Exception e) {
			System.out.printf("%% Error occurred handling client request: %s\n",
					e.toString());
			return;
		} finally {
			try {
				client.close();
			} catch (IOException e) {}
		}
	}
	
	private void handleRequest() throws IOException {
		reader = new ImageClientInputStream(client.getInputStream());
		writer = new ImageClientOutputStream(client);
		
		/*
		 * The protocol request format is like this:
		 * 
		 * INFORMAT svg
		 * OUTFORMAT png
		 * OUTSIZE 1024 768
		 * DATA 1234
		 * <1234 bytes of data>
		 * 
		 * For some formats, OUTSIZE is optional; the output image will be the
		 * same size as the input.
		 * 
		 * A successful reply looks like this:
		 * 
		 * OK
		 * hhhh<hhhh bytes of data>hhhh<hhhh bytes of data>[etc]
		 */

		String informat = null, outformat = null;
		int len = -1;
		int width = -1, height = -1;
		
		for (;;) {
			String line = reader.readLine();
			String[] args = line.split(" ");
			
			if (args.length < 2) {
				System.out.printf("%% Invalid command from client.\n");
				return;
			}
			
			if (args[0].equals("INFORMAT")) {
				informat = args[1];
				continue;
			} else if (args[0].equals("OUTFORMAT")) {
				outformat = args[1];
				continue;
			} else if (args[0].equals("WIDTH")) {
				width = Integer.parseInt(args[1]);
				continue;
			} else if (args[0].equals("HEIGHT")) {
				height = Integer.parseInt(args[1]);
				continue;
			} else if (args[0].equals("DATA")) {
				len = Integer.parseInt(args[1]);
				break;
			} else {
				System.out.printf("%% Invalid command from client.\n");
				return;
			}
		}
		
		if (informat == null) {
			System.out.printf("%% No input format received.\n");
			return;
		}

		if (outformat == null) {
			System.out.printf("%% No output format received.\n");
			return;
		}

		if (config.getUsepngds() &&
				informat.equals("png") && outformat.equals("png") &&
				width != -1 && height != -1)
		{
			transcodepngds(len, width, height);
			reader.close();
			client.close();
			return;
		}
		
		/* If we get here, we're about to receive the data. */
		byte[] data = new byte[len];
		int r, offs = 0, n;
		for (;;) {
			n = reader.read(data, offs, len - offs);
			if (n == -1) {
				System.out.printf("%% Unexpected EOF reading from client.\n");
				return;
			}
			
			offs += n;
			if (offs == len)
				break;
		}

		writer.setChunked(true);
		writer.setHeader("OK\r\n");

		ImageTranscoder tr = new ImageTranscoder();
		
		try {
			/*
			if (informat.equals("svg")) {
				tr.transcodeSVG(informat, outformat, width, height, data, writer);
			} else {
				tr.transcodeRaster(informat, outformat, width, height, data, writer);
			}
			*/
			tr.transcode(informat, outformat, width, height, data, writer);
		} catch (ImageTranscoderException e) {
			String error = e.getMessage();
			Throwable cause = e;
			while ((cause = cause.getCause()) != null) {
				error = error + ": " + cause.getMessage();
			}
			
			writer.cancel();
			String status = "ERROR " + error + "\r\n";
			writer.write(status.getBytes());
			
			System.err.printf("%% [client: %s] %s\n", client.getRemoteSocketAddress().toString(),
					error);
		}
		
		writer.close();
		reader.close();
		client.close();
	}
	
	private int transcodepngds(int len, int width, int height) 
	throws IOException {
		String inp = config.getTmpdir() + "/pngds_" + Thread.currentThread().getId()
				+ "." + pngcount;
		String outp = config.getTmpdir() + "/pngds_" + Thread.currentThread().getId()
				+ "." + pngcount + ".out";
		File inf = new File(inp);
		File outf = new File(outp);
		pngcount++;
		
		/*
		 * Copy data from the client into the temp file.
		 */
		FileOutputStream ins = new FileOutputStream(inf);
		byte[] buf = new byte[8192];
		int r = 0, n;
		for (;;) {
			n = reader.read(buf, 0, 8192);
			if (n == -1) {
				System.out.printf("%% Unexpected EOF reading from client.\n");
				return -1;
			}
			
			ins.write(buf, 0, n);
			r += n;
			if (r >= len)
				break;
		}
		ins.close();
		ins = null;
				
		int ret = PNGResizer.resize(inp, outp, height, height);
		
		if (ret == -1) {
			System.out.printf("%% pngds resizing failed.\n");
		}
		
		/*
		 * Write the converted file back to the client.
		 */
		String status = "OK " + outf.length() + "\r\n";
		OutputStream clout = client.getOutputStream();
		clout.write(status.getBytes());
		
		FileInputStream outs = new FileInputStream(outf);
		for (;;) {
			n = outs.read(buf, 0, 8192);
			if (n == -1)
				break;
			clout.write(buf, 0, n);
		}
		inf.delete();
		outf.delete();
		
		clout.close();
		outs.close();
		
		return ret;
	}
}
