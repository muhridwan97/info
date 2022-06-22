import showAlert from "../components/alert";

$(function () {
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $("#classroom-wrapper").toggleClass("toggled");
    });

    const body = $('body');
    // On click, capture state and save it in localStorage
    if ($(window).width() > 992) {
        if (localStorage.getItem('sidebar-closed') === '1') {
            body.addClass("sidebar-icon-only");
        }
    }

    // sidebar submenu
    const sidebar = $('.sidebar');
    sidebar.on('show.bs.collapse', '.collapse', function () {
        sidebar.find('.collapse.show').collapse('hide');
    });

    // change sidebar
    $('[data-toggle="minimize"]').on("click", function () {
        body.toggleClass('sidebar-icon-only');
        localStorage.setItem('finance-sidebar-closed', body.hasClass('sidebar-icon-only') ? 1 : 0);
    });

    // checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');

    // initialize date picker
	function initDatepicker() {
		const dateFormat = 'DD MMMM YYYY';
		const fullDateFormat = 'DD MMMM YYYY';
		$('.datepicker:not([readonly])').each(function () {
			const options = {
				singleDatePicker: true,
				showDropdowns: true,
				drops: $(this).closest('#modal-filter').length ? 'up' : 'down',
				parentEl: $(this).closest('#modal-filter').length ? '#modal-filter' : 'body',
				autoUpdateInput: false,
				locale: {
					format: dateFormat
				},
			};
			if ($(this).data('min-date')) {
				options.minDate = $(this).data('min-date');
			}
			if ($(this).data('max-date')) {
				options.maxDate = $(this).data('max-date');
			}
			if ($(this).data('parent-el')) {
				options.parentEl = $(this).data('parent-el');
			}
			if ($(this).data('locale-format')) {
				options.locale.format = $(this).data('locale-format');
			}
			if ($(this).data('drops')) {
				options.drops = $(this).data('drops');
			}
			$(this).daterangepicker(options)
				.on("apply.daterangepicker", function (e, picker) {
					picker.element.val(picker.startDate.format(picker.locale.format));

					// update another picker element
					const updateTarget = $(picker.element).data('update-target');
					const updateMin = $(picker.element).data('update-min');
					if (updateTarget) {
						const pickedDate = moment(picker.startDate, picker.locale.format); // must be moment object
						if (updateMin) {
							if (Number(updateMin) > 0) {
								pickedDate.add(Number(updateMin || 0), 'd');
							} else if (Number(updateMin) < 0) {
								pickedDate.subtract(Number(updateMin.toString().replace(/[^0-9]/g, "") || 0), 'd');
							}
						}
						$(updateTarget).data('daterangepicker').minDate = pickedDate;

						// set blank target if minimum date greater than current value
						if (pickedDate.isAfter(moment($(updateTarget).val(), dateFormat))) {
							$(updateTarget).val('').focus();
						}
					}
				})
				.on("hide.daterangepicker", function (e, picker) {
					setTimeout(function () {
						const formattedDate = $(picker.element).closest('.form-group').find('.formatted-date');
						if (picker.element.val()) {
							formattedDate.text(picker.startDate.format(fullDateFormat));
						} else {
							formattedDate.text('(Pick a date)');
						}
					}, 150);
				});
		});
	}
	initDatepicker();
	window.initDatepicker = initDatepicker;

    $('form .datepicker').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

	$('form').on('focus', 'input[type=number]', function (e) {
		$(this).on('wheel.disableScroll', function (e) {
			e.preventDefault()
		})
	})
	$('form').on('blur', 'input[type=number]', function (e) {
		$(this).off('wheel.disableScroll')
	})

    // Select2
    const selects = $('.select2');
    selects.each(function (index, select) {
        $(select).select2({
            minimumResultsForSearch: 10,
            placeholder: 'Select data'
        }).on("select2:open", function () {
            $(".select2-search__field").attr("placeholder", "Search...");
        }).on("select2:close", function () {
            $(".select2-search__field").attr("placeholder", null);
        });
    });

    // Tooltip
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });

    // Popover
    $('[data-toggle="popover"]').popover();

    // Clickable
    $('[data-clickable=true]').on('click', function () {
        window.location.href = $(this).data('url');
    });

	$(document).on('click', '.btn-fullscreen', function () {
		const container = $($(this).data('target'));
		if(container.length) {
			const isFullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
			if (isFullscreen) {
				if (document.exitFullscreen) {
					document.exitFullscreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.webkitExitFullscreen) {
					document.webkitExitFullscreen();
				} else if (document.msExitFullscreen) {
					document.msExitFullscreen();
				}
			} else {
				const element = $(container).get(0);
				if (element.requestFullscreen) {
					element.requestFullscreen();
				} else if (element.mozRequestFullScreen) {
					element.mozRequestFullScreen();
				} else if (element.webkitRequestFullscreen) {
					element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
				} else if (element.msRequestFullscreen) {
					element.msRequestFullscreen();
				}
			}
		} else {
			showAlert('Cannot Fullscreen', 'No media wrapper set as full screen');
		}
	});

	window.showMessage = function(title, message) {
		showAlert(title, message);
	}
});
