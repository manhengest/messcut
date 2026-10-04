/**
 * Case page: chapter rail, result sheet, process stepper.
 */
(function () {
	var sheet = document.querySelector("[data-sheet]");
	var title = sheet ? sheet.querySelector("#sheet-title") : null;
	var body = sheet ? sheet.querySelector("[data-sheet-body]") : null;

	function closeSheet() {
		if (!sheet) return;
		sheet.classList.remove("is-open");
		sheet.setAttribute("aria-hidden", "true");
	}
	document.querySelectorAll("[data-result-title]").forEach(function (card) {
		card.addEventListener("click", function () {
			if (!sheet || !title || !body) return;
			title.textContent = card.getAttribute("data-result-title") || "";
			body.textContent = card.getAttribute("data-result-detail") || "";
			sheet.hidden = false;
			sheet.classList.add("is-open");
			sheet.setAttribute("aria-hidden", "false");
		});
	});
	document.querySelectorAll("[data-sheet-close]").forEach(function (button) {
		button.addEventListener("click", closeSheet);
	});
	if (sheet) {
		sheet.addEventListener("click", function (event) {
			if (event.target === sheet) closeSheet();
		});
	}

	document.querySelectorAll("[data-stepper]").forEach(function (stepper) {
		var template = stepper.querySelector("[data-steps]");
		var panel = stepper.querySelector("[data-step-panel]");
		var buttons = stepper.querySelectorAll(".stepper__track button");
		if (!template || !panel) return;
		var steps = [];
		try { steps = JSON.parse(template.innerHTML); } catch (error) { steps = []; }
		function show(index) {
			var step = steps[index] || {};
			var heading = document.createElement("h3");
			var copy = document.createElement("p");
			heading.textContent = step.title || "";
			copy.textContent = step.text || "";
			panel.replaceChildren(heading, copy);
			buttons.forEach(function (button, buttonIndex) {
				button.setAttribute("aria-selected", buttonIndex === index ? "true" : "false");
			});
		}
		buttons.forEach(function (button, index) {
			button.addEventListener("click", function () { show(index); });
		});
		show(0);
	});

	var rail = document.querySelector("[data-rail]");
	var sections = Array.prototype.slice.call(document.querySelectorAll("[data-chapter]"));
	if (rail && sections.length) {
		sections.forEach(function (section) {
			if (!section.id) return;
			var link = document.createElement("a");
			link.href = "#" + section.id;
			rail.appendChild(link);
		});
		function mark() {
			var current = sections[0];
			sections.forEach(function (section) {
				if (section.getBoundingClientRect().top < 180) current = section;
			});
			rail.querySelectorAll("a").forEach(function (link) {
				link.classList.toggle("is-active", current && link.getAttribute("href") === "#" + current.id);
			});
			rail.classList.add("is-on");
		}
		mark();
		window.addEventListener("scroll", mark, { passive: true });
	}
})();
