package commonist.util

import java.io.File
import java.io.IOException
import java.net.MalformedURLException
import java.net.URL

import scutil.ext.AnyRefExt._
import scutil.ext.OptionExt._

/** loads resources from a set of URL-paths */
final class Loader(settingsDir:File, projectDir:File, resourcePrefix:String) {
	// TODO re-introduce debug output with noneEffect and someEffect in OptionExt
	def resourceURL(path:String):Option[URL] = 
			directoryURL(settingsDir, path) orElse 
			directoryURL(projectDir, path) orElse
			classloaderURL(resourcePrefix, path)
	
	def directoryURL(directory:File, path:String):Option[URL] =
			new File(directory, path) match {
				case file if file.exists	=> Some(file.toURI.toURL)
				case _						=> None
			}
			
	def classloaderURL(resourcePrefix:String, path:String):Option[URL] =
			getClass getResource (resourcePrefix + path) nullOption
}
