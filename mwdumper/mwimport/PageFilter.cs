// created on 8/30/2005 at 11:15 PM

public abstract class PageFilter : IDumpWriter {
	IDumpWriter _sink;
	bool _showThisPage;
	
	public PageFilter(IDumpWriter sink) {
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
		_showThisPage = Pass(page);
		if (_showThisPage)
			_sink.WriteStartPage(page);
	}
	
	public void WriteEndPage() {
		if (_showThisPage)
			_sink.WriteEndPage();
	}
	
	public void WriteRevision(Revision revision) {
		if (_showThisPage)
			_sink.WriteRevision(revision);
	}
	
	protected virtual bool Pass(Page page) {
		return true;
	}
}
