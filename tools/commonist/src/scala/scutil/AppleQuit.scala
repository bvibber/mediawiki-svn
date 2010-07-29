package scutil

import Log._

import java.lang.reflect.{Proxy,Method,InvocationHandler}

object AppleQuit {
	/** tries to install an com.apple.mrj.com.apple.mrj.MRJQuitHandler */
	def install(task: => Unit) {
		try {
			val handlerClass	= Class.forName("com.apple.mrj.MRJQuitHandler")
			val adapterInstance	= Proxy.newProxyInstance(
					this.getClass.getClassLoader,
					Array(handlerClass), 
					new InvocationHandler {
						override def invoke(proxy:Object, method:Method, args:Array[Object]):Object = {
							INFO("apple quit handler executing")
							task
							null
						}
					})
					
			val utilsClass		= Class.forName("com.apple.mrj.MRJApplicationUtils")
			val registerMethod	= utilsClass.getMethod("registerQuitHandler", Array(handlerClass):_*)
			registerMethod.invoke(null, Array[Object](adapterInstance):_*)
			INFO("apple quit handler installed")
		}
		catch {
			// not an apple? no problem ;)
			case e:ClassNotFoundException	=> INFO("apple quithandler not installed")
			case e:Exception				=> ERROR("cannot install apple quithandler", e)
		}
	}
}
