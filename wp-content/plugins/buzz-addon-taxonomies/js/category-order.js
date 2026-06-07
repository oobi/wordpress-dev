(function( $ ) {
	'use strict';

	$(document).ready(function() {

		// Initialise a handler to update categories on sorted articles
		var $list = $( '#the-list' );

		// change cursor
		$('.ui-sortable-handle', $list).css('cursor', 'move');

		// if the tax table contains items
		if( ! $list.find( 'tr:first-child' ).hasClass( 'no-items' ) ) {

			$list.sortable({
				placeholder: "buzz-order-category-placeholder",
				axis: "y",
				// on start set a height for the placeholder to prevent table jumps
				start: function(event, ui) {
					var height = $( ui.item[0] ).css( 'height' );
					$( '.buzz-order-category-placeholder' ).css( 'height', height );
				},
				// update callback
				update: function( event, ui ) {
					// hide checkbox, append a preloader
					$( ui.item[0] ).find( 'input[type="checkbox"]' ).hide().after( '<img src="' + buzz_taxonomies.preloader_url + '" class="buzz-order-category-preloader" />' );

					// empty array
					var updated_array = [];

					// store the updated tax ID
					$list.find( 'tr.ui-sortable-handle' ).each( function() {
						var tax_id = $( this ).attr( 'id' ).replace( 'tag-', '' );
						updated_array.push( [ tax_id, $( this ).index() ] );
					});

					// build the ajax data
					var data = {
						'action': 'update_taxonomy_order',
						'updated_array': updated_array
					};

					// Run the ajax request
					$.post( buzz_taxonomies.ajax_url, data, function( response ) {
						$( '.buzz-order-category-preloader' ).remove();
						$( ui.item[0] ).find( 'input[type="checkbox"]' ).show();
					});
				}
			});
		}

	});

})( jQuery );