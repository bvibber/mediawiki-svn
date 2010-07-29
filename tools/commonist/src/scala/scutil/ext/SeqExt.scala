package scutil.ext

object SeqExt {
	implicit def toSeqExt[S](delegate:Seq[S]) = new SeqExt(delegate)
}
class SeqExt[S](delegate:Seq[S]) {
	def filterMap[T](f:PartialFunction[S,T]):Seq[T] = 
		// delegate flatMap (f.optional andThen Option.option2Iterable[T])
		delegate flatMap { element =>
			if (f isDefinedAt element)	List(f apply element)
			else						Nil
		}
	
	def retainFirst(same:(S,S)=>Boolean):Seq[S] = 
			( delegate.toList.foldLeft(List[S]()) { (retained:List[S],candidate:S) =>
				retained find { same(_,candidate) } match {
					case Some(_)	=> retained
					case None		=> candidate :: retained
				}
			}).toList.reverse
			
	def forceSingle:S = 
			if (delegate.size == 1)	delegate(0) 
			else 					error("expected exactly one element")
}
