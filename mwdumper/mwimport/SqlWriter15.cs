// created on 8/29/2005 at 12:49 AM

// Doesn't actually work yet...

using System;
using System.Collections;
using System.IO;

class SqlWriter15 : SqlWriter {
	Random _random;
	Page _currentPage;
	Revision _lastRevision;
	
	public SqlWriter15(TextWriter output) : base(output) {
		_random = new Random();
	}
	
	public override void WriteStartPage(Page page) {
		IDictionary row = new SortedList();
		row["page_id"] = page.Id;
		row["page_namespace"] = page.Title.Namespace;
		row["page_title"] = TitleFormat(page.Title.Text);
		row["page_restrictions"] = page.Restrictions;
		row["page_counter"] = 0;
		row["page_is_redirect"] = 0;
		row["page_is_new"] = 0;
		row["page_random"] = _random.NextDouble();
		row["page_touched"] = TimestampFormat(DateTime.UtcNow);
		row["page_latest"] = 0; // We'll touch this up at the end...
		row["page_len"] = 0; // .....
		InsertRow("page", row);
	}
	
	public override void WriteEndPage() {
		if (_lastRevision != null)
			UpdatePage(_currentPage, _lastRevision);
		_currentPage = null;
		_lastRevision = null;
	}
	
	public override void WriteRevision(Revision revision) {
		IDictionary row = new SortedList();
		row["old_id"] = null;
		row["old_text"] = revision.Text;
		row["old_flags"] = "utf-8";
		object textId = InsertRow("text", row);

		row = new SortedList();
		row["rev_id"] = revision.Id;
		row["rev_page"] = _currentPage.Id;
		row["rev_text_id"] = textId;
		row["rev_comment"] = revision.Comment;
		row["rev_user"] = revision.Contributor.Id;
		row["rev_user_text"] = revision.Contributor.Username;
		row["rev_timestamp"] = TimestampFormat(revision.Timestamp);
		row["rev_minor_edit"] = revision.Minor ? 1 : 0;
		row["rev_deleted"] = 0;
		
		InsertRow("rev", row);
		_lastRevision = revision;
	}
	
	private void UpdatePage(Page page, Revision revision) {
		IDictionary row = new SortedList();
		row["page_len"] = revision.Text.Length; // TODO: UTF-8 byte length
		row["page_latest"] = revision.Id;
		row["page_is_redirect"] = revision.IsRedirect ? 1 : 0;
		UpdateRow("page", row, "page_id", page.Id);
	}

}
