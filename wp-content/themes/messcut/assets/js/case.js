/**
 * Case page: result sheet, process stepper.
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
		var track = stepper.querySelector(".stepper__track");
		var buttons = stepper.querySelectorAll(".stepper__track button");
		if (!template || !panel) return;
		var steps = [];
		try { steps = JSON.parse(template.innerHTML); } catch (error) { steps = []; }
		var index = 0;
		var copy = window.messcutCase || {};
		function stageLabel(current, total) {
			var template = copy.stage || "Етап %1$d / %2$d";
			return template.replace("%1$d", String(current)).replace("%2$d", String(total));
		}
		function show(next) {
			index = next;
			var step = steps[index] || {};
			var wrap = document.createElement("div");
			var label = document.createElement("span");
			var heading = document.createElement("h3");
			wrap.className = "stepper__body";
			label.className = "eyebrow";
			label.textContent = stageLabel(index + 1, steps.length);
			heading.textContent = step.title || "";
			wrap.appendChild(label);
			wrap.appendChild(heading);
			if (step.text) {
				var paragraph = document.createElement("p");
				paragraph.textContent = step.text;
				wrap.appendChild(paragraph);
			}
			var nav = document.createElement("div");
			var prev = document.createElement("button");
			var nextButton = document.createElement("button");
			nav.className = "stepper__nav";
			prev.type = "button";
			prev.textContent = "← " + (copy.prev || "Назад");
			prev.disabled = index === 0;
			nextButton.type = "button";
			nextButton.className = "is-next";
			nextButton.textContent = (copy.next || "Далі") + " →";
			nextButton.disabled = index === steps.length - 1;
			prev.addEventListener("click", function () { if (index > 0) show(index - 1); });
			nextButton.addEventListener("click", function () { if (index < steps.length - 1) show(index + 1); });
			nav.appendChild(prev);
			nav.appendChild(nextButton);
			panel.replaceChildren(wrap, nav);
			if (track && steps.length > 1) {
				track.style.setProperty("--p", String(index / (steps.length - 1)));
			}
			buttons.forEach(function (button, buttonIndex) {
				button.setAttribute("aria-selected", buttonIndex === index ? "true" : "false");
				button.classList.toggle("is-done", buttonIndex < index);
			});
		}
		buttons.forEach(function (button, buttonIndex) {
			button.addEventListener("click", function () { show(buttonIndex); });
		});
		var startX = null;
		panel.addEventListener("touchstart", function (event) {
			startX = event.touches[0].clientX;
		}, { passive: true });
		panel.addEventListener("touchend", function (event) {
			if (startX === null) return;
			var delta = event.changedTouches[0].clientX - startX;
			startX = null;
			if (Math.abs(delta) < 50) return;
			if (delta < 0 && index < steps.length - 1) show(index + 1);
			else if (delta > 0 && index > 0) show(index - 1);
		});
		show(0);
	});
})();
