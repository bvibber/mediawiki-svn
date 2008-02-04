// Jericho HTML Parser - Java based library for analysing and manipulating HTML
// Version 2.5
// Copyright (C) 2007 Martin Jericho
// http://jerichohtml.sourceforge.net/
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of either one of the following licences:
//
// 1. The Eclipse Public License (EPL) version 1.0,
// included in this distribution in the file licence-epl-1.0.html
// or available at http://www.eclipse.org/legal/epl-v10.html
//
// 2. The GNU Lesser General Public License (LGPL) version 2.1 or later,
// included in this distribution in the file licence-lgpl-2.1.txt
// or available at http://www.gnu.org/licenses/lgpl.txt
//
// This library is distributed on an "AS IS" basis,
// WITHOUT WARRANTY OF ANY KIND, either express or implied.
// See the individual licence texts for more details.

package org.wikimedia.lsearch.util;

import java.util.*;

/**
 * Represents an HTML <a target="_blank" href="http://www.w3.org/TR/REC-html40/charset.html#h-5.3.2">Character Entity Reference</a>.
 * <p>
 * <b>Click <a href="#method_summary">here</a> to scroll down to the method summary.</b>
 * <p>
 * The full list of HTML character entity references can be found at the following URL:<br />
 * <a target="_blank" href="http://www.w3.org/TR/REC-html40/sgml/entities.html">http://www.w3.org/TR/REC-html40/sgml/entities.html</a>.
 * <p>
 * There are a total of 253 HTML character entity references, ranging from codepoints U+0022 to U+2666.
 * <p>
 * Static methods to {@linkplain #encode(CharSequence) encode} and {@linkplain #decode(CharSequence) decode} strings
 * and single characters can be found in the {@link CharacterReference} superclass.
 * <p>
 * The {@link #_apos &amp;apos;} entity reference is not defined for use in HTML.
 * It is defined in the <a target="_blank" href="http://www.w3.org/TR/xhtml1/dtds.html#a_dtd_Special_characters">XHTML Special Characters Entity Set</a>,
 * and is the only one that is not included in both HTML and XHTML.
 * For this reason, the <code>&amp;apos;</code> entity reference is recognised by this library in decoding functions, but in encoding functions
 * the numeric character reference <code>&amp;#39;</code> is used instead.
 * Most modern browsers support it in both XHTML and HTML, with the notable exception
 * of Microsoft Internet Explorer 6.0, which doesn't support it in either.
 * <p>
 * <code>CharacterEntityReference</code> instances are obtained using one of the following methods:
 * <ul>
 *  <li>{@link CharacterReference#parse(CharSequence characterReferenceText)}
 *  <li>{@link Source#findNextCharacterReference(int pos)}
 *  <li>{@link Source#findPreviousCharacterReference(int pos)}
 *  <li>{@link Segment#findAllCharacterReferences()}
 * </ul>
 *
 * @see CharacterReference
 * @see NumericCharacterReference
 */
public class CharacterEntityReference {
	private String name;

	/** <samp>&nbsp;</samp> <code>&amp;nbsp; = &amp;#160;</code> -- no-break space = non-breaking space, U+00A0 ISOnum. */
	public static final char _nbsp='\u00A0';
	/** <samp>&iexcl;</samp> <code>&amp;iexcl; = &amp;#161;</code> -- inverted exclamation mark, U+00A1 ISOnum. */
	public static final char _iexcl='\u00A1';
	/** <samp>&cent;</samp> <code>&amp;cent; = &amp;#162;</code> -- cent sign, U+00A2 ISOnum. */
	public static final char _cent='\u00A2';
	/** <samp>&pound;</samp> <code>&amp;pound; = &amp;#163;</code> -- pound sign, U+00A3 ISOnum. */
	public static final char _pound='\u00A3';
	/** <samp>&curren;</samp> <code>&amp;curren; = &amp;#164;</code> -- currency sign, U+00A4 ISOnum. */
	public static final char _curren='\u00A4';
	/** <samp>&yen;</samp> <code>&amp;yen; = &amp;#165;</code> -- yen sign = yuan sign, U+00A5 ISOnum. */
	public static final char _yen='\u00A5';
	/** <samp>&brvbar;</samp> <code>&amp;brvbar; = &amp;#166;</code> -- broken bar = broken vertical bar, U+00A6 ISOnum. */
	public static final char _brvbar='\u00A6';
	/** <samp>&sect;</samp> <code>&amp;sect; = &amp;#167;</code> -- section sign, U+00A7 ISOnum. */
	public static final char _sect='\u00A7';
	/** <samp>&uml;</samp> <code>&amp;uml; = &amp;#168;</code> -- diaeresis = spacing diaeresis, U+00A8 ISOdia. */
	public static final char _uml='\u00A8';
	/** <samp>&copy;</samp> <code>&amp;copy; = &amp;#169;</code> -- copyright sign, U+00A9 ISOnum. */
	public static final char _copy='\u00A9';
	/** <samp>&ordf;</samp> <code>&amp;ordf; = &amp;#170;</code> -- feminine ordinal indicator, U+00AA ISOnum. */
	public static final char _ordf='\u00AA';
	/** <samp>&laquo;</samp> <code>&amp;laquo; = &amp;#171;</code> -- left-pointing double angle quotation mark = left pointing guillemet, U+00AB ISOnum. */
	public static final char _laquo='\u00AB';
	/** <samp>&not;</samp> <code>&amp;not; = &amp;#172;</code> -- not sign = angled dash, U+00AC ISOnum. */
	public static final char _not='\u00AC';
	/** <samp>&shy;</samp> <code>&amp;shy; = &amp;#173;</code> -- soft hyphen = discretionary hyphen, U+00AD ISOnum. */
	public static final char _shy='\u00AD';
	/** <samp>&reg;</samp> <code>&amp;reg; = &amp;#174;</code> -- registered sign = registered trade mark sign, U+00AE ISOnum. */
	public static final char _reg='\u00AE';
	/** <samp>&macr;</samp> <code>&amp;macr; = &amp;#175;</code> -- macron = spacing macron = overline = APL overbar, U+00AF ISOdia. */
	public static final char _macr='\u00AF';
	/** <samp>&deg;</samp> <code>&amp;deg; = &amp;#176;</code> -- degree sign, U+00B0 ISOnum. */
	public static final char _deg='\u00B0';
	/** <samp>&plusmn;</samp> <code>&amp;plusmn; = &amp;#177;</code> -- plus-minus sign = plus-or-minus sign, U+00B1 ISOnum. */
	public static final char _plusmn='\u00B1';
	/** <samp>&sup2;</samp> <code>&amp;sup2; = &amp;#178;</code> -- superscript two = superscript digit two = squared, U+00B2 ISOnum. */
	public static final char _sup2='\u00B2';
	/** <samp>&sup3;</samp> <code>&amp;sup3; = &amp;#179;</code> -- superscript three = superscript digit three = cubed, U+00B3 ISOnum. */
	public static final char _sup3='\u00B3';
	/** <samp>&acute;</samp> <code>&amp;acute; = &amp;#180;</code> -- acute accent = spacing acute, U+00B4 ISOdia. */
	public static final char _acute='\u00B4';
	/** <samp>&micro;</samp> <code>&amp;micro; = &amp;#181;</code> -- micro sign, U+00B5 ISOnum. */
	public static final char _micro='\u00B5';
	/** <samp>&para;</samp> <code>&amp;para; = &amp;#182;</code> -- pilcrow sign = paragraph sign, U+00B6 ISOnum. */
	public static final char _para='\u00B6';
	/** <samp>&middot;</samp> <code>&amp;middot; = &amp;#183;</code> -- middle dot = Georgian comma = Greek middle dot, U+00B7 ISOnum. */
	public static final char _middot='\u00B7';
	/** <samp>&cedil;</samp> <code>&amp;cedil; = &amp;#184;</code> -- cedilla = spacing cedilla, U+00B8 ISOdia. */
	public static final char _cedil='\u00B8';
	/** <samp>&sup1;</samp> <code>&amp;sup1; = &amp;#185;</code> -- superscript one = superscript digit one, U+00B9 ISOnum. */
	public static final char _sup1='\u00B9';
	/** <samp>&ordm;</samp> <code>&amp;ordm; = &amp;#186;</code> -- masculine ordinal indicator, U+00BA ISOnum. */
	public static final char _ordm='\u00BA';
	/** <samp>&raquo;</samp> <code>&amp;raquo; = &amp;#187;</code> -- right-pointing double angle quotation mark = right pointing guillemet, U+00BB ISOnum. */
	public static final char _raquo='\u00BB';
	/** <samp>&frac14;</samp> <code>&amp;frac14; = &amp;#188;</code> -- vulgar fraction one quarter = fraction one quarter, U+00BC ISOnum. */
	public static final char _frac14='\u00BC';
	/** <samp>&frac12;</samp> <code>&amp;frac12; = &amp;#189;</code> -- vulgar fraction one half = fraction one half, U+00BD ISOnum. */
	public static final char _frac12='\u00BD';
	/** <samp>&frac34;</samp> <code>&amp;frac34; = &amp;#190;</code> -- vulgar fraction three quarters = fraction three quarters, U+00BE ISOnum. */
	public static final char _frac34='\u00BE';
	/** <samp>&iquest;</samp> <code>&amp;iquest; = &amp;#191;</code> -- inverted question mark = turned question mark, U+00BF ISOnum. */
	public static final char _iquest='\u00BF';
	/** <samp>&Agrave;</samp> <code>&amp;Agrave; = &amp;#192;</code> -- latin capital letter A with grave = latin capital letter A grave, U+00C0 ISOlat1. */
	public static final char _Agrave='\u00C0';
	/** <samp>&Aacute;</samp> <code>&amp;Aacute; = &amp;#193;</code> -- latin capital letter A with acute, U+00C1 ISOlat1. */
	public static final char _Aacute='\u00C1';
	/** <samp>&Acirc;</samp> <code>&amp;Acirc; = &amp;#194;</code> -- latin capital letter A with circumflex, U+00C2 ISOlat1. */
	public static final char _Acirc='\u00C2';
	/** <samp>&Atilde;</samp> <code>&amp;Atilde; = &amp;#195;</code> -- latin capital letter A with tilde, U+00C3 ISOlat1. */
	public static final char _Atilde='\u00C3';
	/** <samp>&Auml;</samp> <code>&amp;Auml; = &amp;#196;</code> -- latin capital letter A with diaeresis, U+00C4 ISOlat1. */
	public static final char _Auml='\u00C4';
	/** <samp>&Aring;</samp> <code>&amp;Aring; = &amp;#197;</code> -- latin capital letter A with ring above = latin capital letter A ring, U+00C5 ISOlat1. */
	public static final char _Aring='\u00C5';
	/** <samp>&AElig;</samp> <code>&amp;AElig; = &amp;#198;</code> -- latin capital letter AE = latin capital ligature AE, U+00C6 ISOlat1. */
	public static final char _AElig='\u00C6';
	/** <samp>&Ccedil;</samp> <code>&amp;Ccedil; = &amp;#199;</code> -- latin capital letter C with cedilla, U+00C7 ISOlat1. */
	public static final char _Ccedil='\u00C7';
	/** <samp>&Egrave;</samp> <code>&amp;Egrave; = &amp;#200;</code> -- latin capital letter E with grave, U+00C8 ISOlat1. */
	public static final char _Egrave='\u00C8';
	/** <samp>&Eacute;</samp> <code>&amp;Eacute; = &amp;#201;</code> -- latin capital letter E with acute, U+00C9 ISOlat1. */
	public static final char _Eacute='\u00C9';
	/** <samp>&Ecirc;</samp> <code>&amp;Ecirc; = &amp;#202;</code> -- latin capital letter E with circumflex, U+00CA ISOlat1. */
	public static final char _Ecirc='\u00CA';
	/** <samp>&Euml;</samp> <code>&amp;Euml; = &amp;#203;</code> -- latin capital letter E with diaeresis, U+00CB ISOlat1. */
	public static final char _Euml='\u00CB';
	/** <samp>&Igrave;</samp> <code>&amp;Igrave; = &amp;#204;</code> -- latin capital letter I with grave, U+00CC ISOlat1. */
	public static final char _Igrave='\u00CC';
	/** <samp>&Iacute;</samp> <code>&amp;Iacute; = &amp;#205;</code> -- latin capital letter I with acute, U+00CD ISOlat1. */
	public static final char _Iacute='\u00CD';
	/** <samp>&Icirc;</samp> <code>&amp;Icirc; = &amp;#206;</code> -- latin capital letter I with circumflex, U+00CE ISOlat1. */
	public static final char _Icirc='\u00CE';
	/** <samp>&Iuml;</samp> <code>&amp;Iuml; = &amp;#207;</code> -- latin capital letter I with diaeresis, U+00CF ISOlat1. */
	public static final char _Iuml='\u00CF';
	/** <samp>&ETH;</samp> <code>&amp;ETH; = &amp;#208;</code> -- latin capital letter ETH, U+00D0 ISOlat1. */
	public static final char _ETH='\u00D0';
	/** <samp>&Ntilde;</samp> <code>&amp;Ntilde; = &amp;#209;</code> -- latin capital letter N with tilde, U+00D1 ISOlat1. */
	public static final char _Ntilde='\u00D1';
	/** <samp>&Ograve;</samp> <code>&amp;Ograve; = &amp;#210;</code> -- latin capital letter O with grave, U+00D2 ISOlat1. */
	public static final char _Ograve='\u00D2';
	/** <samp>&Oacute;</samp> <code>&amp;Oacute; = &amp;#211;</code> -- latin capital letter O with acute, U+00D3 ISOlat1. */
	public static final char _Oacute='\u00D3';
	/** <samp>&Ocirc;</samp> <code>&amp;Ocirc; = &amp;#212;</code> -- latin capital letter O with circumflex, U+00D4 ISOlat1. */
	public static final char _Ocirc='\u00D4';
	/** <samp>&Otilde;</samp> <code>&amp;Otilde; = &amp;#213;</code> -- latin capital letter O with tilde, U+00D5 ISOlat1. */
	public static final char _Otilde='\u00D5';
	/** <samp>&Ouml;</samp> <code>&amp;Ouml; = &amp;#214;</code> -- latin capital letter O with diaeresis, U+00D6 ISOlat1. */
	public static final char _Ouml='\u00D6';
	/** <samp>&times;</samp> <code>&amp;times; = &amp;#215;</code> -- multiplication sign, U+00D7 ISOnum. */
	public static final char _times='\u00D7';
	/** <samp>&Oslash;</samp> <code>&amp;Oslash; = &amp;#216;</code> -- latin capital letter O with stroke = latin capital letter O slash, U+00D8 ISOlat1. */
	public static final char _Oslash='\u00D8';
	/** <samp>&Ugrave;</samp> <code>&amp;Ugrave; = &amp;#217;</code> -- latin capital letter U with grave, U+00D9 ISOlat1. */
	public static final char _Ugrave='\u00D9';
	/** <samp>&Uacute;</samp> <code>&amp;Uacute; = &amp;#218;</code> -- latin capital letter U with acute, U+00DA ISOlat1. */
	public static final char _Uacute='\u00DA';
	/** <samp>&Ucirc;</samp> <code>&amp;Ucirc; = &amp;#219;</code> -- latin capital letter U with circumflex, U+00DB ISOlat1. */
	public static final char _Ucirc='\u00DB';
	/** <samp>&Uuml;</samp> <code>&amp;Uuml; = &amp;#220;</code> -- latin capital letter U with diaeresis, U+00DC ISOlat1. */
	public static final char _Uuml='\u00DC';
	/** <samp>&Yacute;</samp> <code>&amp;Yacute; = &amp;#221;</code> -- latin capital letter Y with acute, U+00DD ISOlat1. */
	public static final char _Yacute='\u00DD';
	/** <samp>&THORN;</samp> <code>&amp;THORN; = &amp;#222;</code> -- latin capital letter THORN, U+00DE ISOlat1. */
	public static final char _THORN='\u00DE';
	/** <samp>&szlig;</samp> <code>&amp;szlig; = &amp;#223;</code> -- latin small letter sharp s = ess-zed, U+00DF ISOlat1. */
	public static final char _szlig='\u00DF';
	/** <samp>&agrave;</samp> <code>&amp;agrave; = &amp;#224;</code> -- latin small letter a with grave = latin small letter a grave, U+00E0 ISOlat1. */
	public static final char _agrave='\u00E0';
	/** <samp>&aacute;</samp> <code>&amp;aacute; = &amp;#225;</code> -- latin small letter a with acute, U+00E1 ISOlat1. */
	public static final char _aacute='\u00E1';
	/** <samp>&acirc;</samp> <code>&amp;acirc; = &amp;#226;</code> -- latin small letter a with circumflex, U+00E2 ISOlat1. */
	public static final char _acirc='\u00E2';
	/** <samp>&atilde;</samp> <code>&amp;atilde; = &amp;#227;</code> -- latin small letter a with tilde, U+00E3 ISOlat1. */
	public static final char _atilde='\u00E3';
	/** <samp>&auml;</samp> <code>&amp;auml; = &amp;#228;</code> -- latin small letter a with diaeresis, U+00E4 ISOlat1. */
	public static final char _auml='\u00E4';
	/** <samp>&aring;</samp> <code>&amp;aring; = &amp;#229;</code> -- latin small letter a with ring above = latin small letter a ring, U+00E5 ISOlat1. */
	public static final char _aring='\u00E5';
	/** <samp>&aelig;</samp> <code>&amp;aelig; = &amp;#230;</code> -- latin small letter ae = latin small ligature ae, U+00E6 ISOlat1. */
	public static final char _aelig='\u00E6';
	/** <samp>&ccedil;</samp> <code>&amp;ccedil; = &amp;#231;</code> -- latin small letter c with cedilla, U+00E7 ISOlat1. */
	public static final char _ccedil='\u00E7';
	/** <samp>&egrave;</samp> <code>&amp;egrave; = &amp;#232;</code> -- latin small letter e with grave, U+00E8 ISOlat1. */
	public static final char _egrave='\u00E8';
	/** <samp>&eacute;</samp> <code>&amp;eacute; = &amp;#233;</code> -- latin small letter e with acute, U+00E9 ISOlat1. */
	public static final char _eacute='\u00E9';
	/** <samp>&ecirc;</samp> <code>&amp;ecirc; = &amp;#234;</code> -- latin small letter e with circumflex, U+00EA ISOlat1. */
	public static final char _ecirc='\u00EA';
	/** <samp>&euml;</samp> <code>&amp;euml; = &amp;#235;</code> -- latin small letter e with diaeresis, U+00EB ISOlat1. */
	public static final char _euml='\u00EB';
	/** <samp>&igrave;</samp> <code>&amp;igrave; = &amp;#236;</code> -- latin small letter i with grave, U+00EC ISOlat1. */
	public static final char _igrave='\u00EC';
	/** <samp>&iacute;</samp> <code>&amp;iacute; = &amp;#237;</code> -- latin small letter i with acute, U+00ED ISOlat1. */
	public static final char _iacute='\u00ED';
	/** <samp>&icirc;</samp> <code>&amp;icirc; = &amp;#238;</code> -- latin small letter i with circumflex, U+00EE ISOlat1. */
	public static final char _icirc='\u00EE';
	/** <samp>&iuml;</samp> <code>&amp;iuml; = &amp;#239;</code> -- latin small letter i with diaeresis, U+00EF ISOlat1. */
	public static final char _iuml='\u00EF';
	/** <samp>&eth;</samp> <code>&amp;eth; = &amp;#240;</code> -- latin small letter eth, U+00F0 ISOlat1. */
	public static final char _eth='\u00F0';
	/** <samp>&ntilde;</samp> <code>&amp;ntilde; = &amp;#241;</code> -- latin small letter n with tilde, U+00F1 ISOlat1. */
	public static final char _ntilde='\u00F1';
	/** <samp>&ograve;</samp> <code>&amp;ograve; = &amp;#242;</code> -- latin small letter o with grave, U+00F2 ISOlat1. */
	public static final char _ograve='\u00F2';
	/** <samp>&oacute;</samp> <code>&amp;oacute; = &amp;#243;</code> -- latin small letter o with acute, U+00F3 ISOlat1. */
	public static final char _oacute='\u00F3';
	/** <samp>&ocirc;</samp> <code>&amp;ocirc; = &amp;#244;</code> -- latin small letter o with circumflex, U+00F4 ISOlat1. */
	public static final char _ocirc='\u00F4';
	/** <samp>&otilde;</samp> <code>&amp;otilde; = &amp;#245;</code> -- latin small letter o with tilde, U+00F5 ISOlat1. */
	public static final char _otilde='\u00F5';
	/** <samp>&ouml;</samp> <code>&amp;ouml; = &amp;#246;</code> -- latin small letter o with diaeresis, U+00F6 ISOlat1. */
	public static final char _ouml='\u00F6';
	/** <samp>&divide;</samp> <code>&amp;divide; = &amp;#247;</code> -- division sign, U+00F7 ISOnum. */
	public static final char _divide='\u00F7';
	/** <samp>&oslash;</samp> <code>&amp;oslash; = &amp;#248;</code> -- latin small letter o with stroke, = latin small letter o slash, U+00F8 ISOlat1. */
	public static final char _oslash='\u00F8';
	/** <samp>&ugrave;</samp> <code>&amp;ugrave; = &amp;#249;</code> -- latin small letter u with grave, U+00F9 ISOlat1. */
	public static final char _ugrave='\u00F9';
	/** <samp>&uacute;</samp> <code>&amp;uacute; = &amp;#250;</code> -- latin small letter u with acute, U+00FA ISOlat1. */
	public static final char _uacute='\u00FA';
	/** <samp>&ucirc;</samp> <code>&amp;ucirc; = &amp;#251;</code> -- latin small letter u with circumflex, U+00FB ISOlat1. */
	public static final char _ucirc='\u00FB';
	/** <samp>&uuml;</samp> <code>&amp;uuml; = &amp;#252;</code> -- latin small letter u with diaeresis, U+00FC ISOlat1. */
	public static final char _uuml='\u00FC';
	/** <samp>&yacute;</samp> <code>&amp;yacute; = &amp;#253;</code> -- latin small letter y with acute, U+00FD ISOlat1. */
	public static final char _yacute='\u00FD';
	/** <samp>&thorn;</samp> <code>&amp;thorn; = &amp;#254;</code> -- latin small letter thorn, U+00FE ISOlat1. */
	public static final char _thorn='\u00FE';
	/** <samp>&yuml;</samp> <code>&amp;yuml; = &amp;#255;</code> -- latin small letter y with diaeresis, U+00FF ISOlat1. */
	public static final char _yuml='\u00FF';
	/** <samp>&fnof;</samp> <code>&amp;fnof; = &amp;#402;</code> -- latin small letter f with hook = function = florin, U+0192 ISOtech. */
	public static final char _fnof='\u0192';
	/** <samp>&Alpha;</samp> <code>&amp;Alpha; = &amp;#913;</code> -- greek capital letter alpha, U+0391. */
	public static final char _Alpha='\u0391';
	/** <samp>&Beta;</samp> <code>&amp;Beta; = &amp;#914;</code> -- greek capital letter beta, U+0392. */
	public static final char _Beta='\u0392';
	/** <samp>&Gamma;</samp> <code>&amp;Gamma; = &amp;#915;</code> -- greek capital letter gamma, U+0393 ISOgrk3. */
	public static final char _Gamma='\u0393';
	/** <samp>&Delta;</samp> <code>&amp;Delta; = &amp;#916;</code> -- greek capital letter delta, U+0394 ISOgrk3. */
	public static final char _Delta='\u0394';
	/** <samp>&Epsilon;</samp> <code>&amp;Epsilon; = &amp;#917;</code> -- greek capital letter epsilon, U+0395. */
	public static final char _Epsilon='\u0395';
	/** <samp>&Zeta;</samp> <code>&amp;Zeta; = &amp;#918;</code> -- greek capital letter zeta, U+0396. */
	public static final char _Zeta='\u0396';
	/** <samp>&Eta;</samp> <code>&amp;Eta; = &amp;#919;</code> -- greek capital letter eta, U+0397. */
	public static final char _Eta='\u0397';
	/** <samp>&Theta;</samp> <code>&amp;Theta; = &amp;#920;</code> -- greek capital letter theta, U+0398 ISOgrk3. */
	public static final char _Theta='\u0398';
	/** <samp>&Iota;</samp> <code>&amp;Iota; = &amp;#921;</code> -- greek capital letter iota, U+0399. */
	public static final char _Iota='\u0399';
	/** <samp>&Kappa;</samp> <code>&amp;Kappa; = &amp;#922;</code> -- greek capital letter kappa, U+039A. */
	public static final char _Kappa='\u039A';
	/** <samp>&Lambda;</samp> <code>&amp;Lambda; = &amp;#923;</code> -- greek capital letter lambda, U+039B ISOgrk3. */
	public static final char _Lambda='\u039B';
	/** <samp>&Mu;</samp> <code>&amp;Mu; = &amp;#924;</code> -- greek capital letter mu, U+039C. */
	public static final char _Mu='\u039C';
	/** <samp>&Nu;</samp> <code>&amp;Nu; = &amp;#925;</code> -- greek capital letter nu, U+039D. */
	public static final char _Nu='\u039D';
	/** <samp>&Xi;</samp> <code>&amp;Xi; = &amp;#926;</code> -- greek capital letter xi, U+039E ISOgrk3. */
	public static final char _Xi='\u039E';
	/** <samp>&Omicron;</samp> <code>&amp;Omicron; = &amp;#927;</code> -- greek capital letter omicron, U+039F. */
	public static final char _Omicron='\u039F';
	/** <samp>&Pi;</samp> <code>&amp;Pi; = &amp;#928;</code> -- greek capital letter pi, U+03A0 ISOgrk3. */
	public static final char _Pi='\u03A0';
	/** <samp>&Rho;</samp> <code>&amp;Rho; = &amp;#929;</code> -- greek capital letter rho, U+03A1. */
	public static final char _Rho='\u03A1';
	/** <samp>&Sigma;</samp> <code>&amp;Sigma; = &amp;#931;</code> -- greek capital letter sigma, U+03A3 ISOgrk3. */
	public static final char _Sigma='\u03A3';
	/** <samp>&Tau;</samp> <code>&amp;Tau; = &amp;#932;</code> -- greek capital letter tau, U+03A4. */
	public static final char _Tau='\u03A4';
	/** <samp>&Upsilon;</samp> <code>&amp;Upsilon; = &amp;#933;</code> -- greek capital letter upsilon, U+03A5 ISOgrk3. */
	public static final char _Upsilon='\u03A5';
	/** <samp>&Phi;</samp> <code>&amp;Phi; = &amp;#934;</code> -- greek capital letter phi, U+03A6 ISOgrk3. */
	public static final char _Phi='\u03A6';
	/** <samp>&Chi;</samp> <code>&amp;Chi; = &amp;#935;</code> -- greek capital letter chi, U+03A7. */
	public static final char _Chi='\u03A7';
	/** <samp>&Psi;</samp> <code>&amp;Psi; = &amp;#936;</code> -- greek capital letter psi, U+03A8 ISOgrk3. */
	public static final char _Psi='\u03A8';
	/** <samp>&Omega;</samp> <code>&amp;Omega; = &amp;#937;</code> -- greek capital letter omega, U+03A9 ISOgrk3. */
	public static final char _Omega='\u03A9';
	/** <samp>&alpha;</samp> <code>&amp;alpha; = &amp;#945;</code> -- greek small letter alpha, U+03B1 ISOgrk3. */
	public static final char _alpha='\u03B1';
	/** <samp>&beta;</samp> <code>&amp;beta; = &amp;#946;</code> -- greek small letter beta, U+03B2 ISOgrk3. */
	public static final char _beta='\u03B2';
	/** <samp>&gamma;</samp> <code>&amp;gamma; = &amp;#947;</code> -- greek small letter gamma, U+03B3 ISOgrk3. */
	public static final char _gamma='\u03B3';
	/** <samp>&delta;</samp> <code>&amp;delta; = &amp;#948;</code> -- greek small letter delta, U+03B4 ISOgrk3. */
	public static final char _delta='\u03B4';
	/** <samp>&epsilon;</samp> <code>&amp;epsilon; = &amp;#949;</code> -- greek small letter epsilon, U+03B5 ISOgrk3. */
	public static final char _epsilon='\u03B5';
	/** <samp>&zeta;</samp> <code>&amp;zeta; = &amp;#950;</code> -- greek small letter zeta, U+03B6 ISOgrk3. */
	public static final char _zeta='\u03B6';
	/** <samp>&eta;</samp> <code>&amp;eta; = &amp;#951;</code> -- greek small letter eta, U+03B7 ISOgrk3. */
	public static final char _eta='\u03B7';
	/** <samp>&theta;</samp> <code>&amp;theta; = &amp;#952;</code> -- greek small letter theta, U+03B8 ISOgrk3. */
	public static final char _theta='\u03B8';
	/** <samp>&iota;</samp> <code>&amp;iota; = &amp;#953;</code> -- greek small letter iota, U+03B9 ISOgrk3. */
	public static final char _iota='\u03B9';
	/** <samp>&kappa;</samp> <code>&amp;kappa; = &amp;#954;</code> -- greek small letter kappa, U+03BA ISOgrk3. */
	public static final char _kappa='\u03BA';
	/** <samp>&lambda;</samp> <code>&amp;lambda; = &amp;#955;</code> -- greek small letter lambda, U+03BB ISOgrk3. */
	public static final char _lambda='\u03BB';
	/** <samp>&mu;</samp> <code>&amp;mu; = &amp;#956;</code> -- greek small letter mu, U+03BC ISOgrk3. */
	public static final char _mu='\u03BC';
	/** <samp>&nu;</samp> <code>&amp;nu; = &amp;#957;</code> -- greek small letter nu, U+03BD ISOgrk3. */
	public static final char _nu='\u03BD';
	/** <samp>&xi;</samp> <code>&amp;xi; = &amp;#958;</code> -- greek small letter xi, U+03BE ISOgrk3. */
	public static final char _xi='\u03BE';
	/** <samp>&omicron;</samp> <code>&amp;omicron; = &amp;#959;</code> -- greek small letter omicron, U+03BF NEW. */
	public static final char _omicron='\u03BF';
	/** <samp>&pi;</samp> <code>&amp;pi; = &amp;#960;</code> -- greek small letter pi, U+03C0 ISOgrk3. */
	public static final char _pi='\u03C0';
	/** <samp>&rho;</samp> <code>&amp;rho; = &amp;#961;</code> -- greek small letter rho, U+03C1 ISOgrk3. */
	public static final char _rho='\u03C1';
	/** <samp>&sigmaf;</samp> <code>&amp;sigmaf; = &amp;#962;</code> -- greek small letter final sigma, U+03C2 ISOgrk3. */
	public static final char _sigmaf='\u03C2';
	/** <samp>&sigma;</samp> <code>&amp;sigma; = &amp;#963;</code> -- greek small letter sigma, U+03C3 ISOgrk3. */
	public static final char _sigma='\u03C3';
	/** <samp>&tau;</samp> <code>&amp;tau; = &amp;#964;</code> -- greek small letter tau, U+03C4 ISOgrk3. */
	public static final char _tau='\u03C4';
	/** <samp>&upsilon;</samp> <code>&amp;upsilon; = &amp;#965;</code> -- greek small letter upsilon, U+03C5 ISOgrk3. */
	public static final char _upsilon='\u03C5';
	/** <samp>&phi;</samp> <code>&amp;phi; = &amp;#966;</code> -- greek small letter phi, U+03C6 ISOgrk3. */
	public static final char _phi='\u03C6';
	/** <samp>&chi;</samp> <code>&amp;chi; = &amp;#967;</code> -- greek small letter chi, U+03C7 ISOgrk3. */
	public static final char _chi='\u03C7';
	/** <samp>&psi;</samp> <code>&amp;psi; = &amp;#968;</code> -- greek small letter psi, U+03C8 ISOgrk3. */
	public static final char _psi='\u03C8';
	/** <samp>&omega;</samp> <code>&amp;omega; = &amp;#969;</code> -- greek small letter omega, U+03C9 ISOgrk3. */
	public static final char _omega='\u03C9';
	/** <samp>&thetasym;</samp> <code>&amp;thetasym; = &amp;#977;</code> -- greek small letter theta symbol, U+03D1 NEW. */
	public static final char _thetasym='\u03D1';
	/** <samp>&upsih;</samp> <code>&amp;upsih; = &amp;#978;</code> -- greek upsilon with hook symbol, U+03D2 NEW. */
	public static final char _upsih='\u03D2';
	/** <samp>&piv;</samp> <code>&amp;piv; = &amp;#982;</code> -- greek pi symbol, U+03D6 ISOgrk3. */
	public static final char _piv='\u03D6';
	/** <samp>&bull;</samp> <code>&amp;bull; = &amp;#8226;</code> -- bullet = black small circle, U+2022 ISOpub<br />(see <a href="#_bull">comments</a>).<p>bullet is NOT the same as bullet operator, U+2219</p> */
	public static final char _bull='\u2022';
	/** <samp>&hellip;</samp> <code>&amp;hellip; = &amp;#8230;</code> -- horizontal ellipsis = three dot leader, U+2026 ISOpub. */
	public static final char _hellip='\u2026';
	/** <samp>&prime;</samp> <code>&amp;prime; = &amp;#8242;</code> -- prime = minutes = feet, U+2032 ISOtech. */
	public static final char _prime='\u2032';
	/** <samp>&Prime;</samp> <code>&amp;Prime; = &amp;#8243;</code> -- double prime = seconds = inches, U+2033 ISOtech. */
	public static final char _Prime='\u2033';
	/** <samp>&oline;</samp> <code>&amp;oline; = &amp;#8254;</code> -- overline = spacing overscore, U+203E NEW. */
	public static final char _oline='\u203E';
	/** <samp>&frasl;</samp> <code>&amp;frasl; = &amp;#8260;</code> -- fraction slash, U+2044 NEW. */
	public static final char _frasl='\u2044';
	/** <samp>&weierp;</samp> <code>&amp;weierp; = &amp;#8472;</code> -- script capital P = power set = Weierstrass p, U+2118 ISOamso. */
	public static final char _weierp='\u2118';
	/** <samp>&image;</samp> <code>&amp;image; = &amp;#8465;</code> -- black-letter capital I = imaginary part, U+2111 ISOamso. */
	public static final char _image='\u2111';
	/** <samp>&real;</samp> <code>&amp;real; = &amp;#8476;</code> -- black-letter capital R = real part symbol, U+211C ISOamso. */
	public static final char _real='\u211C';
	/** <samp>&trade;</samp> <code>&amp;trade; = &amp;#8482;</code> -- trade mark sign, U+2122 ISOnum. */
	public static final char _trade='\u2122';
	/** <samp>&alefsym;</samp> <code>&amp;alefsym; = &amp;#8501;</code> -- alef symbol = first transfinite cardinal, U+2135 NEW<br />(see <a href="#_alefsym">comments</a>).<p>alef symbol is NOT the same as hebrew letter alef, U+05D0 although the same glyph could be used to depict both characters</p> */
	public static final char _alefsym='\u2135';
	/** <samp>&larr;</samp> <code>&amp;larr; = &amp;#8592;</code> -- leftwards arrow, U+2190 ISOnum. */
	public static final char _larr='\u2190';
	/** <samp>&uarr;</samp> <code>&amp;uarr; = &amp;#8593;</code> -- upwards arrow, U+2191 ISOnum. */
	public static final char _uarr='\u2191';
	/** <samp>&rarr;</samp> <code>&amp;rarr; = &amp;#8594;</code> -- rightwards arrow, U+2192 ISOnum. */
	public static final char _rarr='\u2192';
	/** <samp>&darr;</samp> <code>&amp;darr; = &amp;#8595;</code> -- downwards arrow, U+2193 ISOnum. */
	public static final char _darr='\u2193';
	/** <samp>&harr;</samp> <code>&amp;harr; = &amp;#8596;</code> -- left right arrow, U+2194 ISOamsa. */
	public static final char _harr='\u2194';
	/** <samp>&crarr;</samp> <code>&amp;crarr; = &amp;#8629;</code> -- downwards arrow with corner leftwards = carriage return, U+21B5 NEW. */
	public static final char _crarr='\u21B5';
	/** <samp>&lArr;</samp> <code>&amp;lArr; = &amp;#8656;</code> -- leftwards double arrow, U+21D0 ISOtech<br />(see <a href="#_lArr">comments</a>).<p>ISO 10646 does not say that lArr is the same as the 'is implied by' arrow but also does not have any other character for that function. So &#63; lArr can be used for 'is implied by' as ISOtech suggests</p> */
	public static final char _lArr='\u21D0';
	/** <samp>&uArr;</samp> <code>&amp;uArr; = &amp;#8657;</code> -- upwards double arrow, U+21D1 ISOamsa. */
	public static final char _uArr='\u21D1';
	/** <samp>&rArr;</samp> <code>&amp;rArr; = &amp;#8658;</code> -- rightwards double arrow, U+21D2 ISOtech<br />(see <a href="#_rArr">comments</a>).<p>ISO 10646 does not say this is the 'implies' character but does not have another character with this function so &#63; rArr can be used for 'implies' as ISOtech suggests</p> */
	public static final char _rArr='\u21D2';
	/** <samp>&dArr;</samp> <code>&amp;dArr; = &amp;#8659;</code> -- downwards double arrow, U+21D3 ISOamsa. */
	public static final char _dArr='\u21D3';
	/** <samp>&hArr;</samp> <code>&amp;hArr; = &amp;#8660;</code> -- left right double arrow, U+21D4 ISOamsa. */
	public static final char _hArr='\u21D4';
	/** <samp>&forall;</samp> <code>&amp;forall; = &amp;#8704;</code> -- for all, U+2200 ISOtech. */
	public static final char _forall='\u2200';
	/** <samp>&part;</samp> <code>&amp;part; = &amp;#8706;</code> -- partial differential, U+2202 ISOtech. */
	public static final char _part='\u2202';
	/** <samp>&exist;</samp> <code>&amp;exist; = &amp;#8707;</code> -- there exists, U+2203 ISOtech. */
	public static final char _exist='\u2203';
	/** <samp>&empty;</samp> <code>&amp;empty; = &amp;#8709;</code> -- empty set = null set = diameter, U+2205 ISOamso. */
	public static final char _empty='\u2205';
	/** <samp>&nabla;</samp> <code>&amp;nabla; = &amp;#8711;</code> -- nabla = backward difference, U+2207 ISOtech. */
	public static final char _nabla='\u2207';
	/** <samp>&isin;</samp> <code>&amp;isin; = &amp;#8712;</code> -- element of, U+2208 ISOtech. */
	public static final char _isin='\u2208';
	/** <samp>&notin;</samp> <code>&amp;notin; = &amp;#8713;</code> -- not an element of, U+2209 ISOtech. */
	public static final char _notin='\u2209';
	/** <samp>&ni;</samp> <code>&amp;ni; = &amp;#8715;</code> -- contains as member, U+220B ISOtech<br />(see <a href="#_ni">comments</a>).<p>should there be a more memorable name than 'ni'&#63;</p> */
	public static final char _ni='\u220B';
	/** <samp>&prod;</samp> <code>&amp;prod; = &amp;#8719;</code> -- n-ary product = product sign, U+220F ISOamsb<br />(see <a href="#_prod">comments</a>).<p>prod is NOT the same character as U+03A0 'greek capital letter pi' though the same glyph might be used for both</p> */
	public static final char _prod='\u220F';
	/** <samp>&sum;</samp> <code>&amp;sum; = &amp;#8721;</code> -- n-ary summation, U+2211 ISOamsb<br />(see <a href="#_sum">comments</a>).<p>sum is NOT the same character as U+03A3 'greek capital letter sigma' though the same glyph might be used for both</p> */
	public static final char _sum='\u2211';
	/** <samp>&minus;</samp> <code>&amp;minus; = &amp;#8722;</code> -- minus sign, U+2212 ISOtech. */
	public static final char _minus='\u2212';
	/** <samp>&lowast;</samp> <code>&amp;lowast; = &amp;#8727;</code> -- asterisk operator, U+2217 ISOtech. */
	public static final char _lowast='\u2217';
	/** <samp>&radic;</samp> <code>&amp;radic; = &amp;#8730;</code> -- square root = radical sign, U+221A ISOtech. */
	public static final char _radic='\u221A';
	/** <samp>&prop;</samp> <code>&amp;prop; = &amp;#8733;</code> -- proportional to, U+221D ISOtech. */
	public static final char _prop='\u221D';
	/** <samp>&infin;</samp> <code>&amp;infin; = &amp;#8734;</code> -- infinity, U+221E ISOtech. */
	public static final char _infin='\u221E';
	/** <samp>&ang;</samp> <code>&amp;ang; = &amp;#8736;</code> -- angle, U+2220 ISOamso. */
	public static final char _ang='\u2220';
	/** <samp>&and;</samp> <code>&amp;and; = &amp;#8743;</code> -- logical and = wedge, U+2227 ISOtech. */
	public static final char _and='\u2227';
	/** <samp>&or;</samp> <code>&amp;or; = &amp;#8744;</code> -- logical or = vee, U+2228 ISOtech. */
	public static final char _or='\u2228';
	/** <samp>&cap;</samp> <code>&amp;cap; = &amp;#8745;</code> -- intersection = cap, U+2229 ISOtech. */
	public static final char _cap='\u2229';
	/** <samp>&cup;</samp> <code>&amp;cup; = &amp;#8746;</code> -- union = cup, U+222A ISOtech. */
	public static final char _cup='\u222A';
	/** <samp>&int;</samp> <code>&amp;int; = &amp;#8747;</code> -- integral, U+222B ISOtech. */
	public static final char _int='\u222B';
	/** <samp>&there4;</samp> <code>&amp;there4; = &amp;#8756;</code> -- therefore, U+2234 ISOtech. */
	public static final char _there4='\u2234';
	/** <samp>&sim;</samp> <code>&amp;sim; = &amp;#8764;</code> -- tilde operator = varies with = similar to, U+223C ISOtech<br />(see <a href="#_sim">comments</a>).<p>tilde operator is NOT the same character as the tilde, U+007E, although the same glyph might be used to represent both</p> */
	public static final char _sim='\u223C';
	/** <samp>&cong;</samp> <code>&amp;cong; = &amp;#8773;</code> -- approximately equal to, U+2245 ISOtech. */
	public static final char _cong='\u2245';
	/** <samp>&asymp;</samp> <code>&amp;asymp; = &amp;#8776;</code> -- almost equal to = asymptotic to, U+2248 ISOamsr. */
	public static final char _asymp='\u2248';
	/** <samp>&ne;</samp> <code>&amp;ne; = &amp;#8800;</code> -- not equal to, U+2260 ISOtech. */
	public static final char _ne='\u2260';
	/** <samp>&equiv;</samp> <code>&amp;equiv; = &amp;#8801;</code> -- identical to, U+2261 ISOtech. */
	public static final char _equiv='\u2261';
	/** <samp>&le;</samp> <code>&amp;le; = &amp;#8804;</code> -- less-than or equal to, U+2264 ISOtech. */
	public static final char _le='\u2264';
	/** <samp>&ge;</samp> <code>&amp;ge; = &amp;#8805;</code> -- greater-than or equal to, U+2265 ISOtech. */
	public static final char _ge='\u2265';
	/** <samp>&sub;</samp> <code>&amp;sub; = &amp;#8834;</code> -- subset of, U+2282 ISOtech. */
	public static final char _sub='\u2282';
	/** <samp>&sup;</samp> <code>&amp;sup; = &amp;#8835;</code> -- superset of, U+2283 ISOtech<br />(see <a href="#_sup">comments</a>).<p>note that nsup, 'not a superset of, U+2283' is not covered by the Symbol font encoding and is not included. Should it be, for symmetry&#63; It is in ISOamsn</p> */
	public static final char _sup='\u2283';
	/** <samp>&nsub;</samp> <code>&amp;nsub; = &amp;#8836;</code> -- not a subset of, U+2284 ISOamsn. */
	public static final char _nsub='\u2284';
	/** <samp>&sube;</samp> <code>&amp;sube; = &amp;#8838;</code> -- subset of or equal to, U+2286 ISOtech. */
	public static final char _sube='\u2286';
	/** <samp>&supe;</samp> <code>&amp;supe; = &amp;#8839;</code> -- superset of or equal to, U+2287 ISOtech. */
	public static final char _supe='\u2287';
	/** <samp>&oplus;</samp> <code>&amp;oplus; = &amp;#8853;</code> -- circled plus = direct sum, U+2295 ISOamsb. */
	public static final char _oplus='\u2295';
	/** <samp>&otimes;</samp> <code>&amp;otimes; = &amp;#8855;</code> -- circled times = vector product, U+2297 ISOamsb. */
	public static final char _otimes='\u2297';
	/** <samp>&perp;</samp> <code>&amp;perp; = &amp;#8869;</code> -- up tack = orthogonal to = perpendicular, U+22A5 ISOtech. */
	public static final char _perp='\u22A5';
	/** <samp>&sdot;</samp> <code>&amp;sdot; = &amp;#8901;</code> -- dot operator, U+22C5 ISOamsb<br />(see <a href="#_sdot">comments</a>).<p>dot operator is NOT the same character as U+00B7 middle dot</p> */
	public static final char _sdot='\u22C5';
	/** <samp>&lceil;</samp> <code>&amp;lceil; = &amp;#8968;</code> -- left ceiling = APL upstile, U+2308 ISOamsc. */
	public static final char _lceil='\u2308';
	/** <samp>&rceil;</samp> <code>&amp;rceil; = &amp;#8969;</code> -- right ceiling, U+2309 ISOamsc. */
	public static final char _rceil='\u2309';
	/** <samp>&lfloor;</samp> <code>&amp;lfloor; = &amp;#8970;</code> -- left floor = APL downstile, U+230A ISOamsc. */
	public static final char _lfloor='\u230A';
	/** <samp>&rfloor;</samp> <code>&amp;rfloor; = &amp;#8971;</code> -- right floor, U+230B ISOamsc. */
	public static final char _rfloor='\u230B';
	/** <samp>&lang;</samp> <code>&amp;lang; = &amp;#9001;</code> -- left-pointing angle bracket = bra, U+2329 ISOtech<br />(see <a href="#_lang">comments</a>).<p>lang is NOT the same character as U+003C 'less than' or U+2039 'single left-pointing angle quotation mark'</p> */
	public static final char _lang='\u2329';
	/** <samp>&rang;</samp> <code>&amp;rang; = &amp;#9002;</code> -- right-pointing angle bracket = ket, U+232A ISOtech<br />(see <a href="#_rang">comments</a>).<p>rang is NOT the same character as U+003E 'greater than' or U+203A 'single right-pointing angle quotation mark'</p> */
	public static final char _rang='\u232A';
	/** <samp>&loz;</samp> <code>&amp;loz; = &amp;#9674;</code> -- lozenge, U+25CA ISOpub. */
	public static final char _loz='\u25CA';
	/** <samp>&spades;</samp> <code>&amp;spades; = &amp;#9824;</code> -- black spade suit, U+2660 ISOpub<br />(see <a href="#_spades">comments</a>).<p>black here seems to mean filled as opposed to hollow</p> */
	public static final char _spades='\u2660';
	/** <samp>&clubs;</samp> <code>&amp;clubs; = &amp;#9827;</code> -- black club suit = shamrock, U+2663 ISOpub. */
	public static final char _clubs='\u2663';
	/** <samp>&hearts;</samp> <code>&amp;hearts; = &amp;#9829;</code> -- black heart suit = valentine, U+2665 ISOpub. */
	public static final char _hearts='\u2665';
	/** <samp>&diams;</samp> <code>&amp;diams; = &amp;#9830;</code> -- black diamond suit, U+2666 ISOpub. */
	public static final char _diams='\u2666';
	/** <samp>&quot;</samp> <code>&amp;quot; = &amp;#34;</code> -- quotation mark = APL quote, U+0022 ISOnum. */
	public static final char _quot='\u0022';
	/** <samp>&amp;</samp> <code>&amp;amp; = &amp;#38;</code> -- ampersand, U+0026 ISOnum. */
	public static final char _amp='\u0026';
	/** <samp>&lt;</samp> <code>&amp;lt; = &amp;#60;</code> -- less-than sign, U+003C ISOnum. */
	public static final char _lt='\u003C';
	/** <samp>&gt;</samp> <code>&amp;gt; = &amp;#62;</code> -- greater-than sign, U+003E ISOnum. */
	public static final char _gt='\u003E';
	/** <samp>&OElig;</samp> <code>&amp;OElig; = &amp;#338;</code> -- latin capital ligature OE, U+0152 ISOlat2. */
	public static final char _OElig='\u0152';
	/** <samp>&oelig;</samp> <code>&amp;oelig; = &amp;#339;</code> -- latin small ligature oe, U+0153 ISOlat2<br />(see <a href="#_oelig">comments</a>).<p>ligature is a misnomer, this is a separate character in some languages</p> */
	public static final char _oelig='\u0153';
	/** <samp>&Scaron;</samp> <code>&amp;Scaron; = &amp;#352;</code> -- latin capital letter S with caron, U+0160 ISOlat2. */
	public static final char _Scaron='\u0160';
	/** <samp>&scaron;</samp> <code>&amp;scaron; = &amp;#353;</code> -- latin small letter s with caron, U+0161 ISOlat2. */
	public static final char _scaron='\u0161';
	/** <samp>&Yuml;</samp> <code>&amp;Yuml; = &amp;#376;</code> -- latin capital letter Y with diaeresis, U+0178 ISOlat2. */
	public static final char _Yuml='\u0178';
	/** <samp>&circ;</samp> <code>&amp;circ; = &amp;#710;</code> -- modifier letter circumflex accent, U+02C6 ISOpub. */
	public static final char _circ='\u02C6';
	/** <samp>&tilde;</samp> <code>&amp;tilde; = &amp;#732;</code> -- small tilde, U+02DC ISOdia. */
	public static final char _tilde='\u02DC';
	/** <samp>&ensp;</samp> <code>&amp;ensp; = &amp;#8194;</code> -- en space, U+2002 ISOpub. */
	public static final char _ensp='\u2002';
	/** <samp>&emsp;</samp> <code>&amp;emsp; = &amp;#8195;</code> -- em space, U+2003 ISOpub. */
	public static final char _emsp='\u2003';
	/** <samp>&thinsp;</samp> <code>&amp;thinsp; = &amp;#8201;</code> -- thin space, U+2009 ISOpub. */
	public static final char _thinsp='\u2009';
	/** <samp>&zwnj;</samp> <code>&amp;zwnj; = &amp;#8204;</code> -- zero width non-joiner, U+200C NEW RFC 2070. */
	public static final char _zwnj='\u200C';
	/** <samp>&zwj;</samp> <code>&amp;zwj; = &amp;#8205;</code> -- zero width joiner, U+200D NEW RFC 2070. */
	public static final char _zwj='\u200D';
	/** <samp>&lrm;</samp> <code>&amp;lrm; = &amp;#8206;</code> -- left-to-right mark, U+200E NEW RFC 2070. */
	public static final char _lrm='\u200E';
	/** <samp>&rlm;</samp> <code>&amp;rlm; = &amp;#8207;</code> -- right-to-left mark, U+200F NEW RFC 2070. */
	public static final char _rlm='\u200F';
	/** <samp>&ndash;</samp> <code>&amp;ndash; = &amp;#8211;</code> -- en dash, U+2013 ISOpub. */
	public static final char _ndash='\u2013';
	/** <samp>&mdash;</samp> <code>&amp;mdash; = &amp;#8212;</code> -- em dash, U+2014 ISOpub. */
	public static final char _mdash='\u2014';
	/** <samp>&lsquo;</samp> <code>&amp;lsquo; = &amp;#8216;</code> -- left single quotation mark, U+2018 ISOnum. */
	public static final char _lsquo='\u2018';
	/** <samp>&rsquo;</samp> <code>&amp;rsquo; = &amp;#8217;</code> -- right single quotation mark, U+2019 ISOnum. */
	public static final char _rsquo='\u2019';
	/** <samp>&sbquo;</samp> <code>&amp;sbquo; = &amp;#8218;</code> -- single low-9 quotation mark, U+201A NEW. */
	public static final char _sbquo='\u201A';
	/** <samp>&ldquo;</samp> <code>&amp;ldquo; = &amp;#8220;</code> -- left double quotation mark, U+201C ISOnum. */
	public static final char _ldquo='\u201C';
	/** <samp>&rdquo;</samp> <code>&amp;rdquo; = &amp;#8221;</code> -- right double quotation mark, U+201D ISOnum. */
	public static final char _rdquo='\u201D';
	/** <samp>&bdquo;</samp> <code>&amp;bdquo; = &amp;#8222;</code> -- double low-9 quotation mark, U+201E NEW. */
	public static final char _bdquo='\u201E';
	/** <samp>&dagger;</samp> <code>&amp;dagger; = &amp;#8224;</code> -- dagger, U+2020 ISOpub. */
	public static final char _dagger='\u2020';
	/** <samp>&Dagger;</samp> <code>&amp;Dagger; = &amp;#8225;</code> -- double dagger, U+2021 ISOpub. */
	public static final char _Dagger='\u2021';
	/** <samp>&permil;</samp> <code>&amp;permil; = &amp;#8240;</code> -- per mille sign, U+2030 ISOtech. */
	public static final char _permil='\u2030';
	/** <samp>&lsaquo;</samp> <code>&amp;lsaquo; = &amp;#8249;</code> -- single left-pointing angle quotation mark, U+2039 ISO proposed<br />(see <a href="#_lsaquo">comments</a>).<p>lsaquo is proposed but not yet ISO standardized</p> */
	public static final char _lsaquo='\u2039';
	/** <samp>&rsaquo;</samp> <code>&amp;rsaquo; = &amp;#8250;</code> -- single right-pointing angle quotation mark, U+203A ISO proposed<br />(see <a href="#_rsaquo">comments</a>).<p>rsaquo is proposed but not yet ISO standardized</p> */
	public static final char _rsaquo='\u203A';
	/** <samp>&euro;</samp> <code>&amp;euro; = &amp;#8364;</code> -- euro sign, U+20AC NEW. */
	public static final char _euro='\u20AC';
	/**
	 * <samp>&apos;</samp> <code>&amp;apos; = &amp;#39;</code> -- apostrophe = APL quote, U+0027 ISOnum<br />(see <a href="#_apos">comments</a>).<p>
	 * apos is only defined for use in XHTML
	 * (see the <a target="_blank" href="http://www.w3.org/TR/xhtml1/dtds.html#a_dtd_Special_characters">XHTML Special Characters Entity Set</a>),
	 * but not in HTML.
	 * @see Config#IsApostropheEncoded
	 */
	public static final char _apos='\'';

	private static Hashtable<String,Character> entities=new Hashtable<String,Character>(); // 253 entities in total

	static {
		entities.put("nbsp",(_nbsp));
		entities.put("iexcl",(_iexcl));
		entities.put("cent",(_cent));
		entities.put("pound",(_pound));
		entities.put("curren",(_curren));
		entities.put("yen",(_yen));
		entities.put("brvbar",(_brvbar));
		entities.put("sect",(_sect));
		entities.put("uml",(_uml));
		entities.put("copy",(_copy));
		entities.put("ordf",(_ordf));
		entities.put("laquo",(_laquo));
		entities.put("not",(_not));
		entities.put("shy",(_shy));
		entities.put("reg",(_reg));
		entities.put("macr",(_macr));
		entities.put("deg",(_deg));
		entities.put("plusmn",(_plusmn));
		entities.put("sup2",(_sup2));
		entities.put("sup3",(_sup3));
		entities.put("acute",(_acute));
		entities.put("micro",(_micro));
		entities.put("para",(_para));
		entities.put("middot",(_middot));
		entities.put("cedil",(_cedil));
		entities.put("sup1",(_sup1));
		entities.put("ordm",(_ordm));
		entities.put("raquo",(_raquo));
		entities.put("frac14",(_frac14));
		entities.put("frac12",(_frac12));
		entities.put("frac34",(_frac34));
		entities.put("iquest",(_iquest));
		entities.put("Agrave",(_Agrave));
		entities.put("Aacute",(_Aacute));
		entities.put("Acirc",(_Acirc));
		entities.put("Atilde",(_Atilde));
		entities.put("Auml",(_Auml));
		entities.put("Aring",(_Aring));
		entities.put("AElig",(_AElig));
		entities.put("Ccedil",(_Ccedil));
		entities.put("Egrave",(_Egrave));
		entities.put("Eacute",(_Eacute));
		entities.put("Ecirc",(_Ecirc));
		entities.put("Euml",(_Euml));
		entities.put("Igrave",(_Igrave));
		entities.put("Iacute",(_Iacute));
		entities.put("Icirc",(_Icirc));
		entities.put("Iuml",(_Iuml));
		entities.put("ETH",(_ETH));
		entities.put("Ntilde",(_Ntilde));
		entities.put("Ograve",(_Ograve));
		entities.put("Oacute",(_Oacute));
		entities.put("Ocirc",(_Ocirc));
		entities.put("Otilde",(_Otilde));
		entities.put("Ouml",(_Ouml));
		entities.put("times",(_times));
		entities.put("Oslash",(_Oslash));
		entities.put("Ugrave",(_Ugrave));
		entities.put("Uacute",(_Uacute));
		entities.put("Ucirc",(_Ucirc));
		entities.put("Uuml",(_Uuml));
		entities.put("Yacute",(_Yacute));
		entities.put("THORN",(_THORN));
		entities.put("szlig",(_szlig));
		entities.put("agrave",(_agrave));
		entities.put("aacute",(_aacute));
		entities.put("acirc",(_acirc));
		entities.put("atilde",(_atilde));
		entities.put("auml",(_auml));
		entities.put("aring",(_aring));
		entities.put("aelig",(_aelig));
		entities.put("ccedil",(_ccedil));
		entities.put("egrave",(_egrave));
		entities.put("eacute",(_eacute));
		entities.put("ecirc",(_ecirc));
		entities.put("euml",(_euml));
		entities.put("igrave",(_igrave));
		entities.put("iacute",(_iacute));
		entities.put("icirc",(_icirc));
		entities.put("iuml",(_iuml));
		entities.put("eth",(_eth));
		entities.put("ntilde",(_ntilde));
		entities.put("ograve",(_ograve));
		entities.put("oacute",(_oacute));
		entities.put("ocirc",(_ocirc));
		entities.put("otilde",(_otilde));
		entities.put("ouml",(_ouml));
		entities.put("divide",(_divide));
		entities.put("oslash",(_oslash));
		entities.put("ugrave",(_ugrave));
		entities.put("uacute",(_uacute));
		entities.put("ucirc",(_ucirc));
		entities.put("uuml",(_uuml));
		entities.put("yacute",(_yacute));
		entities.put("thorn",(_thorn));
		entities.put("yuml",(_yuml));
		entities.put("fnof",(_fnof));
		entities.put("Alpha",(_Alpha));
		entities.put("Beta",(_Beta));
		entities.put("Gamma",(_Gamma));
		entities.put("Delta",(_Delta));
		entities.put("Epsilon",(_Epsilon));
		entities.put("Zeta",(_Zeta));
		entities.put("Eta",(_Eta));
		entities.put("Theta",(_Theta));
		entities.put("Iota",(_Iota));
		entities.put("Kappa",(_Kappa));
		entities.put("Lambda",(_Lambda));
		entities.put("Mu",(_Mu));
		entities.put("Nu",(_Nu));
		entities.put("Xi",(_Xi));
		entities.put("Omicron",(_Omicron));
		entities.put("Pi",(_Pi));
		entities.put("Rho",(_Rho));
		entities.put("Sigma",(_Sigma));
		entities.put("Tau",(_Tau));
		entities.put("Upsilon",(_Upsilon));
		entities.put("Phi",(_Phi));
		entities.put("Chi",(_Chi));
		entities.put("Psi",(_Psi));
		entities.put("Omega",(_Omega));
		entities.put("alpha",(_alpha));
		entities.put("beta",(_beta));
		entities.put("gamma",(_gamma));
		entities.put("delta",(_delta));
		entities.put("epsilon",(_epsilon));
		entities.put("zeta",(_zeta));
		entities.put("eta",(_eta));
		entities.put("theta",(_theta));
		entities.put("iota",(_iota));
		entities.put("kappa",(_kappa));
		entities.put("lambda",(_lambda));
		entities.put("mu",(_mu));
		entities.put("nu",(_nu));
		entities.put("xi",(_xi));
		entities.put("omicron",(_omicron));
		entities.put("pi",(_pi));
		entities.put("rho",(_rho));
		entities.put("sigmaf",(_sigmaf));
		entities.put("sigma",(_sigma));
		entities.put("tau",(_tau));
		entities.put("upsilon",(_upsilon));
		entities.put("phi",(_phi));
		entities.put("chi",(_chi));
		entities.put("psi",(_psi));
		entities.put("omega",(_omega));
		entities.put("thetasym",(_thetasym));
		entities.put("upsih",(_upsih));
		entities.put("piv",(_piv));
		entities.put("bull",(_bull));
		entities.put("hellip",(_hellip));
		entities.put("prime",(_prime));
		entities.put("Prime",(_Prime));
		entities.put("oline",(_oline));
		entities.put("frasl",(_frasl));
		entities.put("weierp",(_weierp));
		entities.put("image",(_image));
		entities.put("real",(_real));
		entities.put("trade",(_trade));
		entities.put("alefsym",(_alefsym));
		entities.put("larr",(_larr));
		entities.put("uarr",(_uarr));
		entities.put("rarr",(_rarr));
		entities.put("darr",(_darr));
		entities.put("harr",(_harr));
		entities.put("crarr",(_crarr));
		entities.put("lArr",(_lArr));
		entities.put("uArr",(_uArr));
		entities.put("rArr",(_rArr));
		entities.put("dArr",(_dArr));
		entities.put("hArr",(_hArr));
		entities.put("forall",(_forall));
		entities.put("part",(_part));
		entities.put("exist",(_exist));
		entities.put("empty",(_empty));
		entities.put("nabla",(_nabla));
		entities.put("isin",(_isin));
		entities.put("notin",(_notin));
		entities.put("ni",(_ni));
		entities.put("prod",(_prod));
		entities.put("sum",(_sum));
		entities.put("minus",(_minus));
		entities.put("lowast",(_lowast));
		entities.put("radic",(_radic));
		entities.put("prop",(_prop));
		entities.put("infin",(_infin));
		entities.put("ang",(_ang));
		entities.put("and",(_and));
		entities.put("or",(_or));
		entities.put("cap",(_cap));
		entities.put("cup",(_cup));
		entities.put("int",(_int));
		entities.put("there4",(_there4));
		entities.put("sim",(_sim));
		entities.put("cong",(_cong));
		entities.put("asymp",(_asymp));
		entities.put("ne",(_ne));
		entities.put("equiv",(_equiv));
		entities.put("le",(_le));
		entities.put("ge",(_ge));
		entities.put("sub",(_sub));
		entities.put("sup",(_sup));
		entities.put("nsub",(_nsub));
		entities.put("sube",(_sube));
		entities.put("supe",(_supe));
		entities.put("oplus",(_oplus));
		entities.put("otimes",(_otimes));
		entities.put("perp",(_perp));
		entities.put("sdot",(_sdot));
		entities.put("lceil",(_lceil));
		entities.put("rceil",(_rceil));
		entities.put("lfloor",(_lfloor));
		entities.put("rfloor",(_rfloor));
		entities.put("lang",(_lang));
		entities.put("rang",(_rang));
		entities.put("loz",(_loz));
		entities.put("spades",(_spades));
		entities.put("clubs",(_clubs));
		entities.put("hearts",(_hearts));
		entities.put("diams",(_diams));
		entities.put("quot",(_quot));
		entities.put("amp",(_amp));
		entities.put("lt",(_lt));
		entities.put("gt",(_gt));
		entities.put("OElig",(_OElig));
		entities.put("oelig",(_oelig));
		entities.put("Scaron",(_Scaron));
		entities.put("scaron",(_scaron));
		entities.put("Yuml",(_Yuml));
		entities.put("circ",(_circ));
		entities.put("tilde",(_tilde));
		entities.put("ensp",(_ensp));
		entities.put("emsp",(_emsp));
		entities.put("thinsp",(_thinsp));
		entities.put("zwnj",(_zwnj));
		entities.put("zwj",(_zwj));
		entities.put("lrm",(_lrm));
		entities.put("rlm",(_rlm));
		entities.put("ndash",(_ndash));
		entities.put("mdash",(_mdash));
		entities.put("lsquo",(_lsquo));
		entities.put("rsquo",(_rsquo));
		entities.put("sbquo",(_sbquo));
		entities.put("ldquo",(_ldquo));
		entities.put("rdquo",(_rdquo));
		entities.put("bdquo",(_bdquo));
		entities.put("dagger",(_dagger));
		entities.put("Dagger",(_Dagger));
		entities.put("permil",(_permil));
		entities.put("lsaquo",(_lsaquo));
		entities.put("rsaquo",(_rsaquo));
		entities.put("euro",(_euro));
		entities.put("apos",(_apos));		
	}
	/** @return char or null if invalid sequence */
	public static Character decodeEntity(String entity){
		return entities.get(entity);
	}
	/** @return char or null if invalid */
	public static Character decodeNumericEntity(String num){
		if(num.equals(""))
			return null;
		int base = 10;
		if(num.charAt(0)=='x' || num.charAt(0)=='X'){
			base = 16;
			num = num.substring(1);
		}
		try{
			int i = Integer.parseInt(num,base);
			return (char)i;
		} catch(Exception e){
			return null;
		}
	}
}

