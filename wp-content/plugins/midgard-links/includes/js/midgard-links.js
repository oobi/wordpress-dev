(function( $ ) {
	'use strict';

	/**
	 * "add new link" functionality
	 */
	$(document).ready(function($){
		var $type = $("#midgard-feed-type");

		// add links action button
		$('.midgard-links-add').on('click', function(event){
			event.preventDefault();
			addNewLink();
		});

		$('.midgard-links-table').on('click', '.midgard-links-delete', function(event){
			event.preventDefault();
			deleteLink( this );
		});

	});

	//////////////////////////////////////////////////////
	// LINKS
	//////////////////////////////////////////////////////

	/**
	 * Remove link row
	 */
	function deleteLink( element ) {
		// remove current row
		var $tr1 = $(element).closest('tr');
		var $tr2 = $tr1.next();
		var $tr3 = $tr2.next();

		$tr1.remove();
		$tr2.remove();
		$tr3.remove();

		remapLinksIndex();
	}

	/**
	 * Add a new links row to the table
	 */
	function addNewLink(url, title, desc, external) {
		var $tbody = $('.midgard-links-table > TBODY');

		if( url == null ) 	url = '';
		if( title == null ) title = '';
		if( desc == null ) desc = '';
		if( external == null ) external = false;

		// create a new row
		var $tr1    = $('<tr class="r1"/>');
		$tr1.append('<td><input class="large-text midgard-links-title" type="text" name="" placeholder="Page Title" value="' + title  + '"/></td>');
		$tr1.append('<td><input class="large-text midgard-links-url" type="text" name="" placeholder="Page URL" value="' + url  + '"/></td>');
		$tr1.append('<td><a class="midgard-links-delete dashicons dashicons-trash" href="#" ></a></td>');

		var $tr2    = $('<tr class="r2"/>');
		$tr2.append('<td colspan="2"><textarea class="large-text midgard-links-desc" name="" placeholder="Description">' + desc + '</textarea></td>');
		$tr2.append('<td>&nbsp;</td>');

		var $tr3    = $('<tr class="r3"/>');
		var checked = external ? 'checked' : '';
		$tr3.append('<td colspan="3"><input type="checkbox" id="" class="midgard-links-external" name="" value="1" ' + checked + '><label for="">Open link in external browser?</label></td>');

		// append to the table
		$tbody.append($tr1);
		$tbody.append($tr2);
		$tbody.append($tr3);

		// remap index
		remapLinksIndex();
	}

	/**
	 * Remap links array indicies
	 */
	function remapLinksIndex() {
		$('.midgard-links-table > TBODY > TR').removeClass('alternate');

		$('.midgard-links-table > TBODY > TR.r1').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.midgard-links-url', tr).prop('name', 'midgard-links[' + n + '][url]');
			$('.midgard-links-title', tr).prop('name', 'midgard-links[' + n + '][title]');
		});

		$('.midgard-links-table > TBODY > TR.r2').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.midgard-links-desc', tr).prop('name', 'midgard-links[' + n + '][desc]');
		});

		$('.midgard-links-table > TBODY > TR.r3').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.midgard-links-external', tr).prop('name', 'midgard-links[' + n + '][external]');
			$('.midgard-links-external', tr).prop('id', 'midgard-links[' + n + '][external]');
			$('.midgard-links-external', tr).next('LABEL').prop('for', 'midgard-links[' + n + '][external]');
		});
	}

})( jQuery );