package org.wikimedia.lsearch.beans;

import java.io.Serializable;

public class Redirect implements Serializable {
	int namespace;
	String title;
	int references;
	public Redirect(int namespace, String title, int references) {
		this.namespace = namespace;
		this.title = title;
		this.references = references;
	}
	public int getNamespace() {
		return namespace;
	}
	public void setNamespace(int namespace) {
		this.namespace = namespace;
	}
	public int getReferences() {
		return references;
	}
	public void setReferences(int references) {
		this.references = references;
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	@Override
	public String toString() {
		return namespace+":"+title+" ("+references+")";
	}
	
	public Title makeTitle(){
		return new Title(namespace,title);
	}
	
	public String getKey(){
		return namespace+":"+title;
	}
	
	
	
}
