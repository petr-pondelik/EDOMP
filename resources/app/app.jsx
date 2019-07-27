/*
    JS MODULES
 */


// Import jQuery modules
// import 'jquery';

// Import jQuery UI modules
import 'jquery-ui-dist/jquery-ui';

// Import jQuery UI touch-punch
import './js/jquery-ui-touch-punch';

// Import Font Awesome modules
import '@fortawesome/fontawesome-free';

// Import Bootstrap modules
import 'bootstrap';
import 'bootstrap-select';
import 'bootstrap-datepicker';

// Import Nette Forms module
import 'nette-forms';

// Import Nette Ajax modules
import 'nette.ajax.js';
import './js/nette.ajax.init';
import './js/spinner.ajax';

// Import Ublaboo Datagrid modules
import 'ublaboo-datagrid';

// Import FilePond modules
import 'filepond';
import 'jquery-filepond/filepond.jquery';
import './js/filepond.config';

// Import custom modules
import './js/sidemenu';
import './js/test-create-filters';
import './js/test-create-logos-droppable';
import './js/test-create-problems-stack';

/*
    STYLE ASSETS
 */


// //Import Font Awesome styles
import '@fortawesome/fontawesome-free/css/all.css';


//Import Bootstrap styles
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-select/dist/css/bootstrap-select.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.standalone.css';


//Import Ublaboo Datagrid styles
import 'ublaboo-datagrid/assets/dist/datagrid.css';
import 'ublaboo-datagrid/assets/dist/datagrid-spinners.css';

//Import File Pond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';


//Import custom styles
import './css/sidemenu.css';

import './scss/main.scss';

//TODO: SCRIPTS NOW WORKING WITH ONE CONDITION TYPE FOR EACH PROTOTYPE (VALIDATION) -> NEED TO BE GENERALIZED
//TODO: CREATE SCRIPT FILE FOR EACH LOGICAL SECTION AND INCLUDE IT HERE

$(document).ready(() => {
    $(document).find('#createModal .type-wrapper').hide();
});

//Admin section: Handle controls toggle for create and edit
$(document).ready(() => {

    console.log('TEST');

    $(document).find('.condition-validation-btn').hide();

    //Watch type changes and disable submit on conditioned type selection
    $(document).on('change', "#type", (e) => {
        let type = e.target.value;

        $(document).find('.type-wrapper').hide();
        $(document).find('#type-wrapper-' + e.target.value).show();

        switch (e.target.value) {
            case 3: $(document).find("#")
        }

        let conditionElem = $(document).find('#condition-' + type);

        //Block form submit only if the problem is prototype
        if(!e.target.dataset.final){

            if(conditionElem.length){
                if (conditionElem.val() != 0) {
                    $(document).find('#conditions-valid').val(0);
                    $(document).find('#submit-btn').attr('disabled', true);
                    $(document).find('#submit-btn').addClass('disabled');
                } else {
                    $(document).find('#conditions-valid').val(1);
                    $(document).find('#submit-btn').attr('disabled', false);
                    $(document).find('#submit-btn').removeClass('disabled');
                }
            }
            else{
                $(document).find('#conditions-valid').val(1);
                $(document).find('#submit-btn').attr('disabled', false);
                $(document).find('#submit-btn').removeClass('disabled');
            }

        }

    });

    //Watch body changes and disable submit button when conditioned body was changed
    $(document).on('change', '#body', (e) => {
        let type = $(document).find('#type').val();
        let conditionElem = $(document).find('#condition-' + type);

        if(!e.target.dataset.final){
            if(conditionElem.length){
                if (conditionElem.val() != 0) {
                    $(document).find('#conditions-valid').val(0);
                    $(document).find('#submit-btn').attr('disabled', true);
                    $(document).find('#submit-btn').addClass('disabled');
                    $(document).find('#condition-validation-btn-' + type).show();
                } else {
                    $(document).find('#conditions-valid').val(1);
                    $(document).find('#submit-btn').attr('disabled', false);
                    $(document).find('#submit-btn').removeClass('disabled');
                }
            }
        }
    });

    $(document).on('change', '.condition', (e) => {
        let key = e.target.dataset.key;

        if(!e.target.dataset.static){
            if (e.target.value != 0) {
                $(document).find('#condition-validation-btn-' + key).show();
                $(document).find('#condition-valid-' + key).val(0);
                $(document).find('#conditions-valid').val(0);
                $(document).find('#submit-btn').attr('disabled', true);
                $(document).find('#submit-btn').addClass('disabled');
            } else {
                $(document).find('#condition-validation-btn-' + key).hide();
                $(document).find('#condition-valid-' + key).val(1);
                $(document).find('#conditions-valid').val(1);
                $(document).find('#submit-btn').attr('disabled', false);
                $(document).find('#submit-btn').removeClass('disabled');
            }
        }

    });

    //Admin section: prototypes conditions validations
    $(document).on('click', '.condition-validation-btn', (e) => {

        let body = $(document).find('#body').val();
        let conditionType = e.target.dataset.key;
        let accessor = $(document).find('#condition-' + conditionType).val();
        let variable = $(document).find('#variable').val();
        let problemType = $(document).find('#type').val();
        let url = "";
        let problemId = null;

        if(e.target.dataset.edit){
            problemId = $(document).find('#problem-id').val();
            url = '?problem_id=' + problemId + '&do=CondValidation';
        }
        else{
            url = '?do=CondValidation';
        }

        $.nette.ajax({
            type: 'POST',
            url: url,
            data: {
                'data': {
                    'conditionType': conditionType,
                    'accessor': accessor,
                    'body': body,
                    'type': problemType,
                    'variable': variable
                },
                'problemId': problemId
            },
            success: (payload) => {
                if (payload.result) {

                    //Enable create button
                    $(document).find('#submit-btn').attr('disabled', false);
                    $(document).find('#submit-btn').removeClass('disabled');

                    //Set condition_valid to true for specific condition
                    console.log(conditionType);
                    $(document).find('#condition-valid-' + conditionType).val(1);
                    $(document).find('#conditions-valid').val(1);

                }
            }
        });

    });

});

//Admin section: Handle controls toggle in edit card
$(document).ready(() => {
    let type = $(document).find('#type').val();

    $(document).find('.type-wrapper').hide();
    $(document).find('#type-wrapper-' + type).show();


    //Disable edit-submit button if non-validated condition appears
    $(document).on('change', '.edit-condition', (e) => {
        let key = e.target.dataset.key;
        if(e.target.dataset.static == true)
            return;
        if (e.target.value != 0) {
            $(document).find('#edit-condition-validation-btn-' + key).show();
            $(document).find('#edit-condition-valid-' + key).val(0);
            $(document).find('#edit-conditions-valid').val(0);
            $(document).find('#edit-submit').attr('disabled', true);
            $(document).find('#edit-submit').addClass('disabled');
        } else {
            $(document).find('#edit-condition-validation-btn-' + key).hide();
            $(document).find('#edit-condition-valid-' + key).val(1);
            $(document).find('#edit-conditions-valid').val(1);
            $(document).find('#edit-submit').attr('disabled', false);
            $(document).find('#edit-submit').removeClass('disabled');
        }
    });
});

//Handle card heading chevrons toggle
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

//Handle Logo Edit section file selection
$(document).ready(() => {
    $(document).on("change", "#edit-logo", (e) => {
        $(document).find("#logo-file-wrapper").toggleClass("d-none");
    });
});

//Handle show result button
$(document).ready(() => {
    $(document).on("click", "#result-switch", () => {
        $(document).find("#result-wrapper").toggle();
    })
});

//Handle change password inputs for user update
$(document).ready(() => {
    $(document).on("click", "#change-password-switch", () => {
        $(document).find("#change-password-wrapper").fadeToggle();
    });
});