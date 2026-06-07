(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function(){
		var $feedType 		= $('#midgard-feed-type');

		if($feedType.length) {
			// the feed type previously saved (not necessarily the current value of the select list)
			var savedFeedType  = $feedType.data('feedtype');

			// sort feed options alphabetically
			$feedType.html($("option", $feedType).sort(function (a, b) {
				return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
			}));

			// show different feed options on change
			$feedType.change(function(){
				showFeedFields($(this).val());
			});

			// show initial value
			$feedType.val( savedFeedType );
			showFeedFields(savedFeedType);

			// add mapping action button
			$('.midgard-add-mapping').on('click', function(event){
				event.preventDefault();
				addNewMapping();
			});

			$('.midgard-mappings').on('click', '.midgard-mapping-delete', function(event){
				event.preventDefault();
				deleteMapping( this );
			});

			// import / export
			$('.midgard-export-mapping').on('click', function(event){
				exportMappings();
			});

			$('.midgard-import-mapping').on('click', function(event){
				importMappings();
			});

			// show/hide map modes
			$('INPUT[name=midgard-mapping-mode]').on( 'change', function() {
				setMappingMode( this.value );
			});

			setMappingMode( $('INPUT[name=midgard-mapping-mode]:checked').val() );

			// allow tab indenting inside advanced mapping
			$(document).delegate('TEXTAREA.midgard-mapping-twig', 'keydown', function(e) {
				var keyCode = e.keyCode || e.which;

				// insert tabs
				if (keyCode == 9) {
					e.preventDefault();
					var start = $(this).get(0).selectionStart;
					var end = $(this).get(0).selectionEnd;

					// set textarea value to: text before caret + tab + text after caret
					$(this).val($(this).val().substring(0, start)
								+ "\t"
								+ $(this).val().substring(end));

					// put caret at right position again
					$(this).get(0).selectionStart =
					$(this).get(0).selectionEnd = start + 1;
				}
			});

			// auto indent
			$('TEXTAREA.midgard-mapping-twig').each(function(n,ta){
				doAutoIndent(ta);
			});

		}
	});

	//////////////////////////////////////////////////////
	// FEED TYPE UI FIELDS
	//////////////////////////////////////////////////////

	function showFeedFields(value) {
		var $feedType 		= $('#midgard-feed-type');
		var container 		= '#data-feed-details-meta-box';
		var $feedSection 	= $("#feed-section-" + value, container);

		// hide all but the relevant feed section fields
		$(".feed-section", container).hide();
		$feedSection.show();

		// optionally hide the feed URI field if the feed section requires it
		if( $feedSection.data('hide-feed-uri')) {
			$('#midgard-feed-uri-wrapper').hide();
		} else {
			$('#midgard-feed-uri-wrapper').show();
		}

		// if invalid feed type show error
		if(value.length && $feedSection.length == 0) {
			$(".feed-section.error").show();
			if($('.midgard-invalid-type', $feedType).length == 0) {
				$feedType.prepend('<option value="invalid" class="midgard-invalid-type" selected="selected">Invalid Feed Type</option>');
			}
		}
	}

	//////////////////////////////////////////////////////
	// MAPPINGS
	//////////////////////////////////////////////////////

	/**
	 * Set mapping field visibility 
	 */
	function setMappingMode( mode ) {
		$('.midgard-map-mode').hide();
		$('.midgard-map-mode[data-mode="' + mode + '"]').show();

		if( mode == 'simple') {
			$('#data-feed-mapping-export-meta-box').show();
		} else {
			$('#data-feed-mapping-export-meta-box').hide();
		}
	}

	/**
	 * Add a new mapping row to the table
	 */
	function addNewMapping(key, path, multi) {
		var $tbody = $('.midgard-mappings > TBODY');

		if( key == null ) key = '';
		if( path == null ) path = '';
		if( multi == null ) multi = 0;

		// create a new row
		var $tr    = $('<tr/>');
		$tr.append('<td><input type="text" class="large-text midgard-mapping-key" name="" class="large-text" placeholder="key_name" required value="' + key + '"></td>');
		$tr.append('<td><input type="text" class="large-text midgard-mapping-path" name="" class="large-text" placeholder="$.jsonpath" required value="' + path + '"></td>');
		$tr.append('<td style="text-align:center"><input type="checkbox" value="1" ' + (multi ? 'checked' : '') + '/></td>');
		$tr.append('<td><a class="midgard-mapping-delete dashicons dashicons-trash" href="#" ></a></td>');

		// append to the table
		$tbody.append($tr);

		// remap index
		remapMappingIndex();
	}

	/**
	 * Remove mapping row
	 */
	function deleteMapping( element ) {
		// remove current row
		$(element).closest('tr').remove();

		remapMappingIndex();
	}

	/**
	 * Remap mapping array indicies
	 */
	function remapMappingIndex() {
		$('.midgard-mappings > TBODY > TR').removeClass('alternate');

		$('.midgard-mappings > TBODY > TR').each(function(n, tr){
			if(n % 2 == 1) $(tr).addClass('alternate');
			$('.midgard-mapping-key', tr).prop('name', 'midgard-feed-mappings[' + n + '][key]');
			$('.midgard-mapping-path', tr).prop('name', 'midgard-feed-mappings[' + n + '][path]');
			$('.midgard-mapping-multi', tr).prop('name', 'midgard-feed-mappings[' + n + '][multi]');
		});
	}

	/**
	 * Export mappings to text field
	 */
	function exportMappings() {
		var mappings = [];
		$('.midgard-mappings > TBODY > TR').each(function(n,tr){
			var map = {
				key 	: $('.midgard-mapping-key', tr).val(),
				path 	: $('.midgard-mapping-path', tr).val(),
				multi 	: $('.midgard-mapping-multi', tr).prop('checked') ? 1 : 0
			}
			mappings.push( map );
		});

		$('#midgard-mapping-data').val( JSON.stringify(mappings) );
	}

	/**
	 * Import mappings from text field
	 */
	function importMappings() {
		var txt = $('#midgard-mapping-data').val();
		var mappings = [];

		try {
			mappings = JSON.parse( txt );
		} catch( e ) {
			alert('Unable to import - invalid JSON or wrong structure');
			console.error('Error parsing JSON', e);
			return;
		}
		
		// remove existing mappings
		$('.midgard-mappings > TBODY > TR').remove();

		for(var i=0; i<mappings.length; i++) {
			var map = $.extend({
				key : '',
				path : '',
				multi : 0
			}, mappings[i]);

			addNewMapping(map.key, map.path, map.multi);
		}
	}

	/**
	 * Text Field auto indent
	 * Credit: https://jsfiddle.net/rudiedirkx/WwCe3/
	 */
	function doAutoIndent(ta, indent) {
		indent || (indent = "\t");
		
		function setValue(text) {
			ta.value = text;
			return ta.value;
		}
		
		function str_repeat(str, n) {
			var out = '';
			while (n--) out += str;
			return out;
		}
		
		function isIndented(line) {
			var regex = new RegExp('^(' + indent + '+)', 'g'),
				match = line.match(regex);
			return match && match[0].length / indent.length || 0;
		}
		
		function addIndent(before, after, num) {
			// num = num ? ~~num : 1;
			if ( !num ) return;
			ta._lastValue = setValue(before + str_repeat(indent, num) + after);
			ta.selectionStart = ta.selectionEnd = before.length + indent.length * num;
		}
		
		function removeIndent(before, after) {
			var remove = before.slice(before.length - 1 - indent.length, before.length - 1);
			if ( remove != indent ) {
				return;
			}
			
			ta._lastValue = setValue(before.slice(0, -1-indent.length) + '}' + after);
			ta.selectionStart = ta.selectionEnd = before.length - indent.length;
		}
		
		function getPrevLine(before) {
			var lines = ta.value.split(/\n/g),
				line = before.trimRight().split(/\n/g).length - 1;
			return lines[line] || '';
		}
		
		function onKeyUp(e) {
			var lastValue = ta._lastValue === undefined ? ta.defaultValue : ta._lastValue,
				change = ta.value.length - lastValue.length;
			ta._lastValue = ta.value;
			if ( !change ) {
				return;
			}
			
			var caret = ta.selectionStart,
				added = change > 0 && ta.value.substr(caret - change, change) || '',
				removed = change < 0 && lastValue.substr(caret, -change) || '';
			
			var code = e.keyCode;
			var value = ta.value,
				before = value.substr(0, caret),
				after = value.substr(caret),
				lastChar = before.trim().slice(-1),
				nextChar = after.substr(0, 1);
			
			// ENTER
			if ( code == 13 ) {
				// Immediately after a {
				if ( lastChar == '{' ) {
					var prevLine = getPrevLine(before),
						indents = isIndented(prevLine),
						more = nextChar == '}' ? 0 : 1;
					return addIndent(before, after, indents + more);
				}
				
				// After an indented line
				var prevLine = getPrevLine(before),
					indents = isIndented(prevLine),
					more = nextChar == '}' ? -1 : 0;
				if ( indents + more > 0 ) {
					addIndent(before, after, indents + more);
				}
			}
			else if ( added == '}' ) {
				removeIndent(before, after);
			}
		}
		
		ta.addEventListener('keyup', onKeyUp, false);
	}

})( jQuery );
