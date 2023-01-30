/*
 * Sticky menu Settings
 */

jQuery(document).ready(function(){
   var wpAdminBar = jQuery('#wpadminbar');
   if (wpAdminBar.length) {
      jQuery('#sticky-header').sticky({topSpacing:wpAdminBar.height(), zIndex:'999'});
   } else {
      jQuery('#sticky-header').sticky({topSpacing:0, zIndex:'999'});
   }
});