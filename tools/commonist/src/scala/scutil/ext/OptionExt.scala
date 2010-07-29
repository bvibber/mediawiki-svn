package scutil.ext

object OptionExt {
	implicit def toOptionExt[T](delegate:Option[T]) = new OptionExt[T](delegate)
	
	/*
	def null2option[T](value:T):Option[T] = value match {
		case null	=> None
		case x		=> Some(x)
	}
	*/
}
final class OptionExt[T](delegate:Option[T]) {
	/** useful for warning messages */
	def noneEffect(fx: =>Unit):Option[T] = { if ( delegate.isEmpty) fx; delegate }
	def someEffect(fx: =>Unit):Option[T] = { if (!delegate.isEmpty) fx; delegate }
	def someEffect(fx:T=>Unit):Option[T] = { if (!delegate.isEmpty) fx(delegate.get); delegate }
	
	// == map some getOrElse none
	def fold[X](some:T => X)(none: => X):X = delegate match {
		case Some(x)	=> some(x)
		case None		=> none
	}
	
	def getOrError(s:String) = delegate getOrElse error(s)
	
	def filterMap[X](f:PartialFunction[T,X]):Option[X] = 
			delegate flatMap { element =>
				if (f isDefinedAt element)	Some(f apply element)
				else						None
			}
}
