export function initVariableBg($) {

	$('.variable-bg-block').each(function(n, el) {
		let block = $(el);
		let bg = block.data('bg');
		let random = block.data('random');
		let delay = parseInt(block.data('delay'));

		if( isNaN(delay) || !delay ) {
			delay = 8000;
		}

		// shuffle the bg array if random start
		if( random ) {
			bg = bg.sort(() => Math.random() - 0.5);
		}

		// preload images and check when all loaded
		let loaded = 0;

		for( let i = 0; i < bg.length; i++ ) {
			let img = new Image();
			img.src = bg[i];

			// check load
			img.onload = () => {
				loaded++;
				if( loaded == bg.length ) {
					startTransition(block, bg, delay);
				}
			}
		}


	});
}

function startTransition(block, backgrounds, delay) {
	// change background every few seconds
	var bgIndex = 0;
	setInterval(() => {
		let newBg = backgrounds[++bgIndex % backgrounds.length];
		block.css('background-image', `url(${newBg})`);
	}, delay);
}