package de.brightbyte.wikiword.lifescience.wikis;

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
import de.brightbyte.wikiword.lifescience.LifeScienceConceptType;

public class WikiConfiguration_enwiki extends WikiConfiguration {
	
	protected static String[] resolveSequence(String s, int max) {
		int idx = s.indexOf('-');
		if (idx<0) return new String[] { s };
		
		return resolveSequence(s.substring(0, idx).trim(), s.substring(idx+1).trim(), max);
	}
	
	protected static String[] resolveSequence(String from, String to, int max) {
		int i = 0;
		int j = from.length()-1;
		int k = to.length()-1;
		
		while (i<from.length() && i<to.length() && from.charAt(i)==to.charAt(i)) {
			i++;
		}
		
		while (j>=i && k>=i && from.charAt(j)==to.charAt(k)) {
			j--;
			k--;
		}
		
		if (j<i || k<i) return null; 
		
		String f = from.substring(i, j+1); 
		String t = to.substring(i, k+1);
		
		String prefix = from.substring(0, i);
		String suffix = from.substring(j+1);
		
		int a;
		int b;
		
		try {
			a = Integer.parseInt(f);
			b = Integer.parseInt(t);
		}
		catch (NumberFormatException ex) {
			return null;
		}
		
		int c = b-a +1;
		if (c>max) return null;
		
		String[] ss = new String[c];
		
		for (int n=0; n<c; n++) {
			ss[n] = prefix + (a+n) + suffix;
		}
		
		return ss;
	}
	
	//FIXME: for some, <br> resp. \n needs to be stripped!

	protected static final String numericChars = "0-9";
	protected static final String upperAlphabeticChars = "A-Z";
	protected static final String alphabeticChars = upperAlphabeticChars+"a-z";
	protected static final String upperAlphaNumericChars = upperAlphabeticChars+numericChars;
	protected static final String alphaNumericChars = alphabeticChars+numericChars;
	protected static final String dashChars = "-\u2212\uFE63\u2010-\u2014\uFE58\uFF0D";
	
	protected static final Pattern identifierSeparatorPattern = Pattern.compile(",\\p{IsZ}+|[\\p{IsZ};]+", 0);
	protected static final Pattern nameSeparatorPattern = Pattern.compile(",\\p{IsZ}+|[\r\n;]+", 0);
	protected static final Pattern badStuffStripPattern = Pattern.compile("[\r\n]+", 0);
	protected static final Pattern spaceStripPattern = Pattern.compile("\\p{IsZ}+", 0);
	protected static final Pattern iupacCleanupPattern = Pattern.compile("(?<=["+dashChars+numericChars+"]|[0-9][a-z])\\p{IsZ}+", 0);
	protected static final Pattern punctuationStripPattern = Pattern.compile("[\r\n,.;/]+", 0);
	protected static final Pattern breakStripPattern = Pattern.compile("[\r\n\\p{IsZ}]+", 0);
	
	private static final String uniProtChars = "["+upperAlphaNumericChars+"]{6,}";
	private static final String pubChemChars = "["+numericChars+"]+";
	private static final String pbbChars = "["+numericChars+"]+";
	private static final String drugBankChars = "["+upperAlphabeticChars+"]{2,}["+numericChars+"]{4,}";
	private static final String casChars = "["+numericChars+"]+(["+dashChars+"]["+numericChars+"]+)*";
	private static final String smilesChars = "["+dashChars+"+="+alphaNumericChars+"/\\\\()@#:\\[\\]>.]+"; //FIXME: not greedy enough
	private static final String atcChars = "["+upperAlphabeticChars+"]{6,}";
	private static final String diseasesDbChars = "["+numericChars+"]+";
	
	private static final String icd10Chars = "["+upperAlphabeticChars+"]["+numericChars+"]+(\\.["+numericChars+"]*)?"; //FIXME: ranges!
	private static final String icd9Chars = "["+numericChars+"]+(\\.["+numericChars+"]*)?"; //FIXME: ranges!
	private static final String icdOChars = "M["+numericChars+"]+(/["+numericChars+"]*)?";
	private static final String omimChars = "["+numericChars+"]{4,}";
	private static final String medlinePlusChars = "["+numericChars+"]{6,}";
	private static final String meshChars = "["+upperAlphabeticChars+"]?["+numericChars+"]+(\\.["+numericChars+"]+)*";
	private static final String eMedicineChars = "["+alphabeticChars+"]+/["+numericChars+"]+";
	private static final String chemAbbrevChars = "["+dashChars+alphaNumericChars+"(),]+";
	
	private static final String inChIChars = "["+dashChars+"+"+alphabeticChars+"\\(\\),/]+"; 
	private static final String einecsChars = "["+numericChars+"]+(["+dashChars+"]["+numericChars+"]+)*";
	private static final String ecChemChars = "["+numericChars+"]+(["+dashChars+"]["+numericChars+"]+)*";
	private static final String uncasnChars = "["+numericChars+"]{4,}";
	private static final String rtecsChars = "["+upperAlphabeticChars+"]+["+numericChars+"]+";
	private static final String keggChars = "["+upperAlphabeticChars+"]+["+numericChars+"]+";
	private static final String chEbiChars = "["+numericChars+"]+";
	private static final String gmelinChars = "["+numericChars+"]+";
	private static final String beilsteinChars = "["+numericChars+"]+(["+dashChars+"]["+numericChars+"]+)*";
	private static final String hgncChars = "["+numericChars+"]+";
	private static final String hgiChars = "["+numericChars+"]+";
	private static final String proteinSymbolChars = "["+alphaNumericChars+"]+(["+dashChars+"]["+alphaNumericChars+"]+)*(\\.["+numericChars+"]+)*";
	private static final String entrezGeneChars = "["+numericChars+"]+";
	private static final String refSeqChars = "["+upperAlphabeticChars+"]+_["+numericChars+"]+"; //NOTE: value may contain a decimal point, but we ignore that bit for better matching 
	private static final String pdbChars = "["+upperAlphaNumericChars+"]{4,}";
	
	private static final String ecEnzymeChars = "["+numericChars+"](\\.["+numericChars+"]+)*";
	private static final String homoloGeneChars = "["+numericChars+"]+";
	private static final String mgiChars = "["+numericChars+"]{6,}";
	private static final String ensemblChars = "["+upperAlphabeticChars+"]{2,}["+numericChars+"]{10,}";
	private static final String icscChars = "["+numericChars+"]{4,}";
	private static final String goCodeChars = "["+numericChars+"]{6,}";
	//private static final String chemFormulaChars = "["+dashChars+"+,\\(\\)"+alphaNumericChars+"]{3,}";
	private static final String chemSpiderChars = "["+numericChars+"]+";
	private static final String threeDMetChars = "["+alphaNumericChars+"]{3,}";
	
	//TODO: exclude "Biography"... 
	public static final String lifeScienceJournalPattern = "(^|[ _])(Chem[a-z]*|Bio[a-z]*|Gen[eo][a-z]*|Med[a-z]*|Cell[a-z]*|DNA|RNA|Nucleic|EMBO|FEBS|Onco[a-z]*|Blood|Immono[a-z]*|Cancer|Virol[a-z]*|Med[a-z]*|Clin[a-z]*|Lancet|Neuro[a-z]*|Zootaxa|JAMA|FASEB|Bacter[a-z]*|Mutat[a-z]*|Mol[a-z]*|Protein|Dermat[a-z]*|Pathol[a-z]*|Endocr[a-z]*|Microbio[a-z]*)($|[_ ])";

	
	protected static WikiTextAnalyzer.DefaultTemplateParameterPropertySpec makeNamePropertySpec(String param, String prop, boolean multi, boolean space) {
		WikiTextAnalyzer.DefaultTemplateParameterPropertySpec spec = new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec(param, prop);
		
		if (multi) {
			if (space) spec.setSplitPattern(nameSeparatorPattern);
			else spec.setSplitPattern(identifierSeparatorPattern);
		}
		
		if (space) spec.addNormalizer(badStuffStripPattern, "");
		else spec.addNormalizer(spaceStripPattern, "");
		
		return spec;
	}

	protected static WikiTextAnalyzer.DefaultTemplateParameterPropertySpec makeIdentifierPropertySpec(String param, String prop, String pattern) {
		WikiTextAnalyzer.DefaultTemplateParameterPropertySpec spec = new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec(param, prop);
		
		pattern = "(?<=[^\\w\\d]|^)("+pattern+")(?=[^\\w\\d]|$)";
		
		spec.setFindPattern(Pattern.compile(pattern));
		
		if (pattern.indexOf('\u2212')>=0) { //XXX: hack for normalizing dashes
			spec.addNormalizer(Pattern.compile("["+dashChars+"]"), "-");
		}
		
		return spec;
	}

	public WikiConfiguration_enwiki() {
		super();
		
		templateExtractorFactory= new TemplateExtractor.Factory() {
			public TemplateExtractor newTemplateExtractor(Context context, AbstractAnalyzer.TextArmor armor) {
				DeepTemplateExtractor extractor = new DeepTemplateExtractor(context, armor);
				extractor.addContainerField("Protbox", "Codes");
				extractor.addContainerField("Protbox", "Caption");
				return extractor;
			}
		};
		
		//NOTE: apply template replacement only when stripping markup, but then before everything else
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ICD9", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ICD10", 3, true), " $2$3.$4 ") ); //XXX: use all 5 params?
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ICDO", 2, true), " M$2/$3 ") ); 
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("CAS", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("ATC", 2, true), " $2$3 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("DiseasesDB2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("OMIM\\d?", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("SMILES", 1, true), " $2 ") ); //FIXME: named param S= !
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("eMedicine2", 2, true), " $2/$3 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("MedlinePlus2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("PDB", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("PDB2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("PDB3", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("EC_number", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("OMIM", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("EntrezGene", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("UniProt", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new WikiTextAnalyzer.RegularExpressionMangler( templatePattern("RefSeq", 1, true), " $2 ") );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor( new WikiTextAnalyzer.ExactNameMatcher("Cite_journal"), 
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("journal", "journal")
						.addNormalizer(punctuationStripPattern, "")
						.setCondition(lifeScienceJournalPattern, 0, false) ) );
		
		WikiTextAnalyzer.TemplateParameterPropertySpec atcSpec = new WikiTextAnalyzer.AbstractTemplateParameterPropertySpec("ATC") {
			private Matcher validator = Pattern.compile("["+upperAlphaNumericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiTextAnalyzer.WikiPage page, TemplateExtractor.TemplateData params) {
				CharSequence code= params.getParameter("ATCCode");
				if (code!=null) {
					if (code.length()==0) return null;
					validator.reset(code);
					if (!validator.matches()) return null;
					return code;
				}
				
				CharSequence pre= params.getParameter("ATC_prefix");
				CharSequence suf= params.getParameter("ATC_suffix");
				
				if (pre==null) pre = params.getParameter("ATCCode_prefix");
				if (suf==null) suf = params.getParameter("ATCCode_suffix");
				
				if (pre==null || suf==null) return null;
				if (pre.length()==0 || suf.length()==0) return null;
				
				validator.reset(pre);
				if (!validator.matches()) return null;
				
				validator.reset(suf);
				if (!validator.matches()) return null;
				
				return pre+""+suf;
			}
		};
		
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
		
		WikiTextAnalyzer.TemplateParameterPropertySpec eMedSpec = new WikiTextAnalyzer.AbstractTemplateParameterPropertySpec("eMedicine") {
			private Matcher subjectValidator = Pattern.compile("["+alphaNumericChars+"]+").matcher("");
			private Matcher topicValidator = Pattern.compile("["+numericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiTextAnalyzer.WikiPage page, TemplateExtractor.TemplateData params) {
				CharSequence pre= params.getParameter("eMedicineSubj");
				CharSequence suf= params.getParameter("eMedicineTopic");
				if (pre==null || suf==null) return null;
				if (pre.length()==0 || suf.length()==0) return null;
				
				subjectValidator.reset(pre);
				if (pre.equals("search")) return null;
				if (!subjectValidator.matches()) return null;
				
				topicValidator.reset(suf);
				if (!topicValidator.matches()) return null;
				
				return pre+"/"+suf;
			}
		};
		
		
		WikiTextAnalyzer.TemplateParameterPropertySpec dorlandsSpec = new WikiTextAnalyzer.AbstractTemplateParameterPropertySpec("Dorlands") {
			private Matcher preValidator = Pattern.compile("["+alphabeticChars+"]_["+numericChars+"]+").matcher("");
			private Matcher sufValidator = Pattern.compile("["+numericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiTextAnalyzer.WikiPage page, TemplateExtractor.TemplateData params) {
				CharSequence pre= params.getParameter("DorlandsPre");
				CharSequence suf= params.getParameter("DorlandsSuf");
				if (pre==null || suf==null) return null;
				if (pre.length()==0 || suf.length()==0) return null;
				
				preValidator.reset(pre);
				if (!preValidator.matches()) return null;
				
				sufValidator.reset(suf);
				if (!sufValidator.matches()) return null;
				
				return pre+"/"+suf;
			}
		};
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Infobox_Disease|Infobox_Symptom|SignSymptom_infobox|DiseaseDisorder_infobox", 0, true),
				makeIdentifierPropertySpec("DiseasesDB", "DiseasesDB", diseasesDbChars),
				makeIdentifierPropertySpec("ICD10", "ICD10", icd10Chars),
				makeIdentifierPropertySpec("ICD9", "ICD9", icd9Chars),
				makeIdentifierPropertySpec("ICDO", "ICDO", icdOChars),
				makeIdentifierPropertySpec("OMIM", "OMIM", omimChars),
				makeIdentifierPropertySpec("MedlinePlus", "MedlinePlus", medlinePlusChars),
				makeIdentifierPropertySpec("MeshID", "MeSH", meshChars), //FIXME: UniqueId vs. TreeNumber
				makeIdentifierPropertySpec("MeshNumber", "MeSH", meshChars), //FIXME: UniqueId vs. TreeNumber
				makeNamePropertySpec("MeshName", "MeSHName", true, true),
				makeIdentifierPropertySpec("OMIM_mult", "OMIM", omimChars),
				makeIdentifierPropertySpec("DiseasesDB_mult", "DiseasesDB", diseasesDbChars),
				makeIdentifierPropertySpec("MedlinePlus_mult", "MedlinePlus", medlinePlusChars),
				makeIdentifierPropertySpec("eMedicine_mult", "eMedicine", eMedicineChars),
				eMedSpec
			) );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Chembox_new"),
				makeNamePropertySpec("IUPACName", "IUPAC", true, true).addCleanup(iupacCleanupPattern, ""),
				makeNamePropertySpec("OtherNames", "Name", true, true) //FIXME: often spaced for auto-breaks and separated by <br>
			) );
		
		//TODO: terms from names
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Chembox_[Pp]harmacology", 0, true), 
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), 
				atcSpec
		) );
				
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Chembox_[Hh]azards", 0, true), 
				makeIdentifierPropertySpec("RTECS", "RTECS", rtecsChars)
		) );
				
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Chembox_[Ii]dentifiers", 0, true), 
				makeIdentifierPropertySpec("Abbreviations", "ChemAbbrev", chemAbbrevChars),
				makeIdentifierPropertySpec("CASNo", "CAS", casChars),
				makeIdentifierPropertySpec("SMILES", "SMILES", smilesChars).addCleanup(breakStripPattern, ""),
				makeIdentifierPropertySpec("FullSMILES", "SMILS", smilesChars).addCleanup(breakStripPattern, ""),
				makeIdentifierPropertySpec("InChI", "InChI", inChIChars).addCleanup(breakStripPattern, ""),
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), 
				makeIdentifierPropertySpec("EINECS", "EINECS", einecsChars),
				makeIdentifierPropertySpec("EC-number", "EC/chem", ecChemChars), //NOTE: replaces EINECS and ELINCS; not be confused with the Enzyme Commission EC number for enzymes.				makeIdentifierPropertySpec("EINECSCASNO", "CAS", true, false),
				makeIdentifierPropertySpec("UNNumber", "UNCASN", uncasnChars),
				makeIdentifierPropertySpec("PubChem", "PubChem", pubChemChars),
				makeIdentifierPropertySpec("RTECS", "RTECS", rtecsChars),
				makeIdentifierPropertySpec("KEGG", "KEGG", keggChars),
				makeNamePropertySpec("MeSHName", "MeSHName", true, true),
				makeIdentifierPropertySpec("ChEBI", "ChEBI", chEbiChars),
				makeIdentifierPropertySpec("Beilstein", "Beilstein", beilsteinChars),
				makeIdentifierPropertySpec("Gmelin", "Gmelin", gmelinChars),
				makeIdentifierPropertySpec("3DMet", "3DMet", threeDMetChars),
				makeIdentifierPropertySpec("ChemSpiderID", "ChemSpider", chemSpiderChars),
				atcSpec
			) );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("NatOrganicBox", 0, true), 
				makeNamePropertySpec("name", "IUPAC", false, false).addCleanup(iupacCleanupPattern, ""),
				makeNamePropertySpec("synonyms", "Name", true, true),
				makeIdentifierPropertySpec("abbreviations", "ChemAbbrev", chemAbbrevChars),
				//makeIdentifierPropertySpec("chemical_formula", "Formula", chemFormulaChars),

				makeIdentifierPropertySpec("CAS", "CAS", casChars),
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), //FIXME: getting "?"
				makeIdentifierPropertySpec("SMILES", "SMILES", smilesChars).addCleanup(breakStripPattern, ""),
				makeIdentifierPropertySpec("EINECS", "EINECS", einecsChars),
				makeIdentifierPropertySpec("PubChem", "PubChem", pubChemChars),
				atcSpec
			) );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Elementbox", 0, true), 
				makeNamePropertySpec("name", "Name", true, true),
				makeIdentifierPropertySpec("number", "ElementNumber", "["+numericChars+"]"),
				makeIdentifierPropertySpec("symbol", "ElementSymbol", "["+alphaNumericChars+"]"),

				makeIdentifierPropertySpec("CAS number", "CAS", casChars),
				atcSpec
			) );
		
		//TODO: ...as terms
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox"),
				makeNamePropertySpec("Name", "ProteinName", false, true),
				makeNamePropertySpec("Names", "ProteinName", true, true),

				//FIXME: stuff nested in Codes=
				
				//makeIdentifierPropertySpec("Gene", "HGNC", hgncChars),
				makeIdentifierPropertySpec("HGNCid", "HGNC", hgncChars),
				makeIdentifierPropertySpec("MGIid", "MGI", hgiChars),
				makeIdentifierPropertySpec("Symbol", "ProteinSymbol", proteinSymbolChars),
				makeIdentifierPropertySpec("AltSymbols", "ProteinSymbol", proteinSymbolChars),
				
				makeIdentifierPropertySpec("EntrezGene", "EntrezGene", entrezGeneChars),
				makeIdentifierPropertySpec("OMIM", "OMIM", omimChars),
				makeIdentifierPropertySpec("RefSeq", "RefSeq", refSeqChars), 
				makeIdentifierPropertySpec("UniProt", "UniProt", uniProtChars),
				makeIdentifierPropertySpec("PDB", "PDB", pdbChars),
				makeIdentifierPropertySpec("ECnumber", "EC/enzyme", ecEnzymeChars)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.PatternNameMatcher("Enzyme_(links|references)", 0, true),
				makeIdentifierPropertySpec("EC_number", "EC/enzyme", ecEnzymeChars)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("GO_code_links"),
				makeNamePropertySpec("name", "ProteinName", false, true),
				makeIdentifierPropertySpec("GO_code", "GO_code", goCodeChars)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("CAS_registry"), //XXX: only as identifying element, or also in-context?
				makeIdentifierPropertySpec("1", "CAS", casChars)
			) );

		//Stuff from the container field Codes in Protbox:
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::OMIM"),
				makeIdentifierPropertySpec("1", "OMIM", omimChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::OMIM2"),
				makeIdentifierPropertySpec("1", "OMIM", omimChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::EntrezGene"),
				makeIdentifierPropertySpec("1", "EntrezGene", entrezGeneChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::UniProt"),
				makeIdentifierPropertySpec("1", "UniProt", uniProtChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::RefSeq"),
				makeIdentifierPropertySpec("1", "RefSeq", refSeqChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::EC_number"),
				makeIdentifierPropertySpec("1", "EC/enzyme", ecEnzymeChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Codes::PDB"),
				makeIdentifierPropertySpec("1", "PDB", pdbChars) ) );
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protbox.Caption::PDB"),
				makeIdentifierPropertySpec("1", "PDB", pdbChars) ) );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Protein"),
				makeIdentifierPropertySpec("Symbol", "ProteinSymbol", proteinSymbolChars),
				makeIdentifierPropertySpec("AltSymbols", "ProteinSymbol", proteinSymbolChars),
				makeIdentifierPropertySpec("CAS_number", "CAS", casChars),
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), //FIXME: getting "?"
				makeIdentifierPropertySpec("EntrezGene", "EntrezGene", entrezGeneChars),
				makeIdentifierPropertySpec("HGNCid", "HGNC", hgncChars),
				makeIdentifierPropertySpec("MGIid", "MGI", hgiChars),
				makeIdentifierPropertySpec("OMIM", "OMIM", omimChars),
				makeIdentifierPropertySpec("PDB", "PDB", pdbChars),
				makeIdentifierPropertySpec("RefSeq", "RefSeq", refSeqChars),
				makeIdentifierPropertySpec("UniProt", "UniProt", uniProtChars),
				makeIdentifierPropertySpec("ECnumber", "EC/enzyme", ecEnzymeChars),
				makeIdentifierPropertySpec("ATC_supplemental", "ATC", atcChars),
				makeIdentifierPropertySpec("CAS_supplemental", "CAS", casChars),
				atcSpec
			) );
		
		//TODO: pull names and symbols as terms!
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("GNF_Protein_box"),
				makeIdentifierPropertySpec("Symbol", "ProteinSymbol", proteinSymbolChars),
				makeIdentifierPropertySpec("AltSymbols", "ProteinSymbol", proteinSymbolChars),
				makeIdentifierPropertySpec("HGNCid", "HGNC", hgncChars),
				makeIdentifierPropertySpec("MGIid", "MGI", hgiChars),
				makeIdentifierPropertySpec("OMIM", "OMIM", omimChars),
				makeIdentifierPropertySpec("PDB", "PDB", pdbChars),
				makeIdentifierPropertySpec("ECnumber", "EC/enzyme", ecEnzymeChars),
				makeIdentifierPropertySpec("Homologene", "HomoloGene", homoloGeneChars),
				makeIdentifierPropertySpec("MGIid", "MGI", mgiChars)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("GNF_Ortholog_box"),
				makeIdentifierPropertySpec("Hs_Uniprot", "UniProt", uniProtChars),
				makeIdentifierPropertySpec("Mm_Uniprot", "UniProt", uniProtChars),
				makeIdentifierPropertySpec("Hs_Ensembl", "Ensembl", ensemblChars),
				makeIdentifierPropertySpec("Mm_Ensembl", "Ensembl", ensemblChars),
				makeIdentifierPropertySpec("Hs_EntrezGene", "EntrezGene", entrezGeneChars),
				makeIdentifierPropertySpec("Mm_EntrezGene", "EntrezGene", entrezGeneChars),
				makeIdentifierPropertySpec("Hs_RefseqProtein", "RefSeq", refSeqChars),
				makeIdentifierPropertySpec("Mm_RefseqProtein", "RefSeq", refSeqChars),
				makeIdentifierPropertySpec("Hs_RefseqmRNA", "RefSeq", refSeqChars),
				makeIdentifierPropertySpec("Mm_RefseqmRNA", "RefSeq", refSeqChars)
			) );
		
		//TODO: {{MedlinePlus}}...?
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Infobox_Anatomy"),
				makeNamePropertySpec("Latin", "AnatomyLatin", true, true),
				makeNamePropertySpec("GraySubject", "GraySubject", true, true),
				makeNamePropertySpec("MeshName", "MeSHName", true, true),
				makeIdentifierPropertySpec("MeshNumber", "MeSH", meshChars),
				dorlandsSpec
			) );
		
		//FIXME: URLDecode for MeshName, etc!

		//TODO: Infobox_(Artery|Brain|Bone|...)
		//      GraySubject, MeSH name&number, DorlandsPre/DorlandsSuf (Elsevier )
		//TODO: NeuroNames

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("ICSC"),
				makeIdentifierPropertySpec("1", "ICSC", icscChars)
			) );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("PubChem"),
				makeIdentifierPropertySpec("1", "PubChem", pubChemChars)
			) );
		
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("PBB"),
				makeIdentifierPropertySpec("geneid", "_PBB_", pbbChars)
			) );
		
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("IUPAC") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("AnatomyLatin") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("ProteinSymbol") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("MeSHName") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("Name") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("Symbol") ); 
		
		supplementSensors.add( new WikiTextAnalyzer.TitleSensor<ResourceType>(ResourceType.SUPPLEMENT, Namespace.TEMPLATE, "PBB/\\d+", 0));
		
		supplementNameExtractors.add( new WikiTextAnalyzer.PropertyValueExtractor("_PBB_").setPrefix("Template:PBB/") );
		
		supplementedConceptExtractors.add( new WikiTextAnalyzer.TitlePartExtractor(Namespace.MAIN, "(.*)_\\(data_page\\)", 0, "$1") );
		supplementedConceptExtractors.add( new WikiTextAnalyzer.TitlePartExtractor(Namespace.TEMPLATE, "Infobox_(.*)", 0, "$1")
				.addCondition( new WikiTextAnalyzer.HasCategorySensor<ResourceType>(ResourceType.SUPPLEMENT, "Periodic_table_infobox_templates") ) );
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.DRUG, "_(treatments|therapies)$", 0));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor<ConceptType>(LifeScienceConceptType.DRUG, "Drugbox", null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.DRUG, "^Drugs_|^DrugsNav$", 0, null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateSensor<ConceptType>(LifeScienceConceptType.DRUG, "Major_Drug_Groups", null));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.PROTEIN, "EC_\\d+(\\.\\d+)*", 0)); //FIXME: too much meta-stuff!
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.PROTEIN, "^(Enzyme_links|PBB|Protein|GNF_.*_box)$", 0, null) ); 
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.CHEMICAL, "^Chembox|^NatOrganicBox$|^ICSC$|^Elementbox$", 0, null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategorySensor<ConceptType>(LifeScienceConceptType.CHEMICAL, "Chemical_elements"));

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.DISEASE, "^(Infobox_Disease|Infobox_Symptom|SignSymptom_infobox|DiseaseDisorder_infobox)$", 0, null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.DISEASE, "(_diseases|_disorders)$", 0));
		
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.ORGAN, "^Infobox_(Brain|Nerve|Muscle|Vein|Artery|Bone|Anatomy)$", 0, null));
		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.ORGAN, "_glands$|^SUNYAnatomy|^(BUHistology|AnatomyAtlasesMicroscopic|Gray's|Anatomy-stub)$", 0, null));
		
		//TODO; LOTS of anatomy navigation boxes

		//TODO: generic markers, such as {{MedlinePlus}}, {{MeshName}}, {{GPnotebook}}, {{Gene}}, etc, or [[Category:EC_.*]]
		
		//TODO: terms from properties! (ids, latin name, box caption, etc)

		
	}
	
}
