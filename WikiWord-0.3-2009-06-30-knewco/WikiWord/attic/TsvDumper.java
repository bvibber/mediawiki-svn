package de.brightbyte.wikiword;

import java.io.IOException;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.LocalConceptReference;

/**
 * A TsvDumper transferts the data optained from a WikiStoreQuerior into a 
 * flat file format, with one line per record, and the fields of each record
 * separated by TAB (TSV format).
 */
public class TsvDumper {

	/*
	public void dumpTermsForConcept(String concept, Output out) throws SQLException, IOException {
		dumpQuery(queryor.queryTermsForConcept(concept), out);
	}

	public void dumpConceptsForTerm(String term, Output out) throws SQLException, IOException {
		dumpQuery(queryor.queryConceptsForTerm(term), out);
	}
	 */
	
	public void dumpLocalConceptReferences(DataSet<LocalConceptReference> data, Output out, boolean hasCard) throws PersistenceException, IOException {
		DataCursor<LocalConceptReference> cursor = data.cursor();
		LocalConceptReference ref;
		while ((ref=cursor.next())!=null) {
			if (hasCard) {
				out.write(String.valueOf(ref.getCardinality()));
				out.write("\t");
			}
			
			out.write(ref.getName());
			
			out.newline();
		}		
	}
	
	public void dumpLocalConcepts(DataSet<LocalConcept> data, Output out, boolean hasCard) throws PersistenceException, IOException {
		DataCursor<LocalConcept> cursor = data.cursor();
		LocalConcept ref;
		while ((ref=cursor.next())!=null) {
			if (hasCard) {
				out.write(String.valueOf(ref.getCardinality()));
				out.write("\t");
			}
			
			out.write(ref.getName());
			
			out.newline();
		}		
	}
	

}
