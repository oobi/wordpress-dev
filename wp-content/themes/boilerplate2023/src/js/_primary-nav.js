export function initPrimaryNav($) {
	// nav activation
	let $nav = $('#primaryNavigation');
	let timeout = 0;

	$('.nav-item.has-children > .nav-link', $nav).on('click', function(event) {
		let $item = $(this).closest('.nav-item');
		event.preventDefault();
		$item.toggleClass('show');

		$('.nav-item')
			.not($item)
			.not($item.parents('.nav-item'))
			.removeClass('show');

		// resize the nav
		resizeNav($);
	});

	// initially show open state
	$('.nav-item.current_page_item', $nav).addClass('show');
	$('.nav-item.current_page_item', $nav).parents('.nav-item').addClass('show');

	window.addEventListener('resize', function() {
		clearTimeout(timeout);
		timeout = setTimeout( () => {
			resizeNav($);
		}, 300)
	});

	// trigger a resize
	window.dispatchEvent(new Event('resize'));


	// flag on body when the menu is open
	$('#primaryNavigation')
		.on('show.bs.collapse', function () {
			$('body').addClass('menu-open');
		})
		.on('shown.bs.collapse', function () {
			resizeNav($);
		})
		.on('hide.bs.collapse', function () {
			$('body').removeClass('menu-open');
		})
}

function resizeNav($) {
	let navList = $('#primaryNavigationList').removeAttr('style');
	let navListHeight = navList.height();

	let $openItems = $('.nav-item.show > .submenu', navList);

	// if there are no open items, we're done
	if ($openItems.length === 0) {
		return;
	}

	// get the height of the open items
	let openItemsHeight = 0;
	$openItems.each(function() {
		openItemsHeight = Math.max($(this).height(), openItemsHeight);
	});

	// set the height of the nav list
	if(openItemsHeight > navListHeight) {
		navList.height(openItemsHeight);
	}
}