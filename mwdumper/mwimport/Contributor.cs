// created on 8/28/2005 at 11:53 PM
public class Contributor {
	public string Username;
	public int Id;
	
	public string Address {
		get {
			return Username;
		}
	}
	
	public bool IsAnon {
		get {
			// Fixme; dumps w/o id numbers...
			return (Id == 0);
		}
	}
	
	public Contributor(string username, int id) {
		Username = username;
		Id = id;
	}
	
	public Contributor(string ip) {
		Username = ip;
		Id = 0;
	}
}
