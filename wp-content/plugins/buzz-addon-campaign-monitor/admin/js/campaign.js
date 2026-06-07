(function( $ ) {
	'use strict';

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
			case 'tab-stats' :
				initStatsTab($container);
				break;
			case 'tab-current' :
				initCurrentTab($container);
				break;
		}
	});

	/********************************************************
	 * Campaign Create page
	 ********************************************************/

	function initCreateTab($container) {
		// get selected contact lists
		var $newsletterList		= $('SELECT#newsletter-to-send', $container);
		var $contactList 		= $('INPUT.contact-list', $container);
		var $contactListChecked = $('INPUT.contact-list:checked', $container);
		var $subject 			= $('INPUT[name=form-subject]', $container);
		var $newsletterCustomField = $("INPUT[name=form-newsletter-to-send-custom]", $container);

		// for each list that is selected on entry to the page, update the segments selector
		$contactListChecked.each( function(i) {
			onListSelection(this);
		});

		// disable the segment checkbox if it's parent is selected
		$contactList.change(function(e) {
			onListSelection(this);
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

	/**
	 * On contact list selection, disable the appropriate contact list (can't send to both a segment and the whole contatc list)
	 */
	function onListSelection( input ) {
		var parentId 	= $(input).val();
		var $segment 	= $('INPUT[data-list-id="' + parentId + '"]');

		if( input.checked ) {
			// disable and uncheck all segments attached to this list
			$segment.attr('disabled', true).prop('checked', false);
		} else {
			// enable segments again
			$segment.removeAttr('disabled');
		}
	}


	/********************************************************
	 * Campaign Delete page
	 ********************************************************/

	function initDeleteTab($container) {
		// Prompt user to confirm choice before submitting Delete Email Campaign form.
		$('#delete-campaign', $container).click(function(e) {
			return confirm('Are you sure you want to PERMANENTLY delete this campaign?');
		});
	}

	/********************************************************
	 * Campaign Send page
	 ********************************************************/

	function initSendTab($container) {
		var admin_url = "./edit.php?post_type=newsletter&page=buzz-campaign-monitor&tab=send";

		$("#campaign-to-send", $container).change(function(){
			var cid = $(this).val();
			window.location = admin_url + "&cid=" + cid;
		});

		var list_id = $('INPUT[name=contact-list]:checked',$container).val();
		// getSegmentsForContactList(list_id);

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

	function initStatsTab($container) {
		var admin_url = "./edit.php?post_type=newsletter&page=buzz-campaign-monitor&tab=stats";
		$("#campaign-to-send", $container).change(function(){
			var cid = $(this).val();
			window.location = admin_url + "&cid=" + cid;
		});
	};

	function initCurrentTab($container) {
		var admin_url = "./edit.php?post_type=newsletter&page=buzz-campaign-monitor&tab=current";
		$(".alignleft.actions.bulkactions").append('<input class="button button-secondary" id="refresh-campaigns" name="refresh-campaigns" type="button" value="Refresh Campaign List"/>');
		$("#refresh-campaigns", $container).click(function(){
			window.location = admin_url + "&refresh=1";
		});
	};

})( jQuery );
