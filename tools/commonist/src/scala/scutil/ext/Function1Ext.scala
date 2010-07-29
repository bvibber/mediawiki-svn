package scutil.ext

object Function1Ext {
	implicit def toFunction1Ext[S,T](delegate:Function1[S,T]) = new Function1Ext[S,T](delegate)
}
final class Function1Ext[S,T](delegate:Function1[S,T]) {
	def |>[X](next:T=>X) = delegate andThen next
	
	def partial(predicate:S=>Boolean):PartialFunction[S,T] = new PartialFunction[S,T] {
		def isDefinedAt(s:S):Boolean	= predicate(s)
		def apply(s:S):T				= delegate(s)
	}
}
