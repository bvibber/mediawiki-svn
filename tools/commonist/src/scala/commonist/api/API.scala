package commonist.api

import java.io.File

import scutil.Log._
import scutil.Functions._
import scutil.ext.OptionExt._
import scutil.json._
import scutil.json.JSExtract._

import commonist.Constants


/** outcome of API#login */
sealed trait LoginResult
case class LoginSuccess(userName:String)		extends LoginResult
case class LoginFailure(failureCode:String)		extends LoginResult
case class LoginError(errorCode:String)			extends LoginResult

/** outcome of API#edit and API#newsection */
sealed trait EditResult
case class EditSuccess(pageTitle:String)		extends EditResult
case class EditFailure(failureCode:String)		extends EditResult
case class EditError(errorCode:String)			extends EditResult
case object EditAborted							extends EditResult

/** outcome of API#edit and API#upload */
sealed trait UploadResult
case class UploadSuccess(fileName:String, pageTitle:String)	extends UploadResult
case class UploadFailure(failureCode:String)				extends UploadResult
case class UploadError(errorCode:String)					extends UploadResult
case object UploadAborted									extends UploadResult

final class API(apiURL:String) {
	// if true nothing is changed on the server
	val dryRun	= !Constants.ENABLE_API_WRITE
	
	val connection	= new Connection(apiURL)
	connection.proxify(Proxy.systemProperties orElse Proxy.environmentVariable)
	
	//------------------------------------------------------------------------------
	
	/** login a user with a given password */
	def login(user:String, password:String):LoginResult = {
		if (dryRun)	{
			DEBUG("api#login", "user=", user)
			return LoginSuccess(user)
		}
		
		val	req1	= List(
			"action"		-> "login",
			"format"		-> "json",
			"lgname"		-> user,
			"lgpassword"	-> password
		)
		val res1	= connection POST req1
		if (res1.isEmpty)	error("no json result")
		errorCode(res1) foreach { code => return LoginError(code) }
		
		val login	= res1 / "login"
		val token	= login / "token" stringValue()
		val outUser	= login / "lgusername" stringValue()
		
		resultCode(login) match {
			case Some("NeedToken")	=>	// handled later
			case Some("Success")	=> return LoginSuccess(outUser getOrError "expected a username")
			case Some(code)			=> return LoginFailure(code)
			case None				=> error("expected a result")
		}
		
		val req2	= req1 ++ optionally("lgtoken" -> token)
		val res2	= connection POST req2
		if (res2.isEmpty)	error("no json result")
		errorCode(res2) foreach { code => return LoginError(code) }
		
		val login2		= res2 / "login"
		val outUser2	= login2 / "lgusername" stringValue()
		
		resultCode(login2) match {
			case Some("Success")	=> LoginSuccess(outUser2 getOrError "expected a username")
			case Some(code)			=> LoginFailure(code)
			case _					=> error("expected a result")
		}
	}
	
	/** logout a user */
	def logout() {
		if (dryRun) {
			DEBUG("api#logout")
			return
		}
		
		val	req	= List(
			"action"	-> "logout",
			"format"	-> "json"
		)
		val res	= connection POST req
		if (res.isEmpty)	error("no json result")
	}
	
	/** simplified edit method to append a new section to a page */
	def newsection(title:String, summary:String, text:String):EditResult = {
		if (dryRun) {
			DEBUG("api#newsection", "title=", title, "summary=", summary, "text=", text)
			return EditSuccess(title)
		}
		
		val	req1	= List(
			"action"	-> "query",
			"format"	-> "json",
			"prop"		-> "info|revisions",
			"intoken"	-> "edit",	// provides edittoken and starttimestamp
			"rvprop"	-> "timestamp",
			"titles"	-> title
		)
		val res1			= connection POST req1
		if (res1.isEmpty)	error("no json result")
		errorCode(res1) foreach { code => return EditError(code) }
		
		val page			= res1 / "query" / "pages" first
		val edittoken		= page / "edittoken" stringValue()
		val starttimestamp	= page / "starttimestamp" stringValue()
		val revision		= page / "revisions" first
		val basetimestamp	= revision / "timestamp" stringValue()
		//val missing			= page / "missing" isDefined
		
		val	req2	= List(
			"action"	-> "edit",
			"format"	-> "json",
			"title"		-> title,
			"summary"	-> summary,
			"text"		-> text,
			"section"	-> "new"	// hardcoded
		) ++ optionally(
			"token"				-> edittoken,
			"basetimestamp"		-> basetimestamp,
			"starttimestamp"	-> starttimestamp
		)
		val res2	= connection POST req2
		if (res2.isEmpty)	error("no json result")
		errorCode(res2) foreach { code => return EditError(code) }
		
		val edit		= res2 / "edit"
		val outTitle	= edit / "title" stringValue()
		
		resultCode(edit) match {
			case Some("Success")	=> EditSuccess(outTitle getOrError "expected a title")
			case Some(code)			=> EditFailure(code)
			case _					=> error("expected a result")
		}
	}
	
	/** edit a page with an editor function, if it returns None editing is aborted */
	def edit(title:String, summary:String, section:Option[Int], change:String=>Option[String]):EditResult = {
		if (dryRun) {
			DEBUG("api#edit", "title=", title, "section=", section, "summary=", summary, "change=", change)
			return EditSuccess(title)
		}
		
		val sectionString	= section map {_.toString}
		
		val	req1	= List(
			"action"	-> "query",
			"format"	-> "json",
			"prop"		-> "info|revisions",
			"intoken"	-> "edit",	// provides edittoken and starttimestamp
			"rvprop"	-> "timestamp|content",
			"titles"	-> title
		) ++
		optionally(
			"rvsection"	-> sectionString
		)
		val res1			= connection POST req1
		if (res1.isEmpty)	error("no json result")
		errorCode(res1) foreach { code => return EditError(code) }
		
		val page			= res1 / "query" / "pages" first
		val edittoken		= page / "edittoken" stringValue()
		val starttimestamp	= page / "starttimestamp" stringValue()
		val revision		= page / "revisions" first
		val basetimestamp	= revision / "timestamp" stringValue()
		val content			= revision / "*" stringValue()
		val missing			= page / "missing" stringValue()
		
		val original	= content orElse (missing map { _ => "" }) 
		val changed		= original flatMap change
		if (changed.isEmpty)	return EditAborted
		val changed1	= changed getOrError "no text???"
		
		val	req2	= List(
			"action"	-> "edit",
			"format"	-> "json",
			"title"		-> title,
			"summary"	-> summary,
			"text"		-> changed1
		) ++ optionally(
			"token"				-> edittoken,
			"basetimestamp"		-> basetimestamp,
			"starttimestamp"	-> starttimestamp,
			"section"			-> sectionString
		)
		val res2	= connection POST req2
		if (res2.isEmpty)	error("no json result")
		errorCode(res2) foreach { code => return EditError(code) }
		
		// TODO handle edit conflicts
		val edit		= res2 / "edit"
		val outTitle	= edit / "title" stringValue()
		
		resultCode(edit) match {
			case Some("Success")	=> EditSuccess(outTitle getOrError "expected a title")
			case Some(code)			=> EditFailure(code)
			case _					=> error("expected a result")
		}
	}	
	
	/** upload a file */
	def upload(filename:String, summary:String, text:String, watch:Boolean, file:File, callback:UploadCallback):UploadResult = {
		if (dryRun) {
			DEBUG("api#upload", "filename=", filename, "summary=", summary, "text=", text, "watch=", watch, "file=", file, "callback=", callback)
			return UploadSuccess(filename, Namespace.file(filename))
		}
		
		val watchString	= if (watch) Some("true") else None
		
		val	req1	= List(
			"action"	-> "query",
			"format"	-> "json",
			"prop"		-> "info",
			"intoken"	-> "edit",
			"titles"	-> Namespace.file(filename)
		)
		
		val res1			= connection POST req1
		if (res1.isEmpty)	error("no json result")
		errorCode(res1) foreach { code => return UploadError(code) }
		
		val page			= res1 / "query" / "pages" first
		val edittoken		= page / "edittoken" stringValue()
		
		val req2	= List(
			"action"	-> "upload",
			"format"	-> "json",
			"filename"	-> filename,
			"comment"	-> summary,
			"text"		-> text
			//	ignorewarnings	url	sessionkey
		) ++ optionally(
			"watch"	-> watchString,
			"token"	-> edittoken
		)
			
		// NOTE either 'sessionkey', 'file', 'url'
		def progress(bytes:Long) { callback.progress(bytes) }
		val res2	= connection POST_multipart (req2, "file", file, progress _)
		if (res2.isEmpty)	error("no json result")
		errorCode(res2) foreach { code => return UploadError(code) }
		
		val upload		= res2 / "upload"
		val outName		= upload / "filename" stringValue()
		val sessionkey	= upload / "sessionkey" intValue()
		val warnings	= upload / "warnings"
		
		resultCode(upload) match {
			case Some("Success")	=> 
				val	name	= outName getOrError "expected filename"
				val	title	= Namespace.file(name)
				return UploadSuccess(name, title)
			case Some("Warning")	=> // handle later
			case Some(code)			=> return UploadFailure(code)
			case _					=> error("expected a result")
		}
		
		val warningKeys:List[String]	= warnings match {
			case Some(JSObject(map))	=> map.keys map { _.value } toList
			case _						=> Nil
		}
		
		// TODO handle more warnings with messages
		val warningWasDeleted		= warningKeys.contains("was-deleted")
		val warningExists			= warningKeys.contains("exists")         
		val warningDuplicate		= warningKeys.contains("duplicate")
		val warningDuplicateArchive	= warningKeys.contains("duplicate-archive")
		if (warningWasDeleted		&& !callback.ignoreFilewasdeleted)		return UploadAborted 
		if (warningExists			&& !callback.ignoreFileexists)			return UploadAborted         
		if (warningDuplicate		&& !callback.ignoreDuplicate)			return UploadAborted
		if (warningDuplicateArchive	&& !callback.ignoreDuplicateArchive)	return UploadAborted
		
		val ignoredWarnings	= List("was-deleted", "exists",	"duplicate", "duplicate-archive", "large-file")
		val graveWarnings	= warningKeys filterNot { ignoredWarnings contains _ }
		if (!graveWarnings.isEmpty)	return UploadFailure(graveWarnings mkString ", ") 
			
		val req3	= List(
			"action"			-> "upload",
			"format"			-> "json",
			"filename"			-> filename,
			"comment"			-> summary,
			"text"				-> text,
			"ignorewarnings"	-> "true"
			// watch url
		) ++ optionally(
			"watch"			-> watchString,
			"token"			-> edittoken,	
			"sessionkey"	-> (sessionkey map { _.toString })
		)
		
		val res3	= connection POST req3
		if (res3.isEmpty)	error("no json result")
		errorCode(res3) foreach { code => return UploadError(code) }
		
		val upload2		= res3 / "upload"
		val outName2	= upload2 / "filename" stringValue()
		
		resultCode(upload2) match {
			case Some("Success")	=> 
				val	name	= outName2 getOrError "expected filename"
				val	title	= Namespace.file(name)
				// NOTE api.php does not write the description page if it's not the initial upload
				if (warningExists) {
					val	editResult	= edit(title, "overwritten", None, constant(Some(text)))
					editResult match {
						case EditFailure(code) =>
							ERROR("could not change overwritten file description (failure)", title, code)
						case EditError(code) =>
							ERROR("could not change overwritten file description (error)", title, code)
						case _ =>
							// ok
					}
				}
				return UploadSuccess(name, title)
			case Some(code)			=> return UploadFailure(code)
			case _					=> error("expected a result")
		}
	}
	
	//------------------------------------------------------------------------------
	
	private def resultCode(response:Option[JSValue]):Option[String] = 
			response / "result" stringValue()  
			
	private def errorCode(response:Option[JSValue]):Option[String] = 
			response / "error" / "code" stringValue()
			
	/** helper function for optional request parameters */
	private def optionally(values:Pair[String,Option[String]]*):List[Pair[String,String]] =
			values.toList flatMap optionally1
			
	private def optionally1(value:Pair[String,Option[String]]):Option[Pair[String,String]] = value match {
		case Pair(key, Some(value))	=> Some(Pair(key, value))
		case Pair(key, None)		=> None
	}
}
