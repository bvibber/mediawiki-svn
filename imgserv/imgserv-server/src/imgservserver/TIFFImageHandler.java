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
import org.apache.batik.ext.awt.image.codec.tiff.TIFFDecodeParam;
import org.apache.batik.ext.awt.image.codec.tiff.TIFFEncodeParam;
import org.apache.batik.ext.awt.image.codec.tiff.TIFFImageDecoder;
import org.apache.batik.ext.awt.image.codec.tiff.TIFFImageEncoder;
import org.apache.batik.ext.awt.image.codec.util.MemoryCacheSeekableStream;

public class TIFFImageHandler 
implements MultiFormatImageReader, MultiFormatImageWriter {

	public TIFFImageHandler(String format) {
	}

	public void setSizeHint(int w, int h) {
	}
	
	public int getPreferredImageType() {
		return BufferedImage.TYPE_4BYTE_ABGR;
	}

	public boolean hasAlpha() {
		return true;
	}
	
	public RenderedImage readImage(byte[] data) 
	throws ImageTranscoderException {
		TIFFImageDecoder coder;
		TIFFDecodeParam param = new TIFFDecodeParam();
		MemoryCacheSeekableStream in = new MemoryCacheSeekableStream(
				new ByteArrayInputStream(data));
		coder = new TIFFImageDecoder(in, param);
		try {
			return coder.decodeAsRenderedImage();
		} catch (Exception e) {
			throw new ImageTranscoderException("Cannot decode TIFF data", e);
		}
	}

	public void writeImage(RenderedImage img, OutputStream out) throws ImageTranscoderException {
		TIFFImageEncoder coder;
		TIFFEncodeParam param = new TIFFEncodeParam();
		param.setCompression(TIFFEncodeParam.COMPRESSION_DEFLATE);
		coder = new TIFFImageEncoder(out, param);
		try {
			coder.encode(img);
		} catch (Exception e) {
			throw new ImageTranscoderException("Cannot encode TIFF data", e);
		}
	}
}
