package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.AbstractAnalyzer;
import de.brightbyte.wikiword.analyzer.DeepTemplateExtractor;
import de.brightbyte.wikiword.analyzer.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.TemplateExtractor.Context;
import de.brightbyte.wikiword.biography.BiographyConceptType;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();
		/*
		templateExtractorFactory= new TemplateExtractor.Factory() {
			public TemplateExtractor newTemplateExtractor(Context context, AbstractAnalyzer.TextArmor armor) {
				DeepTemplateExtractor extractor = new DeepTemplateExtractor(context, armor);
				extractor.addContainerField("Protbox", "Codes");
				extractor.addContainerField("Protbox", "Caption");
				return extractor;
			}
		};
		*/
		/*
		//NOTE: apply template replacement only when stripping markup, but then before everything else
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ICD9", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ICD10", 3, true), " $2$3.$4 ") ); //XXX: use all 5 params?
		*/
		/*
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor( new WikiTextAnalyzer.ExactNameMatcher("Cite_journal"), 
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("journal", "journal")
						.addNormalizer(punctuationStripPattern, "")
						.setCondition(lifeScienceJournalPattern, 0, false) ) );
		
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Drugbox"),
				makeNamePropertySpec("IUPAC_name", "IUPAC", false, false).addCleanup(iupacCleanupPattern, ""),
				makeNamePropertySpec("synonyms", "Name", true, true),
				
				makeIdentifierPropertySpec("PubChem", "PubChem", pubChemChars),
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), 
				makeIdentifierPropertySpec("CAS_number", "CAS", casChars),
				
				makeIdentifierPropertySpec("smiles", "SMILES", smilesChars).addCleanup(breakStripPattern, ""),
				//makeIdentifierPropertySpec("chemical_formula", "Formula", true, false),

				makeIdentifierPropertySpec("ATC_supplemental", "ATC", atcChars),
				makeIdentifierPropertySpec("CAS_supplemental", "CAS", casChars),
				atcSpec
			) );
		*/
		/*
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Chembox_[Pp]harmacology", 0, true), 
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), 
				atcSpec
		) );
				
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Chembox_[Hh]azards", 0, true), 
				makeIdentifierPropertySpec("RTECS", "RTECS", rtecsChars)
		) );
		*/	
		/*
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("IUPAC") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("AnatomyLatin") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("ProteinSymbol") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("MeSHName") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("Name") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("Symbol") );
		*/
		/*
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor<ConceptType>(BiographyConceptType.DRUG, "_(treatments|therapies)$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor<ConceptType>(BiographyConceptType.DRUG, "Drugbox", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(BiographyConceptType.DRUG, "^Drugs_|^DrugsNav$", 0, null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor<ConceptType>(BiographyConceptType.DRUG, "Major_Drug_Groups", null));
		*/
	}
	
}
