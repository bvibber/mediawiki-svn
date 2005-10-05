/*
 * MediaWiki import/export processing tools
 * Copyright 2005 by Brion Vibber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * $Id$
 */

// Doesn't actually work yet...

package org.mediawiki.importer;

import java.util.GregorianCalendar;
import java.util.Random;

public class SqlWriter15 extends SqlWriter {
	Random random = new Random();
	Page currentPage;
	Revision lastRevision;
	
	public SqlWriter15(SqlFileStream output) {
		super(output);
	}
	
	public void writeStartPage(Page page) {
		currentPage = page;
		lastRevision = null;
	}
	
	public void writeEndPage() {
		if (lastRevision != null) {
			updatePage(currentPage, lastRevision);
		}
		currentPage = null;
		lastRevision = null;
	}
	
	public void writeRevision(Revision revision) {
		bufferInsertRow("text", new Object[][] {
				{"old_id", new Integer(revision.Id)},
				{"old_text", revision.Text},
				{"old_flags", "utf-8"}});

		bufferInsertRow("revision", new Object[][] {
				{"rev_id", new Integer(revision.Id)},
				{"rev_page", new Integer(currentPage.Id)},
				{"rev_text_id", new Integer(revision.Id)},
				{"rev_comment", revision.Comment},
				{"rev_user", new Integer(revision.Contributor.Id)},
				{"rev_user_text", revision.Contributor.Username},
				{"rev_timestamp", timestampFormat(revision.Timestamp)},
				{"rev_minor_edit", new Integer(revision.Minor ? 1 : 0)},
				{"rev_deleted", new Integer(0)}});
		
		lastRevision = revision;
	}
	
	private void updatePage(Page page, Revision revision) {
		bufferInsertRow("page", new Object[][] {
				{"page_id", new Integer(page.Id)},
				{"page_namespace", new Integer(page.Title.Namespace)},
				{"page_title", titleFormat(page.Title.Text)},
				{"page_restrictions", page.Restrictions},
				{"page_counter", new Integer(0)},
				{"page_is_redirect", new Integer(revision.isRedirect() ? 1 : 0)},
				{"page_is_new", new Integer(0)},
				{"page_random", new Double(random.nextDouble())},
				{"page_touched", timestampFormat(new GregorianCalendar())},
				{"page_latest", new Integer(revision.Id)},
				{"page_len", new Integer(revision.Text.length())}}); // TODO: UTF-8 byte length
		checkpoint();
	}

}
