// created on 8/30/2005 at 11:10 PM

public class LatestFilter : IDumpWriter {
	IDumpWriter _sink;
	Revision _lastRevision;
	
	public LatestFilter(IDumpWriter sink) {
		_sink = sink;
	}
	
	public void Close() {
		_sink.Close();
	}
	
	public void WriteStartWiki() {
		_sink.WriteStartWiki();
	}
	
	public void WriteEndWiki() {
		_sink.WriteEndWiki();
	}
	
	public void WriteSiteinfo(Siteinfo info) {
		_sink.WriteSiteinfo(info);
	}
	
	public void WriteStartPage(Page page) {
		_sink.WriteStartPage(page);
	}
	
	public void WriteEndPage() {
		if (_lastRevision != null) {
			_sink.WriteRevision(_lastRevision);
			_lastRevision = null;
		}
		_sink.WriteEndPage();
	}
	
	public void WriteRevision(Revision revision) {
		_lastRevision = revision;
	}
}
