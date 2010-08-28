package net.psammead.mwapi.ui;

/** called to indicate upload progress */
public interface ProgressCallback {
	/** called every now and then after some more bytes have been transmitted */
	void bytesWritten(long doneBytes, long maxBytes);
}
