function showConfirm(title, message, callbackYes, callbackNo) {
    const modalConfirm = $('#modal-confirm');
    modalConfirm.find('.modal-title').html(title);
    modalConfirm.find('.modal-message').html(message);

    modalConfirm.find('#btn-yes').off('click');
    modalConfirm.find('#btn-no').off('click');

    if (typeof callbackYes === "function") {
        modalConfirm.find('#btn-yes').on('click', function (e) {
            callbackYes(e, modalConfirm, $(this));
        });
    }

    if (typeof callbackNo === "function") {
        modalConfirm.find('#btn-no').on('click', function (e) {
            callbackNo(e, modalConfirm, $(this));
        });
    } else {
        modalConfirm.find('#btn-no').on('click', function (e) {
            modalConfirm.modal('hide');
        });
    }

    modalConfirm.modal({
        backdrop: 'static',
        keyboard: false
    });
}

export default showConfirm
