package net.psammead.util;

import java.io.File;

import net.psammead.util.annotation.ImmutableValue;

@ImmutableValue 
public final class FileBuilder {
	public static final FileBuilder	from(File file) {
		return new FileBuilder(file);
	}
	
	public static final FileBuilder	current() {
		return new FileBuilder(new File(System.getProperty("user.dir")));
	}
	
	public static final FileBuilder	home() {
		return new FileBuilder(new File(System.getProperty("user.home")));
	}

	private final File	file;
	
	public FileBuilder(File file) {
		this.file	= file;
	}
	
	public File file() {
		return file;
	}
	
	public FileBuilder child(String name) {
		return new FileBuilder(new File(file, name));
	}
	
	public FileBuilder parent() {
		return new FileBuilder(file.getParentFile());
	}
}
