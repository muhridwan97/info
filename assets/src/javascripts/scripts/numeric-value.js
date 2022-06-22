import numeric from '../components/numeric';

// Currency text
$(document).on('keyup', '.currency, .input-currency', function () {
	const value = $(this).val();
	$(this).val(value === '' ? '' : numeric(value || 0, 'Rp. '));
});

// Numeric text
$(document).on('keyup', '.numerical, .input-numeric', function () {
	const value = $(this).val();
	$(this).val(value === '' ? '' : numeric(value || 0));
});
