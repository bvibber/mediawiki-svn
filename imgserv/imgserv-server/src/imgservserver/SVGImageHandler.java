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
import java.io.OutputStream;
import org.apache.batik.transcoder.TranscoderException;

public class SVGImageHandler implements MultiFormatImageReader {
	int width = 0, height = 0;
	
	public SVGImageHandler(String format) {
	}
	
	public void setSizeHint(int width, int height) {
		this.width = width;
		this.height = height;
	}
	
	public RenderedImage readImage(byte[] data)
	throws ImageTranscoderException {
		SVGRasterizer r = new SVGRasterizer(new ByteArrayInputStream(data));
		
		if (height > 0)
			r.setImageHeight(height);
		if (width > 0)
			r.setImageWidth(width);
		
		try {
			return r.createBufferedImage();
		} catch (TranscoderException e) {
			throw new ImageTranscoderException(
					"Cannot rasterize SVG image", e);
		}
	}
}
