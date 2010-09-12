package scutil.json

import scala.util.parsing.input._
import scala.util.parsing.combinator._
import scala.util.parsing.combinator.syntactical._
import scala.util.parsing.combinator.lexical._
	
object JSValue {
	/** parse a JSON formatted String into a JSValue */
	def parse(s:String):Option[JSValue]			= parse(new CharSequenceReader(s))
	def parse(s:Reader[Char]):Option[JSValue]	= JSParser.parse(s)
	
	/** NOTE: don't try to put in Maps with non-String keys! */
	def apply(value:Any):JSValue = value match {
		case null				=> JSNull
		case value:JSValue		=> value
		case value:Int			=> JSNumber(value)
		case value:Long			=> JSNumber(value)
		case value:Float		=> JSNumber(value)
		case value:Double		=> JSNumber(value)
		case value:BigInt		=> JSNumber(value)       
		case value:BigDecimal	=> JSNumber(value)
		case value:Boolean		=> JSBoolean(value)
		case value:String		=> JSString(value)
		case value:List[_]		=> JSArray(value map apply)
		case value:Map[_,_]		=> JSObject(Map.empty ++ value.iterator.map {
			case (key:String, valu)	=> (JSString(key), apply(valu))
			case (key,valu)			=> error("map key is not a String: " + key);
		})
		case value:Any			=> error("not a native json value: " + value)
	}
	
	/** can be used to prepare some value to JSValue standards
	def cooker(prepare:(Any=>Any)) = new {
		def apply(obj:Any):Any = prepare(obj) match {
			case value:Map[_,_]	=> Map.empty ++ value.elements.map { case (key,value) => (apply(key), apply(value)) }
			case value:Seq[_]	=> value map apply
			case value			=> prepare(value)
		}
	} 
	*/
}
sealed trait JSValue {
	def	toJSON:String
}

case object JSNull extends JSValue { 
	def toJSON = "null"
}

object JSNumber {
	def apply(value:Int):JSNumber	= JSNumber(BigDecimal(value))
	def apply(value:Long):JSNumber	= JSNumber(BigDecimal(value))
	def apply(value:Float):JSNumber	= JSNumber(BigDecimal(value))
	def apply(value:Double):JSNumber	= JSNumber(BigDecimal(value))
	def apply(value:BigInt):JSNumber	= JSNumber(BigDecimal(value))
}
case class JSNumber(value:BigDecimal) extends JSValue { 
	def toJSON = value.toString
}

object JSBoolean {
	def apply(value:Boolean)	= if (value) JSTrue else JSFalse
}
sealed abstract class JSBoolean extends JSValue

case object JSTrue extends JSBoolean {
	def toJSON = "true"
}

case object JSFalse extends JSBoolean {
	def toJSON = "false"
}

case class JSString(value:String) extends JSValue { 
	def toJSON = value map {
		_ match {
			case '"' 	=> "\\\""
			case '\\'	=>	"\\\\"
			// this would be allowed but is ugly
			//case '/'	=> "\\/"
			// these are optional
			case '\b'	=> "\\b"
			case '\f'	=> "\\f"
			case '\n'	=> "\\n"
			case '\r'	=> "\\r"
			case '\t'	=> "\\t"
			case c 
			if c < 32	=> "\\u%04x".format(c.toInt)
			case c 		=> c.toString
		}
	} mkString("\"","","\"") 
}

case class JSArray(value:Seq[JSValue]) extends JSValue { 
	def toJSON = value map { _.toJSON } mkString("[", ",", "]")
}

/*
object JSObject {
	def apply(values:Seq[Pair[JSString,JSValue]]):JSObject	= JSObject(Map.empty ++ values)
}
*/
case class JSObject(value:Map[JSString,JSValue]) extends JSValue {
	def toJSON = value.iterator map { 
		case (key,valu) => key.toJSON + ":" + valu.toJSON 
	} mkString("{", ",", "}")
}

object JSParser extends StdTokenParsers with ImplicitConversions {
	type Tokens	= scala.util.parsing.json.Lexer
	
	val lexical	= new Tokens
	lexical.reserved	++= List("true", "false", "null")
	lexical.delimiters	++= List("{", "}", "[", "]", ":", ",")
	
	def value:Parser[JSValue]			= (obj | arr | str | num | tru | fls | nul)
	def arr:Parser[JSArray]				= "[" ~> repsep(value, ",") <~ "]"	^^ { x => JSArray(x) }
	def obj:Parser[JSObject]			= "{" ~> repsep(pair, ",") <~ "}"	^^ { x => JSObject(Map() ++ x) }
	def pair:Parser[(JSString,JSValue)]	= str ~ (":" ~> value)				^^ { case x ~ y => (x, y) }
	def str:Parser[JSString]			= accept("string", { case lexical.StringLit(x)	=> JSString(x) })
	def num:Parser[JSNumber]			= accept("number", { case lexical.NumericLit(x)	=> JSNumber(BigDecimal(x)) })
	def tru:Parser[JSBoolean]			= "true"  ^^^ JSTrue
	def fls:Parser[JSBoolean]			= "false" ^^^ JSFalse
	def nul:Parser[JSValue]				= "null"  ^^^ JSNull
	
	def parse(input:Reader[Char]):Option[JSValue] = phrase(value)(new lexical.Scanner(input)) match {
		case Success(result, _)	=> Some(result)
		case _					=> None
	}
}
