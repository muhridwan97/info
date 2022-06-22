export default function () {
    const modalValidate = $('#modal-validate');
    const buttonValidate = modalValidate.find('[data-submit]');
    const buttonDismiss = modalValidate.find('[data-dismiss]');
    const form = modalValidate.find('form');

    buttonValidate.on('click', function () {
        form.submit();
    });

    $(document).on('click', '.btn-validate', function (e) {
        e.preventDefault();

        form.attr('action', $(this).data('url') || $(this).attr('href'));
        form.find('[name=id]').val($(this).data('id'));
        modalValidate.find('.validate-title').text($(this).data('title'));
        modalValidate.find('.validate-label').text($(this).data('action') + ' data ' + $(this).data('label'));
        modalValidate.find('.validate-email').text($(this).data('email'));
        modalValidate.find('input[name="status"]').val($(this).data('action'));
        modalValidate.find('button[type=submit]').text($(this).data('action').toUpperCase());

        modalValidate.find('button[type=submit]')
            .removeClass('btn-danger')
            .removeClass('btn-success')
            .removeClass('btn-primary');

        switch ($(this).data('action').toLowerCase()) {
            case 'approve':
            case 'validate':
            case 'activate':
            case 'active':
                modalValidate.find('button[type=submit]').addClass('btn-success');
                break;
            case 'warning':
            case 'revise':
                modalValidate.find('button[type=submit]').addClass('btn-warning');
                break;
            case 'reject':
            case 'cancel':
            case 'invalid':
            case 'inactive':
            case 'error':
                modalValidate.find('button[type=submit]').addClass('btn-danger');
                break;
            default:
                modalValidate.find('button[type=submit]').addClass('btn-primary');
                break;
        }

        modalValidate.modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    buttonDismiss.on('click', function () {
        form.attr('action', '#');
        form.find('[name=id]').val('');
        form.find('.validate-title').text('');
        form.find('.validate-label').text('');
    });
};
