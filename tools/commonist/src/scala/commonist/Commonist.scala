package commonist

import scutil.Log._
import scutil.SwingUtil._

object Commonist {
	/** main entry point */
	def main(args:Array[String]) {
		handleUncaught { (t,e) => ERROR("Exception caught in thread: " + t.getName, e)  }
		edt {
			try { new CommonistMain().init() }
			catch { case e:Exception	=> ERROR("cannot start program", e) }
		}
	}
}
