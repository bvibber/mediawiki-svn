/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.awt.image.RenderedImage;

public interface MultiFormatImageReader {
	public void setSizeHint(int width, int height);
	public RenderedImage readImage(byte[] data) throws ImageTranscoderException;
}
