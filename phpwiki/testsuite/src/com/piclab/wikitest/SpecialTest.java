
/*
 * Test functioning of various special pages. Does not bother with
 * some pages like Recentchanges and Userlogin that are adequately
 * exercised by other test, or with developer-only stuff.
 */

package com.piclab.wikitest;
import com.meterware.httpunit.*;

public class SpecialTest extends WikiTest {

public String testName() { return "Special"; }

protected int initTest() throws Exception {
	m_suite.logout();
	return 0;
}

protected int runTest() throws Exception {
	int c = 0;

	if ( 0 != ( c = part1() ) ) { return fail( c ); }
	if ( 0 != ( c = part2() ) ) { return fail( c ); }
	if ( 0 != ( c = part3() ) ) { return fail( c ); }
	if ( 0 != ( c = part4() ) ) { return fail( c ); }
	if ( 0 != ( c = part5() ) ) { return fail( c ); }
	if ( 0 != ( c = part6() ) ) { return fail( c ); }
	if ( 0 != ( c = part7() ) ) { return fail( c ); }
	if ( 0 != ( c = part8() ) ) { return fail( c ); }
	if ( 0 != ( c = part9() ) ) { return fail( c ); }
	if ( 0 != ( c = part10() ) ) { return fail( c ); }
	if ( 0 != ( c = part11() ) ) { return fail( c ); }
	if ( 0 != ( c = part12() ) ) { return fail( c ); }
	if ( 0 != ( c = part13() ) ) { return fail( c ); }
	if ( 0 != ( c = part14() ) ) { return fail( c ); }
	if ( 0 != ( c = part15() ) ) { return fail( c ); }
	if ( 0 != ( c = part16() ) ) { return fail( c ); }
	if ( 0 != ( c = part17() ) ) { return fail( c ); }
	if ( 0 != ( c = part18() ) ) { return fail( c ); }
	if ( 0 != ( c = part19() ) ) { return fail( c ); }
	if ( 0 != ( c = part20() ) ) { return fail( c ); }
	return 0;
}

private int part1() throws Exception {
	m_suite.viewPage( "Special:Allpages" );
	return 0;
}

private int part2() throws Exception {
	m_suite.viewPage( "Special:Booksources" );
	return 0;
}

private int part3() throws Exception {
	m_suite.viewPage( "Special:Contributions" );
	return 0;
}

private int part4() throws Exception {
	m_suite.viewPage( "Special:Emailuser" );
	return 0;
}

private int part5() throws Exception {
	m_suite.viewPage( "Special:Listusers" );
	return 0;
}

private int part6() throws Exception {
	m_suite.viewPage( "Special:Lonelypages" );
	return 0;
}

private int part7() throws Exception {
	m_suite.viewPage( "Special:Longpages" );
	return 0;
}

private int part8() throws Exception {
	m_suite.viewPage( "Special:Movepage" );
	return 0;
}

private int part9() throws Exception {
	m_suite.viewPage( "Special:Neglectedpages" );
	return 0;
}

private int part10() throws Exception {
	m_suite.viewPage( "Special:Newpages" );
	return 0;
}

private int part11() throws Exception {
	m_suite.viewPage( "Special:Popularpages" );
	return 0;
}

private int part12() throws Exception {
	m_suite.viewPage( "Special:Randompage" );
	return 0;
}

private int part13() throws Exception {
	m_suite.viewPage( "Special:Recentchangeslinked" );
	return 0;
}

private int part14() throws Exception {
	m_suite.viewPage( "Special:Shortpages" );
	return 0;
}

private int part15() throws Exception {
	m_suite.viewPage( "Special:Specialpages" );
	return 0;
}

private int part16() throws Exception {
	m_suite.viewPage( "Special:Statistics" );
	return 0;
}

private int part17() throws Exception {
	m_suite.viewPage( "Special:Unusedimages" );
	return 0;
}

private int part18() throws Exception {
	m_suite.viewPage( "Special:Wantedpages" );
	return 0;
}

private int part19() throws Exception {
	m_suite.viewPage( "Special:Watchlist" );
	return 0;
}

private int part20() throws Exception {
	m_suite.viewPage( "Special:Whatlinkshere" );
	return 0;
}

public static void main( String[] params ) {
	(new SpecialTest()).runSingle( params );
}

}
