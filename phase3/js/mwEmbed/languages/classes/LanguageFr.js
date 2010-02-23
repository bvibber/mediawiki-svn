

/** French (Français)
 *
 * @ingroup Language
 */
	/**
	 * Use singular form for zero (see bug 7309)
	 */
	mw.lang.convertPlural = function( count, forms ) {
		
		forms = mw.lang.preConvertPlural( forms, 2 );

		return (count <= 1) ? forms[0] : forms[1];
	}
