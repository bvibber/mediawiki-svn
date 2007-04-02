package sixdeg;
import com.opensymphony.xwork2.ActionSupport;
import org.apache.struts2.interceptor.ParameterAware;
import java.util.Map;
import org.wikimedia.links.linksc;

public class PathFinder extends ActionSupport implements ParameterAware {

	Map parameters;
	linksc.PathEntry[] path;
	String error;

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

		error = "Test";

		path = null;
		error = null;

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
			path = pathfinder.findPath("cywiki_p", wikiise(rfrom), wikiise(rto), ign_date);
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
