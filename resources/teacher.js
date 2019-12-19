/**
 * JS MODULES
 */

// Import jQuery UI modules
import 'jquery-ui-dist/jquery-ui';

// Import jQuery UI touch-punch
import 'jquery-ui-touch-punch';

// Import Font Awesome modules
import '@fortawesome/fontawesome-free';

// Import Bootstrap modules
import 'bootstrap';
import 'bootstrap-select';
import './js/bootstrap-tooltip-init';

// Import Nette Forms module
import 'nette-forms';

// Import Nette Ajax modules
import 'nette.ajax.js';
import './js/nette.ajax.init';
import './js/nette-flashes-modal';
import './js/spinner.ajax';

// Import Ublaboo Datagrid modules
import 'ublaboo-datagrid';

// Import FilePond modules
import 'filepond';
import 'jquery-filepond/filepond.jquery';
import './js/filepond.config';

// Import ACE Web Code Editor
import 'ace-builds/src-min-noconflict/ace';
import './js/ace-code-editor';

// Import custom modules
import './js/sidemenu';
import './js/logo';
import './js/problem-template-type-validation';
import './js/problem-template-conditions-validation';
import './js/test-create-filters';
import './js/test-create-logos-droppable';
import './js/test-create-problems-stack';
import './js/card-header-toggle';
import './js/animateScroll';

/**
 * STYLES MODULES
 */

// Import Font Awesome styles
import '@fortawesome/fontawesome-free/css/all.css';

// Import Bootstrap styles
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-select/dist/css/bootstrap-select.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.standalone.css';

// Import Ublaboo Datagrid styles
import 'ublaboo-datagrid/assets/dist/datagrid.css';
import 'ublaboo-datagrid/assets/dist/datagrid-spinners.css';

// Import File Pond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

// Import custom styles
import './css/sidemenu.css';
import './scss/main.scss';


//TODO: CREATE SCRIPT FILE FOR EACH LOGICAL SECTION AND INCLUDE IT HERE

$(document).ready(() => {
    $(document).find('#createModal .type-wrapper').hide();
});

//Admin section: Handle controls toggle for create and edit
$(document).ready(() => {

    $(document).find('.condition-validation-btn').hide();

    //Watch type changes and disable submit on conditioned type selection
    $(document).on('change', "#type", (e) => {
        $(document).find('.type-wrapper').hide();
        $(document).find('#type-wrapper-' + e.target.value).show();
    });

    // Watch body changes and disable submit button when conditioned body was changed
    $(document).on('change', '#body', (e) => {
        let type = $(document).find('#type').val();
        let conditionElem = $(document).find('#condition-' + type);
        if(!e.target.dataset.final){
            if(conditionElem.length){
                if (conditionElem.val() != 0) {
                    $(document).find('#condition-validation-btn-' + type).show();
                }
            }
        }
    });

    $(document).on('change', '.condition', (e) => {
        let key = e.target.dataset.key;
        if(!e.target.dataset.static){
            if (e.target.value != 0) {
                $(document).find('#condition-validation-btn-' + key).show();
            } else {
                $(document).find('#condition-validation-btn-' + key).hide();
            }
        }
    });

});

// Admin section: Handle controls toggle in edit card
$(document).ready(() => {
    let type = $(document).find('#type').val();
    $(document).find('.type-wrapper').hide();
    $(document).find('#type-wrapper-' + type).show();
});