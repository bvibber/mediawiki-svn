package pngds;

public class PNGResizer {
	public static native int resize(String in, String out, int width, int height);

	static {
		System.loadLibrary("pngds");
	}
};
