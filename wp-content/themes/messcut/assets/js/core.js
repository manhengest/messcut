/**
 * Header, menu, progress, sticky CTA, tabs, accordion, funnel, lead form.
 */
(function () {
	document.documentElement.classList.add("js");

	var header = document.querySelector("[data-header]");
	var menu = document.querySelector("[data-mobile-menu]");
	var openBtn = document.querySelector("[data-menu-open]");
	var progress = document.querySelector("[data-progress]");
	var sticky = document.querySelector("[data-sticky-cta]");

	function onScroll() {
		var y = window.scrollY || 0;
		if (header) header.classList.toggle("is-scrolled", y > 8);
		if (progress) {
			var max = document.documentElement.scrollHeight - window.innerHeight;
			progress.style.width = (max > 0 ? (y / max) * 100 : 0) + "%";
		}
		if (sticky) sticky.classList.toggle("is-visible", y > 500);
	}
	onScroll();
	window.addEventListener("scroll", onScroll, { passive: true });

	function setMenu(open) {
		if (!menu || !openBtn) return;
		menu.classList.toggle("is-open", open);
		menu.setAttribute("aria-hidden", open ? "false" : "true");
		openBtn.setAttribute("aria-expanded", open ? "true" : "false");
		document.body.classList.toggle("has-menu", open);
	}
	if (openBtn) openBtn.addEventListener("click", function () { setMenu(true); });
	document.querySelectorAll("[data-menu-close]").forEach(function (btn) {
		btn.addEventListener("click", function () { setMenu(false); });
	});
	if (menu) {
		menu.querySelectorAll("a").forEach(function (link) {
			link.addEventListener("click", function () { setMenu(false); });
		});
	}

	document.querySelectorAll("[data-tabs]").forEach(function (tabs) {
		tabs.querySelectorAll("button").forEach(function (button) {
			button.addEventListener("click", function () {
				var key = button.getAttribute("data-tab");
				tabs.querySelectorAll("button").forEach(function (item) {
					item.setAttribute("aria-selected", item === button ? "true" : "false");
				});
				var grid = tabs.parentElement.querySelector(".comparison-grid");
				if (!grid) return;
				grid.querySelectorAll("[data-panel]").forEach(function (panel) {
					panel.classList.toggle("is-active", panel.getAttribute("data-panel") === key);
				});
			});
		});
	});

	var funnel = document.querySelector("[data-funnel]");
	if (funnel) {
		funnel.querySelectorAll("button").forEach(function (button) {
			button.addEventListener("click", function () {
				funnel.querySelectorAll("button").forEach(function (item) {
					item.classList.toggle("is-on", item === button);
				});
				var key = button.getAttribute("data-service");
				document.querySelectorAll("[data-path-service]").forEach(function (card) {
					var match = card.getAttribute("data-path-service") === key;
					card.classList.toggle("is-hot", match);
					card.classList.toggle("is-dim", !match);
				});
			});
		});
	}

	var brandLabel = document.querySelector(".brands-marquee__label-track");
	var brandLogos = document.querySelector(".brands-marquee__track");
	if (brandLabel && brandLogos) {
		function syncBrandLabel() {
			if (!brandLabel.scrollWidth || !brandLogos.scrollWidth) return;
			var speed = brandLogos.scrollWidth / 34;
			brandLabel.style.animationDuration = (brandLabel.scrollWidth / speed) + "s";
		}
		syncBrandLabel();
		window.addEventListener("load", syncBrandLabel);
		window.addEventListener("resize", syncBrandLabel);
	}

	document.querySelectorAll("[data-lead-form]").forEach(function (form) {
		var step1 = form.querySelector('[data-step="1"]');
		var step2 = form.querySelector('[data-step="2"]');
		var done = form.querySelector('[data-step="done"]');
		var error = form.querySelector("[data-lead-error]");
		function show(node) {
			[step1, step2, done].forEach(function (el) {
				if (el) el.hidden = el !== node;
			});
		}
		var next = form.querySelector("[data-lead-next]");
		var back = form.querySelector("[data-lead-back]");
		if (next) next.addEventListener("click", function () {
			var name = form.querySelector('[name="name"]');
			if (name && !name.value.trim()) {
				name.focus();
				return;
			}
			show(step2);
		});
		if (back) back.addEventListener("click", function () { show(step1); });
		form.addEventListener("submit", function (event) {
			event.preventDefault();
			if (!window.messcutData) return;
			var data = new FormData(form);
			var body = {
				name: data.get("name") || "",
				project_type: data.get("project_type") || "new",
				email: data.get("email") || "",
				phone: data.get("phone") || "",
				contact_method: data.get("contact_method") || "email",
				website: data.get("website") || ""
			};
			fetch(messcutData.restUrl, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": messcutData.nonce
				},
				body: JSON.stringify(body)
			}).then(function (response) {
				return response.json().then(function (payload) {
					return { ok: response.ok, payload: payload };
				});
			}).then(function (result) {
				if (!result.ok) {
					var message = (result.payload && result.payload.message) || messcutData.errorSubmit;
					if (error) {
						error.hidden = false;
						error.textContent = message;
					}
					return;
				}
				if (error) error.hidden = true;
				var success = form.querySelector("[data-lead-success]");
				if (success) success.textContent = (result.payload && result.payload.message) || messcutData.successMsg;
				show(done);
			}).catch(function () {
				if (error) {
					error.hidden = false;
					error.textContent = messcutData.errorNetwork;
				}
			});
		});
	});
})();
