/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	var $style = $( '#shoppingcart-color-scheme-css' ),
		api = wp.customize;

	if ( ! $style.length ) { 
		$style = $( 'head' ).append( '<style type="text/css" id="shoppingcart-color-scheme-css" />' )
		                    .find( '#shoppingcart-color-scheme-css' );
	}

	// Site title.
	api( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	// Site tagline.
	api( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

} )( jQuery );