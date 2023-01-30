( function( api ) {

	// Extends our custom "shoppingcart" section.
	api.sectionConstructor['shoppingcart'] = api.Section.extend( {

		// No shoppingcarts for this type of section.
		attachShoppingCarts: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );
