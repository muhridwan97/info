// import global variable
import variables from "./components/variables";
import notification from './components/notification';

// jquery and bootstrap is main library of this app.
try {
    // get jquery ready in global scope
    window.$ = window.jQuery = require('jquery');
    $.ajaxSetup({
        headers: {
            "X-CSRFToken": variables.csrfToken,
            "csrf_token": variables.csrfToken
        }
    });

    // loading library core
    require('bootstrap');
    require('jquery-validation');
    window.moment = require('moment');
    require('daterangepicker');
    require('select2');
    require('chart.js');
	window.Mustache = require('mustache');

    // loading misc scripts
    require('./scripts/validation');
    require('./scripts/custom-upload-button');
    require('./scripts/table-responsive');
    require('./scripts/numeric-value');
    require('./scripts/one-touch-submit');
    require('./scripts/save-filter');
    require('./scripts/miscellaneous');

    // init notification Pusherjs
    notification();

    // load async page scripts
    if ($('.btn-delete').length && $('#modal-delete').length) {
        import("./components/delete").then(modalDelete => modalDelete.default());
    }

    if ($('.btn-validate').length && $('#modal-validate').length) {
        import("./components/validate").then(modalValidate => modalValidate.default());
    }

	if ($('.btn-toggle-expand').length || $('.btn-toggle-expand-all').length) {
		import("./components/section-toggle").then(sectionToggle => sectionToggle.default());
	}

	if ($('.uploader-wrapper').length) {
		import("./pages/uploader").then(uploader => uploader.default());
	}

    if ($('#form-role').length) {
        import("./pages/role").then(role => role.default());
    }

    if ($('#form-research').length) {
        import("./pages/research").then(research => research.default());
    }
    if ($('#form-menu').length) {
        import("./pages/menu").then(menu => menu.default());
    }

} catch (e) {
    console.log(e);
}

// include sass (but extracted by webpack into separated css file)
import '../sass/app.scss';
