package commonist

// scala -classpath build/classes:lib/bsh-2.0b2-fixed.jar:lib/minibpp.jar:lib/sanselan-0.97-incubator.jar:lib/commons-httpclient-3.1.jar:lib/commons-logging-1.1.jar:lib/commons-codec-1.3.jar commonist.Test 
object Test {
	def main(args:Array[String]) {
		/*
		import commonist.util.EXIF
		
		val file	= new java.io.File(args(0))
		val exif	= EXIF.extract(file)
		println("exif=" + exif)
		*/
		
		/*
		import commonist.api._
		
		val	api		= new API("http://commons.wikimedia.org/w/api.php")
		
		val editResult	= api.edit("User:ClemRutter/gallery", "test", None, { oldText =>
			Some("test\n\n" + oldText)
		})
		println(editResult)
		*/
		
		/*
		val str	= new scutil.TextFile(new java.io.File("/tmp/tmp.js"), "UTF-8").read
		val js	= scutil.json.JS(str)
		println(js)
		*/
		
		/*
		val str	= "{foobar:{}}"
		val js	= scutil.json.JS(str)
		println(js)
		*/
		
		/*
		// OOME
		new net.psammead.minibpp.Compiler() compile "ï¿½"
		*/
		
		import commonist.api._
		
		/*
		// http://commons.wikimedia.org/wiki/User:Daniel_Bar%C3%A1nek
		
		# NotExists
		* The username you provided doesn't exist
		
		action=login&format=json&lgname=Daniel_Bar%EC%8E%A1nek&lgpassword=foobar
		action=login&format=json&lgname=Daniel_Bar%EC%8E%A1nek&lgpassword=foobar&lgtoken=6805207800fdc58a1f1336c1d071ed05
    	*/
    	val	api		= new API("http://commons.wikimedia.org/w/api.php")
		//println(api.login("Daniel Bar\u00e1nek", "foobar"));
		println(api.login("ránnár", "rán"));
	}
}

