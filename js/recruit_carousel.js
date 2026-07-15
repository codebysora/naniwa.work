(function () {
	function initCarousel(root) {
		var viewport = root.querySelector('.recruit-carousel__viewport');
		var track = root.querySelector('.recruit-carousel__track');
		var slides = Array.prototype.slice.call(root.querySelectorAll('.recruit-carousel__slide'));
		var prevBtn = root.querySelector('.recruit-carousel__btn--prev');
		var nextBtn = root.querySelector('.recruit-carousel__btn--next');
		var dotsWrap = root.querySelector('.recruit-carousel__dots');
		if (!viewport || !track || slides.length === 0) return;

		var index = 0;
		var autoplayMs = 4500;
		var timer = null;
		var startX = 0;
		var deltaX = 0;
		var dragging = false;

		function perView() {
			return window.matchMedia('(max-width: 980px)').matches ? 1 : 2;
		}

		function maxIndex() {
			return Math.max(0, slides.length - perView());
		}

		function goTo(i, animate) {
			index = Math.max(0, Math.min(i, maxIndex()));
			if (animate === false) {
				track.style.transition = 'none';
			} else {
				track.style.transition = 'transform 0.45s ease';
			}
			var offset = index * (100 / perView());
			track.style.transform = 'translateX(-' + offset + '%)';
			updateDots();
		}

		function buildDots() {
			if (!dotsWrap) return;
			dotsWrap.innerHTML = '';
			var n = maxIndex() + 1;
			for (var i = 0; i < n; i++) {
				(function (di) {
					var btn = document.createElement('button');
					btn.type = 'button';
					btn.setAttribute('aria-label', 'スライド ' + (di + 1));
					btn.addEventListener('click', function () {
						goTo(di);
						restartAutoplay();
					});
					dotsWrap.appendChild(btn);
				})(i);
			}
			updateDots();
		}

		function updateDots() {
			if (!dotsWrap) return;
			var buttons = dotsWrap.querySelectorAll('button');
			for (var i = 0; i < buttons.length; i++) {
				buttons[i].classList.toggle('is-active', i === index);
			}
		}

		function next() {
			if (index >= maxIndex()) {
				goTo(0);
			} else {
				goTo(index + 1);
			}
		}

		function prev() {
			if (index <= 0) {
				goTo(maxIndex());
			} else {
				goTo(index - 1);
			}
		}

		function startAutoplay() {
			stopAutoplay();
			timer = setInterval(next, autoplayMs);
		}

		function stopAutoplay() {
			if (timer) {
				clearInterval(timer);
				timer = null;
			}
		}

		function restartAutoplay() {
			stopAutoplay();
			startAutoplay();
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				prev();
				restartAutoplay();
			});
		}
		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				next();
				restartAutoplay();
			});
		}

		viewport.addEventListener('touchstart', function (e) {
			if (!e.touches || !e.touches[0]) return;
			dragging = true;
			startX = e.touches[0].clientX;
			deltaX = 0;
			stopAutoplay();
			track.style.transition = 'none';
		}, { passive: true });

		viewport.addEventListener('touchmove', function (e) {
			if (!dragging || !e.touches || !e.touches[0]) return;
			deltaX = e.touches[0].clientX - startX;
			var base = -(index * (100 / perView()));
			var pct = (deltaX / viewport.clientWidth) * (100 / perView());
			track.style.transform = 'translateX(' + (base + pct) + '%)';
		}, { passive: true });

		viewport.addEventListener('touchend', function () {
			if (!dragging) return;
			dragging = false;
			var threshold = viewport.clientWidth * 0.18;
			if (deltaX < -threshold) {
				next();
			} else if (deltaX > threshold) {
				prev();
			} else {
				goTo(index);
			}
			restartAutoplay();
		});

		window.addEventListener('resize', function () {
			buildDots();
			goTo(Math.min(index, maxIndex()), false);
		});

		root.addEventListener('mouseenter', stopAutoplay);
		root.addEventListener('mouseleave', startAutoplay);

		buildDots();
		goTo(0, false);
		startAutoplay();
	}

	function boot() {
		var carousels = document.querySelectorAll('.recruit-carousel');
		for (var i = 0; i < carousels.length; i++) {
			initCarousel(carousels[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
