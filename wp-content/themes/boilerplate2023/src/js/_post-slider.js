export function initPostSlider($) {
	$(".slider.post-slider").each(function (n, el) {
		let type  = $(el).data('type');
		let numItems = 2.75;

		if( type == 'event' ) {
			numItems = 2.25;
		}

        var carousel = $(".owl-carousel", el).owlCarousel({
            items: 1,
            loop: true,
            nav: false,
            dots: false,
            autoplay: false,
            smartSpeed: 1000,

			responsive: {
				0 : {
					items: 1
				},
				768: {
					items: numItems
				}
			}
        });

		// mouse follow
		let $follower = $('<div class="follower"></div>');
		let active = false;
		let next, prev;

		$(el).addClass('mousefollow').append($follower);
		$(el).on('mousemove', (e) => {

			var rect = e.currentTarget.getBoundingClientRect();
			var x = e.clientX - rect.left; //x position within the element.
			var y = e.clientY - rect.top;  //y position within the element.

			$follower.css({
				top: y + 'px',
				left: x + 'px'
			});

			// track mouse for next/previous
			prev = x < rect.width/6;
			next = x > rect.width * 5/6;
		})
		.on('mouseover', (e) => {
			$follower.addClass('active');
			active = true;
		})
		.on('mouseout', (e) => {
			$follower.removeClass('active');
			active = false;
		});

		// track mouse position for next/previous
		setInterval( ()=> {
			if( ! active ) return;

			// track mouse for next/previous
			if( prev ) {
				carousel.trigger('prev.owl.carousel', [1000]);
			} else if( next ) {
				carousel.trigger('next.owl.carousel', [1000]);
			}
		}, 1000);
    });
}