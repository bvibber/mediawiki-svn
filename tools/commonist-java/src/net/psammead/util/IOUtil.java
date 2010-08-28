package net.psammead.util;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Reader;
import java.io.Writer;
import java.net.URL;

import net.psammead.util.annotation.FullyStatic;

/** IO functions */
@FullyStatic 
public final class IOUtil {
	private static final Logger	log	= new Logger(IOUtil.class);
		
	/** function collection, shall not be instantiated */
	private IOUtil() {}
	
	//-------------------------------------------------------------------------
	
	/** read a String from an URL with a given encoding  */
	public static String readStringFromURL(URL url, String charSet) throws IOException {
		return readStringFromStream(url.openConnection().getInputStream(), charSet);
	}
	
	/** read a String from a File */
	public static String readStringFromFile(File file, String charSet) throws IOException {
		return readStringFromStream(new FileInputStream(file), charSet);
	}
	
	/** read a String from an InputStream with a given encoding and close the Stream */
	public static String readStringFromStream(InputStream stream, String charSet) throws IOException {
		final char[]		chars	= new char[16384];
		final StringBuilder	out		= new StringBuilder();
		Reader	in		= null;
		try {
			in	= new InputStreamReader(stream, charSet);	 
			for (;;) {
				final int	len	= in.read(chars);
				if (len < 0)	break; 
				out.append(chars, 0, len);
			}
			return out.toString();
		}
		finally {
            closeSilent(in);
		}
	}
	
	//-------------------------------------------------------------------------
	
	/** write a String into a File with a given encoding */
	public static void writeStringToFile(File target, String text, String charSet) throws IOException {
		writeStringToStream(new FileOutputStream(target), text, charSet);
	}
	
	/** write a String into an OutputStream with a given encoding and close the Stream */
	public static void writeStringToStream(OutputStream stream, String text, String charSet) throws IOException {
		Writer	out	= null;
		try {
			out	= new OutputStreamWriter(stream, charSet);
			out.write(text);
		}
		finally {
            closeSilent(out);
		}
	}
	
	//-------------------------------------------------------------------------
	
	/** writes a byte array into a Stream and clos the Stream  */
	public static  void writeBytesToStream(byte[] bytes, OutputStream os) throws IOException {
		try { os.write(bytes); }
		finally { closeSilent(os); }
	}
	
	/** read a byte array from a Stream and close the Stream */
	public static  byte[] readBytesFromStream(InputStream is) throws IOException {
		final ByteArrayOutputStream bs = new ByteArrayOutputStream();
		try { copyStream(is, bs); }
		finally { closeSilent(is); }
		return bs.toByteArray();
	}
	
	//-------------------------------------------------------------------------
	
	private static final int GUESSED_BLOCK_SIZE	= 1<<16;
	
	/** copy the contents of an InputStream into an OutputStream */
    public static void copyStream(InputStream is, OutputStream os) throws IOException {
    	final byte[]	buffer	= new byte[GUESSED_BLOCK_SIZE];
    	for (;;) {
    		int	count	= is.read(buffer, 0, buffer.length);
    		if (count == -1)	break;
    		os.write(buffer, 0, count);
    	}
    }
    
	//-------------------------------------------------------------------------
    
    /** close an InputStream ignoring any problems */
    public static void closeSilent(InputStream st) {
        if (st == null) return;
        try { st.close(); }
        catch (IOException e) { log.error("cannot close", e); }
    }
    
    /** close an OutputStream ignoring any problems */
    public static void closeSilent(OutputStream st) {
        if (st == null) return;
        try { st.close(); }
        catch (IOException e) { log.error("cannot close", e); }
    }
    
    /** close a Reader ignoring any problems */
    public static void closeSilent(Reader st) {
        if (st == null) return;
        try { st.close(); }
        catch (IOException e) { log.error("cannot close", e); }
    }
    
    /** close a Writer ignoring any problems */
    public static void closeSilent(Writer st) {
        if (st == null) return;
        try { st.close(); }
        catch (IOException e) { log.error("cannot close", e); }
    }
	
	//-------------------------------------------------------------------------
    
    /** copies a File to another File */
    public static void copyFile(File from, File to) throws IOException {
    	InputStream	is	= null;
    	try { 
    		is	= new FileInputStream(from);
    		OutputStream	os	= null;
    		try {
    			os	= new FileOutputStream(to);
    			copyStream(is, os);
    		}
    		finally {
    	    	closeSilent(os);
    		}
    	}
    	finally {
    		closeSilent(is);
    	}
    }
    
	/** find a non-existing file for a backup */ 
	public static File backupFile(File file, int digits) {
		// BETTER rotate backup files
		int	i	= 0;
		for (;;) {
			String	suffix	= StringUtil.padLeft(""+i, '0', digits);
			if (suffix.length() > digits)	return null;
			final File backup	= new File(file.getAbsolutePath() + "_" + suffix);
			if (!backup.exists())	return backup;
			i++;
		}
	}
}
