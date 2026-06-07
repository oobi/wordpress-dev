(function( $ ) {
	'use strict';

	$(document).ready(function(){

		/********************************************************
		 * FF CALENDAR OPTIONS PAGE
		 ********************************************************/

		// add mapping action button
		$('.ff-calendar-add').on('click', function(event){
			event.preventDefault();
			addNewFeed();
		});

		$('.ff-calendar-feeds').on('click', '.ff-calendar-delete', function(event){
			event.preventDefault();
			deleteFeed( this );
		});

		// open help menu
		$('.ff-calendar-feeds').on('click', '.ff-calendar-help', function(event){
			event.preventDefault();
			openHelpMenu();
		});

		/**
		 * Add a new feed row to the table
		 */
		function addNewFeed( id, name, url, categoryKey ) {
			var $tbody = $('.ff-calendar-feeds > TBODY');

			if( id == null ) id = '';
			if( name == null ) name = '';
			if( url == null ) url = '';
			if( categoryKey == null ) categoryKey = '';

			// create a new row
			var $tr    = $('<tr/>');
			$tr.append('<td><input type="text" class="large-text ff-calendar-id" name="" placeholder="ID generated on save" readonly value="' + id + '"></td>');
			$tr.append('<td><input type="text" class="large-text ff-calendar-name" name="" placeholder="Name" required value="' + name + '"></td>');
			$tr.append('<td><input type="text" class="large-text ff-calendar-url" name="" placeholder="eg. http://www.example.com/calendar.ics" required value="' + url + '"></td>');
			$tr.append('<td><input type="text" class="large-text ff-calendar-category-key" name="" placeholder="eg. X-CATEGORY" value="' + categoryKey + '"></td>');
			$tr.append('<td><a class="ff-calendar-delete dashicons dashicons-trash" href="#" ></a></td>');

			// append to the table
			$tbody.append($tr);

			// remap index
			remapFeedIndex();
		}

		/**
		 * Remove mapping row
		 */
		function deleteFeed( element ) {
			// remove current row
			$(element).closest('tr').remove();

			remapFeedIndex();
		}

		/**
		 * Remap mapping array indicies
		 */
		function remapFeedIndex() {
			$('.ff-calendar-feeds > TBODY > TR').removeClass('alternate');

			$('.ff-calendar-feeds > TBODY > TR').each(function(n, tr){
				if(n % 2 == 1) $(tr).addClass('alternate');
				$('.ff-calendar-id', tr).prop('name', 'ff_calendar_settings[calendar_feeds][' + n + '][id]');
				$('.ff-calendar-name', tr).prop('name', 'ff_calendar_settings[calendar_feeds][' + n + '][name]');
				$('.ff-calendar-url', tr).prop('name', 'ff_calendar_settings[calendar_feeds][' + n + '][url]');
				$('.ff-calendar-category-key', tr).prop('name', 'ff_calendar_settings[calendar_feeds][' + n + '][category-key]');
			});
		}

		/**
		 * Open the help menu
		 */
		function openHelpMenu() {
			// open menu
			$('#contextual-help-link').addClass('screen-meta-active').attr('aria-expanded','true');
			$('#screen-meta,#contextual-help-wrap').show();

			// set active tab
			var $tab = $('#tab-link-ff-cal-feeds,#tab-panel-ff-cal-feeds');
			$tab.siblings().removeClass('active');
			$tab.addClass('active');
		}

	});

})( jQuery );
