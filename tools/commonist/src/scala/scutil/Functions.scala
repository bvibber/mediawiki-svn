package scutil

import java.util.concurrent.Callable

object Functions {
	//def const[S](s:S) = new { def apply[T](t: =>T):S = s }
	def constant[T](value: =>T):Function[Any,T]	= _ => value
	
	implicit def functionToRunnable(delegate: =>Unit):Runnable = new Runnable {
		def run() { delegate } 
	}
	
	implicit def functionToCallable[T](delegate: =>T):Callable[T] = new Callable[T] {
		def call():T = delegate
	}
}
