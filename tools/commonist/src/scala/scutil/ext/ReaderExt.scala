package scutil.ext

import java.io._

object ReaderExt {
    implicit def toReaderExt(delegate:Reader)	= new ReaderExt(delegate)
}

/** utility methods for java File objects */ 
class ReaderExt(delegate:Reader) {
	def readFully():String = {
		val	buffer	= new Array[Char](16384)
		val out		= new StringBuilder
		var	running	= true
		while (running) {
			val len	= delegate.read(buffer)
			if (len != -1)	out appendAll (buffer, 0, len)
			else			running	= false
		}
		out.toString
	}
}
