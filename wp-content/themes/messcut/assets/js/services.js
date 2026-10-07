/**
 * Services page: direction accordions, detail toggles, orb.
 */
(function () {
	var desktop = window.matchMedia("(min-width: 1001px)");

	function openAll() {
		if (!desktop.matches) return;
		document.querySelectorAll("[data-service-group]").forEach(function (group) {
			var button = group.querySelector(".service-group__toggle");
			group.classList.add("is-open");
			if (button) button.setAttribute("aria-expanded", "true");
		});
	}

	openAll();
	desktop.addEventListener("change", openAll);

	document.querySelectorAll("[data-service-group]").forEach(function (group) {
		var button = group.querySelector(".service-group__toggle");
		if (!button) return;
		button.addEventListener("click", function () {
			var open = !group.classList.contains("is-open");
			group.classList.toggle("is-open", open);
			button.setAttribute("aria-expanded", open ? "true" : "false");
		});
	});

	document.querySelectorAll("[data-details]").forEach(function (button) {
		button.addEventListener("click", function () {
			var card = button.closest(".service-card");
			if (!card) return;
			var open = !card.classList.contains("is-detailed");
			card.classList.toggle("is-detailed", open);
			button.setAttribute("aria-expanded", open ? "true" : "false");
			var label = button.querySelector("span");
			if (!label) return;
			label.textContent = open ? button.getAttribute("data-close") : button.getAttribute("data-open");
		});
	});

	var orb = document.querySelector("[data-orb]");
	if (orb) {
		var ticking = false;
		function placeOrb() {
			ticking = false;
			var max = document.documentElement.scrollHeight - window.innerHeight;
			var progress = max > 0 ? window.scrollY / max : 0;
			orb.style.transform = "translate(" + (Math.sin(progress * Math.PI * 6) * 90) + "px," + (window.innerHeight * 0.12 + progress * window.innerHeight * 0.55) + "px) rotate(" + (progress * 240) + "deg)";
		}
		window.addEventListener("scroll", function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(placeOrb);
			}
		}, { passive: true });
		placeOrb();
	}
})();
