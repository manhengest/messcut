/**
 * Services page: direction accordions, detail toggles, orb, chapter rail.
 */
(function () {
	var desktop = window.matchMedia("(min-width: 1001px)");

	document.querySelectorAll("[data-service-group]").forEach(function (group) {
		var button = group.querySelector(".service-group__toggle");
		if (!button) return;
		button.addEventListener("click", function () {
			if (desktop.matches) return;
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
		});
	});

	var orb = document.querySelector("[data-orb]");
	if (orb) {
		window.addEventListener("scroll", function () {
			orb.style.top = (120 + window.scrollY * 0.15) + "px";
			orb.style.left = "-80px";
		}, { passive: true });
	}

	var rail = document.querySelector("[data-rail]");
	var sections = Array.prototype.slice.call(document.querySelectorAll("[data-service-group], #lead-form"));
	if (rail && sections.length) {
		sections.forEach(function (section, index) {
			var link = document.createElement("a");
			link.href = "#" + (section.id || ("section-" + index));
			if (!section.id) section.id = "section-" + index;
			link.href = "#" + section.id;
			rail.appendChild(link);
		});
		function mark() {
			var current = sections[0];
			sections.forEach(function (section) {
				if (section.getBoundingClientRect().top < 160) current = section;
			});
			rail.querySelectorAll("a").forEach(function (link) {
				link.classList.toggle("is-active", link.getAttribute("href") === "#" + current.id);
			});
			rail.classList.add("is-on");
		}
		mark();
		window.addEventListener("scroll", mark, { passive: true });
	}
})();
