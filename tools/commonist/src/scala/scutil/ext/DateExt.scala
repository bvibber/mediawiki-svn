package scutil.ext

import java.util.Date
import java.text.SimpleDateFormat

object DateExt {
	implicit def toDateExt(delegate:Date) = new DateExt(delegate)
}
final class DateExt(delegate:Date) {
	def format(fmt:String):String = new SimpleDateFormat(fmt).format(delegate)
}
