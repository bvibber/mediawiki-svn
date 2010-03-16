package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;

/**
 * Searchable implementation that minimizes the number of calls to
 * searchable object. 
 * 
 * @author rainman
 *
 */
public interface SearchableMul extends Searchable {
	  /** Expert: Returns the stored fields of documents
	   * @see Searchable#document(int)
	   */
	  Document[] docs(int[] i) throws IOException;
	  /** Expert: Returns the stored fields of documents using a selector
	   * @see Searchable#document(int)
	   */  
	  Document[] docs(int[] i, FieldSelector sel) throws IOException;
}
