/**
 * Returns a string of concatenated class names based on the input arguments.
 *
 * @param {...(string|object)} args - A variable number of arguments, each of which can be a string or an object.
 *   If a string is provided, it will be added to the resulting class name string as-is.
 *   If an object is provided, its keys will be used as class names if their corresponding values are truthy.
 * @returns {string} A space-separated string of class names.
 */
export function classnames(...args) {
	const classes = [];

	for (const arg of args) {
		if (typeof arg === "string") {
			classes.push(arg);
		} else if (typeof arg === "object") {
			for (const [key, value] of Object.entries(arg)) {
				if (value) {
					classes.push(key);
				}
			}
		}
	}

	return classes.join(" ");
}



/**
 * return first n words of article excerpt
 * @param {*} article
 * @param {*} n
 * @returns
 */
export function excerpt(article, n) {
	// html is either the manually input excerpt or the article content
	const html = article.excerpt.raw
		? article.excerpt.raw
		: article.content.rendered;
	// strip tags
	const tempDiv = document.createElement("div");
	tempDiv.innerHTML = html;
	const text = (tempDiv.textContent || tempDiv.innerText || "").trim();

	// return first n words
	const words = text.trim().split(/\s+/);
	const resultWords = words.slice(0, n);
	const result = resultWords.join(" ");
	const ellipsis = result.length < text.length ? "..." : "";
	return result + ellipsis;
}
