package de.brightbyte.wikiword.builder;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.zip.GZIPInputStream;

import org.apache.commons.compress.bzip2.CBZip2InputStream;

import de.brightbyte.io.IOUtil;
import de.brightbyte.wikiword.TweakSet;

public class InputFileHelper {

	private String externalBunzip = null;
	private String externalGunzip = null;

	public InputFileHelper(TweakSet tweaks) {
		this( tweaks.getTweak("input.externalGunzip", (String)null), 
				tweaks.getTweak("input.externalBunzip", (String)null) );
	}

	public InputFileHelper(String gz, String bz2) {
		externalGunzip = gz;
		externalBunzip = bz2;
	}
	
	protected static final Pattern extensionPattern = Pattern.compile("\\.([^./\\\\]+)(\\.gz|\\.bz2)?$", Pattern.CASE_INSENSITIVE);
	
	public static String getFormat(String n) {
		Matcher m = extensionPattern.matcher(n);
		
		if (!m.find()) return null;
		else return m.group(1).toLowerCase();
	}
	
	public URL getURL(String n) {
		if (n==null || n.equals("-")) n = new File(".").getAbsolutePath();
		
		try {
			URL u = new URL(n);
			return u;
		} catch (MalformedURLException e) {
			//ignore and continue
		}
		
		try {
			File f = new File(n);
			return f.toURI().toURL();
		} catch (MalformedURLException e) {
			throw new IllegalArgumentException("failed to convert file name to URL: "+n);
		}
	}	
	

	public URL getInputURL(String d) {
		return getInputURL(null, d);
	}

	public URL getInputURL(URL base, String n) {
		try {
				URL u = base == null || base.getProtocol().equals("file") ? new URL(n) : new URL(base, n);
				return u;
		} catch (MalformedURLException e) {
			//ignore and continue
		}
		
		try {
			File f = new File(n);
			return f.toURI().toURL();
		} catch (MalformedURLException e) {
			throw new RuntimeException("failed to create file URL", e);
		}
	}
	
	public InputStream open(String n) throws IOException {
		return open(null, n);
	}
	
	public InputStream open(URL base, String n) throws IOException {
		if (n.equals("-")) return  new BufferedInputStream(System.in);
		
		try {
			URL u = base == null || base.getProtocol().equals("file") ? new URL(n) : new URL(base, n);
			return openURL(u);
		} catch (MalformedURLException e) {
			//ignore and continue
		}
		
		File f = new File(n);
		
		if (!f.isAbsolute() && base!=null && base.getProtocol().equals("file")) {
			File b = new File(base.getPath());
			if (b.isFile()) b = b.getParentFile();
			f = new File(b, n);
		} 
		
		return openFile(f);
	}
	
	public InputStream openURL(URL u) throws IOException {
		String p = u.getProtocol();
		
		if (p.equals("file")) {
			File f = new File(u.getPath());
			return openFile(f);
		}
		else {
			URLConnection con = u.openConnection();
			String mime = con.getContentType();
			mime = mime.replaceAll(";.*$", "");
			InputStream in = con.getInputStream();
			
			if (mime.equals("application/x-gzip")) { 
				return  new GZIPInputStream(in); //FIXME: somehow, this doesn't seem to work. or was the external gunzipper the problem? check this!
			}
			else if (mime.equals("application/x-bzip2")) { 
				validateBZ2(in);
				return new CBZip2InputStream(in);
			}
			else {
				return in;
			}
		}
	}
	
	public InputStream openFile(File file) throws IOException {
		String f = file.getAbsolutePath();
		
		if (f.equals("-"))
			return new BufferedInputStream(System.in);
		
		InputStream in = new BufferedInputStream(new FileInputStream(file));
		if (f.endsWith(".gz")) {
			if (externalGunzip!=null) return openProc(externalGunzip, file);
			else return new GZIPInputStream(in);
		}
		else if (f.endsWith(".bz2")) {
			if (externalBunzip!=null) {
				return openProc(externalBunzip, file);
			}
			else {
				validateBZ2(in);
				return new CBZip2InputStream(in);
			}
		}
		else
			return in;
	}
	
	protected static void validateBZ2(InputStream in) throws IOException {
		int first = in.read();
		int second = in.read();
		if (first != 'B' || second != 'Z')
			throw new IOException("Didn't find BZ file signature");
	}
	
	protected static final Pattern commandParamPattern = Pattern.compile("^(.*) +([^/\\\\]+)$");
	
	public static InputStream openProc(String command, File f) throws IOException {
		String[] cmd;
		
		Matcher m = commandParamPattern.matcher(command);
		if (m.matches()) {
			String[] p = m.group(2).trim().split("\\s+");

			cmd = new String[p.length+2];
			cmd[0] = m.group(1).trim();
			System.arraycopy(p, 0, cmd, 1, p.length);
			
			cmd[cmd.length-1] = f.getAbsolutePath();
		}
		else {
			cmd = new String[] {
					command,
					f.getAbsolutePath()
			};
		}
		
		Process proc = Runtime.getRuntime().exec(cmd);
		final InputStream err = proc.getErrorStream();
		
		//HACK!
		Thread slurper = new Thread("stderr slurper for "+proc) {
			@Override
			public void run() {
				try {
					IOUtil.pump(err, System.err);
				} catch (IOException e) {
					e.printStackTrace(System.err);
				}
			}
		};
		
		slurper.start();
		
		return new BufferedInputStream(proc.getInputStream());
	}

}
