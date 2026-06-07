;(function($){
	var container = '.ff-docraptor';
	var interval = 0;

	$(document).ready(function(){
		$('select', container).on('change', function() {
			onParamChange();
		});

		$('.ff-docraptor-btn', container).on('click', function(event) {
			event.preventDefault();
			startGenerator();
		});

		onParamChange();
	});

	function onParamChange() {
		var month = $('[name=ff-docraptor-month]', '.ff-docraptor-params').val();
		var mode = $('[name=ff-docraptor-mode]', '.ff-docraptor-params').val();

		if( month != '' && mode != '') {
			enableGenerateButton( true );
		} else {
			enableGenerateButton( false );
		}
	}

	function enableGenerateButton( isEnabled ) {
		if( isEnabled ) {
			$('.ff-docraptor-btn', container).removeAttr('disabled');
		} else {
			$('.ff-docraptor-btn', container).attr('disabled', 'disabled');
		}
	}

	function updateStatus( message, replace) {
		if( !replace ) {
			$('.ff-docraptor-status').append('<div class="ff-docraptor-message">' + message + '</div>');
		} else {
			$('.ff-docraptor-status').html('<div class="ff-docraptor-message">' + message + '</div>');
		}
	}

	function showSpinner(isEnabled) {
		$('.ff-docraptor-status', container).append('<div class="ff-docraptor-working"><div class="spinner is-active"></div> Working.</div>');
	}

	function hideSpinner() {
		$('.ff-docraptor-working', container).remove();
	}

	function startGenerator() {
		enableGenerateButton(false);
		$('select', container).attr('disabled', 'disabled');
		updateStatus('Generating PDF - please wait', true);

		showSpinner();

		var data = {
			action : 'docraptor_start',
			month : $('[name=ff-docraptor-month]', '.ff-docraptor-params').val(),
			mode : $('[name=ff-docraptor-mode]', '.ff-docraptor-params').val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response.status ) {
				startStatusPoll(response.status);
			} else {
				stopGenerator();
				console.error( response );
				updateStatus('<span class="error">An error occurred.</span> No status key available. Please try again or check the logs if the problem persists.', true);
			}
		}, 'json');
	}

	function stopGenerator(cancel) {
		hideSpinner();
		enableGenerateButton(true);
		stopStatusPoll();

		$('select', container).removeAttr('disabled');

		var message = 'Process Complete';
		if( cancel ) {
			message = 'Process Cancelled';
		}
		updateStatus(message, true);
	}

	function startStatusPoll(id) {
		stopStatusPoll();
		interval = setInterval( function() {
			pollStatus(id);
		}, 3000);
		pollStatus(id);
	}

	function pollStatus(id) {
		var data = {
			action : 'docraptor_status',
			status_id : id
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response.status ) {
				switch( response.status ) {
					case 'completed' :
						stopGenerator();
						updateStatus('<a href="' + response.download_url + '">Click here to download your PDF</a>');
						break;
					case 'working' :
						$('.ff-docraptor-working', container).append(' . ');
						break;
				}

			}
		}, 'json');
	}

	function stopStatusPoll() {
		clearInterval( interval );
	}

})(jQuery);