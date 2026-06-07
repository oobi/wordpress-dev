// scroll window to top on link click
export function scrollToTop(selector) {
    var toTopLink = document.querySelector(selector);
	if( toTopLink === null ) return;

    toTopLink.addEventListener("click", function (event) {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });
}
