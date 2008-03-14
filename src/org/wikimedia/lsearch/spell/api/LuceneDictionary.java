package org.wikimedia.lsearch.spell.api;

/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;

import org.apache.lucene.index.TermEnum;
import org.wikimedia.lsearch.util.ProgressReport;

import java.io.*;
import java.text.MessageFormat;

/**
 * Lucene Dictionary: terms taken from the given field
 * of a Lucene index.
 *
 * When using IndexReader.terms(Term) the code must not call next() on TermEnum
 * as the first call to TermEnum, see: http://issues.apache.org/jira/browse/LUCENE-6
 *
 * @author Nicolas Maisonneuve
 * @author Christian Mallwitz
 */
public class LuceneDictionary implements Dictionary {
  private TermEnum termEnum;
  private String field;
  private boolean first = true;
  private String prefix = null;
  private ProgressReport progress;

  public LuceneDictionary(IndexReader reader, String field) {
	  this(reader,field,"");
  }
  
  public LuceneDictionary(IndexReader reader, String field, String prefix) {
	  if(!prefix.equals(""))
		  this.prefix = prefix;

	  progress = new ProgressReport();
	  try {
		  this.field = field;
		  termEnum = reader.terms(new Term(field, prefix));
	  } catch (IOException e) {
		  throw new RuntimeException(e);
	  }    
  }
  
  /** Don't print progress */
  public void setNoProgressReport(){
	  progress = null;
  }
  
  public void setProgressReport(ProgressReport progress){
	  this.progress = progress;
  }
  
  public Word next() {
	  if(progress!=null)
		  progress.inc();
	  try {
		  while(true){
			  if(first && termEnum.term() != null){
				  first = false;
				  break;
			  }
			  else if(!termEnum.next())
				  return null;
			  else if(!termEnum.term().field().equals(field))
				  return null; // end of our field
			  else if(prefix != null && !termEnum.term().text().startsWith(prefix))
				  return null; // no longer same prefix
			  
			  break;
		  }
	  } catch (IOException e) {
		  throw new RuntimeException(e);
	  }
	  return new Word(termEnum.term().text(),termEnum.docFreq());
  }
  
}
