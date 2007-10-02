package org.wikimedia.lsearch.util;

import java.io.File;
import java.io.IOException;

/**
 * Various abstraction of file system operations: delete dirs,
 * make soft/hard links ... 
 * 
 * Based on FileUtil.java from Lucene Hadoop project (Apache Licence)
 * @author rainman
 *
 */
public class FSUtils {
	public static final String PATH_SEP = System.getProperty("file.separator");
	
	enum OSType { OS_TYPE_UNIX, OS_TYPE_WINXP };

	protected static String[] hardLinkCommand;
	
	static {
		switch(getOSType()) {
		case OS_TYPE_WINXP:
			hardLinkCommand = new String[] {"fsutil","hardlink","create", null, null};
			break;
		case OS_TYPE_UNIX:
		default:
			hardLinkCommand = new String[] {"ln", null, null};
		}
	}

	static OSType getOSType() {
		String osName = System.getProperty("os.name");
		if (osName.indexOf("Windows") >= 0 && 
				(osName.indexOf("XP") >= 0 || osName.indexOf("2003") >= 0))
			return OSType.OS_TYPE_WINXP;
		else
			return OSType.OS_TYPE_UNIX;
	}
	
	/**
	 * Create a hardlink in the filesystem. 
	 * 
	 * @param target
	 * @param linkName
	 * @throws IOException
	 */
	public static void createHardLink(File target, File linkName) throws IOException {
		int len = hardLinkCommand.length;
		hardLinkCommand[len-2] = target.getCanonicalPath();
		hardLinkCommand[len-1] = linkName.getCanonicalPath();
		Command.exec(hardLinkCommand);
	}

	/**
	 * Create hard links recursively if the target is a directory
	 * 
	 * @param target
	 * @param linkname
	 * @throws IOException
	 */
	public static void createHardLinkRecursive(String target, String linkname) throws IOException {
		File file = new File(target);
		if(!file.exists())
			throw new IOException("Trying to hardlink nonexisting file "+target);
		if(file.isDirectory()){
			File[] files = file.listFiles();
			for(File f: files)
				createHardLinkRecursive(format(new String[]{target,f.getName()}),format(new String[] {linkname,f.getName()}));
		} else
			createHardLink(new File(target),new File(linkname));
	}

	
	/**
	 * Create a soft link between a src and destination
	 * only on a local disk. HDFS does not support this
	 * @param target the target for symlink 
	 * @param linkname the symlink
	 */
	public static void createSymLink(String target, String linkname) throws IOException{
		String cmd = "ln -s " + target + " " + linkname;
		Command.exec(cmd);
	}
		
	/**
	 * Append path parts via the systems path separator. 
	 * I.e. {"/usr/local", "search" } -> "/usr/local/search"
	 * @param parts
	 */
	public static String format(String[] parts){
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(String p : parts){
			if(!first && p.startsWith(PATH_SEP))
				p = p.substring(PATH_SEP.length());
			sb.append(p);
			if(!p.endsWith(PATH_SEP))
				sb.append(PATH_SEP);
			if(first)
				first = false;			
		}		
		return sb.toString();
	}
	
	/**
	 * Construct a file from parts of path
	 * @param parts
	 */
	public static File formatFile(String[] parts){
		return new File(format(parts));
	}
	
	/**
	 * Delete a file recursively
	 * 
	 * @param file
	 */
	public static void deleteRecursive(File file){
		if(!file.exists())
			return;
		else if(file.isDirectory()){
			File[] files = file.listFiles();
			for(File f: files)
				deleteRecursive(f);
			file.delete();
		} else{
			file.delete();			
		}
	}

	/** Delete single file */
	public static void delete(String path) {
		File f = new File(path);
		if(f.exists()) // if doesn't exist don't complain
			f.delete();		
	}

}
