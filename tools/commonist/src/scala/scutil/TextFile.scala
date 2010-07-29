package scutil

import java.io._

import ext.AnyRefExt._
import ext.FileExt._
import ext.ReaderExt._

final class TextFile(delegate:File, encoding:String) {
	/** execute a closure with a Reader reading from this File */
	def withReader[T](code:(Reader=>T)):T =
			delegate.withReader(encoding)(code)
	
	/** execute a closure with a Writer writing into this File */
	def withWriter[T](encoding:String)(code:(Writer=>T)):T =
			delegate.withWriter(encoding)(code)
	
	//------------------------------------------------------------------------------

	/** read this File into a String */
	def read:String = withReader { _.readFully() }
			
	/** write a String into this File */
	def write(text:String) {
		delegate.withWriter(encoding) { 
			_.write(text) 
		}
	}
	
	//------------------------------------------------------------------------------
	// TODO create a LineFile? merge with IOUtil? see slurpLines
	
	/*
	def readLines(reader:BufferedReader):List[String] = {
		def readLines(previous:List[String]):List[String] = reader.readLine match {
			case null	=> previous
			case line	=> readLines(line :: previous)
		}
		readLines(Nil).reverse
	}
	
	def eachLine[T](encoding:String)(code:String=>Unit) {
		withLines(encoding) { _ foreach code }
	}
	
	def withLines[T](encoding:String)(code:Iterator[String]=>T):T =
			withReader { reader =>
				code(lineIterator(new BufferedReader(reader)))
			}
			
	private def lineIterator(reader:BufferedReader):Iterator[String] = new ReadIterator[String] {
		protected override def read():Option[String] = {
			reader.readLine.nullOption
		}
	}
	*/
}
