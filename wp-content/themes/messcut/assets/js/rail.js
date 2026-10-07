/**
 * Chapter rail: the bead stays a dot. When the next section is reached it
 * stretches into a line, then springs back into a dot on that section.
 */
(function () {
	var rail = document.querySelector("[data-rail]");
	if (!rail) return;
	var selector = rail.getAttribute("data-rail");
	if (!selector) return;
	var sections = Array.prototype.slice.call(document.querySelectorAll(selector));
	if (!sections.length) return;

	var links = [];
	sections.forEach(function (section, index) {
		if (!section.id) section.id = "section-" + index;
		var link = document.createElement("a");
		var heading = section.querySelector("h1, h2, h3");
		var text = heading ? heading.textContent.replace(/\s+/g, " ").trim() : "";
		link.href = "#" + section.id;
		link.setAttribute("aria-label", text || ("Розділ " + (index + 1)));
		link.addEventListener("click", function (event) {
			if (event.defaultPrevented || event.button !== 0) return;
			if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
			event.preventDefault();
			var pad = parseFloat(window.getComputedStyle(document.documentElement).scrollPaddingTop) || 0;
			var top = section.getBoundingClientRect().top + window.scrollY - pad;
			window.scrollTo({
				top: Math.max(0, top),
				behavior: reduced.matches ? "instant" : "smooth"
			});
			if (window.history && window.history.pushState) {
				window.history.pushState(null, "", "#" + section.id);
			}
		});
		rail.appendChild(link);
		links.push(link);
	});

	var bead = document.createElement("span");
	bead.className = "chapter-rail__bead";
	bead.setAttribute("aria-hidden", "true");
	rail.appendChild(bead);

	var reduced = window.matchMedia("(prefers-reduced-motion: reduce)");
	var stretchK = 320;
	var stretchC = 24;
	var catchK = 200;
	var catchC = 18;
	var tops = [];
	var dot = 6;
	var head = 0;
	var tail = 0;
	var headTarget = 0;
	var tailTarget = 0;
	var headV = 0;
	var tailV = 0;
	var phase = "idle";
	var activeIndex = 0;
	var running = false;
	var booting = true;
	var last = 0;
	var raf = 0;
	var scrollQueued = false;

	function measure() {
		dot = links[0].offsetHeight || 6;
		tops = links.map(function (link) {
			return link.offsetTop;
		});
	}

	function render() {
		var speed = Math.max(Math.abs(headV), Math.abs(tailV));
		var squash = reduced.matches ? 1 : 1 - Math.min(0.2, speed / 500);
		var height = head - tail;
		if (height < 4) height = 4;
		bead.style.height = height + "px";
		bead.style.transform = "translate3d(0," + tail + "px,0) scaleX(" + squash + ")";
	}

	function place(index) {
		activeIndex = index;
		tail = tailTarget = tops[index];
		head = headTarget = tops[index] + dot;
		headV = 0;
		tailV = 0;
		phase = "idle";
		running = false;
		if (raf) window.cancelAnimationFrame(raf);
		raf = 0;
		render();
	}

	function playTo(index) {
		activeIndex = index;
		var destTop = tops[index];
		var destBottom = destTop + dot;
		if (destBottom > head + 0.5) {
			phase = "stretch-down";
			headTarget = destBottom;
			tailV = 0;
		} else if (destTop < tail - 0.5) {
			phase = "stretch-up";
			tailTarget = destTop;
			headV = 0;
		} else {
			phase = "catch";
			tailTarget = destTop;
			headTarget = destBottom;
		}
		if (!running) {
			running = true;
			last = 0;
			raf = window.requestAnimationFrame(tick);
		}
	}

	function tick(now) {
		if (!last) {
			last = now;
			raf = window.requestAnimationFrame(tick);
			return;
		}
		var dt = Math.min(0.032, (now - last) / 1000);
		last = now;
		var k = phase === "catch" ? catchK : stretchK;
		var c = phase === "catch" ? catchC : stretchC;

		if (phase === "stretch-down" || phase === "catch") {
			headV += (k * (headTarget - head) - c * headV) * dt;
			head += headV * dt;
		}
		if (phase === "stretch-up" || phase === "catch") {
			tailV += (k * (tailTarget - tail) - c * tailV) * dt;
			tail += tailV * dt;
		}

		if (phase === "stretch-down" && head >= headTarget) {
			head = headTarget;
			headV = 0;
			phase = "catch";
			tailTarget = tops[activeIndex];
			headTarget = tops[activeIndex] + dot;
		} else if (phase === "stretch-up" && tail <= tailTarget) {
			tail = tailTarget;
			tailV = 0;
			phase = "catch";
			tailTarget = tops[activeIndex];
			headTarget = tops[activeIndex] + dot;
		}

		if (phase === "catch" && Math.abs(headTarget - head) < 0.15 && Math.abs(tailTarget - tail) < 0.15 && Math.abs(headV) < 2 && Math.abs(tailV) < 2) {
			place(activeIndex);
			return;
		}
		render();
		raf = window.requestAnimationFrame(tick);
	}

	function currentIndex() {
		var trigger = window.innerHeight * 0.45;
		var active = 0;
		sections.forEach(function (section, index) {
			if (section.getBoundingClientRect().top < trigger) active = index;
		});
		return active;
	}

	function mark(active) {
		links.forEach(function (link, index) {
			link.classList.toggle("is-done", index < active);
			if (index === active) link.setAttribute("aria-current", "true");
			else link.removeAttribute("aria-current");
		});
	}

	function update() {
		var active = currentIndex();
		mark(active);
		if (booting || reduced.matches) {
			place(active);
			return;
		}
		if (active !== activeIndex) playTo(active);
	}

	function onScroll() {
		if (scrollQueued) return;
		scrollQueued = true;
		window.requestAnimationFrame(function () {
			scrollQueued = false;
			update();
		});
	}

	measure();
	update();
	booting = false;
	rail.classList.add("is-on");

	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		measure();
		var active = currentIndex();
		mark(active);
		if (phase === "idle" || reduced.matches || active !== activeIndex) {
			place(active);
			return;
		}
		if (phase === "stretch-down") headTarget = tops[activeIndex] + dot;
		else if (phase === "stretch-up") tailTarget = tops[activeIndex];
		else {
			tailTarget = tops[activeIndex];
			headTarget = tops[activeIndex] + dot;
		}
	});
	reduced.addEventListener("change", function () {
		if (reduced.matches) place(currentIndex());
	});
})();
