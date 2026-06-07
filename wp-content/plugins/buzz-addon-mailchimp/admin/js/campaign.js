(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	$(document).ready(function(){
		var $container = $(".tab-content");
		var tab = $container.attr('id');

		// Init Email Management page tabs
		switch(tab) {
			case 'tab-create' :
			case 'tab-update' :
				initCreateTab($container);
				break;
			case 'tab-send' :
				initSendTab($container);
				break;
			case 'tab-delete' :
				initDeleteTab($container);
				break;
		}
	});


	/********************************************************
	 * Campaign Create page
	 ********************************************************/

	function initCreateTab($container) {
		// update newsletter subject when selection changes
		var $newsletterList		= $('SELECT#newsletter-to-send', $container);
		var $subject 			= $('INPUT[name=form-subject]', $container);
		var $newsletterCustomField = $("INPUT[name=form-newsletter-to-send-custom]", $container);

		// update segments based on initial contact list selection
		var $contactList 		= $('SELECT[name=form-contact-list]', $container);
		var $contactListChecked = $contactList.val();

		// if a list is selected on entry to the page, update the segments selector
		if($contactListChecked.length) {
			getSegmentsForContactList($container, $contactListChecked);
		}

		// update segments on contact list change
		$contactList.change(function(e) {
			getSegmentsForContactList($container, $(this).val());
		});

		// on newsletter change generate a new subject line
		$newsletterList.change(function(e) {
			var value = $("option:selected", this).data('subject');
			$subject.val( value );

			// only show the newsletter custom URL field if the "Insert Custom URL" option is selected (ie. no subject)
			if( value ) {
				$newsletterCustomField.hide();
			} else {
				$newsletterCustomField.show();
			}
		});

		$newsletterList.change();
	}

	function getSegmentsForContactList($container, list_id) {
		var inputData = {
			'action'	: 'get_segments',
			'list_id'	: list_id
		};

		var $segmentContainer 	= $('SELECT[name=form-list-segment]', $container);
		if($segmentContainer.length == 0) {
			return;
		}

		// show segments section
		$('#segments').show();

		// start loading spinner
		$(".segment", $container).addClass('loading');

		// disable contact list select
		$('[name=form-contact-list]', $container).attr('disabled','disabled');

		// AJAX call
		// 'ajaxurl' is always defined in the admin header and points to admin-ajax.php
		$.ajax({
			type		: "GET",
			url			: ajaxurl,
			data		: inputData,
			dataType	: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			if(data && !data.error) {
				var segmentData = data.segments;
				var output;

				// get segment data and sort alphabetically
				//segmentData = $.merge(data.static, data.saved);
				//segmentData.sort();
				output = "";

				// check if segments are available
				if(segmentData.length > 0) {

					// add default value
					output += '<option value="">No segment (send to entire list)</option>';

					// loop segment data and display
					for(var i=0;i<segmentData.length;i++) {
						var item = segmentData[i];

						output += '<option value="' + item.id + '">' + item.name + ' (' + item.member_count + ') : ' + item.type + '</option>';

					}
					$segmentContainer.html(output);

				} else {

					// add default value
					$segmentContainer.html('<option value="">No segment (send to entire list)</option>');

				}

			} else {
				console.error('Error occurred loading data', data);
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown){
			console.error('Mailchimp fail!', textStatus, errorThrown);
			alert('Unable to retrieve segments');
		})
		.always(function(data, textStatus, errorThrown){

			// remove loading spinner on complete
			$(".segment", $container).removeClass('loading');

			// if a segment ID is present on the segment select data then set its value
			$('SELECT[name=form-list-segment]', $container).each(function(){
				var value = $(this).data('value');
				$(this).val(value);
			});

			$('[name=form-contact-list]', $container).removeAttr('disabled');
		});
	}

	/********************************************************
	 * Campaign Send page
	 ********************************************************/

	function initSendTab($container) {
		var admin_url = "/wp-admin/edit.php?post_type=newsletter&page=buzz-mailchimp&tab=send";

		$("#campaign-to-send", $container).change(function(){
			var cid = $(this).val();
			window.location = admin_url + "&cid=" + cid;
		});

		var list_id = $('INPUT[name=contact-list]:checked',$container).val();
		getSegmentsForContactList(list_id);

		// Prompt user to confirm choice before submitting Delete Email Campaign form.
		$('#send-campaign', $container).click(function(e) {
			var $table 	= $('#campaign-summary');
			var $data 	= $('#title, #contact-list, #total-emails', $table);

			var txt = "";
			$data.each(function(n,tr){
				var label = $.trim($("td:first", tr).text());
				var value = $.trim($("td:last", tr).text());
				txt += label + " : " + value + "\n";
			});

			return confirm('Are you sure you want to send the following campaign?\n\n' + txt);
		});
	};


	/********************************************************
	 * Campaign Delete page
	 ********************************************************/

	function initDeleteTab($container) {
		// Prompt user to confirm choice before submitting Delete Email Campaign form.
		$('#delete-campaign', $container).click(function(e) {
			return confirm('Are you sure you want to PERMANENTLY delete this campaign?');
		});
	}

})( jQuery );
