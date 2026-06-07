(function( $ ) {
	'use strict';

	$(document).ready(function(){

		$('.subject-tags .tag').click(function() {
			var targetSelector = $(this).data('target');
			var value = $(this).data('value');
			var $target = $(targetSelector);

			// append value to field
			if( $target.length ) {
				$target.val( ($target.val() + ' ' + value).trim() );
			}
		});

	});


})( jQuery );
