package scutil

import java.io._

import Log._

object Resource {
	implicit def closeableResource[S <: Closeable](delegate:S) = 
			new Resource(delegate, delegate.close)
}

final class Resource[+S](value:S, close: =>Unit) {
	def use[T](work:S=>T) = {
		try {
			work(value) 
		}
		finally {
			try { 
				close 
			}
			catch {
				case e:Exception	=> ERROR("cannot close resource", value, e) 
			}
		}
	}
}
