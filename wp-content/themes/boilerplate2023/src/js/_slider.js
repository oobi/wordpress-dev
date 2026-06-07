export function initSlider($) {
    $(".slider.slider-standard").each(function (n, el) {
        let delay = parseInt($(el).data("delay"));

		if( isNaN(delay) ) {
			delay = 8000;
		}

        $(".owl-carousel", el).owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            dots: true,
            autoplay: delay > 0,
            autoplayTimeout: delay,
            autoplayHoverPause: true,
            smartSpeed: 1000,

            // animateIn: 'fadeIn',
            // animateOut: 'fadeOut',
        });
    });

    $(".slider.slider-block").each(function (n, el) {
        let $slider = $(".owl-carousel", el);
        let delay = parseInt($(el).data("delay"));
        let random = $(el).data("random");

		if( isNaN(delay) ) {
			delay = 8000;
		}

        // randomise the slide order
        if (random) {
            for (var i = $slider[0].children.length; i >= 0; i--) {
                $slider[0].appendChild($slider[0].children[(Math.random() * i) | 0]);
            }
        }

        $slider.owlCarousel({
            items: 1,
            loop: true,
            nav: false,
            dots: true,
            autoplay: delay > 0,
            autoplayTimeout: delay,
            smartSpeed: 1000,

            animateIn: "fadeIn",
            animateOut: "fadeOut",
        });
    });
}
