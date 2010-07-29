package commonist.api

import commonist.util.ProgressInputStream

import scutil.Log._
import scutil.json._
import scutil.ext.OptionExt._

import org.apache.commons.httpclient._
import org.apache.commons.httpclient.util._
import org.apache.commons.httpclient.auth._
import org.apache.commons.httpclient.cookie._
import org.apache.commons.httpclient.params._
import org.apache.commons.httpclient.methods._
import org.apache.commons.httpclient.methods.multipart._

import java.io.File
import java.io.InputStream
import java.io.FileInputStream

final class Connection(apiURL:String) {
	val apiTarget		= URIData.parse(apiURL).target.getOrError("invalid api url: " + apiURL)
	val charSet			= "UTF-8"
	val userAgent		= "commonist/0.0";
	val cookiePolicy	= CookiePolicy.RFC_2109
	
	var noproxyVar:Option[Target=>Boolean]	= None
	def noproxy	= noproxyVar getOrElse NoProxy.all _
	val manager	= new NonProxyConnectionManager(noproxy)
		
	val managerParams	= manager.getParams
	managerParams.setDefaultMaxConnectionsPerHost(6)
	managerParams.setMaxTotalConnections(18)
	managerParams.setStaleCheckingEnabled(true)
		
	val client	= new HttpClient(manager)
	
	def dispose() {
		manager.shutdown()
	}
	
	def GET(params:List[Pair[String,String]]):Option[JSValue] = {
		//println("--- GET ---")
		//println(params)
		val	method	= new GetMethod(apiURL)
		try {
			method.getParams.setCookiePolicy(cookiePolicy)
			method.setFollowRedirects(false)
			method.addRequestHeader("User-Agent", userAgent)
			method.setQueryString(params.map{ it => new NameValuePair(it._1, it._2) }.toArray[NameValuePair])
			
			val responseCode	= client executeMethod method
			val responseBody	= method.getResponseBodyAsString
			val statusLine		= method.getStatusLine
			debug(method)
			
			if (responseCode != 200) error("unexpected response: " + statusLine)
			//println(responseBody)
			JSValue.parse(responseBody)
		}
		finally { 
			method.releaseConnection()
		}
	}
	
	def POST(params:List[Pair[String,String]]):Option[JSValue] = {
		//println("--- POST ---")
		//println(params)
		val method	= new PostMethod(apiURL)
		try {
			method.getParams.setCookiePolicy(cookiePolicy)
			method.setFollowRedirects(false)
			method.addRequestHeader("User-Agent", userAgent)
		
			// NOTE HTTPClient uses the Content-Type header in getRequestCharSet to find out which encoding the site uses
			method.addRequestHeader("Content-Type", PostMethod.FORM_URL_ENCODED_CONTENT_TYPE + "; charset=" + charSet)
			method.addParameters(params.map{ it => new NameValuePair(it._1, it._2) }.toArray[NameValuePair])
			
			val responseCode	= client executeMethod method
			val responseBody	= method.getResponseBodyAsString
			val statusLine		= method.getStatusLine
			debug(method)
			
			if (responseCode != 200) error("unexpected response: " + statusLine)
			//println(responseBody)
			JSValue.parse(responseBody)
		}
		finally { 
			method.releaseConnection()
		}
	}
	
	/** HttpMethod factory encoding the parameters with the site charset */
	def POST_multipart(params:List[Pair[String,String]], fileField:String, file:File, progress:Long=>Unit):Option[JSValue] = {
		//println("--- POST_multipart ---")
		//println(params)
		val method	= new PostMethod(apiURL)
		try {
			method.getParams.setCookiePolicy(cookiePolicy)
			method.setFollowRedirects(false)
			method.addRequestHeader("User-Agent", userAgent)
	
			// NOTE setting the encoding like this does not work :(
			//method.addRequestHeader("Content-Type", MultipartPostMethod.MULTIPART_FORM_CONTENT_TYPE + "; charset=" + charSet)
			val filePart	= new FilePart(
					fileField, 
					new ProgressFilePartSource(file, progress),
					"application/octet-stream",
					charSet)
			val paramParts	= params map { it => new StringPart(it._1, it._2, charSet) }
			val parts	= (filePart :: paramParts).toArray[Part]
			method.setRequestEntity(new MultipartRequestEntity(parts, method.getParams))
			
			val responseCode	= client executeMethod method
			val responseBody	= method.getResponseBodyAsString
			val statusLine		= method.getStatusLine
			debug(method)
			
			if (responseCode != 200) error("unexpected response: " + statusLine)
			//println(responseBody)
			JSValue.parse(responseBody)
		}
		finally { 
			method.releaseConnection()
		}
	}	
	
	/** print debug info for a HTTP-request */
	private def debug(method:HttpMethod) {
		DEBUG(
				"HTTP " + method.getName + 
				" " + method.getURI + 
				" " + method.getStatusLine)
	}
	
	/** 
	 * set credentials for a host
	 * user and password may be null to disable 
	 */
	 def identify(cred:Option[Cred]) {
		// we don't want to be asked
		client.getParams.setAuthenticationPreemptive(true)
		client.getState.setCredentials(
				new AuthScope(
						apiTarget.host,
						apiTarget.port,
						AuthScope.ANY_REALM,
						AuthScope.ANY_SCHEME), 
				cred map clientCred _ getOrElse null)
	}
	
	/** configure the proxy to be used. */
	def proxify(proxy:Option[Proxy]) {
		proxy match {
			case Some(proxy)	=>
				client.getHostConfiguration.setProxy(proxy.target.host, proxy.target.port)
				client.getState.setProxyCredentials(
						new AuthScope(
								AuthScope.ANY_HOST,
								AuthScope.ANY_PORT,
								AuthScope.ANY_REALM,
								AuthScope.ANY_SCHEME), 
						proxy.cred map clientCred _ getOrElse null)
				noproxyVar	= proxy.noproxy
			case None	=>
				client.getHostConfiguration.setProxyHost(null)
		}
	}
	
	private def clientCred(cred:Cred):UsernamePasswordCredentials = new UsernamePasswordCredentials(cred.user, cred.password)
	
	/** works like a FileSource but calls a ProgressListener */
	final class ProgressFilePartSource(file:File, callback:Long=>Unit) extends PartSource {
		def getLength():Long	= file.length
		def getFileName:String	= file.getName
		def createInputStream():InputStream = new ProgressInputStream(new FileInputStream(file), callback)
	}
	
	/** this is a hack to add support for nonProxy-hosts */
	final class NonProxyConnectionManager(noproxy:Target=>Boolean) extends MultiThreadedHttpConnectionManager {
		/** this is a hack to add support for nonProxy-hosts */
		override def getConnectionWithTimeout(hostConfiguration:HostConfiguration, timeout:Long):HttpConnection = {
			val target		= Target(hostConfiguration.getHost, hostConfiguration.getPort)
			val useConfig	= if (target.host != null && noproxy(target))	noProxy(hostConfiguration) else hostConfiguration
			super.getConnectionWithTimeout(useConfig, timeout)
		}
		
		private def noProxy(hostConfiguration:HostConfiguration):HostConfiguration = hostConfiguration.synchronized {
			val	tmp = new HostConfiguration(hostConfiguration)
			tmp.setProxyHost(null)
			tmp
		}
	}
}
