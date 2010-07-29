package commonist.util

object JavaCollectionUtil {
	// TODO scutil
	def mkJavaList[T](values:List[T]):java.util.List[T] = {
		val	out	= new java.util.ArrayList[T]
		values foreach { out add _ }
		out
	}
}
