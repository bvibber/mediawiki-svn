	/**
	 * @see de.brightbyte.wikiword.LocalConceptQuerior#checkConsistency()
	 */
	public void checkConsistency() throws PersistenceException {
		checkIdSanity(resourceTable, "id");
		checkIdSanity(conceptTable, "id");
		//checkIdSanity(termTable, "id");
		checkIdSanity(definitionTable, "concept");
		checkIdSanity(rawTextTable, "resource");
		checkIdSanity(plainTextTable, "resource");
		
		checkReferentialIntegrity(conceptTable, "resource", true); //NOTE: red links generate concepts with no resource assigned  
		
		checkReferentialIntegrity(rawTextTable, "resource", false);
		checkReferentialIntegrity(plainTextTable, "resource", false);
		checkReferentialIntegrity(definitionTable, "concept", false);
	
		//XXX: checkReferentialIntegrity(linkTable, "term");
		checkReferentialIntegrity(linkTable, "anchor", true);
		checkReferentialIntegrity(linkTable, "target", false);
		//XXX: checkReferencePairConsistency(linkTable, "term", "term_text");
		checkReferencePairConsistency(linkTable, "anchor", "anchor_name");
		checkReferencePairConsistency(linkTable, "target", "target_name");
	
		checkReferentialIntegrity(broaderTable, "narrow", false);
		checkReferentialIntegrity(broaderTable, "broad", false);
		checkReferencePairConsistency(broaderTable, "narrow", "narrow_name");
		checkReferencePairConsistency(broaderTable, "broad", "broad_name");
	
		checkReferentialIntegrity(aliasTable, "source", false);
		checkReferentialIntegrity(aliasTable, "target", false);
		checkReferencePairConsistency(aliasTable, "source", "source_name");
		checkReferencePairConsistency(aliasTable, "target", "target_name");
	
		/*
		checkReferentialIntegrity(referenceTable, "source", false);
		checkReferentialIntegrity(referenceTable, "target", false);
		checkReferencePairConsistency(referenceTable, "source", "source_name");
		checkReferencePairConsistency(referenceTable, "target", "target_name");
		*/
		
		checkReferentialIntegrity(langlinkTable, "concept", false);
		checkReferencePairConsistency(langlinkTable, "concept", "concept_name");
	
		checkReferentialIntegrity(meaningTable, "concept", false);
		checkReferencePairConsistency(meaningTable, "concept", "concept_name");

		checkReferentialIntegrity(degreeTable, "concept", false);
		checkReferencePairConsistency(degreeTable, "concept", "concept_name");
	}
