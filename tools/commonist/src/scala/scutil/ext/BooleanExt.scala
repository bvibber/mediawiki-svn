package scutil.ext

object BooleanExt {
	implicit def toBooleanExt(delegate:Boolean) = new BooleanExt(delegate)
}
final class BooleanExt(delegate:Boolean) {
	def fold[T](trueValue: =>T, falseValue: =>T):T = delegate match {
		case true	=> trueValue
		case false	=> falseValue
	}
	
	def option[T](value: =>T):Option[T] = delegate match {
		case true	=> Some(value)
		case false	=> None
	}
}
