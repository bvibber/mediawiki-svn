package net.psammead.mwapi.net;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FilterInputStream;
import java.io.IOException;

import net.psammead.mwapi.ui.ProgressCallback;

/** calls a ProgressListener when some bytes have been read */
public final class ProgressFileInputStream extends FilterInputStream {
	private ProgressCallback	progressListener;
	private long	doneBytes;
	private long	maxBytes;

	public ProgressFileInputStream(File file, ProgressCallback progressListener) throws FileNotFoundException {
		super(new FileInputStream(file));
		this.progressListener	= progressListener;
		doneBytes	= 0;
		maxBytes	= file.length();
	}
	
	@Override
	public int read() throws IOException {
		final int	tmp	= super.read();
		doneBytes++;
		if (progressListener != null)
				progressListener.bytesWritten(doneBytes, maxBytes);
		return tmp;
	}
	
	@Override
	public int read(byte b[], int off, int len) throws IOException {
		final int	tmp	= super.read(b, off, len);
		doneBytes	+= len;
		if (progressListener != null)
				progressListener.bytesWritten(doneBytes, maxBytes);
		return tmp;
	}
	
	@Override
	public int read(byte[] b) throws IOException {
		return read(b, 0, b.length);
	}
}