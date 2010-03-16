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
	
	enum OSType { OS_TYPE_UNIX, OS_TYPE_WINXP, OS_TYPE_LINUX };

	protected static String[] hardLinkCommand;
	protected static String[] hardLinkRecursive = null;
	
	static {
		switch(getOSType()) {
		case OS_TYPE_WINXP:
			hardLinkCommand = new String[] {"fsutil","hardlink","create", null, null};
			break;
		case OS_TYPE_LINUX:
			hardLinkRecursive = new String[] {"cp", "-lr", null, null};
		case OS_TYPE_UNIX:
		default:
			hardLinkCommand = new String[] {"ln", "-f", null, null};
		}
	}

	static OSType getOSType() {
		String osName = System.getProperty("os.name");
		if (osName.indexOf("Windows") >= 0 && 
				(osName.indexOf("XP") >= 0 || osName.indexOf("2003") >= 0))
			return OSType.OS_TYPE_WINXP;
		else if(osName.indexOf("Linux")>=0)
			return OSType.OS_TYPE_LINUX;
		else
			return OSType.OS_TYPE_UNIX;
	}
	
	public static void createHardLink(String from, String to) throws IOException {
		createHardLink(new File(from),new File(to));
	}
	
	/**
	 * Create a hardlink in the filesystem. 
	 * 
	 * @param from
	 * @param to
	 * @throws IOException
	 */
	public static void createHardLink(File from, File to) throws IOException {
		String[] command = hardLinkCommand.clone();
		int len = command.length;
		command[len-2] = from.getCanonicalPath();
		command[len-1] = to.getCanonicalPath();
		Command.exec(command);
	}
	
	protected static void createHardLinkRecursive(File from, File to) throws IOException {
		String[] command = hardLinkRecursive.clone();
		int len = command.length;
		command[len-2] = from.getCanonicalPath();
		command[len-1] = to.getCanonicalPath();
		Command.exec(command);
	}

	/**
	 * Create hard links recursively if the target is a directory
	 * 
	 * @param from
	 * @param to
	 * @throws IOException
	 */
	public static void createHardLinkRecursive(String from, String to) throws IOException {
		createHardLinkRecursive(from,to,false);
	}
	
	/**
	 * Creates hard link, with additional option if to use cp -lr since it's default
	 * behavior differs from that of ln -f when the destination is a directory.
	 * 
	 * In most non-critical application, the you might want to slowish but predicatable version
	 * 
	 * @param fast
	 * @throws IOException
	 */
	public static void createHardLinkRecursive(String from, String to, boolean fast) throws IOException {
		//System.out.println("Hard-linking "+from+" -> "+to);		
		File file = new File(from);
		if(!file.exists())
			throw new IOException("Trying to hardlink nonexisting file "+from);
		// snsure we can make the target
		new File(to).getParentFile().mkdirs();
		if(fast && hardLinkRecursive != null){
			// do a quick cp -lr if it's supported
			createHardLinkRecursive(new File(from),new File(to));
		} else{
			if(file.isDirectory()){
				File[] files = file.listFiles();
				for(File f: files)
					createHardLinkRecursive(format(new String[]{from,f.getName()}),format(new String[] {to,f.getName()}));
			} else
				createHardLink(new File(from),new File(to));
		}
	}

	
	/**
	 * Create a soft link between a src and destination
	 * only on a local disk. HDFS does not support this
	 * @param target the target for symlink 
	 * @param linkname the symlink
	 */
	public static void createSymLink(String target, String linkname) throws IOException{
		String cmd = "ln -s -f " + target + " " + linkname;
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
		String path = sb.toString();
		if(path.endsWith(PATH_SEP))
			return path.substring(0,path.length()-PATH_SEP.length());
		else
			return path;
	}
	
	/**
	 * Construct a file from parts of path
	 * @param parts
	 */
	public static File formatFile(String[] parts){
		return new File(format(parts));
	}
	
	public static void deleteRecursive(String path){
		deleteRecursive(new File(path));
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
