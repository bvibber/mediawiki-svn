/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.awt.image.BufferedImage;
import java.awt.image.RenderedImage;
import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Iterator;
import javax.imageio.ImageIO;
import javax.imageio.ImageReader;
import javax.imageio.ImageWriter;
import javax.imageio.stream.MemoryCacheImageInputStream;
import javax.imageio.stream.MemoryCacheImageOutputStream;

public class ImageIOImageHandler 
implements MultiFormatImageReader, MultiFormatImageWriter {
	String format;
	boolean hasAlpha = true;
	
	public ImageIOImageHandler(String f) {
		format = f;
		if (format.equals("jpeg"))
			hasAlpha = false;
	}
	
	public boolean hasAlpha() {
		return hasAlpha;
	}
	
	public void setSizeHint(int width, int height) {
	}
	
	public int getPreferredImageType() {
		if (hasAlpha)
			return BufferedImage.TYPE_INT_ARGB;
		else
			return BufferedImage.TYPE_INT_RGB;
	}

	public RenderedImage readImage(byte[] data)
	throws ImageTranscoderException {
		ByteArrayInputStream bis = new ByteArrayInputStream(data);
		
		Iterator<ImageReader> readers = ImageIO.getImageReadersByFormatName(format);
		if (!readers.hasNext()) {
			throw new ImageTranscoderException(
					"No reader found for format \"" + format + "\"");
		}

		ImageReader imgr = readers.next();
		imgr.setInput(new MemoryCacheImageInputStream(
				new ByteArrayInputStream(data)));
		
		BufferedImage img;
		
		try {
			img = imgr.read(0);
			return img;
		} catch (Exception e) {
			throw new ImageTranscoderException(
					"Could not read source image", e);
		}
	}

	public void writeImage(RenderedImage image, OutputStream out)
	throws ImageTranscoderException {
		Iterator<ImageWriter> writers = ImageIO.getImageWritersByFormatName(format);
		if (!writers.hasNext()) {
			throw new ImageTranscoderException(
					"No writer found for format \"" + format + "\"");
		}

		ImageWriter imgw = writers.next();
		imgw.setOutput(new MemoryCacheImageOutputStream(out));
		
		try {
			imgw.write(image);
		} catch (Exception e) {
			throw new ImageTranscoderException(
					"Cannot write image to client", e);
		}
	}

	
}
