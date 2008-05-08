/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.awt.Graphics2D;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;
import java.io.BufferedInputStream;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.Socket;
import java.util.Iterator;
import javax.imageio.ImageIO;
import javax.imageio.ImageReader;
import javax.imageio.ImageWriter;
import javax.imageio.stream.MemoryCacheImageInputStream;
import javax.imageio.stream.MemoryCacheImageOutputStream;
import org.apache.batik.transcoder.Transcoder;
import org.apache.batik.transcoder.TranscoderException;
import org.apache.batik.transcoder.TranscoderInput;
import org.apache.batik.transcoder.TranscoderOutput;
import org.apache.batik.transcoder.TranscodingHints;
import org.apache.batik.transcoder.image.JPEGTranscoder;
import org.apache.batik.transcoder.image.PNGTranscoder;
import org.apache.batik.transcoder.image.TIFFTranscoder;

public class ImageClient extends Thread {
	Socket	client;
	BufferedInputStream reader;
	
	public ImageClient(Socket cl) {
		client = cl;
		
		try {
			handleRequest();
		} catch (Exception e) {
			System.out.printf("%% Error occurred handling client request: %s\n",
					e.getMessage());
			return;
		} finally {
			try {
				client.close();
			} catch (IOException e) {}
		}
	}
	
	private void handleRequest() throws IOException {
		reader = new BufferedInputStream(client.getInputStream());
		
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
		 * OK 2345
		 * <2345 bytes of data>
		 */

		String informat = null, outformat = null;
		int len = -1;
		int width = -1, height = -1;
		
		for (;;) {
			String line = readLine();
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

		byte[] out;
		
		if (informat.equals("svg")) {
			out = transcodeSVG(informat, outformat, width, height, data);
		} else {
			out = transcodeRaster(informat, outformat, width, height, data);
		}
		
		String status = "OK " + out.length + "\r\n";
		OutputStream clout = client.getOutputStream();
		clout.write(status.getBytes());
		clout.write(out);
		client.close();
	}
	
	private String readLine() throws IOException {
		StringBuilder b = new StringBuilder();
		int i;
		
		while ((i = reader.read()) != -1) {
			char c = (char) i;
			if (c == '\r')
				continue;
			if (c == '\n')
				return b.toString();
			b.append(c);
		}
		
		throw new IOException("Unexpected end of stream looking for \\r\\n");
	}
	
	private byte[] transcodeRaster(String informat, String outformat, 
			int width, int height,
			byte[] data) throws IOException {
		ByteArrayInputStream bis = new ByteArrayInputStream(data);
		Iterator<ImageReader> readers = ImageIO.getImageReadersByFormatName(informat);
		if (!readers.hasNext()) {
			System.out.printf("%% No reader found for format \"%s\".\n", 
					informat);
			return null;
		}
		
		ImageReader imgr = readers.next();
		imgr.setInput(new MemoryCacheImageInputStream(
				new ByteArrayInputStream(data)));
		BufferedImage img = imgr.read(0), dest;
		
		if (width != -1 || height != -1) {
			if (width == -1)
				width = (int) (img.getWidth() * ((double)height / img.getHeight()));
			if (height == -1)
				height = (int) (img.getHeight() * ((double)width / img.getWidth()));
			dest = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
			Graphics2D g = dest.createGraphics();
			AffineTransform at = AffineTransform.getScaleInstance(
					(double)width/img.getWidth(),
					(double)height/img.getHeight());
			g.drawRenderedImage(img, at);
		} else {
			dest = img;
		}
		
		Iterator<ImageWriter> writers = ImageIO.getImageWritersByFormatName(outformat);
		if (!writers.hasNext()) {
			System.out.printf("%% No writer found for format \"%s\".\n",
					outformat);
			return null;
		}
		
		ByteArrayOutputStream outs = new ByteArrayOutputStream();
		ImageWriter imgw = writers.next();
		imgw.setOutput(new MemoryCacheImageOutputStream(outs));
		imgw.write(dest);
		
		byte[] out = outs.toByteArray();
		return out;
	}
	
	static class NamedTranscoder {
		Class clas;
		String name;
		
		public NamedTranscoder(String name, Class clas) {
			this.name = name;
			this.clas = clas;
		}
		
		public Transcoder getInstance() 
		throws InstantiationException, IllegalAccessException {
			return (Transcoder) clas.newInstance();
		}
		
		public String getName() {
			return name;
		}
	}
	
	static final NamedTranscoder[] namedTranscoders = {
		new NamedTranscoder("jpeg", JPEGTranscoder.class),
		new NamedTranscoder("png", PNGTranscoder.class),
		new NamedTranscoder("tiff", TIFFTranscoder.class),
	};
	
	private byte[] transcodeSVG(String informat, String outformat, 
			int width, int height,
			byte[] data) throws IOException {
		Transcoder coder = null;
		
		for (int i = 0; i < namedTranscoders.length; ++i) {
			if (namedTranscoders[i].getName().equals(outformat)) {
				try {
					coder = namedTranscoders[i].getInstance();
				} catch (Exception e) {
					System.out.printf("%% Exception trying to instantiate transcoder \"%s\": %s.\n",
							namedTranscoders[i].getName(), e.getMessage());
					return null;
				}
				break;
			}
		}
	
		if (coder == null) {
			System.out.printf("%% No SVG transcoder found for format \"%s\".\n",
					outformat);
			return null;
		}

		if (coder instanceof JPEGTranscoder)
			((JPEGTranscoder) coder).addTranscodingHint(JPEGTranscoder.KEY_QUALITY,
                             new Float(.8));
		
		TranscodingHints.Key kwidth, kheight;
		try {
			kwidth = (TranscodingHints.Key) 
					coder.getClass().getField("KEY_WIDTH").get(null);
			kheight = (TranscodingHints.Key) 
					coder.getClass().getField("KEY_WIDTH").get(null);
		} catch (Exception e) {
			System.out.printf("%% Couldn't extract width or height keys from transcoder.\n");
			return null;
		}
		
		if (width > 0)
	        coder.addTranscodingHint(kwidth, new Float(width));
		if (height > 0)
	        coder.addTranscodingHint(kheight, new Float(height));
		
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		TranscoderInput input = new TranscoderInput(new ByteArrayInputStream(data));
		TranscoderOutput output = new TranscoderOutput(out);
		
		try {
			coder.transcode(input, output);
		} catch (Exception e) {
			System.out.printf("%% Exception transcoding SVG image: %s\n",
					e.toString());
			return null;
		}
		return out.toByteArray();
	}
}
