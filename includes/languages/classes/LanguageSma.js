
/**
 *
 * @ingroup Language
 */
	mw.lang.convertPlural = function( count, forms ) {
		

		// plural forms per http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html#sma
		forms = mw.lang.preConvertPlural( forms, 3 );

		if ( count == 1 ) {
			$index = 1;
		} else if( count == 2 ) {
			$index = 2;
		} else {
			$index = 3;
		}
		return forms[$index];
	}
