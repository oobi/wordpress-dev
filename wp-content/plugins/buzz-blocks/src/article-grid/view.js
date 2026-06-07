/**
 * Toggle accordion
 */

// do the following on page ready
document.addEventListener("DOMContentLoaded", function () {
	const accordions = document.querySelectorAll(".wp-block-buzz-article-grid.is-collapsible");

	accordions.forEach((element) => {
		// listen for collapse event
		const cb = element;

		// toggle the accordion open/closed when clicked
		cb.addEventListener("change", (event) => {
			const content = element.querySelector(".bz-grid-articles");
			var maxHeight = parseInt(content.style.maxHeight);

			if( maxHeight === 0 ) {
				content.style.maxHeight = content.scrollHeight + "px";
			} else {
				content.style.maxHeight = 0;
			}
		});

		// set initial height
		const content = element.querySelector(".bz-grid-articles");
		content.style.maxHeight = content.scrollHeight + "px";
	});

	window.addEventListener("resize", (event) => {
		onResize();
	});

	function onResize() {
		const containers = document.querySelectorAll(".wp-block-buzz-article-grid.is-collapsible");
		containers.forEach((element) => {
			// set initial height
			const content = element.querySelector(".bz-grid-articles");
			content.style.maxHeight = content.scrollHeight + "px";
		});
	}


	onResize();
});
