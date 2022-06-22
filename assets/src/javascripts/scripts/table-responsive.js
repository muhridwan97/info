const setTableViewport = function () {
	// screen.width
	if ($(window).width() > 768) {
		$('table.responsive .responsive-label').remove();
		$('table.responsive td').find('.dropdown').css('display', '');
		$('table.responsive td[data-colspan]').each(function (i, td) {
			$(td).attr('colspan', $(td).data('colspan'));
			$(td).removeAttr('data-colspan');
		});
	}
	else {
		$('table.responsive').each(function (i, table) {
			let head = [];
			$(table).find('>thead th').each(function (i, th) {
				head.push($(th).text());
			});
			$(table).find('>tbody > tr:not(.row-no-header)').each(function (i, tr) {
				if ($(tr).find('>td .responsive-label').length === 0) {
					const cells = $(tr).find('>td');
					const targetHeaderClass = $(tr).data('header');
					if (targetHeaderClass) {
						const targetHeader = [];
						$(`.${targetHeaderClass} th`).each(function (i, th) {
							targetHeader.push($(th).text());
						});
						if ($(tr).find('td').length === targetHeader.length) {
							$(tr).find('td').each(function (i, td) {
								$(td).prepend(`<span class="responsive-label">${targetHeader[i] || ''}</span>`);
								$(td).css('maxWidth', '');
								$(td).find('input').css('maxWidth', '');
								if ($(td).attr('colspan')) {
									$(td).attr('data-colspan', $(td).attr('colspan'));
									$(td).removeAttr('colspan');
								}
							});
							$(tr).find('.dropdown').css('display', 'inline-block');
						}
					} else {
						cells.each(function (i, td) {
							if (cells.length === head.length) {
								$(td).prepend(`<span class="responsive-label">${head[i] || ''}</span>`);
							} else {
								$(td).prepend(`<span class="responsive-label">${$(td).data('header-title') || ''}</span>`);
							}
							$(td).css('maxWidth', '');
							$(td).find('input').css('maxWidth', '');
							if ($(td).attr('colspan')) {
								$(td).attr('data-colspan', $(td).attr('colspan'));
								$(td).removeAttr('colspan');
							}
						});
						$(tr).find('.dropdown').css('display', 'inline-block');
					}
				}
			});
		});
	}
};

setTableViewport();

window.onresize = function () {
	setTableViewport();
};

export default setTableViewport;
