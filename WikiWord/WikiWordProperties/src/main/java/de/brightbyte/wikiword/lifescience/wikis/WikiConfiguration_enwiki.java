package de.brightbyte.wikiword.lifescience.wikis;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.extractor.CategoryPatternParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.PagePropertyValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.PropertyValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateNamePatternParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TitlePartExtractor;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasCategorySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasPropertySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.TitleSensor;
import de.brightbyte.wikiword.analyzer.template.AbstractTemplateParameterPropertySpec;
import de.brightbyte.wikiword.analyzer.template.DeepTemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.DefaultTemplateParameterPropertySpec;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateParameterPropertySpec;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor.Context;
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
	
	protected static final Pattern identifierSeparatorPattern = Pattern.compile(",\\p{IsZ}+|[\\p{IsZ};]+|<br */?>", 0);
	protected static final Pattern nameSeparatorPattern = Pattern.compile(",\\p{IsZ}+|[\r\n;]+|<br */?>", 0);
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
	private static final String pagesChars = "["+numericChars+"]+(\\s*["+dashChars+",]\\s*["+numericChars+"]+)*";
	
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
	
	private static final String dorlandsChars = "["+alphabeticChars+"]+/["+numericChars+"]+";
	private static final String neuroNamesChars = "["+alphabeticChars+"]+-["+numericChars+"]+";
	
	//TODO: exclude "Biography"... 
	//public static final String lifeScienceJournalPattern = "(^|[ _])(Chem[a-z]*|Biol?[.a-z]*|Gen[eo][a-z]*|Med[a-z]*|Cell[a-z]*|DNA|RNA|Nucleic|EMBO|FEBS|Onco[a-z]*|Blood|Immono[a-z]*|Cancer|Virol[a-z]*|Med[a-z]*|Clin[a-z]*|Lancet|Nature|PLoS|Neuro[a-z]*|Zootaxa|JAMA|FASEB|Bacter[a-z]*|Mutat[a-z]*|Mol[a-z]*|Protein|Dermat[a-z]*|Pathol[a-z]*|Endocr[a-z]*|Microbio[a-z]*)($|[_ ])";

	
	protected static DefaultTemplateParameterPropertySpec makeNamePropertySpec(String param, String prop, boolean multi, boolean space) {
		DefaultTemplateParameterPropertySpec spec = new DefaultTemplateParameterPropertySpec(param, prop);
		
		if (multi) {
			if (space) spec.setSplitPattern(nameSeparatorPattern);
			else spec.setSplitPattern(identifierSeparatorPattern);
		}
		
		if (space) spec.addNormalizer(badStuffStripPattern, "");
		else spec.addNormalizer(spaceStripPattern, "");
		
		return spec;
	}

	protected static DefaultTemplateParameterPropertySpec makeIdentifierPropertySpec(String param, String prop, String pattern) {
		DefaultTemplateParameterPropertySpec spec = new DefaultTemplateParameterPropertySpec(param, prop);
		
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
			public TemplateExtractor newTemplateExtractor(Context context, TextArmor armor) {
				DeepTemplateExtractor extractor = new DeepTemplateExtractor(context, armor);
				extractor.addContainerField("Protbox", "Codes");
				extractor.addContainerField("Protbox", "Caption");
				//FIXME: this needs to accumulate!!!! //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME //FIXME
				return extractor;
			}
		};
		
		//NOTE: apply template replacement only when stripping markup, but then before everything else
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("ICD9", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("ICD10", 3, true), " $2$3.$4 ") ); //XXX: use all 5 params?
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("ICDO", 2, true), " M$2/$3 ") ); 
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("CAS", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("ATC", 2, true), " $2$3 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("DiseasesDB2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("OMIM\\d?", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("SMILES", 1, true), " $2 ") ); //FIXME: named param S= !
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("eMedicine2", 2, true), " $2/$3 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("MedlinePlus2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("PDB", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("PDB2", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("PDB3", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("EC_number", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("OMIM", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("EntrezGene", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("UniProt", 1, true), " $2 ") );
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("RefSeq", 1, true), " $2 ") );
		
		propertyExtractors.add( new TemplateParameterExtractor( new ExactNameMatcher("Cite_journal"), 
				new DefaultTemplateParameterPropertySpec("journal", "journal")
						.addNormalizer(punctuationStripPattern, "")/*
						.setCondition(lifeScienceJournalPattern, 0, false)*/ ) );
		
		TemplateParameterPropertySpec atcSpec = new AbstractTemplateParameterPropertySpec("ATC") {
			private Matcher validator = Pattern.compile("["+upperAlphaNumericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiPage page, TemplateData params) {
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Drugbox"),
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
		
		TemplateParameterPropertySpec eMedSpec = new AbstractTemplateParameterPropertySpec("eMedicine") {
			private Matcher subjectValidator = Pattern.compile("["+alphaNumericChars+"]+").matcher("");
			private Matcher topicValidator = Pattern.compile("["+numericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiPage page, TemplateData params) {
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
		
		
		TemplateParameterPropertySpec dorlandsSpec = new AbstractTemplateParameterPropertySpec("Dorlands") {
			private Matcher preValidator = Pattern.compile("["+alphabeticChars+"]_["+numericChars+"]+").matcher("");
			private Matcher sufValidator = Pattern.compile("["+numericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiPage page, TemplateData params) {
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
		
		TemplateParameterPropertySpec neuroNamesSpec = new AbstractTemplateParameterPropertySpec("NeuroNames") {
			private Matcher typeValidator = Pattern.compile("["+alphabeticChars+"]+").matcher("");
			private Matcher numValidator = Pattern.compile("["+numericChars+"]+").matcher("");
			
			@Override
			public CharSequence getPropertyValue(WikiPage page, TemplateData params) {
				CharSequence type= params.getParameter("BrainInfoType");
				CharSequence num= params.getParameter("BrainInfoNumber");
				if (type==null || num==null) return null;
				if (type.length()==0 || num.length()==0) return null;
				
				typeValidator.reset(type);
				if (!typeValidator.matches()) return null;
				
				typeValidator.reset(num);
				if (!numValidator.matches()) return null;
				
				return type+"-"+num;
			}
		};
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Infobox_Disease|Infobox_Symptom|SignSymptom_infobox|DiseaseDisorder_infobox|Interventions_infobox", 0, true),
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Chembox_new"),
				makeNamePropertySpec("IUPACName", "IUPAC", true, true).addCleanup(iupacCleanupPattern, ""),
				makeNamePropertySpec("OtherNames", "Name", true, true) //FIXME: often spaced for auto-breaks and separated by <br>
			) );
		
		//TODO: terms from names
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Chembox_[Pp]harmacology", 0, true), 
				makeIdentifierPropertySpec("DrugBank", "DrugBank", drugBankChars), 
				atcSpec
		) );
				
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Chembox_[Hh]azards", 0, true), 
				makeIdentifierPropertySpec("RTECS", "RTECS", rtecsChars)
		) );
				
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Chembox_[Ii]dentifiers", 0, true), 
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("NatOrganicBox", 0, true), 
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Elementbox", 0, true), 
				makeNamePropertySpec("name", "Name", true, true),
				makeIdentifierPropertySpec("number", "ElementNumber", "["+numericChars+"]"),
				makeIdentifierPropertySpec("symbol", "ElementSymbol", "["+alphaNumericChars+"]"),

				makeIdentifierPropertySpec("CAS number", "CAS", casChars),
				atcSpec
			) );
		
		//TODO: ...as terms
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox"),
				makeNamePropertySpec("Name", "ProteinName", false, true),
				makeNamePropertySpec("Names", "ProteinName", true, true),

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
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Taxobox"),
				makeNamePropertySpec("name", "Name", false, true),
				makeNamePropertySpec("regnum", "taxo-regnum", true, true),
				makeNamePropertySpec("divisio", "taxo-divisio", true, true),
				makeNamePropertySpec("classis", "taxo-classis", true, true),
				makeNamePropertySpec("ordo", "taxo-ordo", true, true),
				makeNamePropertySpec("familia", "taxo-familia", true, true),
				makeNamePropertySpec("genus", "taxo-genus", true, true),
				makeNamePropertySpec("species", "taxo-species", true, true)
		) );

		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Enzyme_(links|references)", 0, true),
				makeIdentifierPropertySpec("EC_number", "EC/enzyme", ecEnzymeChars)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("GO_code_links"),
				makeNamePropertySpec("name", "ProteinName", false, true),
				makeIdentifierPropertySpec("GO_code", "GO_code", goCodeChars)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("CAS_registry"), //XXX: only as identifying element, or also in-context?
				makeIdentifierPropertySpec("1", "CAS", casChars)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("MSW3_Groves"), 
				makeIdentifierPropertySpec("id", "GrovesId", numericChars),
				makeIdentifierPropertySpec("pages", "GrovesPages", pagesChars)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Rfam"), 
				makeIdentifierPropertySpec("id", "RNA family", alphaNumericChars),
				makeNamePropertySpec("name", "Name", false, true)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Rfam_box", 0, true), 
				makeIdentifierPropertySpec("acc", "RNA family", alphaNumericChars),
				makeNamePropertySpec("description", "Name", true, true),
				makeNamePropertySpec("abbreviation", "Name", true, true),
				makeNamePropertySpec("type", "RNA type", true, true),
				new DefaultTemplateParameterPropertySpec("journal", "journal")
					.addNormalizer(punctuationStripPattern, "") /*
					.setCondition(lifeScienceJournalPattern, 0, false) */ 
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Infobox_chemical_analysis", 0, true), 
				makeNamePropertySpec("name", "Name", true, true),
				makeNamePropertySpec("acronym", "Name", true, true),
				makeNamePropertySpec("classification", "AnalysisClass", true, true),
				makeNamePropertySpec("analytes", "Analytes", true, true)
			) );
		
		//Stuff from the container field Codes in Protbox:
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::OMIM"),
				makeIdentifierPropertySpec("1", "OMIM", omimChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::OMIM2"),
				makeIdentifierPropertySpec("1", "OMIM", omimChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::EntrezGene"),
				makeIdentifierPropertySpec("1", "EntrezGene", entrezGeneChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::UniProt"),
				makeIdentifierPropertySpec("1", "UniProt", uniProtChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::RefSeq"),
				makeIdentifierPropertySpec("1", "RefSeq", refSeqChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::EC_number"),
				makeIdentifierPropertySpec("1", "EC/enzyme", ecEnzymeChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Codes::PDB"),
				makeIdentifierPropertySpec("1", "PDB", pdbChars) ) );
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protbox.Caption::PDB"),
				makeIdentifierPropertySpec("1", "PDB", pdbChars) ) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Protein"),
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("GNF_Protein_box"),
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

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("GNF_Ortholog_box"),
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
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_neuron"),
				makeNamePropertySpec("neuron_name", "Name", false, true),
				makeNamePropertySpec("function", "Function", false, true),
				makeNamePropertySpec("GraySubject", "GraySubject", true, true)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_neurotransmitter"),
				makeNamePropertySpec("name", "Name", false, true),
				makeNamePropertySpec("abbrev", "Name", false, true)
			) );
		
		//TODO: {{MedlinePlus}}...?
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Infobox_(Anatomy|Artery|Vein|Bone|Brain|Nerve|Muscle|Embryology|Ligament|Lymph)", 0, true),
				makeNamePropertySpec("Name", "Name", true, true),
				makeNamePropertySpec("Latin", "AnatomyLatin", true, true),
				makeNamePropertySpec("GraySubject", "GraySubject", true, true),
				makeNamePropertySpec("MeshName", "MeSHName", true, true),
				makeIdentifierPropertySpec("MeshNumber", "MeSH", meshChars),
				makeNamePropertySpec("DorlandsID", "DorlandsName", true, true),
				makeIdentifierPropertySpec("Dorlands", "Dorlands", dorlandsChars),
				dorlandsSpec,
				neuroNamesSpec
			) );
		
		//FIXME: URLDecode for MeshName, etc!

		//TODO: Infobox_(Artery|Brain|Bone|...)
		//      GraySubject, MeSH name&number, DorlandsPre/DorlandsSuf (Elsevier )

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("ICSC"),
				makeIdentifierPropertySpec("1", "ICSC", icscChars)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("PubChem"),
				makeIdentifierPropertySpec("1", "PubChem", pubChemChars)
			) );
		
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("PBB"),
				makeIdentifierPropertySpec("geneid", "_PBB_", pbbChars)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_abortion_method"),
				makeNamePropertySpec("name", "Name", false, true),
				makeNamePropertySpec("AKA/Abbreviation", "Name", true, true),
				makeNamePropertySpec("Abortion_type", "AbortionType", false, true)
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_Birth_control"),
				makeNamePropertySpec("name", "Name", false, true) //not really interesting, just make the concept show up as relevant for LS
			) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Infobox_(((Medical)_)?[Pp]erson|Scientist)", 0, true),
				new DefaultTemplateParameterPropertySpec("name", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("other_names", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birth_date", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("occupation", "person-occupation").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("known_for", "person-known-for").setStripMarkup(true).setSplitPattern(nameSeparatorPattern),
				new DefaultTemplateParameterPropertySpec("nationality", "person-nationality").setStripMarkup(true)
			) );
		
		propertyExtractors.add(new CategoryPatternParameterExtractor("(_|$)([Ff]oods|[Vv]egetables|[Ff]ruits)", null, 0, "food-group"));
		propertyExtractors.add(new TemplateNamePatternParameterExtractor("((.+-)?(Med(ical)?|Treatment|Pathology|Anatomy|Antibiotic|Disease)(-.+)?)-stub", "$2", 0, "med-stub-group")); //TODO: no limits to this one
		
		pageTermExtractors.add( new PagePropertyValueExtractor("IUPAC") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("AnatomyLatin") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("ProteinSymbol") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("ProteinName") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("MeSHName") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("Name") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("Symbol") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("DorlandsName") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("person-name") ); 

		supplementSensors.add( new TitleSensor<ResourceType>(ResourceType.SUPPLEMENT, Namespace.TEMPLATE, "PBB/\\d+", 0));
		
		supplementNameExtractors.add( new PropertyValueExtractor("_PBB_").setPrefix("Template:PBB/") );
		
		supplementedConceptExtractors.add( new TitlePartExtractor(Namespace.MAIN, "(.*)_\\(data_page\\)", 0, "$1") );
		supplementedConceptExtractors.add( new TitlePartExtractor(Namespace.TEMPLATE, "Infobox_(.*)", 0, "$1")
				.addCondition( new HasCategorySensor<ResourceType>(ResourceType.SUPPLEMENT, "Periodic_table_infobox_templates") ) );
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.DRUG, "_(treatments|therapies)$", 0));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(LifeScienceConceptType.DRUG, "Drugbox"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.DRUG, "^Drugs_|^DrugsNav$", 0));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(LifeScienceConceptType.DRUG, "Major_Drug_Groups"));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.PROTEIN, "EC_\\d+(\\.\\d+)*", 0)); //FIXME: too much meta-stuff!
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.PROTEIN, "^(Enzyme_links|PBB|Protein|GNF_.*_box)$", 0) ); 
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.CHEMICAL, "^Chembox|^NatOrganicBox$|^ICSC$|^Elementbox|^(Complex_)?Enzymatic_Reaction", 0));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(LifeScienceConceptType.CHEMICAL, "Chemical_elements"));

		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.DISEASE, "^(Infobox_Disease|Infobox_Symptom|SignSymptom_infobox|DiseaseDisorder_infobox)$", 0));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.DISEASE, "(_diseases|_disorders)$", 0, false));
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.ORGAN, "^Infobox_(Brain|Nerve|Muscle|Vein|Artery|Bone|Anatomy|Ligament|Lymph)$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.ORGAN, "_glands$|^SUNYAnatomy|^(BUHistology|AnatomyAtlasesMicroscopic|Gray's|Anatomy-stub)$", 0));

		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(LifeScienceConceptType.FOOD, "Nutritional_value", 0));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(LifeScienceConceptType.FOOD, "(_|$)([Ff]oods|[Vv]egetables|[Ff]ruits)", 0, false));
		
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox"));
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.LIFEFORM, "GrovesId"));
		
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-name"));
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-birth-date"));
		
		//TODO; LOTS of anatomy navigation boxes

		//TODO: generic markers, such as {{MedlinePlus}}, {{MeshName}}, {{GPnotebook}}, {{Gene}}, etc, or [[Category:EC_.*]]
		
		//TODO: terms from properties! (ids, latin name, box caption, etc)

		
	}
	
}
