/*
 * An example program using pngds
 */

import pngds.PNGResizer;

public class Resizer {
	public static void main(String[] args) {
		PNGResizer.resize(args[0], args[1],
			Integer.valueOf(args[2]),
			Integer.valueOf(args[3]));
	}
};
