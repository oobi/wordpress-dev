import Headroom from "headroom.js";

export function initHeadroom($) {
	const app = document.querySelector("#app");
	const header = document.querySelector('.site-header');

	if( !header ) {
		return;
	}

	var headroomTimeout = 0;

	const headroom = new Headroom(header, {
		// headroom options here
		// https://wicky.nillia.ms/headroom.js/
		onTop : () => {
			// trigger a resize to ensure we have the most up to date padding
			// wait out the transition
			headroomTimeout = setTimeout( () => {
				window.dispatchEvent(new Event('resize'));
			}, 400);
		},
		// when not at the top, clear the resize timeout
		onNotTop : () => {
			clearTimeout(headroomTimeout)
		},
		// when unpinned, hide any dropdowns in the header
		onUnpin: () => {
			try {
				$('.dropdown-menu', header).parent().dropdown('hide');
			} catch(e) {
				// do nothing
			}
		}
	});

	headroom.init();

	// debounce window resize
	var timeout = 0;
	window.addEventListener('resize', function() {
		clearTimeout(timeout);
		timeout = setTimeout( () => {
			headroom.offset = header.offsetHeight;
			app.style.paddingTop = `${header.offsetHeight}px`;
		}, 300)
	});



	// trigger a resize
	window.dispatchEvent(new Event('resize'));
}
