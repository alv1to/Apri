

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('input', event => {
	const range = event.target.closest('[data-order-status-range]');
	if (!range) return;

	const track = range.closest('[data-order-status-swipe]');
	const progress = Number(range.value) / Number(range.max);
	track.style.setProperty('--swipe-progress', progress);

	if (progress >= 0.95) {
		range.disabled = true;
		track.classList.add('is-submitting');
		range.closest('form').requestSubmit();
	}
});

document.addEventListener('change', event => {
	const range = event.target.closest('[data-order-status-range]');
	if (!range || range.disabled) return;

	range.value = 0;
	range.closest('[data-order-status-swipe]').style.setProperty('--swipe-progress', 0);
});
