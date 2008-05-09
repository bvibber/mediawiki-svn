/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

public class MultiFormatImageFactory {
	static class ImageHandler {
		Class handler;
		String name;
		
		public ImageHandler(String name, Class handler) {
			this.name = name;
			this.handler = handler;
		}
		
		public String getName() {
			return name;
		}
		
		public Class getHandler() {
			return handler;
		}
	}
	
	static String[][] normalisedNames = {
		{ "png", "png" },
		{ "PNG", "png" },
		{ "jpeg", "jpeg" },
		{ "JPEG", "jpeg" },
		{ "JPG", "jpeg" },
		{ "jpg", "jpeg" },
		{ "bmp", "bmp" },
		{ "BMP", "bmp" },
		{ "tiff", "tiff" },
		{ "tif", "tiff" },
		{ "TIFF", "tiff" },
		{ "TIF", "tiff" },
		{ "svg", "svg" },
		{ "SVG", "svg" },
	};
	
	static String normaliseName(String name) {
		for(String[] s : normalisedNames) {
			if (s[0].equals(name))
				return s[1];
		}
		
		return null;
	}
	static ImageHandler[] readers = {
		new ImageHandler("png", ImageIOImageHandler.class),
		new ImageHandler("jpeg", ImageIOImageHandler.class),
		new ImageHandler("bmp", ImageIOImageHandler.class),
		new ImageHandler("tiff", TIFFImageHandler.class),
		new ImageHandler("svg", SVGImageHandler.class),
	};

	static ImageHandler[] writers = {
		new ImageHandler("png", ImageIOImageHandler.class),
		new ImageHandler("jpeg", ImageIOImageHandler.class),
		new ImageHandler("bmp", ImageIOImageHandler.class),
		new ImageHandler("tiff", TIFFImageHandler.class),
	};

	static ImageHandler findHandler(ImageHandler[] list, String name) {
		for (ImageHandler h: list)
			if (h.getName().equals(name))
				return h;
		return null;
	}
	
	static Object getHandler(ImageHandler[] list, String format) 
	throws ImageTranscoderException {
		format = normaliseName(format);
		
		ImageHandler h = findHandler(list, format);
		if (h == null)
			throw new ImageTranscoderException(
					"No handler found for type \"" + format + "\"");
		
		Class c = h.getHandler();
		try {
			return c.getConstructor(String.class).newInstance(format);
		} catch (Exception e) {
			throw new ImageTranscoderException(
					"Cannot instantiate handler for format \""+format+"\"", e);
		}
	}
	
	public static MultiFormatImageReader getReader(String format) 
	throws ImageTranscoderException {
		try {
			return (MultiFormatImageReader) getHandler(readers, format);
		} catch (Exception e) {
			throw new ImageTranscoderException(
					"Cannot instantiate handler for format \""+format+"\"", e);
		}
	}

	public static MultiFormatImageWriter getWriter(String format) 
	throws ImageTranscoderException {
		try {
			return (MultiFormatImageWriter) getHandler(writers, format);
		} catch (Exception e) {
			throw new ImageTranscoderException(
					"Cannot instantiate handler for format \""+format+"\"", e);
		}
	}
}
