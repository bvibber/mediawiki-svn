// created on 8/28/2005 at 11:52 PM
using System;
using System.Collections;

class Revision {
	public int Id;
	public DateTime Timestamp;
	public Contributor Contributor;
	public string Comment;
	public string Text;
	public bool Minor;
	
	public bool IsRedirect {
		get {
			// todo
			return false;
		}
	}
	
	public Revision() {
		Comment = "";
		Text = "";
		Minor = false;
	}
}
