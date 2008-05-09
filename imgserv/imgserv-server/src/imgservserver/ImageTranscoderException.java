/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

public class ImageTranscoderException extends Exception {
	public ImageTranscoderException(String message) {
		super(message);
	}
	
	public ImageTranscoderException(String message, Exception nested) {
		super(message, nested);
	}
}
