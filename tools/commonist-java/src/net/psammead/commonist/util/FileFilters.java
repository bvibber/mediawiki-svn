package net.psammead.commonist.util;

import java.io.File;
import java.io.FileFilter;

/** some imple FileFilter implementations */
public class FileFilters {
	/** shall not be instantiated */	
	private FileFilters() {}
	
	/** a FileFilter accepting non-hidden directories */
	public static final FileFilter VISIBLE_DIRECTORIES_ONLY	= new FileFilter() {
		public boolean accept(File tested) {
			return tested.isDirectory() 
				&& !tested.isHidden();
		}
	};

	/** a FileFilter accepting non-hidden files */ 
	public static final FileFilter VISIBLE_FILES_ONLY = new FileFilter() {
		public boolean accept(File tested) {
			return tested.isFile()
				&& !tested.isHidden();
		}
	};
}
