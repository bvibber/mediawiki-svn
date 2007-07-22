package org.wikimedia.lsearch.suggest.api;

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

import java.util.Iterator;

import org.apache.lucene.index.TermEnum;
import org.wikimedia.lsearch.suggest.api.WordsIndexer.Word;

import java.io.*;

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
  private int REPORT = 1000;
  private TermEnum termEnum;
  private int count = 0;
  private String field;

  public LuceneDictionary(IndexReader reader, String field) {
    try {
   	 this.field = field;
       termEnum = reader.terms(new Term(field, ""));
     } catch (IOException e) {
       throw new RuntimeException(e);
     }    
  }
  
  public Word next() {
	  if(++count % REPORT == 0){
		  System.out.println("Processed "+count+" terms");
	  }
	  try {
		  while(true){
			  if(!termEnum.next())
				  return null;
			  else if(!termEnum.term().field().equals(field))
				  continue; // skip terms that are not from the desired field
			  
			  break;
		  }
	  } catch (IOException e) {
		  throw new RuntimeException(e);
	  }
	  return new Word(termEnum.term().text(),termEnum.docFreq());
  }
  
}
