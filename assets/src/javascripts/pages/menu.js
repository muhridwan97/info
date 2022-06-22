export default function () {

    const menuForm = $('#form-menu');
    const btnType = menuForm.find('#link_type');
    const internalInput = menuForm.find('#type-internal');
    const externalInput = menuForm.find('#type-external');

    /**
     * url: /master/role/{create/({edit/:id})}
     * Toggle check all permissions in same module name, find by class name.
     */
     btnType.on('change', function () {
        let type = $(this).val();
        if (type == 'INTERNAL') {
            internalInput.show();
            externalInput.hide();
        }
        else {
            internalInput.hide();
            externalInput.show();
        }
    });
};