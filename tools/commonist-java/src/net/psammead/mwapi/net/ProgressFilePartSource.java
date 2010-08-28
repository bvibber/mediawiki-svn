package net.psammead.mwapi.net;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;

import net.psammead.mwapi.ui.ProgressCallback;

import org.apache.commons.httpclient.methods.multipart.PartSource;

/** works like a FileSource but calls a ProgressListener */
public final class ProgressFilePartSource implements PartSource {
	private final File				file;
	private final ProgressCallback	progressListener;

	public ProgressFilePartSource(File file, ProgressCallback progressListener) {
		this.file				= file;
		this.progressListener	= progressListener;
	}

	public long getLength() {
		return file.length();
	}

	public String getFileName() {
		return file.getName();
	}

	public InputStream createInputStream() throws IOException {
		return new ProgressFileInputStream(file, progressListener);
	}
}