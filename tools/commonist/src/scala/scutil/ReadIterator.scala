package scutil

abstract class ReadIterator[T] extends Iterator[T] {
	private var need			= true
	private var item:Option[T]	= None
	
	final override def hasNext = {
		if (need) {
			item	= read()
			need	= false
		}
		item.isDefined
	}
	
	final override def next():T = {
		if (!hasNext)	throw new NoSuchElementException
		need	= true
		item.get
	}
	
	protected def read():Option[T]
}
