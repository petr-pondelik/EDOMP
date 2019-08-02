/**
 * JS MODULES
 */

// Import jQuery
import 'jquery';

// Import Font Awesome modules
import '@fortawesome/fontawesome-free';

// Import Font Awesome modules
import '@fortawesome/fontawesome-free';

// Import Bootstrap modules
import 'bootstrap';
import 'bootstrap-select';

// Import Nette Forms module
import 'nette-forms';

// Import Nette Ajax modules
import 'nette.ajax.js';
import './../vendor/vojtech-dobes/nette-ajax-history/client-side/history.ajax';
import './js/nette.ajax.init';
import './js/spinner.ajax';

// Import Ublaboo Datagrid modules
// import 'ublaboo-datagrid';

// Import custom modules
import './js/sidemenu';

/**
 * STYLES MODULES
 */

// Import Font Awesome styles
import '@fortawesome/fontawesome-free/css/all.css';

// Import Bootstrap styles
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-select/dist/css/bootstrap-select.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.standalone.css';

// Import custom styles
import './css/sidemenu.css';
import './scss/main.scss';

// Handle card heading chevrons toggle
$(document).ready(() => {

    $(document).on('click', '.heading-test-logo, .heading-filters', (e) => {
        $(e.target).children().toggleClass('d-none');
    });

    $(document).find('.logo-img').click((e) => {

        let logoId = e.target.dataset.logoId;
        let logoLabel = e.target.dataset.logoLabel;

        $(document).find('#test-logo-label').val(logoLabel);
        $(document).find('#test-logo-id').val(logoId);

        $(document).find('.heading-logo').removeClass('active');

        $(document).find('.accordion .fa-chevron-down').removeClass('d-none');
        $(document).find('.accordion .fa-chevron-up').addClass('d-none');

    });

});

// Handle show result button
$(document).ready(() => {
    $(document).on("click", "#result-switch", () => {
        $(document).find("#result-wrapper").toggle();
    })
});