package scutil

import java.lang.reflect.InvocationTargetException
import javax.swing.SwingUtilities

object SwingUtil {
	def edt(task: =>Unit) {
		SwingUtilities.invokeLater(
			new Runnable { 
				override def run() { 
					task 
				}
			}
		)
	}
	
	def edtWait[T](task: =>T):T = {
		try {
			var	out	= new scala.concurrent.SyncVar[T]
			SwingUtilities.invokeAndWait(
				new Runnable {
					override def run() {
						out set task 
					} 
				}
			)
			out.get
		}
		catch {
			// TODO handle InterruptedException?
			case e:InvocationTargetException =>
				throw e.getCause
		}
	}
	
	// TODO see scala.concurrent.ops.spawn
	// TODO handle exceptions in the Thread somehow
	def background(task: =>Unit) {
		new Thread {
			override def run() {
				task
			}
		}.start
	}
	
	def handleUncaught(handler:(Thread,Throwable)=>Unit) {
		Thread.setDefaultUncaughtExceptionHandler(
			new Thread.UncaughtExceptionHandler {
				def uncaughtException(t:Thread, e:Throwable) { 
					handler(t, e) 
				}
			}
		)
	}
}
