package scutil.ext

object StringExt {
	implicit def toStringExt(delegate:String) = new StringExt(delegate)
}
final class StringExt(delegate:String) {
	def cutPrefix(prefix:String):Option[String] = 
			if (delegate.startsWith(prefix))	Some(delegate.substring(prefix.length)) 
			else								None
				
	def cutSuffix(suffix:String):Option[String] = 
			if (delegate.endsWith(suffix))		Some(delegate.substring(0, delegate.length-suffix.length)) 
			else								None
			
	def indent(prefix:String):String =
			delegate.replaceAll("(?m)^", prefix) 
			
	def splitAround(separator:Char):List[String] = {
		val	out	= new scala.collection.mutable.ListBuffer[String]
		val	len	= delegate.length
		var	pos	= 0
		for (i <- 0 until len) {
			val	c	= delegate.charAt(i)
			if (c == separator) {
				out	+= delegate.substring(pos, i)
				pos	= i + 1
			}
		}
		if (pos <= len) {
			out	+= delegate.substring(pos, len)
		}
		out.toList
	}
}
