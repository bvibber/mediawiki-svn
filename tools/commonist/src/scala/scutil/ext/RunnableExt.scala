package scutil.ext

object RunnableExt {
	implicit def mkRunnable(runner: => Unit):Runnable = new Runnable {
		def run() { runner } 
	}
}
                                                              