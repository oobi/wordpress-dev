export function initSlider($) {
	$('.slider').each(function(n, el) {
		$('.owl-carousel', el).owlCarousel({
			items: 1,
			loop: true,
			nav: true,
			dots: false,
			autoplay: true,
			autoplayTimeout: 5000,

			animateIn: 'fadeIn',
			animateOut: 'fadeOut',
		});
	});
}