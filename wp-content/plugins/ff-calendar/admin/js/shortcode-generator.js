(function( $ ) {
	'use strict';

	$(document).ready(function(){

		/********************************************************
		 * FF CALENDAR SHORTCODE GENERATOR
		 ********************************************************/

		// shortcode config
		// allows shortcode interpreter to differentiate versions. Bump constant whenever shortcode format changes.
		var version 			= $('.current-version', '#ff-cal-shortcode-version').text();

		// get elements and data
		var $form 				= $('FORM[name="ff-calendar-shortcode-generate"]');
		var $output 			= $('#shortcode-output');
		var $selectView 		= $('SELECT[name="view"]', $form);
		var $selectIds 			= $('.feed-checkbox', $form);
		var checkedFeeds		= $selectIds.map(mapFeedIds).get();
		var $selectHeight 		= $('.height', $form);
		var $selectSnap 		= $('.snap', $form);
		var $selectWeekStart	= $('SELECT[name="weekstart"]', $form);
		var $selectNoControls	= $('.nocontrols-checkbox', $form);
		var $selectNoWeekends	= $('.noweekends-checkbox', $form);

		// collect params from form fields
		var shortcodeParams	= {
			ids 		: checkedFeeds.join(','),
			view		: $selectView.val(),
			height		: $selectHeight.val(),
			snap		: $selectSnap.val(),
			weekstart	: $selectWeekStart.val(),
			noweekends	: $selectNoControls.prop('checked'),
			nocontrols	: $selectNoWeekends.prop('checked')
		};

		// init the default output
		updateShortcode(shortcodeParams);

		// set up on change events
		$selectIds.on('change', function() {
			checkedFeeds = $('.feed-checkbox:checked', $form).map(mapFeedIds).get();
			shortcodeParams['ids'] = checkedFeeds.join(',');
			updateShortcode(shortcodeParams);
		});

		$selectView.on('change', function(){
			shortcodeParams['view'] = $selectView.val();
			updateShortcode(shortcodeParams);
		});

		$selectHeight.on('change keyup paste', function(){
			shortcodeParams['height'] = $selectHeight.val();
			updateShortcode(shortcodeParams);
		});

		$selectSnap.on('change keyup paste', function(){
			shortcodeParams['snap'] = $selectSnap.val();
			updateShortcode(shortcodeParams);
		});

		$selectWeekStart.on('change', function(){
			shortcodeParams['weekstart'] = $selectWeekStart.val();
			updateShortcode(shortcodeParams);
		});

		$selectNoWeekends.on('change', function(){
			shortcodeParams['noweekends'] = $selectNoWeekends.prop('checked');
			updateShortcode(shortcodeParams);
		});

		$selectNoControls.on('change', function(){
			shortcodeParams['nocontrols'] = $selectNoControls.prop('checked');
			updateShortcode(shortcodeParams);
		});

		// map all the feed iDs to format
		function mapFeedIds() {
			return this.value;
		}

		// update the shortcode and display
		function updateShortcode(params) {
			var paramString = '';

			// loop params and append valid ones to string
			for(var key in params) {
				if(params.hasOwnProperty(key)) {
					if(params[key]) {
						paramString += key + '="' + params[key] + '" '; // leave trailing space
					}
				}
			}

			// output shortcode to box
			$output.val( '[ff-calendar ' + version + ' ' + paramString.trim() + ']' );
		}

	});

})( jQuery );
