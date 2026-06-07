(function( $ ) {
	'use strict';

	var $modal;

	/*TODO:FIXME:
	# Fix it so that cookies are stored PER CALENDAR INSTANCE not globally
	# Cookies are retrieved for EVERY RENDER EVENT which is hugely inefficient.
	*/

	$(document).ready(function(){

		/**************************************************************
		 * FF CALENDAR
		 * FullCalendar Documentation: https://fullcalendar.io/docs/
		 **************************************************************/

		// set up selectors
		var $calendars = $('.ff-calendar-wrapper');



		$calendars.each(function(n, el){
			initCalendar(el);
		}); // end ff calendar (foreach)

		// init the modal
		$modal = initModal();

	}); // end document.ready

	/**
	 * Initialise a single calendar intance
	 * @param {*} el 	- element wrapper for calendar
	 */
	function initCalendar(el) {
		var $ffCalendar 			= $('.ff-calendar', el);
		var $ffCalendarLoader 		= $('.ff-calendar-loading', el);

		// show loader
		$ffCalendarLoader.show();

		// get shortcode parameters
		var shortcodeIds			= $ffCalendar.data('ids');	// feed IDs to include
		var config = {
			'version' 		: $ffCalendar.data('version'),
			'ids' 			: shortcodeIds.split(','),
			'feeds'			: [], // populated below
			'categories'	: [], // populated below
			'view'			: $ffCalendar.data('view'),
			'height'		: $ffCalendar.data('height'),
			'snap'			: $ffCalendar.data('snap'),
			'weekStart'		: $ffCalendar.data('weekstart'),
			'noWeekends'	: $ffCalendar.data('noweekends'),
			'noControls'	: $ffCalendar.data('nocontrols'),
			'limitNum'		: $ffCalendar.data('limit-num'),
			'limitType'		: $ffCalendar.data('limit-type')
		};

		// defaults
		var minDesktopWidth 		= config.snap ? config.snap : 992;
		var mobileView 				= config.view == 'month' ? 'listMonth' : config.view; // only force a different mobile view if 'month'
		var desktopView 			= config.view;
		var headerControls 			= { left: 'title',
										center: '',
										right: 'prev,today,next eventCategories month,listMonth' };

		// create/sanitize the limit data object
		var limitData = {
			type 	: config.limitType 	? config.limitType 	: false,
			num		: config.limitNum 	? config.limitNum 	: false
		};


		// all event data relevant to this calendar instance
		var allEvents = [];
		var allCategories = [];

		// construct our data URLs
		var dataURL = FF_CALENDAR.events_route + shortcodeIds + '/';

		// apply limit params if defined
		if(limitData.type && limitData.num) {
			var typeString = 'invalid';
			switch( limitData.type ) {
				case 'event' :
					typeString = FF_CALENDAR.limit_events;
					break;
				case 'day' :
					typeString = FF_CALENDAR.limit_days;
					break;
			}
			dataURL += typeString + limitData.num + '/';
		}

		var categoryURL = FF_CALENDAR.categories_route + shortcodeIds + '/';

		// grab ALL event data here so we can use it to setup multiple calendars if required
		// without the need to load the JSON more than once
		var p1 = $.getJSON(dataURL)
			.done(function( data ){
				allEvents = data;

				// FIXME: limit to 50 items for performance (WIDGET VIEW)
				// TODO: look at speed here - the render of the 'upcoming' view is abysmal
				if( config.view == 'upcoming' ) {
					allEvents.splice( 50, allEvents.length );
				}

			})
			.always(function(){
				$ffCalendarLoader.hide();
			});

		// Grab category data
		var p2 = $.getJSON( categoryURL )
			.done( function( data ) {
				// push the categories onto the config as well
				allCategories = data;
				config.categories = allCategories;
			});


		// complete the setup
		$.when.apply($, [p1,p2]).done(function() {

			// add data necessary for callbacks to $ffCalendar to make trucking this data around easier
			$ffCalendar.data({
				$loader 		: $ffCalendarLoader,
				minDesktopWidth : minDesktopWidth,
				mobileView		: mobileView,
				desktopView		: desktopView,
				categories		: config.categories
			});

			// calendar configuration settings
			var calendarConfig = {
				events 			: allEvents,																// all event data
				views 			: {																			// add custom views
					upcoming : {
						type: 'UpcomingView',
						buttonText: 'upcoming'
					}
				},
				header			: config.noControls ? false : headerControls, 								// set controls on/off
				customButtons	: config.categories.length > 1 ? customButtonSetup($ffCalendar) : false, 	// if more than one category, show categories button
				contentHeight	: config.height ? config.height : false, 									// set calendar height
				firstDay		: config.weekStart ? config.weekStart : 0, 									// set first day of week
				weekends		: config.noWeekends ? false : true, 										// show weekends in render
				eventLimit		: true, 																	// truncates events if too many on a single day
				timezone 		: 'local',																	// timezone
				defaultView		: $(window).width() < minDesktopWidth ? mobileView : desktopView,			// set intial view depending on window width
				eventClick		: function(event){															// init the event click event
									eventDetailsOpen(event, $ffCalendar)
								},
				windowResize	: function(view) {															// init the window resize event
									windowResized(view, $ffCalendar);
								}
			};

			// if we are limiting events then don't use the category limitier
			if(!(limitData.type && limitData.num)) {
				calendarConfig.eventRender 	= function( event, element, view ){
					return categoriesCallback( event, element, view, el)
				};
			}

			// finally, create hte FullCalendar instance with our new config
			$ffCalendar.fullCalendar(calendarConfig);
		});

	}


	function createFullCalendar( $el, calendarConfig ) {

	}

	// init the modal wrapper
	function initModal() {
		var $m = $('<div class="ff-calendar-modal"><div class="ff-calendar-modal-inner ff-calendar-modal--transition"><div class="ff-calendar-modal-html"></div><div class="ff-calendar-modal-close"><i class="fa fa-close"></i></div></div></div>');

		$('.ff-calendar-modal-close', $m).on('click', function(){
			hideModal();
		});

		$m.on('click', function(e){
			e.preventDefault();
			hideModal();
		});

		$('.ff-calendar-modal-inner', $m).on('click', function(e){
			e.stopPropagation();
		});

		// stick this as last item in body
		$('body').append($m);

		return $m;
	}

	/********************************************************
	 * CALLBACK - CATEGORIES
	 ********************************************************/

	/**
	 * This callback is called for every event as it is rendered
	 * Used to check whether or not to display an event depending on which the currently selected categories
	 */
	function categoriesCallback(event, element, view, calendarElement) {
		var $calendarCats 		= $('.ff-calendar-modal #calendar_categories', calendarElement);
		var activeCats			= getCategoryCookie();
		var render 				= true;

		// if no cookie set, show all events
		if(activeCats !== undefined) {
			// if event has category that is not an active category, do not show it
			$.each(event.categories, function(index, value) {
				if($.inArray(value.label, activeCats) < 0) {
					render = false;
					return false; // breaks out of loop (does not return function)
				}
			});
		}

		return render;
	}

	// return the custom buttons
	function customButtonSetup($ffCalendar) {
		// return the custom button object
		return {
			eventCategories: {
				text: 'Categories',
				click: function() {
					categoriesOpen($ffCalendar);
				}
			}
		};
	}

	// init the categories modal
    function categoriesOpen($ffCalendar) {
        var template 		= $('#ff-tpl-calendar-categories').html();
        var $categories 	= $('<div/>').html( template );
        var $form 			= $('FORM', $categories);
        var categories		= $ffCalendar.data('categories');

		// clear the form contents
		$form.empty();

		// add a toggle checkboxes button
		$form.append('<p><button id="ff-cal-toggle-categories" type="checkbox" name="toggle">Check/Uncheck All</button></p>');

		// get the active categories from cookie
		var cookie = getCategoryCookie();

		// loop categories and append checkboxes
		$.each(categories, function(index, value) {
			var isActive 	= false;

			if(cookie == undefined || $.inArray(value.label, cookie) >= 0) {
				isActive = true;
			}

			var checked = isActive ? 'checked="checked"' : '';

			var wrapperID 	= 'ff-cal-wrapper-' + index;
			var inputID		= 'ff-cal-feed-' + index;
			var input 		= '<input id="' + inputID + '" type="checkbox" name="category" ' + checked + ' value="' + value.label + '"/>';
			var label 		= '<label for="' + inputID + '"class="' + value.className + '">' + value.label + '</label>';

			var $wrapper = $('<div id="' + wrapperID + '" class="ff-category-wrapper"></div>');
			$wrapper.append(input);
			$wrapper.append(label);
			$form.append($wrapper);
		});

        // add html to modal
        showModal( $categories.html() );

		// Toggle all categories and save to cookie
        $('#ff-cal-toggle-categories', $modal).click(function(e){
			e.preventDefault();

			// toggle checkboxes
			var $calendarCats = $('#calendar_categories', '.ff-calendar-modal');
			var checkboxes = $calendarCats.find(':checkbox');
			checkboxes.prop('checked', !checkboxes.prop('checked'));

			// get active categories
			var activeCats			= $('input[name="category"]:checked', $calendarCats).map(function(){
											return this.value;
										}).get();

			// save cookie
			setCategoryCookie(activeCats, categories);

			// re-render the events
			$ffCalendar.fullCalendar('rerenderEvents');
        });

        // re-render the events on checkbox change
        $('INPUT[name="category"]', $modal).change(function(e){
			var $calendarCats 		= $('#calendar_categories', '.ff-calendar-modal');
			var activeCats			= $('input[name="category"]:checked', $calendarCats).map(function(){
											return this.value;
										}).get();

			// save cookie
			setCategoryCookie(activeCats, categories);

			// re-render the events
			$ffCalendar.fullCalendar('rerenderEvents');
        });
	}

	/********************************************************
	 * CALLBACK - EVENTS
	 ********************************************************/

	// fill and load the event details when the event is clicked
	function eventDetailsOpen(event, $ffCalendar) {
		var template = $('#ff-tpl-calendar-details').html();
		var $details = $('<div/>').html( template );

		// add the feed className to the modal
		// $('.ff-calendar-details', $details).addClass(event.source.className[0]);

		// fill event details
		$('.title', $details).html(event.title);
		if(event.location.length) {
			$('.location', $details).show();
			$('.location SPAN', $details).html(event.location);
		} else {
			$('.location', $details).hide();
		}
		$('.description', $details).html(event.description);

		// concat categories and fill
		var cats = $.map(event.categories, function(value, index) {
			return value.label;
		});
		// console.log(cats);
		$('.categories', $details).html(cats);

		// format date and add segment if available
		var date = '';

		// event start and end are the same day
		// OR there is no end date specified
		if(event.end == undefined || event.start.isSame(event.end, 'day')) {
			date += event.start.format('DD MMMM YYYY');
		}
		// if it's all day...
		else if(event.allDay) {
			// subtract 1s from end date and render start/end
			// this is because start and end happens at midnight
			var diff = event.end.diff(event.start, 'days') - 1;

			// do not set the event.end value as this function can be called multiple times
			// set a temp variable instead
			var new_end = event.start.clone().add(diff, 'days');

			// if the difference is 24hr or less, only show one date
			if( diff <= 1 ) {
				date += event.start.format('DD MMMM YYYY');
			}
			// otherwise show the range
			else {
				date += event.start.format('DD MMMM YYYY') + ' - ' + new_end.format('DD MMMM YYYY');
			}
		}
		// otherwise show both start and end dates
		else {
			date += event.start.format('DD MMMM YYYY') + ' - ' + event.end.format('DD MMMM YYYY');
		}

		// if an event segment is available add it too
		if(event.segment) {
			date += ' (' + event.segment + ')';
		}
		$('.date SPAN',	$details).html(date);

		// format time
		var time = '';
		if(event.allDay) {
			time += '<span class="all-day">All Day</span>';
		} else {
			time += event.start.format('h:mm A');
			if(event.end) {
				time += ' - ' + event.end.format('h:mm A');
			}
		}
		$('.time SPAN',	$details).html(time);

		// show detail modal
		showModal( $details.html() );
	}

	/********************************************************
	 * MODALS
	 ********************************************************/

	// fill and show the modal
	function showModal(html) {
		$('.ff-calendar-modal-html', $modal).html(html);
		$modal.addClass('ff-calendar-modal--show');
	}

	// hide and empty the modal
	function hideModal() {
		$modal.removeClass('ff-calendar-modal--show');
		$('.ff-calendar-modal-html', $modal).empty();
	}

	/********************************************************
	 * UTILITIES
	 ********************************************************/

	// change the view depending on window width
	function windowResized(view, $ffCalendar) {
		var mobileView 		= $ffCalendar.data('mobileView');
		var desktopView 	= $ffCalendar.data('desktopView');
		var minDesktopWidth = $ffCalendar.data('minDesktopWidth');

		if ($(window).width() < minDesktopWidth) {
			$ffCalendar.fullCalendar( 'changeView', mobileView );
		} else {
			$ffCalendar.fullCalendar( 'changeView', desktopView );
		}
	}

	/**
	 * Array sort function - sort events by start date
	 * @param {object} event1
	 * @param {object} event2
	 */
	function isBefore(event1, event2) {
		var d1 = Date.parse(event1.start);
		var d2 = Date.parse(event2.start);
		return d1 > d2;
	}

	/**
	 * Format a string into a CSS-compatible class name
	 * @param {String} className
	 * @param {String} prefix 			Custom prefix (defaults to 'ff-cal-')
	 * @return {String}
	 */
	function formatClassName(className, prefix) {
		// default the prefix (can't default the argument or we lose compatibility in IE11)
		prefix = typeof prefix !== 'undefined' ? b : 'ff-cal-';
		return prefix + className.toLowerCase().replace(/[^a-zA-Z0-9]+/g, '-');
	}

	/**
	 * Set the categories cookie
	 * @param {Array} activeCats 		The active categories
	 * @param {Array} allCats 			All categories
	 */
	function setCategoryCookie(activeCats, allCats) {
		// save the cookie
		Cookies.set('ff_calendar_categories', activeCats);

		// set class of categories button if selections have been made
		if(activeCats.length < allCats.length) {
			$('.fc-eventCategories-button').addClass('fc-state-active');
		} else {
			$('.fc-eventCategories-button').removeClass('fc-state-active');
		}
	}

	/**
	 * Get the categories cookie
	 * @return 	{Any} 	The cookie
	 */
	function getCategoryCookie() {
		return Cookies.getJSON('ff_calendar_categories');
	}

/*
TODO: 	init the listMonth view to the current day when viewed.
		Should not run while on any other view and should re-run whenever the view is changed to listMonth view
	function scrollToToday() {
		//var date = '2016-11-11';
		var today = moment().format('YYYY-MM-DD');
		var $scroll = $('[data-date="' + today + '"]').position();
		$('.fc-scroller', '.fc-listMonth-view').scrollTop($scroll.top);
	}
*/

})( jQuery );
