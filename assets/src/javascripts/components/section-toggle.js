export default function () {
	const btnToggleExpandAll = $('.btn-toggle-expand-all');
	const btnToggleExpand = $('.btn-toggle-expand');
	const btnToggleAutoHide = $('.btn-toggle-auto-hide');

	/**
	 * Toggle expand-collapse all group.
	 */
	btnToggleExpandAll.on('click', function () {
		const targetToggled = $(this).data('target');
		const button = $(this);

		// loop through the target and toggle it
		$(`.${targetToggled}`).each(function () {
			const target = $(this).data('target');
			if (button.hasClass("state-collapse")) {
				expand(target, this)
			} else {
				collapse(target, this);
			}
		});

		// toggle button itself
		if (button.hasClass("state-collapse")) {
			expand('', this);
		} else {
			collapse('', this);
		}
	});

	/**
	 * Toggle expand-collapse group.
	 */
	btnToggleExpand.on('click', function () {
		const target = $(this).data('target');

		if ($(this).hasClass("state-collapse")) {
			expand(target, this);
		} else {
			collapse(target, this);
		}
	});

	/**
	 * This event must registered after regular btn-toggle-expand.on(click),
	 * to trigger the latest state first.
	 */
	btnToggleExpandAll.each(function () {
		if ($(this).hasClass('btn-toggle-persist')) return;

		const targetToggled = $(`.${$(this).data('target')}`);
		const button = $(this);

		if (targetToggled.length === 0) {
			$(this).remove();
		}

		targetToggled.each(function () {
			$(this).on('click', function () {
				if (targetToggled.filter('.state-collapse').length === targetToggled.length) {
					collapse('', button);
				}
				if (targetToggled.filter(':not(.state-collapse)').length === targetToggled.length) {
					expand('', button);
				}
			});
		});
	});

	/**
	 * Expand target value and passing the button.
	 * @param target
	 * @param t
	 */
	function expand(target, t) {
		const btnId = $(t).data('id');

		$(t).removeClass('state-collapse');
		$(t).find('i').addClass('mdi-minus').removeClass('mdi-plus');
		$("[data-parent='" + target + "'").show();

		// sync another toggle button with same id
		if (btnId) {
			$(`.btn-toggle-expand[data-id="${btnId}"]`).not(this)
				.removeClass('state-collapse')
				.find('i')
				.addClass('mdi-minus')
				.removeClass('mdi-plus');
		}
	}

	/**
	 * Collapse target and children recursively.
	 * @param target
	 * @param t
	 */
	function collapse(target, t) {
		const element = $("[data-parent='" + target + "'");
		const btnId = $(t).data('id');

		$(t).addClass('state-collapse');
		$(t).find('i').addClass('mdi-plus').removeClass('mdi-minus');

		if (element.length && element.is(':visible')) {
			collapse(element.data('id'), element.find('.btn-toggle-expand').get(0));
			element.hide();

			// sync another toggle button with same id
			if (btnId) {
				$(`.btn-toggle-expand[data-id="${btnId}"]`).not(this)
					.addClass('state-collapse')
					.find('i')
					.addClass('mdi-plus')
					.removeClass('mdi-minus');
			}
		}
	}

	btnToggleAutoHide.addClass('visually-hidden');
	$('.toggle-row').on('mouseover', function () {
		const buttonTarget = $(this).data('button');
		$(`.btn-toggle-auto-hide[data-target="${buttonTarget}"]`).removeClass('visually-hidden');
	});
	$('.toggle-row').on('mouseout', function () {
		const buttonTarget = $(this).data('button');
		$(`.btn-toggle-auto-hide[data-target="${buttonTarget}"]`).addClass('visually-hidden');
	});
};
