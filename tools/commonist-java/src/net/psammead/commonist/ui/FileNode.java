package net.psammead.commonist.ui;

import java.io.File;
import java.util.Arrays;

import javax.swing.tree.DefaultMutableTreeNode;

import net.psammead.commonist.util.FileFilters;

/** a TreeNode for a File in the DirectoryTree */
public final class FileNode extends DefaultMutableTreeNode {
	// getUserObject setUserObject
	private static final File[] NO_FILES	= new File[0];
	
	// state
	
	private File	file;
	private	File[]	childFiles;
//	private	boolean	upToDate;
	
	// API
	
	/** create a FileNode */
	public FileNode(File file) {
		this.file	= file;
		childFiles	= NO_FILES;
//		upToDate	= false;
	}
	
	/** get the File this Node stands for */
	public File getFile() {
		return file;
	}
	
	/** ensures the node has a single child every directory below it */
	public void update() {
		removeAllChildren();
		childFiles	= NO_FILES;
		
		final File[]	listed	= file.listFiles(FileFilters.VISIBLE_DIRECTORIES_ONLY);
		if (listed == null)	return;
		
		childFiles	= listed;
		Arrays.sort(childFiles);	//  Comparator?
		for (int i=0; i<childFiles.length; i++) {
			add(new FileNode(childFiles[i]));
		}
		
//		upToDate	= true;
	}
	
	// Node
	
	@Override
	public boolean getAllowsChildren() {
//		if (!upToDate)	return true;
		return childFiles.length != 0;
	}

	@Override
	public boolean isLeaf() {
		return false;
	}
	
	@Override
	public String toString() {
		//if (!upToDate)	update();
		return file.getName().length() != 0
			? file.getName() : file.getPath();
		//  + " (" + childFiles.length + ")";
	}
}

