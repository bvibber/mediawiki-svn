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
import org.apache.log4j.Logger;
import pngds.PNGResizer;

public class ImageClient extends Thread {
	private static Logger logger = Logger.getLogger(ImageClient.class);
	
	Socket	client;
	ImageClientInputStream reader;
	ImageClientOutputStream writer;
	Configuration config;
	int pngcount = 0;
	
	void error(String fmt, Object... args) {
		logger.error(String.format("[%s] %s", 
				client.getRemoteSocketAddress().toString(),
				String.format(fmt, args)));
	}

	void info(String fmt, Object... args) {
		logger.error(String.format("[%s] %s", 
				client.getRemoteSocketAddress().toString(),
				String.format(fmt, args)));
	}

	public ImageClient(Socket cl, Configuration c) {
		client = cl;
		config = c;
	}
	
	public void run() {
		try {
			handleRequest();
		} catch (Exception e) {
			error("Error occurred handling client request: %s", e.toString());
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
			
			if (args.length == 0) {
				error("Invalid command from client: empty line.");
				return;
			}
			
			if (args.length < 2) {
				error("Invalid command from client: not enough arguments.");
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
				error("Invalid command from client: \"%s\" unrecognised.",
						args[0]);
				return;
			}
		}
		
		if (informat == null) {
			error("No input format received.");
			return;
		}

		if (outformat == null) {
			error("No output format received.");
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
				error("Unexpected EOF reading from client.");
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
			String errorstr = e.getMessage();
			Throwable cause = e;
			while ((cause = cause.getCause()) != null) {
				errorstr = errorstr + ": " + cause.getMessage();
			}
			
			writer.cancel();
			String status = "ERROR " + errorstr + "\r\n";
			writer.write(status.getBytes());
			
			error("%s", errorstr);
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
				error("Unexpected EOF reading from client.");
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
			error("pngds resizing failed.");
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
