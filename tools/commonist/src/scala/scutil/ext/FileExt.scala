package scutil.ext

import java.io._

import scutil.Files
import scutil.Resource._

import AnyRefExt._

object FileExt {
    implicit def toFileExt(delegate:File)	= new FileExt(delegate)
}

/** utility methods for java File objects */ 
class FileExt(delegate:File) {
	/** add a component to this Files's path */
	def / (path:String):File = new File(delegate, path)
	
	/** list files in this directory matching a predicate */
	def listFiles(predicate:File=>Boolean):Option[Array[File]] =
			delegate listFiles (Files mkFileFilter predicate) nullOption
	
	def existsOption:Option[File] = 
			if (delegate.exists)	Some(delegate)
			else					None
			
	//------------------------------------------------------------------------------
	
	/** execute a closure with a Reader reading from this File */
	def withReader[T](encoding:String)(code:(Reader=>T)):T =
			new InputStreamReader(new FileInputStream(delegate), encoding) use code
	
	/** execute a closure with a Writer writing into this File */
	def withWriter[T](encoding:String)(code:(Writer=>T)):T =
			new OutputStreamWriter(new FileOutputStream(delegate), encoding) use code
	
	//------------------------------------------------------------------------------
			
	/** execute a closure with an InputStream reading from this File */
	def withInputStream[T](code:(InputStream=>T)):T =
			new FileInputStream(delegate) use code
	
	/** execute a closure with an OutputStream writing into this File */
	def withOutputStream[T](code:(OutputStream=>T)):T =
			new FileOutputStream(delegate) use code
}
