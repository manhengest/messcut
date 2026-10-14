/**
 * Article: copy link.
 */
(function () {
	var copyBtn = document.querySelector("[data-article-copy]");
	if (!copyBtn) return;

	var copied =
		typeof messcutArticle !== "undefined" && messcutArticle.copied
			? messcutArticle.copied
			: "Скопійовано";

	copyBtn.addEventListener("click", function () {
		var label = copyBtn.textContent;
		if (!navigator.clipboard || !navigator.clipboard.writeText) return;

		navigator.clipboard.writeText(location.href).then(
			function () {
				copyBtn.textContent = copied;
				setTimeout(function () {
					copyBtn.textContent = label;
				}, 1800);
			},
			function () {}
		);
	});
})();
