package net.psammead.util;

import java.util.*;

import net.psammead.util.annotation.FullyStatic;

/** static helper class: encodes and decodes HTML 4.0 entities in Strings */
@FullyStatic 
public final class HTMLEntities {
	private static final Map<Character, String>	charToEntity	= new HashMap<Character, String>();
	private static final Map<String, Character>	entityToChar	= new HashMap<String, Character>();

	private static int	shortEntity;
	private static int	longEntity;

	/** function collection, shall not be instantiated */
	private HTMLEntities() {}
	
	//------------------------------------------------------------------------------
	//## public codec methods
	
	/** encode a whole String */
	public static String encode(String s) {
		final StringBuilder	out	= new StringBuilder();
		final int			len	= s.length();
		for (int i=0; i<len; i++) {
			out.append(
					encode(
							s.charAt(i)));
		}
		return out.toString();
	}
	
	/** encode a single Character */
	public static String encode(char c) {
		final String	s	= charToEntity.get(new Character(c));
			 if (s != null)	return "&" + s + ";";
		else if (c < 256)	return Character.toString(c);
		else				return "&#" + Integer.toString(c) + ";"; 
	}
	
	/** decode a whole String */
	public static String decode(String s) {
		if (s.indexOf('&') < 0)	return s;
		final StringBuilder	out	= new StringBuilder();
		final int			len	= s.length();
		String			may;
		Character		chr;
		char			ch;
		int				pos;
		for (int i=0; i<len; i++) {
			pos	= s.indexOf('&', i);
			if (pos < 0) {
				out.append(s.substring(i));
				break;
			}
			out.append(s.substring(i, pos));
			i	= pos;
			pos	= s.indexOf(';', i);
			if (pos < 0) {
				out.append("&");
				continue;
			}
			if (pos - i - 1 < shortEntity ||
				pos - i - 1 > longEntity) {
				out.append('&');
				continue;
			}
			may	= s.substring(i+1, pos);
			try {
				if (may.startsWith("#x")) {
					ch	= (char)Integer.parseInt(may.substring(2), 16);
					out.append(ch);
					i	= pos;
					continue;
				}
				if (may.startsWith("#")) {
					ch	= (char)Integer.parseInt(may.substring(1));
					out.append(ch);
					i	= pos;
					continue;
				}
			}
			catch (NumberFormatException e) {
				out.append('&');
				continue;
			}
			chr	= entityToChar.get(may);
			if (chr != null) {
				ch	= chr.charValue();
				out.append(ch);
				i	= pos;
				continue;
			}
			out.append('&');
		}
		return out.toString();
	}

	//------------------------------------------------------------------------------
	//## private char - html associations
	
	static {
		shortEntity	= Integer.MIN_VALUE;
		longEntity	= Integer.MAX_VALUE;
		build();
		int	min	= "#0".length();
		if (shortEntity < min)	shortEntity	= min;
		if (longEntity	< min)	longEntity	= min;
	}
	
	/** store an association between a character and an entity name */
	private static void associate(int character, String entity) {
		final Character	chr	= new Character((char)character);
		charToEntity.put(chr, entity);
		entityToChar.put(entity, chr);
		int len	= entity.length();
		if (len < shortEntity)		shortEntity	= len;
		if (len > longEntity)		longEntity	= len;
	}
	
	/** build associations between characters and entity names */
	private static void build() {
		associate(34,	"quot");
		associate(38,	"amp");
		associate(60,	"lt");
		associate(62,	"gt");
		associate(160,	"nbsp");
		associate(161,	"iexcl");
		associate(162,	"cent");
		associate(163,	"pound");
		associate(164,	"curren");
		associate(165,	"yen");
		associate(166,	"brvbar");
		associate(167,	"sect");
		associate(168,	"uml");
		associate(169,	"copy");
		associate(170,	"ordf");
		associate(171,	"laquo");
		associate(172,	"not");
		associate(173,	"shy");
		associate(174,	"reg");
		associate(175,	"macr");
		associate(176,	"deg");
		associate(177,	"plusmn");
		associate(178,	"sup2");
		associate(179,	"sup3");
		associate(180,	"acute");
		associate(181,	"micro");
		associate(182,	"para");
		associate(183,	"middot");
		associate(184,	"cedil");
		associate(185,	"sup1");
		associate(186,	"ordm");
		associate(187,	"raquo");
		associate(188,	"frac14");
		associate(189,	"frac12");
		associate(190,	"frac34");
		associate(191,	"iquest");
		associate(192,	"Agrave");
		associate(193,	"Aacute");
		associate(194,	"Acirc");
		associate(195,	"Atilde");
		associate(196,	"Auml");
		associate(197,	"Aring");
		associate(198,	"AElig");
		associate(199,	"Ccedil");
		associate(200,	"Egrave");
		associate(201,	"Eacute");
		associate(202,	"Ecirc");
		associate(203,	"Euml");
		associate(204,	"Igrave");
		associate(205,	"Iacute");
		associate(206,	"Icirc");
		associate(207,	"Iuml");
		associate(208,	"ETH");
		associate(209,	"Ntilde");
		associate(210,	"Ograve");
		associate(211,	"Oacute");
		associate(212,	"Ocirc");
		associate(213,	"Otilde");
		associate(214,	"Ouml");
		associate(215,	"times");
		associate(216,	"Oslash");
		associate(217,	"Ugrave");
		associate(218,	"Uacute");
		associate(219,	"Ucirc");
		associate(220,	"Uuml");
		associate(221,	"Yacute");
		associate(222,	"THORN");
		associate(223,	"szlig");
		associate(224,	"agrave");
		associate(225,	"aacute");
		associate(226,	"acirc");
		associate(227,	"atilde");
		associate(228,	"auml");
		associate(229,	"aring");
		associate(230,	"aelig");
		associate(231,	"ccedil");
		associate(232,	"egrave");
		associate(233,	"eacute");
		associate(234,	"ecirc");
		associate(235,	"euml");
		associate(236,	"igrave");
		associate(237,	"iacute");
		associate(238,	"icirc");
		associate(239,	"iuml");
		associate(240,	"eth");
		associate(241,	"ntilde");
		associate(242,	"ograve");
		associate(243,	"oacute");
		associate(244,	"ocirc");
		associate(245,	"otilde");
		associate(246,	"ouml");
		associate(247,	"divide");
		associate(248,	"oslash");
		associate(249,	"ugrave");
		associate(250,	"uacute");
		associate(251,	"ucirc");
		associate(252,	"uuml");
		associate(253,	"yacute");
		associate(254,	"thorn");
		associate(255,	"yuml");
		associate(338,	"OElig");
		associate(339,	"oelig");
		associate(352,	"Scaron");
		associate(353,	"scaron");
		associate(376,	"Yuml");
		associate(402,	"fnof");
		associate(710,	"circ");
		associate(732,	"tilde");
		associate(913,	"Alpha");
		associate(914,	"Beta");
		associate(915,	"Gamma");
		associate(916,	"Delta");
		associate(917,	"Epsilon");
		associate(918,	"Zeta");
		associate(919,	"Eta");
		associate(920,	"Theta");
		associate(921,	"Iota");
		associate(922,	"Kappa");
		associate(923,	"Lambda");
		associate(924,	"Mu");
		associate(925,	"Nu");
		associate(926,	"Xi");
		associate(927,	"Omicron");
		associate(928,	"Pi");
		associate(929,	"Rho");
		associate(931,	"Sigma");
		associate(932,	"Tau");
		associate(933,	"Upsilon");
		associate(934,	"Phi");
		associate(935,	"Chi");
		associate(936,	"Psi");
		associate(937,	"Omega");
		associate(945,	"alpha");
		associate(946,	"beta");
		associate(947,	"gamma");
		associate(948,	"delta");
		associate(949,	"epsilon");
		associate(950,	"zeta");
		associate(951,	"eta");
		associate(952,	"theta");
		associate(953,	"iota");
		associate(954,	"kappa");
		associate(955,	"lambda");
		associate(956,	"mu");
		associate(957,	"nu");
		associate(958,	"xi");
		associate(959,	"omicron");
		associate(960,	"pi");
		associate(961,	"rho");
		associate(962,	"sigmaf");
		associate(963,	"sigma");
		associate(964,	"tau");
		associate(965,	"upsilon");
		associate(966,	"phi");
		associate(967,	"chi");
		associate(968,	"psi");
		associate(969,	"omega");
		associate(977,	"thetasym");
		associate(978,	"upsih");
		associate(982,	"piv");
		associate(8194,	"ensp");
		associate(8195,	"emsp");
		associate(8201,	"thinsp");
		associate(8204,	"zwnj");
		associate(8205,	"zwj");
		associate(8206,	"lrm");
		associate(8207,	"rlm");
		associate(8211,	"ndash");
		associate(8212,	"mdash");
		associate(8216,	"lsquo");
		associate(8217,	"rsquo");
		associate(8218,	"sbquo");
		associate(8220,	"ldquo");
		associate(8221,	"rdquo");
		associate(8222,	"bdquo");
		associate(8224,	"dagger");
		associate(8225,	"Dagger");
		associate(8226,	"bull");
		associate(8230,	"hellip");
		associate(8240,	"permil");
		associate(8242,	"prime");
		associate(8243,	"Prime");
		associate(8249,	"lsaquo");
		associate(8250,	"rsaquo");
		associate(8254,	"oline");
		associate(8260,	"frasl");
		associate(8364,	"euro");
		associate(8465,	"image");
		associate(8472,	"weierp");
		associate(8476,	"real");
		associate(8482,	"trade");
		associate(8501,	"alefsym");
		associate(8592,	"larr");
		associate(8593,	"uarr");
		associate(8594,	"rarr");
		associate(8595,	"darr");
		associate(8596,	"harr");
		associate(8629,	"crarr");
		associate(8656,	"lArr");
		associate(8657,	"uArr");
		associate(8658,	"rArr");
		associate(8659,	"dArr");
		associate(8660,	"hArr");
		associate(8704,	"forall");
		associate(8706,	"part");
		associate(8707,	"exist");
		associate(8709,	"empty");
		associate(8711,	"nabla");
		associate(8712,	"isin");
		associate(8713,	"notin");
		associate(8715,	"ni");
		associate(8719,	"prod");
		associate(8721,	"sum");
		associate(8722,	"minus");
		associate(8727,	"lowast");
		associate(8730,	"radic");
		associate(8733,	"prop");
		associate(8734,	"infin");
		associate(8736,	"ang");
		associate(8743,	"and");
		associate(8744,	"or");
		associate(8745,	"cap");
		associate(8746,	"cup");
		associate(8747,	"int");
		associate(8756,	"there4");
		associate(8764,	"sim");
		associate(8773,	"cong");
		associate(8776,	"asymp");
		associate(8800,	"ne");
		associate(8801,	"equiv");
		associate(8804,	"le");
		associate(8805,	"ge");
		associate(8834,	"sub");
		associate(8835,	"sup");
		associate(8836,	"nsub");
		associate(8838,	"sube");
		associate(8839,	"supe");
		associate(8853,	"oplus");
		associate(8855,	"otimes");
		associate(8869,	"perp");
		associate(8901,	"sdot");
		associate(8968,	"lceil");
		associate(8969,	"rceil");
		associate(8970,	"lfloor");
		associate(8971,	"rfloor");
		associate(9001,	"lang");
		associate(9002,	"rang");
		associate(9674,	"loz");
		associate(9824,	"spades");
		associate(9827,	"clubs");
		associate(9829,	"hearts");
		associate(9830,	"diams");
	}
}
