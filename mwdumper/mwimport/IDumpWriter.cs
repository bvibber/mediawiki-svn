// created on 8/29/2005 at 12:06 AM

public interface IDumpWriter {
	void Close();
	
	void WriteStartWiki();
	void WriteEndWiki();
	
	void WriteSiteinfo(Siteinfo info);
	
	void WriteStartPage(Page page);
	void WriteEndPage();
	
	void WriteRevision(Revision revision);
	//void WriteUpload(Upload upload); // for the future
}
