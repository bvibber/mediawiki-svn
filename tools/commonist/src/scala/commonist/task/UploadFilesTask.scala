package commonist.task

import java.io.File

import javax.swing.JOptionPane

import scutil.Log._
import scutil.TextFile
import scutil.SwingUtil._
import scutil.ext.FileExt._

import commonist.Task
import commonist.Parser
import commonist.Constants
import commonist.api._
import commonist.data._
import commonist.task.upload._
import commonist.ui.MainWindow
import commonist.ui.ImageListUI
import commonist.ui.StatusUI
import commonist.ui.later._
import commonist.util.Loader
import commonist.util.Messages
import commonist.util.TextUtil2
import commonist.util.JavaCollectionUtil

/** upload files selected in the ImageListUI */
class UploadFilesTask(
	settingsDir:File,
	loader:Loader, 
	commonData:CommonData, 
	imageListData:ImageListData, 
	mainWindow:MainWindow,
	imageListUI:ImageListUI,
	statusUI:StatusUI 
) extends Task {
	private val statusUILater		= new StatusUILater(statusUI)
	private val imageListUILater	= new ImageListUILater(imageListUI)
			
	private val	wiki			= commonData.wiki
	private val	api				= new API(wiki.api)
	private val wikiName		= wiki.toString
	private val uploadTemplates	= new UploadTemplates(loader, wiki)
	
	override protected def execute() {
		if (!imageListData.hasSelected) {
			INFO("nothing to upload")
			statusUILater.halt("status.upload.empty")
			return
		}
		
		try {
			if (!login())	return
			val common	= Common(
				commonData.description.trim,
				commonData.date.trim,
				commonData.source.trim,
				commonData.author.trim,
				commonData.license.template,
				commonData.license.description,
				Parser parseCategories commonData.categories
			)
			val	uploads	= upload(common)
			if (Constants.ENABLE_GALLERY) {
				gallery(common, uploads)
			}
			INFO("upload finished")
		}
		catch { 
			case e:AbortedException =>
				ERROR("upload task aborted")
				statusUILater.halt("status.upload.aborted")
			case e:Exception =>
				ERROR("upload task error", e)
				// TODO hack
				// status.upload.error=Hochladen von {0} fehlgeschlagen ({1})
				statusUILater.halt("status.upload.error", "", e.getMessage)
		}
	}
	
	/*
	statusUILater.halt("status.login.successful", wikiName)
	statusUILater.halt("status.login.aborted")
	statusUILater.halt("status.login.error", wikiName, e.getMessage)
	statusUILater.halt("status.login.wrongpw", wikiName)
	*/
	private def login():Boolean = {
		INFO("logging in")
		statusUILater.indeterminate("status.login.started", wikiName)
		check()
			
		val loginResult	= api.login(commonData.user.trim, commonData.password)
		loginResult match {
			case LoginSuccess(userName)	=>
				INFO("login successful: " + userName)
				statusUILater.halt("status.login.successful", wikiName)
				true
			case LoginFailure(code)	=>
				INFO("login failed: " + code)
				// TODO more detail
				statusUILater.halt("status.login.wrongpw", wikiName)
				false
			case LoginError(code)	=>
				INFO("login error: " + code)
				statusUILater.halt("status.login.error", wikiName, code)
				false
		}
	}
	
	/*
	statusUILater.halt("status.upload.aborted")
	statusUILater.halt("status.upload.error", path, e.getMessage)
	*/
	private def upload(common:Common):List[Upload] = {
		INFO("uploading files")
		
		// TODO normalizeTitle(FileName.fix( is stupid
		val	selected	= imageListData.selected
		def titleAt(index:Int):String	=
					 if (index < 0)					null
				else if (index >= selected.size)	null
				else Namespace.file(Filename.normalizeTitle(Filename.fix(selected(index).name)))
				
		selected.zipWithIndex map { case Pair(imageData, index) => 
			check()
			
			val	file		= imageData.file
			val fileLength	= file.length
			val fileName	= file.getName
			val filePath	= file.getPath
			val	name		= Filename.normalizeTitle(Filename.fix(imageData.name))
			val title		= Namespace.file(name)
			val previous	= titleAt(index-1)
			val next		= titleAt(index+1)
			val coords		= imageData.coordinates
			val coordParts	= Parser parseCoordinates imageData.coordinates
			val latitude	= coordParts map { _._1 } getOrElse null
			val longitude	= coordParts map { _._2 } getOrElse null
			val categories	= Parser parseCategories imageData.categories
			
			val upload	= Upload(
				name,
				title,
				null,
				previous,
				next,
				imageData.description.trim,
				imageData.date.trim,
				imageData.permission.trim,
				categories,
				coords.trim,
				latitude,
				longitude
			)
			
			statusUILater.halt("status.upload.started", fileName)
			val callback	= new MyUploadCallback(mainWindow, statusUILater, fileLength, fileName, name)
			val text		= uploadTemplates.imageDescription(common, upload)
			val	watch		= true
			
			val uploaded	= api.upload(name, "", text, watch, file, callback)
			uploaded match {
				case UploadSuccess(fileName, pageTitle)	=> 
					INFO("upload successful: " + fileName + " to " + pageTitle)
					statusUILater.halt("status.upload.successful", fileName, pageTitle)
					imageListUILater.uploadFinished(file, true)
					upload.copy(name=fileName, title=pageTitle)
				case UploadAborted	=>
					// TODO more detail
					ERROR("upload aborted: " + fileName)
					statusUILater.halt("status.upload.error", fileName, "aborted")
					imageListUILater.uploadFinished(file, false)
					// TODO just remove it from the list?
					upload.copy(error="aborted")
				case UploadFailure(code)	=>
					// TODO more detail
					ERROR("upload failed: " + fileName + " because " + code)
					statusUILater.halt("status.upload.error", fileName, code)
					imageListUILater.uploadFinished(file, false)
					upload.copy(error=code)
				case UploadError(code)	=>
					ERROR("upload error: " + fileName + " because " + code)
					statusUILater.halt("status.upload.error", fileName, code)
					imageListUILater.uploadFinished(file, false)
					upload.copy(error=code)
			}
		}
	}
	
	/*
	statusUILater.halt("status.gallery.error", e.getMessage)
	statusUILater.halt("status.gallery.editConflict",	"[[" + title + "]]")
	*/
	def gallery(common:Common, uploads:List[Upload]) = {
		INFO("changing gallery")
		check()
		
		val	title	= Namespace.user(commonData.user + "/gallery")
		val (sucesses, failures)	= uploads partition { _.error == null }
		
		val	batch	= Batch(
			JavaCollectionUtil.mkJavaList(uploads),
			JavaCollectionUtil.mkJavaList(sucesses),
			JavaCollectionUtil.mkJavaList(failures)
		)
		val summary	= uploadTemplates.gallerySummary(Constants.VERSION, failures.size)
		val text	= uploadTemplates.galleryDescription(common, batch)
		
		// backup gallery text
		val backup	= settingsDir / "gallery.txt"
		INFO("writing gallery to: " + backup)
		new TextFile(backup, "UTF-8").write(text)
		
		statusUILater.indeterminate("status.gallery.loading", "[[" + title + "]]")
		val editResult	= api.edit(title, summary, None, { oldText =>
			statusUILater.indeterminate("status.gallery.storing", "[[" + title + "]]")
			val newText	= text + "\n\n" + TextUtil2.trimLF(oldText)
			Some(newText)
		})
		editResult match {
			case EditSuccess(pageTitle)	=> 
				statusUILater.halt("status.gallery.updated",	"[[" + title + "]]")
			case EditAborted		=>
				// will not happen
			case EditFailure(code)	=> 
				// TODO more detail
				statusUILater.halt("status.gallery.error",		code + " in [[" + title + "]]")
			case EditError(code)	=> 
				statusUILater.halt("status.gallery.error",		code + " in [[" + title + "]]")
		}
	}
		
	/*
	statusUILater.halt("status.logout.error", wikiName, e.getMessage)
	if (!success) { statusUILater.halt("status.logout.failed", wikiName); return }
	*/
	private def logout() {
		INFO("logging out")
		statusUILater.indeterminate("status.logout.started", wiki)
		api.logout()
		statusUILater.halt("status.logout.successful", wikiName)
	}
	
	//==============================================================================
	
	/** asks the user when somwething about a file upload is unclear */
	private class MyUploadCallback(mainWindow:MainWindow, statusUILater:StatusUILater, fileLength:Long, fileName:String, name:String) extends UploadCallback {
		def progress(bytes:Long) {
			// System.err.println("written " + bytes + " of " + ofBytes)
			val percent	= (bytes * 100 / fileLength).toInt
			statusUILater.determinate("status.upload.progress", percent, 100, fileName, int2Integer(percent))
			// rate = (bytes - oldBytes) / (time  - oldTime)
		}
				
		/** when true, the file overwites another file */
		def ignoreFileexists():Boolean = callbackQuery(
				name,
				"query.upload.ignoreFileexists.title", 
				"query.upload.ignoreFileexists.message")	// false
		
		/** if true, previously uploaded and deleted files will be uploaded nevertheless */
		def ignoreFilewasdeleted():Boolean = callbackQuery(
				name,
				"query.upload.ignoreFilewasdeleted.title", 
				"query.upload.ignoreFilewasdeleted.message")	// true
		
		/** if true, already existing files will be uploaded nevertheless */
		def ignoreDuplicate():Boolean = callbackQuery(
				name,
				"query.upload.ignoreDuplicate.title", 
				"query.upload.ignoreDuplicate.message")
		
		/** if true, already existing files will be uploaded nevertheless */
		def ignoreDuplicateArchive():Boolean = callbackQuery(
				name,
				"query.upload.ignoreDuplicateArchive.title", 
				"query.upload.ignoreDuplicateArchive.message")
		
		/** ask the user a yes/no message */
		private def callbackQuery(name:String, titleKey:String, messageKey:String):Boolean ={
			try {
				edtWait {
						JOptionPane.YES_OPTION == JOptionPane.showConfirmDialog(
								mainWindow.window,
								Messages.message(messageKey, name),
								Messages.text(titleKey),
								JOptionPane.YES_NO_OPTION)
				}
			}
			catch { 
				case e:Exception =>
					ERROR("callback error", e)
					throw e
			}
		}
	}
}
