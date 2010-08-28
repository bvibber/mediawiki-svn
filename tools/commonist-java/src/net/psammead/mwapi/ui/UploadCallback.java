package net.psammead.mwapi.ui;

/** callback for the FileUpload action */
public interface UploadCallback {
	/** when true, the file overwites another file */
	boolean ignoreFileexists();
	
	/** when non-null the result is used as new file name */
	String renameFileexists();
	
	/** if true, a large file will be written */
	boolean ignoreLargefile();

	/** if true, previously uploaded and deleted files will be uploaded nevertheless */
	boolean ignoreFilewasdeleted();
}
