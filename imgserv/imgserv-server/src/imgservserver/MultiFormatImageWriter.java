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
import java.io.OutputStream;

public interface MultiFormatImageWriter {
	public void writeImage(RenderedImage img, OutputStream out) throws ImageTranscoderException;
	public int getPreferredImageType();
	public boolean hasAlpha();
}
