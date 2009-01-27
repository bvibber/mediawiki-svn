package de.brightbyte.wikiword;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

import de.brightbyte.application.Arguments;
import de.brightbyte.util.StructuredDataCodec;
import de.brightbyte.util.SystemUtils;

/**
 * A tweak set represents a set of options (key value pairs) controlling 
 * some parameters of different components of WikiWord. Such parameters
 * generally depend on the environment the program is run in. It can be used
 * to optimize utilization of the available hardware.
 */
public class TweakSet {
	protected Map<String, Object> parameters = new HashMap<String, Object>();
	
	public void loadTweaks(File f) throws IOException {
		setTweaks( SystemUtils.loadProperties(f, null), null );
	}
	
	public void loadTweaks(URL u) throws IOException {
		setTweaks( SystemUtils.loadProperties(u, null), null );
	}
	
	public void loadTweaks(InputStream in) throws IOException {
		setTweaks( SystemUtils.loadProperties(in, null), null );
	}
	
	public void setTweaks(Properties params, String prefix) {
		//filter by prefix if desired
		if (prefix != null && prefix.length()>0) {
			Properties p = new Properties();
			
			Enumeration en = p.propertyNames();
			while (en.hasMoreElements()) {
				String n = (String)en.nextElement();
				if (n.startsWith(prefix)) {
					String k = n.substring(prefix.length());
					p.setProperty(k, params.getProperty(k));
				}
			}
			
			params = p;
		}
		
		Map<String, Object> m = StructuredDataCodec.instance.decodeValues(params);
		setTweaks(m);
	}

	public void setTweaks(Arguments args, String prefix) {
		Map<String, Object> opts = args.getOptions();
		if (opts.isEmpty()) return;
		
		Map<String, Object> m = new HashMap<String, Object>();
		for (Map.Entry<String, Object> e: opts.entrySet()) {
			String k = e.getKey();
			if (prefix!=null && prefix.length()>0) {
				if (!k.startsWith(prefix)) continue;
				else k = k.substring(prefix.length());
			}
			
			Object v = e.getValue();
			if (v instanceof String) v = StructuredDataCodec.instance.decodeValue((String)v);
			
			m.put(k, v);
		}
		
		setTweaks(m);
	}
	
	public void setTweaks(Map<String, Object> params) {
		parameters.putAll(params);
	}
	
	public void setTweak(String key, Object value) {
		parameters.put(key, value);
	}
	
	@SuppressWarnings("unchecked")
	public <T>T getTweak(String key, T def) {
		if (!parameters.containsKey(key)) return def;
		return (T)parameters.get(key);
	}
}
