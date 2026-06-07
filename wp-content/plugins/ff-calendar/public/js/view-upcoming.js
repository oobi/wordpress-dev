(function( $ ) {
	'use strict';

	var FC = $.fullCalendar; 	// a reference to FullCalendar's root namespace
	var View = FC.View;    // the class that all views must inherit from
	var Grid = FC.Grid;
	var Scroller = FC.Scroller;
	
	// Upcoming View
	var UpcomingView = View.extend({ // make a subclass of View
		
		grid: null,
		scroller: null,

		initialize: function() {
			this.grid = new UpcomingViewGrid(this);
			this.scroller = new Scroller({
				overflowX: 'hidden',
				overflowY: 'auto'
			});
		},

		setRange: function(range) {
			View.prototype.setRange.call(this, range); // super

			this.grid.setRange(range); // needs to process range-related options
		},

		renderSkeleton: function() {
			this.el.addClass(
				'fc-upcoming-view ' +
				this.widgetContentClass
			);

			this.scroller.render();
			this.scroller.el.appendTo(this.el);

			this.grid.setElement(this.scroller.scrollEl);
		},

		unrenderSkeleton: function() {
			this.scroller.destroy(); // will remove the Grid too
		},

		setHeight: function(totalHeight, isAuto) {
			//this.scroller.setHeight(this.computeScrollerHeight(totalHeight));
			this.scroller.setHeight(totalHeight);
		},
/*
		computeScrollerHeight: function(totalHeight) {
			return totalHeight -
				subtractInnerElHeight(this.el, this.scroller.el); // everything that's NOT the scroller
		},
*/
		renderEvents: function(events) {
			this.grid.renderEvents(events);
		},

		unrenderEvents: function() {
			this.grid.unrenderEvents();
		},

		isEventResizable: function(event) {
			return false;
		},

		isEventDraggable: function(event) {
			return false;
		}

	});

	// Upcoming View Grid
	var UpcomingViewGrid = Grid.extend({

		segSelector: '.fc-list-item', // which elements accept event actions
		hasDayInteractions: false, // no day selection or day clicking

		// slices by day
		spanToSegs: function(span) {
			var view = this.view;
			var dayStart = view.start.clone().time(0); // timed, so segs get times!
			var dayIndex = 0;
			var seg;
			var segs = [];

			while (dayStart < view.end) {

				seg = FC.intersectRanges(span, {
					start: dayStart,
					end: dayStart.clone().add(1, 'day')
				});

				if (seg) {
					seg.dayIndex = dayIndex;
					segs.push(seg);
				}

				dayStart.add(1, 'day');
				dayIndex++;

				// detect when span won't go fully into the next day,
				// and mutate the latest seg to the be the end.
				if (
					seg && !seg.isEnd && span.end.hasTime() &&
					span.end < dayStart.clone().add(this.view.nextDayThreshold)
				) {
					seg.end = span.end.clone();
					seg.isEnd = true;
					break;
				}
			}

			return segs;
		},

		// like "4:00am"
		computeEventTimeFormat: function() {
			return this.view.opt('mediumTimeFormat');
		},

		// for events with a url, the whole <tr> should be clickable,
		// but it's impossible to wrap with an <a> tag. simulate this.
		handleSegClick: function(seg, ev) {
			var url;

			Grid.prototype.handleSegClick.apply(this, arguments); // super. might prevent the default action

			// not clicking on or within an <a> with an href
			if (!$(ev.target).closest('a[href]').length) {
				url = seg.event.url;
				if (url && !ev.isDefaultPrevented()) { // jsEvent not cancelled in handler
					window.location.href = url; // simulate link click
				}
			}
		},

		// returns list of foreground segs that were actually rendered
		renderFgSegs: function(segs) {
			segs = this.renderFgSegEls(segs); // might filter away hidden events

			if (!segs.length) {
				this.renderEmptyMessage();
			}
			else {
				this.renderSegList(segs);
			}

			return segs;
		},

		renderEmptyMessage: function() {
			this.el.html(
				'<div class="fc-list-empty-wrap2">' + // TODO: try less wraps
					'<div class="fc-list-empty-wrap1">' +
						'<div class="fc-list-empty">' +
							FC.htmlEscape(this.view.opt('noEventsMessage')) +
						'</div>' +
					'</div>' +
				'</div>'
			);
		},

		// render the event segments in the view
		renderSegList: function(allSegs) {
			var segsByDay = this.groupSegsByDay(allSegs); // sparse array
			var dayIndex;
			var daySegs;
			var i;
			var tableEl = $('<div class="fc-list-table"></div>');
			//var tbodyEl = tableEl;

			for (dayIndex = 0; dayIndex < segsByDay.length; dayIndex++) {
				daySegs = segsByDay[dayIndex];
				if (daySegs) { // sparse array, so might be undefined

					// append a row 
					tableEl.append('<div class="fc-upcoming-row fc-upcoming-row-' + dayIndex + '"></div>');
					var $tableRow = $('.fc-upcoming-row-' + dayIndex, tableEl);

					// append a day header
					$tableRow.append(this.dayHeaderHtml(
						this.view.start.clone().add(dayIndex, 'days')
					));

					this.sortEventSegs(daySegs);

					// loop events and append
					$tableRow.append('<div class="fc-upcoming-col fc-upcoming-col-' + dayIndex + '"></div>');
					for (i = 0; i < daySegs.length; i++) {
						$('.fc-upcoming-col-' + dayIndex, tableEl).append(daySegs[i].el); // append event row
					}
				}
			}

			this.el.empty().append(tableEl);
		},

		// Returns a sparse array of arrays, segs grouped by their dayIndex
		groupSegsByDay: function(segs) {
			var segsByDay = []; // sparse array
			var i, seg;

			for (i = 0; i < segs.length; i++) {
				seg = segs[i];
				(segsByDay[seg.dayIndex] || (segsByDay[seg.dayIndex] = []))
					.push(seg);
			}

			return segsByDay;
		},

		// generates the HTML for the day headers that live amongst the event rows
		dayHeaderHtml: function(dayDate) {
			var view = this.view;
			var mainFormat = view.opt('listDayFormat');
			var altFormat = view.opt('listDayAltFormat');

			// create date element
			var dateHTML = '<div class="fc-upcoming-date">';
				dateHTML += '<div class="fc-upcoming-day">' + dayDate.format('D') + '</div>';
				dateHTML += '<div class="fc-upcoming-month">' + dayDate.format('MMM') + '</div>';
				//dateHTML += '<div class="fc-upcoming-year">' + dayDate.format('YYYY') + '</div>';
			dateHTML += '</div>';

			return '<div class="fc-list-heading" data-date="' + dayDate.format('YYYY-MM-DD') + '">' +
				'<div class="' + view.widgetHeaderClass + '">' +
					(mainFormat ?
						view.buildGotoAnchorHtml(
							dayDate,
							{ 'class': 'fc-list-heading-main' },
							dateHTML // inner HTML
						) :
						'') +
					(altFormat ?
						view.buildGotoAnchorHtml(
							dayDate,
							{ 'class': 'fc-list-heading-alt' },
							dateHTML // inner HTML
						) :
						'') +
				'</div>' +
			'</div>';
		},

		// generates the HTML for a single event row
		fgSegHtml: function(seg) {
			var view = this.view;
			var classes = [ 'fc-list-item' ].concat(this.getSegCustomClasses(seg));
			var event = seg.event;
			var url = event.url;
			var timeHtml;

			if (event.allDay) {
				timeHtml = view.getAllDayHtml();
			}
			else if (view.isMultiDayEvent(event)) { // if the event appears to span more than one day
				if (seg.isStart || seg.isEnd) { // outer segment that probably lasts part of the day
					timeHtml = FC.htmlEscape(this.getEventTimeText(seg));
				}
				else { // inner segment that lasts the whole day
					timeHtml = view.getAllDayHtml();
				}
			}
			else {
				// Display the normal time text for the *event's* times
				timeHtml = FC.htmlEscape(this.getEventTimeText(event));
			}

			if (url) {
				classes.push('fc-has-url');
			}

			return '<div class="' + classes.join(' ') + '">' +
				'<div class="fc-list-item-title ' + view.widgetContentClass + '">' +
					'<a' + (url ? ' href="' + FC.htmlEscape(url) + '"' : '') + '>' +
						FC.htmlEscape(seg.event.title || '') +
					'</a>' +
				'</div>' +
				(this.displayEventTime ?
					'<div class="fc-list-item-time ' + view.widgetContentClass + '">' +
						(timeHtml || '') +
					'</div>' :
					'') +
			'</div>';
		}

	});

	// register class with the view system
	FC.views.upcoming = {
		'class': UpcomingView,
		buttonTextKey: 'upcoming', 	// what to lookup in locale files
		duration: { year: 2 },		// shows upcoming events for the rest of the year
		defaults: {
			buttonText: 'upcoming', // text to display for English
			listDayFormat: 'LL', 	// x = timestamp (for formatting again later)
			noEventsMessage: 'No events to display'
		}
	}; 

})( jQuery );