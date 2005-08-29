// created on 8/29/2005 at 12:13 AM
using System;
using System.Collections;
using System.IO;

class SqlWriter14 : SqlWriter {
	Random _random;
	Page _currentPage;
	Revision _lastRevision;
	
	public SqlWriter14(TextWriter output) : base(output) {
		_random = new Random();
	}
	
	public override void WriteStartPage(Page page) {
		_currentPage = page;
		_lastRevision = null;
	}
	
	public override void WriteEndPage() {
		if (_lastRevision != null)
			WriteCurRevision(_currentPage, _lastRevision);
		_currentPage = null;
		_lastRevision = null;
	}
	
	public override void WriteRevision(Revision revision) {
		if (_lastRevision != null)
			WriteOldRevision(_currentPage, _lastRevision);
		_lastRevision = revision;
	}
	
	private void WriteOldRevision(Page page, Revision revision) {
		IDictionary row = new Hashtable();
		row["old_id"] = revision.Id;
		row["old_namespace"] = page.Title.Namespace;
		row["old_title"] = TitleFormat(page.Title.Text);
		row["old_text"] = revision.Text;
		row["old_comment"] = revision.Comment;
		row["old_user"] = revision.Contributor.Id;
		row["old_user_text"] = revision.Contributor.Username;
		row["old_timestamp"] = TimestampFormat(revision.Timestamp);
		row["old_minor_edit"] = revision.Minor ? 1 : 0;
		row["old_flags"] = "utf-8";
		row["inverse_timestamp"] = InverseTimestamp(revision.Timestamp);
		InsertRow("old", row);
	}
	
	private void WriteCurRevision(Page page, Revision revision) {
		IDictionary row = new Hashtable();
		row["cur_id"] = revision.Id;
		row["cur_namespace"] = page.Title.Namespace;
		row["cur_title"] = TitleFormat(page.Title.Text);
		row["cur_text"] = revision.Text;
		row["cur_comment"] = revision.Comment;
		row["cur_user"] = revision.Contributor.Id;
		row["cur_user_text"] = revision.Contributor.Username;
		row["cur_timestamp"] = TimestampFormat(revision.Timestamp);
		row["cur_restrictions"] = page.Restrictions;
		row["cur_counter"] = 0;
		row["cur_is_redirect"] = revision.IsRedirect ? 1 : 0;
		row["cur_minor_edit"] = revision.Minor ? 1 : 0;
		row["cur_random"] = _random.NextDouble();
		row["cur_touched"] = TimestampFormat(DateTime.UtcNow);
		row["inverse_timestamp"] = InverseTimestamp(revision.Timestamp);
		InsertRow("cur", row);
	}

}
