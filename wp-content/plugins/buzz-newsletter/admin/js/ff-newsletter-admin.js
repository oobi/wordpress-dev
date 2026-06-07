(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
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

	$(document).ready(function() {

		// differentiate admin pages by body class
		var isArticleEditPage 	 = $("body").is(".post-type-article.post-php, .post-type-article.post-new-php");
		var isNewsletterEditPage = $("body").is(".post-type-newsletter.post-php, .post-type-newsletter.post-new-php");

		// execute relevant init
		if(isArticleEditPage) {
			initNewArticleButton();
			initCustomPublishMetabox();
		}
		else if(isNewsletterEditPage) {
			initArticleSort();
			initArticleAttributes();
		}

	});

	////////////////////////////////////////////////////////////////////////////////////
	// ARTICLE EDIT
	////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Initialise the New Article button - set the Parent ID of the newsletter to create it in
	 * Affects post.php in EDIT mode
	 */
	function initNewArticleButton() {

		// Replace Add New button text
		var $addNewButton 	= $('A.add-new-h2', '.post-type-article');
		// $addNewButton.text('Add New to current Newsletter');

		// Append parent ID to Add New link src
		var parentID 		= $("[name='ff-parent-id']").val();
		var href 			= $addNewButton.attr('href');
		$addNewButton.attr('href', href + '&parent_id=' + parentID);

	}

	/**
	 * Initialise the Newsletter option in the Publish Box
	 * Affects post.php in EDIT mode
	 */
	function initCustomPublishMetabox() {

		var $selectArea	= $('.misc-pub-parent-id #parent-id-select');
		var $editLink 	= $('.misc-pub-parent-id .edit-parent-id');
		var $saveLink 	= $('.misc-pub-parent-id .save-parent-id');
		var $cancelLink = $('.misc-pub-parent-id .cancel-parent-id');
		var slideSpeed	= 200;

		// show selectbox on Edit click
		$editLink.on('click', function(e) {
			e.preventDefault();

			// show/hide links and areas
			$selectArea.slideDown(slideSpeed);
			$editLink.hide();
		});

		// hide selectbox on Cancel click
		$cancelLink.on('click', function(e) {
			e.preventDefault();

			// show/hide links and areas
			$selectArea.slideUp(slideSpeed);
			$editLink.show();

		});

		// set fields on Save click and hide selectbox
		$saveLink.on('click', function(e) {
			e.preventDefault();

			// get selected values
			var $selectBox 	= $('#parent_id_select', $selectArea);
			var selectVal 	= $selectBox.children(":selected").val();
			var selectText 	= $selectBox.children(":selected").text();

			// set value of hidden input
			$('INPUT#ff-parent-id', $selectArea).val(selectVal);

			// set label text
			$('#parent-id-display').text(selectText);

			// show/hide links and areas
			$selectArea.slideUp(slideSpeed);
			$editLink.show();

		});

	}

	////////////////////////////////////////////////////////////////////////////////////
	// NEWSLETTER EDIT
	////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Initialise sortable article tiles on newsletter edit screen
	 */
	function initArticleSort() {
		var $list = $('.buzz-article-sortable-list');

		initSavePublish($list);

		// Setup sortable list(s)
		// On update, tell WordPress about the new order
		$list.sortable({
			// 'items'		 : '> LI',
			'placeholder': 'sort-placeholder',
			'connectWith': '.buzz-article-sortable-list',
			'tolerance'  : 'pointer',
			'handle'	 : '.thumbnail'
		});

		// extract event binding to control event order for sub-processes
		$list.on('sortupdate', function(event, ui) {

			runSortUpdate($list);
			
		});


		checkEmptyArticles( $list );
	}

	function runSortUpdate($list) {
		var $items = $('li', $list);
		var articles = [];

		checkEmptyArticles( $list );

		$items.each( function(n, item) {
			// update the data property - this is really just to debug it in the inspector. We don't use it.
			$(item).attr('data-order', n);

			// collect IDs in the new order
			articles.push( $(item).data('id') );
		});

		$.post( ajaxurl, {
			action : 'update-article-order',
			articles  : articles
		});
	}

	function checkEmptyArticles( $list ) {
		// show "no articles" prompt if empty
		$list.each( function(n, el) {
			console.log(  $('.buzz-article-sortable-list > *').length);
			if( $('> *', el).length ==0 ) {
				$(el).siblings('.buzz-no-articles').show();
			} else {
				$(el).siblings('.buzz-no-articles').hide();
			}
		});
	}

	function initSavePublish($list) {
		//console.log( 'initSavePublish()' );

		// run on load if new article added
		runSortUpdate($list);

		var $draft = $('#save-post');
		var $publish = $('#publish');

		$draft.click(function() {
			runSortUpdate($list);
		});

		$publish.click(function() {
			runSortUpdate($list);
		});
	}

	/**
	 * Initialise clickable article attributes
	 */
	function initArticleAttributes() {
		var $items = $('.buzz-article-sortable-list LI .attributes .tick');
		$items.on('click', function(e){
			var $item 		  = $(this);
			var $article 	  = $item.closest('li');
			var $spinner 	  = $item.parent().find(".spinner");

			// new attribute value to set
			var attributeName = $(this).data("property");
			var newValue	  = !$(this).hasClass("active") ? $(this).data('value') : '';

			e.preventDefault();

			// activate spinner
			$item.parent().addClass("busy");
			$spinner.addClass("is-active");

			// update the valye via AJAX
			$.ajax({
				type	: "POST",
				url		: ajaxurl,
				data	: {
					action 		: 'update-article-attribute',
					article_id	: $article.data('id'),
					attribute	: attributeName,
					value		: newValue
				}
			})
			// success
			.done(function(){
				// change changed display date
				$item.toggleClass("active inactive");
			})
			// error
			.fail(function(){

			})
			// after everything...
			.always(function(){
				$item.parent().removeClass("busy");
				$spinner.removeClass("is-active");
			});

		});
	}

})( jQuery );
