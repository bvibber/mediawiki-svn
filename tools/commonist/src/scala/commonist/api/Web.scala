package commonist.api

import java.net.URI

import scala.collection.JavaConversions._

import scutil.ext.AnyRefExt._

/*
[scheme:][//authority][path][?query][#fragment] 
[user-info@]host[:port] 
*/
object URIData {
	def parse(str:String):URIData = parse(new URI(str))
	def parse(uri:URI):URIData = new URIData(
		uri.getScheme.nullOption,
		uri.getAuthority.nullOption,
		uri.getPath.nullOption,
		uri.getQuery.nullOption,
		uri.getFragment.nullOption,
		uri.getUserInfo.nullOption,
		uri.getHost.nullOption,
		uri.getPort match { case -1 => None; case x => Some(x) }
	)
}
case class URIData(
	scheme:Option[String],
	authority:Option[String],
	path:Option[String],
	query:Option[String],
	fragment:Option[String],
	userInfo:Option[String],
	host:Option[String],
	port:Option[Int]
) {
	def target:Option[Target]	=
			for {
				sch	<- scheme
				hst	<- host
				prt	<- port orElse defaultPort
			} yield {
				Target(hst, prt)
			}
	def cred:Option[Cred]	=
			userInfo map { it =>
				it indexOf (':') match {
					case -1	=> Cred(it, "")
					case x	=> Cred(it.substring(0,x), it.substring(x+1))
				}
			}
	def defaultPort:Option[Int] = 
			scheme map { _ match { 
				case "http"		=> 80	
				case "https"	=> 443
				case x			=> error("unexpected scheme: " + x)
			} }
}

case class Target(host:String, port:Int)	
case class Cred(user:String, password:String)

case class Proxy(target:Target, cred:Option[Cred], noproxy:Option[Target=>Boolean])
object Proxy {
	def systemProperties:Option[Proxy] = {
		val	sysProps:scala.collection.mutable.Map[String,String]	= System.getProperties
		for {
			host	<- sysProps get "http.proxyHost"
			port	<- sysProps get "http.proxyPort"
		}
		yield {
			val	target	= Target(host, Integer.parseInt(port))
			val noproxy	= sysProps get ("http.nonProxyHosts") map NoProxy.sun _
			Proxy(target, None, noproxy)
		}
	}
	
	def environmentVariable:Option[Proxy] =
			for {
				http_proxy	<- System.getenv("http_proxy").nullOption
				val data	= URIData.parse(http_proxy)
				target		<- data.target
			} 
			yield {
				Proxy(target, data.cred, None)
			}
}

object NoProxy {
	def all(target:Target):Boolean	= false
	
	/** like suns format, f.e. "*.test.de|localhost" */
	def sun(re:String)(target:Target):Boolean	=  target.host matches re.replaceAll("\\.", "\\\\.").replaceAll("\\*", ".*?")
}
