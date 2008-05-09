/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* @(#) $Id$ */
package imgservserver;

import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Transparency;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;
import java.awt.image.RenderedImage;
import java.io.OutputStream;

public class ImageTranscoder {
	public void transcode(String informat, String outformat,
			int width, int height,
			byte[] data, OutputStream out) 
	throws ImageTranscoderException {
		MultiFormatImageReader reader = MultiFormatImageFactory.getReader(informat);
		MultiFormatImageWriter writer = MultiFormatImageFactory.getWriter(outformat);

		reader.setSizeHint(width, height);
		RenderedImage img = reader.readImage(data);
		RenderedImage result;
		
		/*
		 * Some readers (e.g. SVG) can render the input at the size we want if 
		 * given a size hint.  If so, we don't need to scale the image here.
		 */
		if ((width != -1 && img.getWidth() != width) 
				|| (height != -1 && img.getHeight() != height)) 
		
		{
			BufferedImage dest;

			if (width == -1)
				width = (int) (img.getWidth() * ((double) height / img.getHeight()));

			if (height == -1)
				height = (int) (img.getHeight() * ((double) width / img.getWidth()));

			dest = new BufferedImage(width, height, writer.getPreferredImageType());
			Graphics2D g = dest.createGraphics();
			AffineTransform at = AffineTransform.getScaleInstance(
					(double) width / img.getWidth(),
					(double) height / img.getHeight());
			g.drawRenderedImage(img, at);
			result = dest;
		} else {
			/*
			 * If we don't scale the image, there's no chance to convert the image
			 * to the preferred image type of the output format.  So we remove
			 * any alpha channel here if it's needed.
			 */
			if (!writer.hasAlpha() && img.getColorModel().getTransparency() != Transparency.OPAQUE)
				result = removeAlpha(img);
			else
				result = img;
		}

		writer.writeImage(result, out);
	}
	
	public RenderedImage removeAlpha(RenderedImage img) {
		int w = img.getWidth();
        int h = img.getHeight();
        BufferedImage ret = new BufferedImage(w, h, BufferedImage.TYPE_INT_RGB);
        Graphics2D g = ret.createGraphics();
        g.setColor(Color.WHITE);
        g.fillRect(0,0,w,h);
        g.drawRenderedImage(img, null);
        g.dispose();
		return ret;
	}
}
