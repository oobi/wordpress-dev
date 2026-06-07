// import Headroom from "headroom.js";
// import { initSlider } from "./_slider";

// var mobileThreshold = 992;
// var isMobile = window.innerWidth < mobileThreshold;

// const app = document.querySelector("#app");
// const header = document.querySelector('.site-header');

// /**
//  * We'll load jQuery and the Bootstrap jQuery plugin which provides support
//  * for JavaScript based Bootstrap features such as modals and tabs. This
//  * code may be modified to fit the specific needs of your application.
//  */

import 'bootstrap';

import baguetteBox from 'baguettebox.js';
import { lightbox } from './_lightbox.js';

lightbox(baguetteBox);

// const headroom = new Headroom(document.querySelector(".site-header"), {
//     // headroom options here
//     // https://wicky.nillia.ms/headroom.js/
// });


// function onWindowResize() {
//     // mobile width?
//     isMobile = window.innerWidth < mobileThreshold;

//     // Our header is positioned fixed, so let's offset the app element to the headers height
//     headroom.offset = header.offsetHeight;
//     app.style.paddingTop = `${header.offsetHeight}px`;
// }

// // inits
// jQuery(function($) {
//     headroom.init();
//     onWindowResize();
//     initSlider($);

// 	// debounce window resize
// 	var timeout = 0;
// 	$(window).on( 'resize', event => {
// 		clearTimeout(timeout);
// 		timeout = setTimeout( () => {
// 			onWindowResize();
// 		}, 300)
// 	});
// });