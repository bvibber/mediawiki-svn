package net.psammead.mwapi.ui;

/** a File cannot be uploaded, because it is too large */
public final class UploadFileLargeException extends UploadException {
	/** Constructs a new exception with the specified detail message. */
	public UploadFileLargeException(String message) {
		super(message);
	}
}
