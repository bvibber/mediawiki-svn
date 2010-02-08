

/** Armenian (Հայերեն)
 *
 * @ingroup Language
 * @author Ruben Vardanyan (Me@RubenVardanyan.com)
 */
	
	mw.lang.convertPlural = function( count, forms ) {
		
		forms = mw.lang.preConvertPlural( forms, 2 );

		return (Math.abs(count) <= 1) ? forms[0] : forms[1];
	}
	
