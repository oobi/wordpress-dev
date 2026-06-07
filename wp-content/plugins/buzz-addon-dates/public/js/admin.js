(function( $ ) {
	'use strict';

	$(document).ready(function(){

		// add mapping action button
		$('.postbox').on('click', '.buzz-dates-add', function(event){
			event.preventDefault();
			addNewRow( this );
		});

		$('.postbox').on('click', '.buzz-dates-delete', function(event){
			event.preventDefault();
			deleteRow( this );
		});

		// sortable
		$('.buzz-dates-row-container').sortable({
			handle 		: '.drag-handle',
			placeholder : "buzz-dates-sortable-placeholder",
			connectWith : ".buzz-dates-row-container",
			stop 		: function(event, ui) {
				$('.buzz-dates-row-container').each( function(n, container) {
					remapRowIndex( container );
				});
			}
		});
	});


	/**
	 * Add a new mapping row to the table
	 */
	function addNewRow( element ) {
		// only add new row to current metabox
		var $metabox 	= $(element).closest('.postbox');
		var $container 	= $('.buzz-dates-row-container', $metabox);
		var $template 	= $('[type="text/template"]', $metabox);

		// add a new row
		$container.append ( $template.html() );

		// remap index
		remapRowIndex( $container );
	}

	/**
	 * Remove mapping row
	 */
	function deleteRow( element ) {
		var $metabox 	= $(element).closest('.postbox');
		var $container 	= $('.buzz-dates-row-container', $metabox);

		// remove current row
		$(element).closest('.buzz-dates-row').remove();

		// remap index
		remapRowIndex($container);
	}


	/**
	 * Remap mapping array indicies
	 */
	function remapRowIndex( container ) {
		var setting = $(container).data('setting');

		$('.buzz-dates-row', container).each(function(index, tr) {
			$('input,select,textarea', tr).each( function( n, input ) {
				var name = $(input).data('name');
				// var oldname = $(input).prop('name');
				// var newname = oldname.replace(/\[([0-9]+)\]/, '[' + index + ']');
				var newname = setting + '[' + index + ']' + '[' + name + ']';
				$(input).prop('name', newname);
			});
		});
	}

})( jQuery );
