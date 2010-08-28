package net.psammead.mwapi.net;

import org.apache.commons.httpclient.ConnectionPoolTimeoutException;
import org.apache.commons.httpclient.HostConfiguration;
import org.apache.commons.httpclient.HttpConnection;
import org.apache.commons.httpclient.MultiThreadedHttpConnectionManager;

/** this is a hack to add support for nonProxy-hosts */
public final class NonProxyConnectionManager extends MultiThreadedHttpConnectionManager {
	// regexp matcHing hosts no proxy should be used for
	private String	nonProxyHostsRE	= "";

	/** sets Hosts we dont's want want to use a Proxy for. <br>
	  * the Argument may be null or empty to indicate there are no such hosts<br>
	  * its format is like suns format, f.e. "*.test.de|localhost"
	  */
	public void setNonProxyHosts(String nonProxyHosts) {
		if (nonProxyHosts != null) {
			nonProxyHostsRE	= nonProxyHosts.replaceAll("\\.", "\\\\.")
											.replaceAll("\\*", ".*?");
		}
		else {
			nonProxyHostsRE	= "";
		}
	}
	
	/** this is a hack to add support for nonProxy-hosts */
	@Override
	public HttpConnection getConnectionWithTimeout(HostConfiguration hostConfiguration, long timeout) throws ConnectionPoolTimeoutException {
		final HostConfiguration	useConfig;
		final String	hostName	= hostConfiguration.getHost();		
		if (hostName != null && hostName.matches(nonProxyHostsRE)) {
			synchronized (hostConfiguration) {
				useConfig	= new HostConfiguration(hostConfiguration);
				useConfig.setProxyHost(null);
			}
		}
		else {
			useConfig	= hostConfiguration;
		}
		return super.getConnectionWithTimeout(useConfig, timeout);
	}
}
