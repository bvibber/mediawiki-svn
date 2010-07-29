package commonist.util

import java.io.IOException
import java.io.InputStream
import java.net.URL
import java.text.MessageFormat
import java.util.Properties

import scala.collection.JavaConversions._

import scutil.Resource._

// TODO ugly
object Messages {
	var SELF:Messages	= null
	
	def init(defaultURL:URL, userLangURL:Option[URL]) {
		SELF	= new Messages(defaultURL, userLangURL)
	}
	
	def text(key:String):String 					= SELF.getText(key)
	def message(key:String, args:Object*):String	= SELF.getMessage(key, args:_*)
}
	
/** encapsulates user messages loaded from a properties document */
class Messages(defaultURL:URL, userLangURL:Option[URL]) {
	val defaultProps	= load(defaultURL)
	val userLangProps	= userLangURL map load _ getOrElse Map.empty

	def getText(key:String):String = get(key)
	
	def getMessage(key:String, args:Object*):String	= 
			MessageFormat.format(get(key), args.map(_.asInstanceOf[AnyRef]) : _*)
	
	private def get(key:String):String =
			(userLangProps get key) orElse
			(defaultProps get key) getOrElse
			error("message: " + key + " not available")
	
	private def load(url:URL):Map[String,String] =
			url.openStream() use { in =>
				val props	= new Properties()
				props.load(in)
				Map() ++ props
			}
}
