(function () {
	'use strict';

	function compareTabsRoot(from) {
		return from && from.closest ? from.closest('[data-compare-tabs]') : null;
	}

	function compareTabsActivate(root, nextTab, focus) {
		if (!root || !nextTab) {
			return;
		}

		var tabs = root.querySelectorAll('[role="tab"]');
		var panels = root.querySelectorAll('[role="tabpanel"]');
		var controls = nextTab.getAttribute('aria-controls');

		tabs.forEach(function (tab) {
			var selected = tab === nextTab;
			tab.setAttribute('aria-selected', selected ? 'true' : 'false');
			tab.tabIndex = selected ? 0 : -1;
		});
		panels.forEach(function (panel) {
			panel.hidden = panel.id !== controls;
		});
		if (focus) {
			nextTab.focus();
		}
	}

	document.addEventListener('click', function (event) {
		var tab = event.target.closest ? event.target.closest('[data-compare-tab]') : null;
		var root = compareTabsRoot(tab);
		if (!tab || !root) {
			return;
		}
		compareTabsActivate(root, tab, false);
	});

	document.addEventListener('keydown', function (event) {
		var tab = event.target.closest ? event.target.closest('[data-compare-tab]') : null;
		var root = compareTabsRoot(tab);
		if (!tab || !root) {
			return;
		}

		var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
		var index = tabs.indexOf(tab);
		if (index < 0) {
			return;
		}

		var offset = 0;
		if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
			offset = 1;
		} else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
			offset = -1;
		} else if (event.key === 'Home') {
			event.preventDefault();
			compareTabsActivate(root, tabs[0], true);
			return;
		} else if (event.key === 'End') {
			event.preventDefault();
			compareTabsActivate(root, tabs[tabs.length - 1], true);
			return;
		} else {
			return;
		}

		event.preventDefault();
		compareTabsActivate(root, tabs[(index + offset + tabs.length) % tabs.length], true);
	});

	var navToggle = document.querySelector('.nav-toggle');
	var primaryNav = document.getElementById('primary-navigation');
	var siteHeader = document.querySelector('.site-header');

	function setNavOpen(isOpen) {
		if (!primaryNav || !navToggle) {
			return;
		}

		primaryNav.classList.toggle('is-open', isOpen);
		navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		document.body.classList.toggle('has-nav-open', isOpen);
	}

	function closeNav() {
		setNavOpen(false);
	}

	if (navToggle && primaryNav) {
		navToggle.addEventListener('click', function () {
			setNavOpen(!primaryNav.classList.contains('is-open'));
		});

		primaryNav.querySelectorAll('a').forEach(function (link) {
			link.addEventListener('click', closeNav);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeNav();
			}
		});
	}

	if (siteHeader) {
		var onScroll = function () {
			siteHeader.classList.toggle('is-scrolled', window.scrollY > 48);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	var heroVideo = document.querySelector('[data-hero-video]');
	if (heroVideo) {
		var mobileQuery = window.matchMedia('(max-width: 767.98px)');
		var reduceQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
		var inView = true;

		function heroVideoSrc() {
			return heroVideo.getAttribute('data-src') || '';
		}

		function shouldPlayHeroVideo() {
			return inView && mobileQuery.matches && !reduceQuery.matches && !!heroVideoSrc();
		}

		function enableHeroVideo() {
			var src = heroVideoSrc();
			if (!src) {
				return;
			}
			if (heroVideo.getAttribute('src') !== src) {
				heroVideo.setAttribute('src', src);
				heroVideo.load();
			}
			if (shouldPlayHeroVideo()) {
				heroVideo.play().catch(function () {});
			}
		}

		function disableHeroVideo() {
			heroVideo.pause();
			if (heroVideo.getAttribute('src')) {
				heroVideo.removeAttribute('src');
				heroVideo.load();
			}
		}

		function syncHeroVideo() {
			if (shouldPlayHeroVideo()) {
				enableHeroVideo();
				return;
			}
			heroVideo.pause();
			if (!mobileQuery.matches || reduceQuery.matches) {
				disableHeroVideo();
			}
		}

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					inView = entry.isIntersecting;
					syncHeroVideo();
				});
			}, { threshold: 0.25 }).observe(heroVideo);
		}

		function onHeroQueryChange() {
			syncHeroVideo();
		}

		if (mobileQuery.addEventListener) {
			mobileQuery.addEventListener('change', onHeroQueryChange);
			reduceQuery.addEventListener('change', onHeroQueryChange);
		} else if (mobileQuery.addListener) {
			mobileQuery.addListener(onHeroQueryChange);
			reduceQuery.addListener(onHeroQueryChange);
		}

		syncHeroVideo();
	}

	document.querySelectorAll('[data-lead-form]').forEach(function (form) {
		var steps = form.querySelectorAll('[data-lead-step]');
		var statusEl = form.querySelector('[data-lead-status]');
		var thanksEl = form.querySelector('[data-lead-thanks]');
		var currentStep = 1;

		function getStepEl(step) {
			return form.querySelector('[data-lead-step="' + step + '"]');
		}

		function showStep(step) {
			steps.forEach(function (el) {
				var elStep = el.getAttribute('data-lead-step');
				var isActive = String(step) === elStep;
				el.hidden = !isActive;
				el.classList.toggle('is-active', isActive);
			});
			currentStep = step;
			form.setAttribute('data-step', String(step));
			updateProgress(step);
			if (statusEl) {
				statusEl.hidden = true;
			}
		}

		function updateProgress(step) {
			var segs = form.querySelectorAll('[data-lead-progress]');
			var stepNum = step === 'thanks' ? 3 : Number(step);
			segs.forEach(function (seg) {
				var n = Number(seg.getAttribute('data-lead-progress'));
				seg.classList.toggle('is-complete', n < stepNum || step === 'thanks');
				seg.classList.toggle('is-current', n === stepNum && step !== 'thanks');
			});
		}

		function getProjectType() {
			var checked = form.querySelector('[name="project_type"]:checked');
			return checked ? checked.value : '';
		}

		function validateStep(stepEl) {
			if (!stepEl) {
				return false;
			}

			var fields = stepEl.querySelectorAll('input, select, textarea');
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				if (field.disabled || field.closest('[hidden]')) {
					continue;
				}
				if (!field.checkValidity()) {
					field.reportValidity();
					return false;
				}
			}

			return true;
		}

		form.querySelectorAll('[data-lead-next]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var stepEl = getStepEl(currentStep);
				if (!validateStep(stepEl)) {
					return;
				}
				if (currentStep === 1 && !getProjectType()) {
					showStatus(statusEl, 'error', (window.messcutData && window.messcutData.errorRequired) || 'Заповніть обовʼязкові поля.');
					return;
				}
				showStep(currentStep + 1);
			});
		});

		form.querySelectorAll('[data-lead-prev]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (currentStep > 1) {
					showStep(currentStep - 1);
				}
			});
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			if (!window.messcutData) {
				return;
			}

			var stepEl = getStepEl(2);
			if (!validateStep(stepEl)) {
				showStep(2);
				return;
			}

			var submitBtn = form.querySelector('[type="submit"]');
			var payload = {
				name: form.querySelector('[name="name"]')?.value || '',
				project_type: getProjectType(),
				phone: form.querySelector('[name="phone"]')?.value || '',
				email: form.querySelector('[name="email"]')?.value || '',
				contact_method: form.querySelector('[name="contact_method"]:checked')?.value || '',
				brand: '',
				message: '',
				website: form.querySelector('[name="website"]')?.value || '',
			};

			if (!payload.name.trim() || !payload.phone.trim() || !payload.email.trim() || !payload.project_type || !payload.contact_method) {
				showStatus(statusEl, 'error', window.messcutData.errorRequired || 'Заповніть обовʼязкові поля.');
				return;
			}

			if (submitBtn) {
				submitBtn.disabled = true;
			}

			fetch(window.messcutData.restUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.messcutData.nonce,
				},
				body: JSON.stringify(payload),
			})
				.then(function (response) {
					return response.json().then(function (data) {
						return { ok: response.ok, data: data };
					});
				})
				.then(function (result) {
					if (result.ok && result.data && result.data.success) {
						form.reset();
						var message = result.data.message || window.messcutData.successMsg;
						if (thanksEl) {
							thanksEl.textContent = message;
						}
						showStep('thanks');
						return;
					}

					var message =
						(result.data && result.data.message) ||
						window.messcutData.errorSubmit ||
						'Не вдалося надіслати заявку. Спробуйте пізніше.';
					showStatus(statusEl, 'error', message);
				})
				.catch(function () {
					showStatus(statusEl, 'error', window.messcutData.errorNetwork || 'Помилка мережі. Спробуйте пізніше.');
				})
				.finally(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
					}
				});
		});
	});

	function showStatus(el, type, message) {
		if (!el) {
			return;
		}
		el.hidden = false;
		el.textContent = message;
		el.classList.remove('is-success', 'is-error');
		el.classList.add(type === 'success' ? 'is-success' : 'is-error');
	}

	document.querySelectorAll('[data-case-excerpt]').forEach(function (wrap) {
		var toggle = wrap.querySelector('[data-case-excerpt-toggle]');
		if (!toggle) {
			return;
		}

		toggle.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			var isExpanded = wrap.classList.toggle('is-expanded');
			toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
		});
	});

	document.querySelectorAll('[data-accordion]').forEach(function (list) {
		var items = list.querySelectorAll('details');

		items.forEach(function (item) {
			var summary = item.querySelector('summary');
			if (!summary) {
				return;
			}

			summary.addEventListener('click', function (event) {
				event.preventDefault();
				var willOpen = !item.open;
				if (willOpen) {
					items.forEach(function (other) {
						if (other !== item) {
							other.open = false;
						}
					});
				}
				item.open = willOpen;
			});
		});
	});

	document.querySelectorAll('[data-marquee]').forEach(function (track) {
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}

		var root = track.closest('[data-marquee-root]') || track.closest('.partner-logos');
		var wrap = track.parentElement;
		var group = track.querySelector('[data-marquee-group]') || track.querySelector('.partner-logos__group');
		if (!root || !wrap || !group) {
			return;
		}

		var paused = false;
		var running = false;
		var offset = 0;
		var last = 0;
		var speed = root.classList.contains('path__ticker') ? 48 : 70;

		function fillTrack() {
			var copies = 0;
			while (track.scrollWidth < wrap.clientWidth * 2 && copies < 6) {
				track.appendChild(group.cloneNode(true));
				copies += 1;
			}
		}

		function tick(now) {
			if (!running) {
				return;
			}

			if (!last) {
				last = now;
			}

			var dt = (now - last) / 1000;
			last = now;
			if (dt > 0.05) {
				dt = 0.05;
			}

			if (!paused) {
				var width = group.offsetWidth;
				if (width > 0) {
					offset += speed * dt;
					if (offset >= width) {
						offset -= width;
					}
					track.style.transform = 'translate3d(' + (-offset).toFixed(2) + 'px,0,0)';
				}
			}

			window.requestAnimationFrame(tick);
		}

		function start() {
			fillTrack();
			root.classList.add('is-marquee-ready');
			if (running) {
				return;
			}
			running = true;
			last = 0;
			window.requestAnimationFrame(tick);
		}

		wrap.addEventListener('mouseenter', function () {
			paused = true;
		});
		wrap.addEventListener('mouseleave', function () {
			paused = false;
			last = 0;
		});

		var pending = 0;
		track.querySelectorAll('img').forEach(function (img) {
			if (img.complete) {
				return;
			}
			pending += 1;
			img.addEventListener('load', onImageReady, { once: true });
			img.addEventListener('error', onImageReady, { once: true });
		});

		function onImageReady() {
			pending -= 1;
			if (pending <= 0) {
				start();
			}
		}

		if (pending === 0) {
			start();
		} else {
			window.setTimeout(start, 2000);
		}

		window.addEventListener('resize', fillTrack);
	});

	document.querySelectorAll('[data-cases-loop]').forEach(function (track) {
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}

		var originals = Array.prototype.slice.call(track.children);
		if (originals.length < 2) {
			return;
		}

		var mq = window.matchMedia('(max-width: 767.98px)');
		var primed = false;
		var jumping = false;

		function teardown() {
			track.querySelectorAll('[data-cases-clone]').forEach(function (node) {
				node.remove();
			});
			primed = false;
		}

		function setWidth() {
			var firstReal = originals[0];
			var firstEnd = track.querySelector('[data-cases-clone="end"]');
			if (!firstReal || !firstEnd) {
				return 0;
			}
			return firstEnd.offsetLeft - firstReal.offsetLeft;
		}

		function jumpBy(delta) {
			if (!delta) {
				return;
			}
			jumping = true;
			var snap = track.style.scrollSnapType;
			track.style.scrollSnapType = 'none';
			track.scrollLeft += delta;
			window.requestAnimationFrame(function () {
				track.style.scrollSnapType = snap;
				jumping = false;
			});
		}

		function onScroll() {
			if (!primed || jumping || !mq.matches) {
				return;
			}

			var firstReal = originals[0];
			var firstEnd = track.querySelector('[data-cases-clone="end"]');
			var width = setWidth();
			if (!firstReal || !firstEnd || width <= 0) {
				return;
			}

			if (track.scrollLeft >= firstEnd.offsetLeft) {
				jumpBy(-width);
			} else if (track.scrollLeft < firstReal.offsetLeft - 2) {
				jumpBy(width);
			}
		}

		function setup() {
			teardown();
			if (!mq.matches) {
				return;
			}

			var startFrag = document.createDocumentFragment();
			var endFrag = document.createDocumentFragment();

			originals.forEach(function (node) {
				var startClone = node.cloneNode(true);
				startClone.setAttribute('data-cases-clone', 'start');
				startClone.setAttribute('aria-hidden', 'true');
				startClone.querySelectorAll('a').forEach(function (link) {
					link.setAttribute('tabindex', '-1');
				});
				startFrag.appendChild(startClone);

				var endClone = node.cloneNode(true);
				endClone.setAttribute('data-cases-clone', 'end');
				endClone.setAttribute('aria-hidden', 'true');
				endClone.querySelectorAll('a').forEach(function (link) {
					link.setAttribute('tabindex', '-1');
				});
				endFrag.appendChild(endClone);
			});

			track.insertBefore(startFrag, track.firstChild);
			track.appendChild(endFrag);

			jumping = true;
			track.style.scrollSnapType = 'none';
			track.scrollLeft = originals[0].offsetLeft;
			window.requestAnimationFrame(function () {
				track.style.scrollSnapType = '';
				jumping = false;
				primed = true;
			});
		}

		track.addEventListener('scroll', onScroll, { passive: true });
		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', setup);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(setup);
		}
		setup();
	});

	var painFunnel = document.querySelector('[data-pain-funnel]');
	if (painFunnel) {
		var vessel = painFunnel.querySelector('[data-pain-vessel]');
		var pills = painFunnel.querySelectorAll('[data-pain-pill]');
		var funnelServices = painFunnel.querySelectorAll('[data-pain-service]');
		var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var narrowQuery = window.matchMedia('(max-width: 639px)');
		var layoutTimer = 0;
		var lastWidth = 0;
		var lastHeight = 0;

		function isNarrow() {
			return narrowQuery.matches;
		}

		function prepareMobilePills() {
			var seen = {};
			pills.forEach(function (pill) {
				var key = (pill.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
				if (isNarrow() && seen[key]) {
					pill.hidden = true;
				} else {
					pill.hidden = false;
					seen[key] = true;
				}
			});
		}

		function getServiceEls() {
			return painFunnel.querySelectorAll('[data-pain-service]');
		}

		function openMatchingService(target) {
			var accordionItem = painFunnel.querySelector(
				'details[data-pain-service="' + target + '"]'
			) || document.querySelector(
				'.services-list__item[data-pain-service="' + target + '"]'
			);
			if (!accordionItem) {
				return;
			}
			var list = accordionItem.closest('[data-accordion]');
			if (list) {
				list.querySelectorAll('details').forEach(function (other) {
					if (other !== accordionItem) {
						other.open = false;
					}
				});
			}
			accordionItem.open = true;
		}

		function assignDrift(pill) {
			if (reduceMotion || pill.hasAttribute('data-pain-drift')) {
				return;
			}
			var dx = (5 + Math.random() * 6) * (Math.random() < 0.5 ? -1 : 1);
			var dy = (4 + Math.random() * 6) * (Math.random() < 0.5 ? -1 : 1);
			var dx2 = (4 + Math.random() * 5) * (Math.random() < 0.5 ? -1 : 1);
			var dy2 = (4 + Math.random() * 5) * (Math.random() < 0.5 ? -1 : 1);
			pill.style.setProperty('--dx', dx.toFixed(1) + 'px');
			pill.style.setProperty('--dy', dy.toFixed(1) + 'px');
			pill.style.setProperty('--dx2', dx2.toFixed(1) + 'px');
			pill.style.setProperty('--dy2', dy2.toFixed(1) + 'px');
			pill.style.setProperty('--drift-dur', (10 + Math.random() * 6).toFixed(1) + 's');
			pill.style.setProperty('--drift-delay', (-Math.random() * 9).toFixed(2) + 's');
			pill.setAttribute('data-jx', ((Math.random() - 0.5) * 12).toFixed(1));
			pill.setAttribute('data-jy', ((Math.random() - 0.5) * 10).toFixed(1));
			pill.setAttribute('data-pain-drift', '');
		}

		function triangleInner(y, width, height, pad) {
			var t = Math.min(1, Math.max(0, y / height));
			var half = (width / 2) * (1 - t * 0.88);
			return {
				left: width / 2 - half + pad,
				right: width / 2 + half - pad
			};
		}

		function layoutPills(force) {
			if (!vessel) {
				return;
			}

			prepareMobilePills();

			var width = vessel.clientWidth;
			var height = vessel.clientHeight;
			if (width < 40 || height < 40) {
				return;
			}
			if (!force && width === lastWidth && height === lastHeight && vessel.classList.contains('is-ready')) {
				return;
			}
			lastWidth = width;
			lastHeight = height;

			var items = Array.prototype.filter.call(pills, function (pill) {
				return !pill.hidden;
			}).map(function (pill) {
				assignDrift(pill);
				return {
					el: pill,
					w: Math.max(pill.offsetWidth, 32),
					h: Math.max(pill.offsetHeight, 24)
				};
			});

			if (!items.length) {
				vessel.classList.add('is-ready');
				return;
			}

			var hGap = width < 640 ? 28 : 36;
			var pad = Math.max(12, width * 0.04);
			var topPad = height * 0.05;
			var bottomLimit = height * 0.76;
			var maxPerRow = width < 600 ? 2 : width < 900 ? 3 : 4;
			var remaining = items.slice();
			var rows = [];
			var probeY = topPad;

			while (remaining.length) {
				var bounds = triangleInner(probeY, width, height, pad);
				var avail = Math.max(64, bounds.right - bounds.left);
				var row = [];
				var rowW = 0;
				while (remaining.length) {
					var item = remaining[0];
					var next = row.length === 0 ? item.w : rowW + hGap + item.w;
					if (row.length && (next > avail || row.length >= maxPerRow)) {
						break;
					}
					row.push(remaining.shift());
					rowW = next;
				}
				if (!row.length) {
					row.push(remaining.shift());
					rowW = row[0].w;
				}
				var rowH = row[0].h;
				for (var r = 1; r < row.length; r++) {
					if (row[r].h > rowH) {
						rowH = row[r].h;
					}
				}
				rows.push({ items: row, w: rowW, h: rowH });
				probeY += rowH + 28;
			}

			var contentH = 0;
			for (var c = 0; c < rows.length; c++) {
				contentH += rows[c].h;
			}
			var slack = Math.max(0, bottomLimit - topPad - contentH);
			var rowGap = rows.length > 1 ? slack / (rows.length - 0.15) : slack * 0.4;
			rowGap = Math.max(width < 640 ? 22 : 28, rowGap);

			var y = topPad + rowGap * 0.2;
			rows.forEach(function (row) {
				var placeBounds = triangleInner(y + row.h / 2, width, height, pad);
				var placeAvail = Math.max(row.w, placeBounds.right - placeBounds.left);
				var innerGap = hGap;
				if (row.items.length > 1) {
					var extra = Math.max(0, placeAvail - row.w);
					innerGap = hGap + extra * 0.62 / (row.items.length - 1);
				}
				var used = row.w + (innerGap - hGap) * Math.max(0, row.items.length - 1);
				var startX = placeBounds.left + Math.max(0, (placeBounds.right - placeBounds.left - used) / 2);
				var x = startX;
				row.items.forEach(function (pillItem) {
					var jx = Number(pillItem.el.getAttribute('data-jx') || 0);
					var jy = Number(pillItem.el.getAttribute('data-jy') || 0);
					pillItem.el.style.left = Math.round(x + jx) + 'px';
					pillItem.el.style.top = Math.round(y + jy) + 'px';
					x += pillItem.w + innerGap;
				});
				y += row.h + rowGap;
			});

			vessel.classList.add('is-ready');
		}

		function requestLayout() {
			window.clearTimeout(layoutTimer);
			layoutTimer = window.setTimeout(layoutPills, 100);
		}

		function clearPain() {
			pills.forEach(function (pill) {
				pill.setAttribute('aria-pressed', 'false');
			});
			getServiceEls().forEach(function (service) {
				service.classList.remove('is-active', 'is-dimmed');
				if (service.tagName === 'DETAILS') {
					service.open = false;
				}
			});
		}

		function syncPills(target) {
			pills.forEach(function (pill) {
				var isActive = !!target && pill.getAttribute('data-pain-target') === target;
				pill.setAttribute('aria-pressed', isActive ? 'true' : 'false');
			});
		}

		function activatePain(target) {
			syncPills(target);

			getServiceEls().forEach(function (service) {
				var isActive = service.getAttribute('data-pain-service') === target;
				service.classList.toggle('is-active', isActive);
				service.classList.toggle('is-dimmed', !isActive && service.matches('.pain-funnel__service'));
			});

			openMatchingService(target);
		}

		pills.forEach(function (pill) {
			pill.addEventListener('click', function () {
				var target = pill.getAttribute('data-pain-target');
				if (!target) {
					return;
				}
				if (pill.getAttribute('aria-pressed') === 'true') {
					clearPain();
					return;
				}
				activatePain(target);
			});
		});

		funnelServices.forEach(function (service) {
			if (service.tagName === 'DETAILS') {
				service.addEventListener('toggle', function () {
					if (service.open) {
						syncPills(service.getAttribute('data-pain-service'));
						return;
					}
					if (!painFunnel.querySelector('details[data-pain-service][open]')) {
						pills.forEach(function (pill) {
							pill.setAttribute('aria-pressed', 'false');
						});
						getServiceEls().forEach(function (el) {
							el.classList.remove('is-active', 'is-dimmed');
						});
					}
				});
				return;
			}

			service.addEventListener('click', function (event) {
				if (event.target.closest('a')) {
					return;
				}
				var target = service.getAttribute('data-pain-service');
				if (target) {
					activatePain(target);
				}
			});
		});

		if (vessel && window.ResizeObserver) {
			new ResizeObserver(requestLayout).observe(vessel);
		} else {
			window.addEventListener('resize', requestLayout);
		}

		if (narrowQuery.addEventListener) {
			narrowQuery.addEventListener('change', function () {
				layoutPills(true);
			});
		} else if (narrowQuery.addListener) {
			narrowQuery.addListener(function () {
				layoutPills(true);
			});
		}

		layoutPills(true);
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(function () {
				layoutPills(true);
			});
		}
	}
})();
