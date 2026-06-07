(function( $ ) {
	'use strict';

	/**
	 * "add new link" functionality
	 */
	$(document).ready(function($){
		var $type = $("#midgard-feed-type");

		// add feed2 action button
		$('.midgard-feed2-add').on('click', function(event){
			event.preventDefault();
			addNewFeed();
		});

		$('.midgard-feed2-table').on('click', '.midgard-feed2-delete', function(event){
			event.preventDefault();
			deleteFeed( this );
		});

	});

	//////////////////////////////////////////////////////
	// FEEDS
	//////////////////////////////////////////////////////

	/**
	 * Remove feed row
	 */
	function deleteFeed( element ) {
		// remove current row
		var $tr1 = $(element).closest('tr');
		var $tr2 = $tr1.next();

		$tr1.remove();
		$tr2.remove();

		remapFeed2Index();
	}

	/**
	 * Add a new feed row to the table
	 */
	function addNewFeed(url, key, root) {
		var $tbody = $('.midgard-feed2-table > TBODY');

		if( url == null ) 	 url = '';
		if( key == null )    key = '';
		if( root == null )   root = '';

		// create a new row
		var $tr1    = $('<tr/>');
		$tr1.append('<td><input required class="large-text midgard-feed2-key" type="text" name="" placeholder="Key" value="' + key  + '"/></td>');
		$tr1.append('<td><input required class="large-text midgard-feed2-url" type="text" name="" placeholder="Feed URL" value="' + url  + '"/></td>');
		$tr1.append('<td><input class="large-text midgard-feed2-root" type="text" name="" placeholder="JSONPath Expression" value="' + root  + '"/></td>');
		$tr1.append('<td><a class="midgard-feed2-delete dashicons dashicons-trash" href="#" ></a></td>');

		// append to the table
		$tbody.append($tr1);

		// remap index
		remapFeed2Index();
	}

	/**
	 * Remap feed2 array indicies
	 */
	function remapFeed2Index() {
		$('.midgard-feed2-table > TBODY > TR').removeClass('alternate');

		$('.midgard-feed2-table > TBODY > TR').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.midgard-feed2-url', tr).prop('name', 'midgard-feed2[' + n + '][url]');
			$('.midgard-feed2-key', tr).prop('name', 'midgard-feed2[' + n + '][key]');
			$('.midgard-feed2-root', tr).prop('name', 'midgard-feed2[' + n + '][root]');
		});
	}

})( jQuery );