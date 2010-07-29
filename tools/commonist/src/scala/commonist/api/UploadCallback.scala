package commonist.api

trait UploadCallback {
	def progress(bytes:Long)
	
	/** when true, the file overwrites another file */
	def ignoreFileexists():Boolean
	
	/** if true, previously uploaded and deleted files will be uploaded nevertheless */
	def ignoreFilewasdeleted():Boolean
	
	/** if true, already existing files will be uploaded nevertheless */
	def ignoreDuplicate():Boolean
	
	/** if true, already existing files will be uploaded nevertheless */
	def ignoreDuplicateArchive():Boolean
}
