jQuery( function() {

		/* allow keyboard access for catalog sub menu items */
		var catalogMenuLink = jQuery('.menu-item').children('a');

		    catalogMenuLink.on( 'focus', function(){
		        jQuery(this).parents('ul').addClass('focus');
		    });
		    catalogMenuLink.on( 'focusout', function(){
		        jQuery(this).parents('ul').removeClass('focus');
		    });

		// Add class
		jQuery( function() {
			var jQuerymuse = jQuery("#page div");
			var jQuerysld = jQuery("body");

			if (jQuerymuse.hasClass("main-slider")) {
			       jQuerysld.addClass("sld-plus");
			}
		});

		// Main Menu toggle for below 981px screens.
		( function() {
			var togglenav = jQuery( '.main-navigation' ), button, menu;
			if ( ! togglenav ) {
				return;
			}

			button = togglenav.find( '.menu-toggle' );
			if ( ! button ) {
				return;
			}
			
			menu = togglenav.find( '.menu' );
			if ( ! menu || ! menu.children().length ) {
				button.hide();
				return;
			}

			jQuery( '.menu-toggle' ).on( 'click', function() {
				jQuery(this).toggleClass("on");
				togglenav.toggleClass( 'toggled-on' );
			} );
		} )();

		// Top Menu toggle for below 981px screens.
		( function() {
			var togglenav = jQuery( '.top-bar-menu' ), button, menu;
			if ( ! togglenav ) {
				return;
			}

			button = togglenav.find( '.top-menu-toggle' );
			if ( ! button ) {
				return;
			}
			
			menu = togglenav.find( '.top-menu' );
			if ( ! menu || ! menu.children().length ) {
				button.hide();
				return;
			}

			jQuery( '.top-menu-toggle' ).on( 'click', function() {
				jQuery(this).toggleClass("on");
				togglenav.toggleClass( 'toggled-on' );
			} );
		} )();

		// Menu toggle for catalog menu.
		jQuery(document).ready( function() {
		  //when the button is clicked
		  jQuery(".show-menu-toggle").click( function() {
		    //apply toggleable classes
		    jQuery(".catalog-menu-box").toggleClass("show");
		    jQuery(".page-overlay").toggleClass("catalog-menu-open"); 
		    jQuery("#page").addClass("catalog-content-open");  
		  });
		  
		  jQuery(".hide-menu-toggle, .page-overlay").click( function() {
		    jQuery(".catalog-menu-box").removeClass("show");
		    jQuery(".page-overlay").removeClass("catalog-menu-open");
		    jQuery("#page").removeClass("catalog-content-open");
		  });
		});

		// Catalog menu below 768px
		jQuery( function() {
			if(jQuery( window ).width() < 767){
				jQuery(".catalog-menu .menu-item-has-children ul, .catalog-menu .page_item_has_children ul").hide();
				jQuery(".catalog-menu .menu-item-has-children a, .catalog-menu .page_item_has_children a").click(function () {
					jQuery(this).parent(".catalog-menu .menu-item-has-children, .catalog-menu .page_item_has_children").children("ul").slideToggle("100");
				});
			}
		});

		jQuery( function() {
			if(jQuery( window ).width() < 767){
				//responsive sub menu toggle
                jQuery('#site-navigation .menu-item-has-children, #site-navigation .page_item_has_children').prepend('<span class="sub-menu-toggle"> <i class="fa fa-plus"></i> </span>');
				jQuery(".main-navigation .menu-item-has-children ul, .main-navigation .page_item_has_children ul").hide();
				jQuery(".main-navigation .menu-item-has-children > .sub-menu-toggle, .main-navigation .page_item_has_children > .sub-menu-toggle").on('click', function () {
					jQuery(this).parent(".main-navigation .menu-item-has-children, .main-navigation .page_item_has_children").children('ul').first().slideToggle();
					jQuery(this).children('.fa-plus').first().toggleClass('fa-minus');
					
				});
			}
		});

		/* allow keyboard access for shopping cart link */
		var shoppingCartLink = jQuery('.header-right .cart-box .sx-cart-views, .header-right .cart-box .widget_shopping_cart .widget_shopping_cart_content .cart_list .mini_cart_item, .header-right .cart-box .widget_shopping_cart .widget_shopping_cart_content p').children('a');

		    shoppingCartLink.on( 'focus', function(){
		        jQuery(this).parents('.header-right .cart-box').addClass('focus');
		    });
		    shoppingCartLink.on( 'focusout', function(){
		        jQuery(this).parents('.header-right .cart-box').removeClass('focus');
		    });

		// Go to top button.
		jQuery(document).ready(function(){

		// Hide Go to top icon.
		jQuery(".go-to-top").hide();

		  jQuery(window).scroll(function(){

		    var windowScroll = jQuery(window).scrollTop();
		    if(windowScroll > 900)
		    {
		      jQuery('.go-to-top').fadeIn();
		    }
		    else
		    {
		      jQuery('.go-to-top').fadeOut();
		    }
		  });

		  // scroll to Top on click
		  jQuery('.go-to-top').click(function(){
		    jQuery('html,header,body').animate({
		    	scrollTop: 0
			}, 700);
			return false;
		  });

		});

} );