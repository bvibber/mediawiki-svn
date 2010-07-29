package commonist.util

import scutil.Resource._

import java.io.InputStreamReader
import java.io.BufferedReader
import java.net.URL

// TODO scutil
object IOUtil {
	def slurpLines(url:URL):List[String] = 
			new BufferedReader(new InputStreamReader(url.openStream(), "UTF-8")) 
			.use { slurpLines _ }
			
	def slurpLines(in:BufferedReader):List[String] = {
		val out	= new scala.collection.mutable.ListBuffer[String]
		eachLine(in) { out += _ }
		out.toList
	}
	
	def eachLine(in:BufferedReader)(effect:String=>Unit) {
		while (true) {
			in.readLine match {
				case null	=> return
				case line	=> effect(line)
			}
		}
		error("silence! i kill you!")
	}
}
