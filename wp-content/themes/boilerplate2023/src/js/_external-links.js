// open all external links in a new window
export function externalLinks() {
    var currentDomain = window.location.hostname;
    var links = document.getElementsByTagName("a");
    for (var i = 0; i < links.length; i++) {
        var linkDomain = links[i].hostname;
        var linkHref = links[i].href;

		// check that the domain is different to the current domain
		// or that the link is a file (identified by the presence of a file extension) (not a page)
        if (linkDomain !== currentDomain || /\.[^/.]+$/.test(linkHref)) {
            links[i].target = "_blank";
        }
    }
}
