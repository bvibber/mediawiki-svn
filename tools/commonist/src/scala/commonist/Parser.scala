package commonist

import java.io.BufferedReader
import java.io.IOException
import java.net.URL
import java.util.regex.Matcher
import java.util.regex.Pattern

import commonist.api.Namespace
import commonist.data.WikiData
import commonist.data.LicenseData
import commonist.util.IOUtil

import scutil.Log._
import scutil.Resource._
import scutil.ext.StringExt._

object Parser {
	def parseCategories(s:String):String = {
		val SEPARATOR	= '|'
		val LINK_START	= "[["
		val LINK_END	= "]]"
		
		// if source contains link markers leave it unchanged
		val maybeLink	= s.containsSlice(LINK_START) || s.containsSlice(LINK_END)
		if (maybeLink)	return s
		
		// else compile wikitext
		s
		.splitAround(SEPARATOR)
		.map { _.trim }
		.filter { _.length != 0 }
		.map { name => LINK_START + Namespace.category(name) + LINK_END }
		.mkString("")
	}
	
	def parseCoordinates(s:String):Option[Pair[String,String]] =
			s splitAround ',' map { parseCoordinate _ } match {
				case List(Some(latitude), Some(longitude))	=>
					Some(Pair(latitude, longitude))
				case _	=> 
					WARN("could not parse coordinates", s); None
			}
	def parseCoordinate(s:String):Option[String] =
			s.trim match {
				case ""	=> None
				case x	=> Some(x)
			}
	
	//------------------------------------------------------------------------------

	val WikiDataPattern	= """\s*(\S+)\s+(\S+)\s+(\S+)\s*""".r
	def parseWikis(url:URL):List[WikiData] = parseURL(url) {
		_ match {
			case WikiDataPattern(family, site, api)	
				=> Some(WikiData(family, parseSite(site), api))
			case x									
				=> WARN("could not parse line", x); None
		}
	}
	def parseSite(s:String):Option[String] = if (s == "_") None else Some(s)
	
	val	LicenseDataPattern	= """(\{\{[^\}]+\}\})\s*(.*)""".r
	def parseLicenses(url:URL):List[LicenseData] = parseURL(url) { 
		_ match {
			case LicenseDataPattern(template, description)	
				=> Some(LicenseData(template, description))
			case x											
				=> WARN("could not parse line", x); None
		}
	}
	
	private def parseURL[T](url:URL)(parseLine:String=>Iterable[T]):List[T] = 
			IOUtil
			.slurpLines(url)
			.map { _.trim } 
			.filter { _.length != 0 }
			.filter { !_.startsWith("#") } 
			.flatMap { parseLine }
}
