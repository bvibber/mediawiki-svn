// created on 8/28/2005 at 11:53 PM
struct Contributor {
	public string Username;
	public int Id;
	
	public string Address {
		get {
			return Username;
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
