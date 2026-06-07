(function( $ ) {
	'use strict';

	$(document).ready(function() {

		// Disable sorting of category / article metaboxes
		// This can create a confusing impression of category order
		// and screws the calculation of article order
		$('#normal-sortables').sortable({
			disabled: true
		});

    	$('#normal-sortables .postbox .hndle').css('cursor', 'pointer');


		// Initialise a handler to update categories on sorted articles
		var $list = $('.buzz-article-sortable-list');

		$list.each(function(n, el) {

			// update category parameters and tell WordPress
			$(el).on('sortreceive', function(event, ui) {
				var categoryId = $(el).data('category-id');
				var $item = $(ui.item);

				$('[name=categories]', el).val(categoryId);

				// track down and remove any items from the display with this ID
				// we don't support duplicate categories
				var $duplicateItems = $('.buzz-article-sortable-list [data-id=' + $item.data('id') + ']' ).not($item);
				$duplicateItems.remove();

				// collect item ids in this category
				var $items = $('li', el);
				var articles = [];

				$items.each( function(n, item) {
					articles.push( $(item).data('id') );
				});

				$.post( ajaxurl, {
					action : 'update-article-categories',
					category : categoryId,
					articles  : articles
				});
			});

		});
	});

})( jQuery );