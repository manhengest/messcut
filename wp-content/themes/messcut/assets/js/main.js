(function () {
	'use strict';

	var navToggle = document.querySelector('.nav-toggle');
	var primaryNav = document.getElementById('primary-navigation');
	var siteHeader = document.querySelector('.site-header');
	var navToggleLabel = navToggle ? navToggle.querySelector('.nav-toggle__label') : null;
	var navToggleOpenLabel = (window.messcutData && window.messcutData.navCloseLabel) || 'Закрити';
	var navToggleClosedLabel = (navToggleLabel && navToggleLabel.textContent) || 'Меню';

	function setNavOpen(isOpen) {
		if (!primaryNav || !navToggle) {
			return;
		}

		primaryNav.classList.toggle('is-open', isOpen);
		navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		document.body.classList.toggle('has-nav-open', isOpen);

		if (navToggleLabel) {
			navToggleLabel.textContent = isOpen ? navToggleOpenLabel : navToggleClosedLabel;
		}
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

	document.querySelectorAll('[data-lead-form]').forEach(function (form) {
		var steps = form.querySelectorAll('[data-lead-step]');
		var statusEl = form.querySelector('[data-lead-status]');
		var thanksEl = form.querySelector('[data-lead-thanks]');
		var brandField = form.querySelector('[data-lead-brand-field]');
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
			if (statusEl) {
				statusEl.hidden = true;
			}
		}

		function getProjectType() {
			var checked = form.querySelector('[name="project_type"]:checked');
			return checked ? checked.value : '';
		}

		function toggleBrandField() {
			if (!brandField) {
				return;
			}
			brandField.hidden = getProjectType() !== 'existing';
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

		form.querySelectorAll('[name="project_type"]').forEach(function (input) {
			input.addEventListener('change', toggleBrandField);
		});
		toggleBrandField();

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

			var stepEl = getStepEl(3);
			if (!validateStep(stepEl)) {
				showStep(3);
				return;
			}

			var submitBtn = form.querySelector('[type="submit"]');
			var payload = {
				name: form.querySelector('[name="name"]')?.value || '',
				project_type: getProjectType(),
				phone: form.querySelector('[name="phone"]')?.value || '',
				email: form.querySelector('[name="email"]')?.value || '',
				contact_method: form.querySelector('[name="contact_method"]:checked')?.value || '',
				brand: form.querySelector('[name="brand"]')?.value || '',
				message: form.querySelector('[name="message"]')?.value || '',
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
						toggleBrandField();
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
})();
