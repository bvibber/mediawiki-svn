
/**
 * Walloon (Walon)
 *
 * @ingroup Language
 */

	/**
	 * Use singular form for zero
	 */
	mw.lang.convertPlural = function( count, forms ) {
		
		forms = mw.lang.preConvertPlural( forms, 2 );

		return (count <= 1) ? forms[0] : forms[1];
	}
