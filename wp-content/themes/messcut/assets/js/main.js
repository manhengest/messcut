(function () {
	'use strict';

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

		toggle.addEventListener('click', function () {
			var isExpanded = wrap.classList.toggle('is-expanded');
			toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
			var icon = toggle.querySelector('.card__excerpt-toggle-icon');
			if (icon) {
				icon.textContent = isExpanded ? '−' : '+';
			}
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

		var section = track.closest('.partner-logos');
		var wrap = track.parentElement;
		var group = track.querySelector('.partner-logos__group');
		if (!section || !wrap || !group) {
			return;
		}

		var paused = false;
		var running = false;
		var offset = 0;
		var last = 0;
		var speed = 70;

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
			section.classList.add('is-marquee-ready');
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
				if (isNarrow()) {
					pill.style.left = '';
					pill.style.top = '';
				}
			});
		}

		function getServiceEls() {
			return painFunnel.querySelectorAll('[data-pain-service]');
		}

		function openMatchingService(target) {
			var accordionItem = document.querySelector(
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

		function randomPointInTriangle(width, height) {
			var r1 = Math.sqrt(Math.random());
			var r2 = Math.random();
			return {
				x: (1 - r1) * 0 + r1 * (1 - r2) * width + r1 * r2 * (width / 2),
				y: (1 - r1) * 0 + r1 * (1 - r2) * 0 + r1 * r2 * height
			};
		}

		function isInInvertedTriangle(x, y, width, height) {
			if (y < 0 || y > height) {
				return false;
			}
			var half = (width / 2) * (1 - y / height);
			var cx = width / 2;
			return x >= cx - half && x <= cx + half;
		}

		function rectInTriangle(rect, width, height, inset) {
			var maxY = height * 0.78;
			var corners = [
				[rect.left - inset, rect.top - inset],
				[rect.left + rect.w + inset, rect.top - inset],
				[rect.left - inset, rect.top + rect.h + inset],
				[rect.left + rect.w + inset, rect.top + rect.h + inset]
			];
			return corners.every(function (point) {
				return point[1] <= maxY && isInInvertedTriangle(point[0], point[1], width, height);
			});
		}

		function rectsOverlap(a, b, gap) {
			return !(
				a.left + a.w + gap <= b.left ||
				b.left + b.w + gap <= a.left ||
				a.top + a.h + gap <= b.top ||
				b.top + b.h + gap <= a.top
			);
		}

		function assignDrift(pill) {
			if (reduceMotion || pill.hasAttribute('data-pain-drift')) {
				return;
			}
			var dx = (4 + Math.random() * 4) * (Math.random() < 0.5 ? -1 : 1);
			var dy = (4 + Math.random() * 4) * (Math.random() < 0.5 ? -1 : 1);
			pill.style.setProperty('--dx', dx.toFixed(1) + 'px');
			pill.style.setProperty('--dy', dy.toFixed(1) + 'px');
			pill.style.setProperty('--drift-dur', (6 + Math.random() * 4).toFixed(1) + 's');
			pill.style.setProperty('--drift-delay', (Math.random() * 3).toFixed(2) + 's');
			pill.setAttribute('data-pain-drift', '');
		}

		function layoutPills(force) {
			if (!vessel) {
				return;
			}

			prepareMobilePills();
			if (isNarrow()) {
				vessel.classList.add('is-ready');
				return;
			}

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

			var placed = [];
			var gaps = [10, 6, 2, 0];

			items.forEach(function (item) {
				var found = null;
				var last = null;
				for (var g = 0; g < gaps.length && !found; g++) {
					var gap = gaps[g];
					for (var i = 0; i < 40; i++) {
						var point = randomPointInTriangle(width, height);
						var rect = {
							left: point.x - item.w / 2,
							top: point.y - item.h / 2,
							w: item.w,
							h: item.h
						};
						last = rect;
						if (!rectInTriangle(rect, width, height, 12)) {
							continue;
						}
						var hits = placed.some(function (other) {
							return rectsOverlap(rect, other, gap);
						});
						if (!hits) {
							found = rect;
							break;
						}
					}
				}
				if (!found) {
					found = last || {
						left: (width - item.w) / 2,
						top: height * 0.2,
						w: item.w,
						h: item.h
					};
				}
				placed.push(found);
				item.el.style.left = Math.round(found.left) + 'px';
				item.el.style.top = Math.round(found.top) + 'px';
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
			});
		}

		function activatePain(target) {
			pills.forEach(function (pill) {
				var isActive = pill.getAttribute('data-pain-target') === target;
				pill.setAttribute('aria-pressed', isActive ? 'true' : 'false');
			});

			getServiceEls().forEach(function (service) {
				var isActive = service.getAttribute('data-pain-service') === target;
				service.classList.toggle('is-active', isActive);
				service.classList.toggle('is-dimmed', !isActive);
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
