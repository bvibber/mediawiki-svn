package scutil

import java.io.PrintStream

import ext.StringExt._

object Log {
	val DEBUG	= new Log(System.err, "DEBUG")
	val INFO	= new Log(System.err, "INFO")
	val WARN	= new Log(System.err, "WARN")
	val ERROR	= new Log(System.err, "ERROR")
	
	/** print an error to stderr and exit the VM */
	def errorExit(message:String) {
		System.err.println(message)
		System.exit(1)
	}
}

class Log(stream:PrintStream, prefix:String) {
	def apply(elements:Any*) {
		val (exceptions,messages)	= elements partition { _.isInstanceOf[Throwable] }
		stream println (prefix + messages.mkString("\n").indent("\t"))
		exceptions map { _.asInstanceOf[Throwable] } foreach  { _.printStackTrace(stream) } // { stream.println(_.getStackTraceString.index) }
	}
}
