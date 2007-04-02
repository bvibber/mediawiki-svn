package sixdeg;
import com.opensymphony.xwork2.ActionSupport;
import org.apache.struts2.interceptor.ParameterAware;
import java.util.Map;
import java.util.TreeMap;
import org.wikimedia.links.linksc;

public class PathFinder extends ActionSupport implements ParameterAware {
	public static class Wiki {
		String url;
		String code;
		String database;

		public String getDatabase() {
			return database;
		}

		public String getUrl() {
			return url;
		}

		public String getCode() {
			return code;
		}

		public Wiki(String db) {
			this.database = db;
			this.code = db.substring(0, db.length() - 6);
			this.url = "http://" + code + ".wikipedia.org/";
		}
	}

	static Wiki[] wikis = {
		new Wiki("cywiki_p"),
		new Wiki("dewiki_p"),
		new Wiki("enwiki_p"),
		new Wiki("frwiki_p")
	};

	static Wiki getAWiki(String name) {
		for (Wiki w : wikis) {
			if (w.getDatabase().equals(name))
				return w;
		}

		return getAWiki("enwiki_p");
	}

	Map parameters;
	linksc.PathEntry[] path;
	String error;
	Wiki wiki;

	public Map<String, String> getWikimap() {
		Map<String, String> m = new TreeMap<String, String>();
		for (Wiki w : wikis)
			m.put(w.getDatabase(), w.getUrl());

		return m;
	}

	public Wiki getWiki() {
		return wiki;
	}

	public int getLength() {
		return path.length;
	}

	public String getError() {
		return error;
	}

	public linksc.PathEntry[] getPath() {
		return path;
	}

	public void setParameters(Map parameters) {
		this.parameters = parameters;
	}

	private String wikiise (String s) {
		String r = s.substring(0, 1).toUpperCase() + s.substring(1, s.length());
		return r.replaceAll(" ", "_");
	}

	public String execute() throws Exception {
		String[] from_ = ((String[]) parameters.get("from"));
		String[] to_ = ((String[]) parameters.get("to"));
		String[] wikiname = ((String[]) parameters.get("wiki"));

		path = null;
		error = null;

		if (wikiname == null)
			wiki = getAWiki("enwiki_p");
		else
			wiki = getAWiki(wikiname[0]);

		if (from_ == null || to_ == null || from_[0].length() == 0 || to_[0].length() == 0)
			return INPUT;

		linksc pathfinder = new linksc();

		String from = from_[0].trim();
		String to = to_[0].trim();

		from = new String(from.getBytes("ISO-8859-1"), "UTF-8");
		to = new String(to.getBytes("ISO-8859-1"), "UTF-8");

		boolean ign_date = false;
		ign_date = (parameters.get("ign_dates") != null);

		String rfrom = wikiise(from);
		String rto = wikiise(to);

		try {
			path = pathfinder.findPath(wiki.getDatabase(), wikiise(rfrom), wikiise(rto), ign_date);
		} catch (org.wikimedia.links.ErrorException e) {
			error = e.geterror();
			return "error";
		}

		if (path != null && path.length == 0) {
			error = "No route found.";
			return "error";
		}

		return "path";
	}
}
