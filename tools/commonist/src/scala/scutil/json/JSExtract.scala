package scutil.json

// TODO see dispatch.json._

object JSExtract {
	implicit def extendJSExtract(value:Option[JSValue]):JSExtract	= new JSExtract(value)
	implicit def extendJSExtract(value:JSValue):JSExtract			= new JSExtract(Some(value))
}
class JSExtract(value:Option[JSValue]) {
	def /(name:String):Option[JSValue]	= value flatMap { _ match {
		case JSObject(data)	=> data get JSString(name)
		case _				=> None
	} }
	
	def first():Option[JSValue] = value flatMap { _ match {
		case JSObject(data)	=> data.values.toList.headOption
		case JSArray(data)	=> data.headOption
		case _				=> None
	} }
	
	//------------------------------------------------------------------------------
	
	def stringValue():Option[String]	= value flatMap { _ match {
		case JSString(data)	=> Some(data)
		case _				=> None
	} }
	
	def longValue():Option[Long]	= value flatMap { _ match {
		case JSNumber(data) => Some(data.longValue)
		case _				=> None
	} }
	
	def intValue():Option[Int]	= value flatMap { _ match {
		case JSNumber(data) => Some(data.intValue)
		case _				=> None
	} }
	
	def doubleValue():Option[Double]	= value flatMap { _ match {
		case JSNumber(data) => Some(data.doubleValue)
		case _				=> None
	} }
	
	def floatValue():Option[Float]	= value flatMap { _ match {
		case JSNumber(data) => Some(data.floatValue)
		case _				=> None
	} }
	
	def arrayValue():Option[Seq[JSValue]]	= value flatMap { _ match {
		case JSArray(data)	=> Some(data)
		case _				=> None
	} }
	
	def objectValue():Option[Map[JSString,JSValue]] = value flatMap { _ match {
		case JSObject(data)	=> Some(data)
		case _				=> None
	} }
}
