/*
    JS MODULES
 */


//Import jQuery modules
import 'jquery';


//Import Font Awesome modules
import '@fortawesome/fontawesome-free';


//Import Bootstrap modules
import 'bootstrap';
import 'bootstrap-select';
import 'bootstrap-datepicker';


//Import Nette Forms module
import 'nette-forms';


//Import Nette Ajax modules
import 'nette.ajax.js';
//import './../../vendor/vojtech-dobes/nette-ajax-history/client-side/history.ajax'
import './js/nette.ajax.init';
import './js/spinner.ajax';
import './js/nette.toggle';

//Import Ublaboo Datagrid modules
import 'ublaboo-datagrid';


//Import FilePond modules
import 'filepond';
import 'jquery-filepond/filepond.jquery';
import './js/filepond.config';

//Import custom modules
import './js/sidemenu';


/*
    STYLE ASSETS
 */


//Import Font Awesome styles
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

import './scss/global.scss';

//TODO: SCRIPTS NOW WORKING WITH ONE CONDITION TYPE FOR EACH PROTOTYPE (VALIDATION) -> NEED TO BE GENERALIZED
//TODO: CREATE SCRIPT FILE FOR EACH LOGICAL SECTION AND INCLUDE IT HERE

$(document).ready(() => {

    $(document).find('#createModal .type-wrapper').hide();
    $(document).find('#createModal #type-wrapper-1').show();

});

//Admin section: Handle controls toggle for create and edit
$(document).ready(() => {

    console.log('TEST');

    $(document).find('.condition-validation-btn').hide();

    //Watch type changes and disable submit on conditioned type selection
    $(document).on('change', "#type", (e) => {
        console.log(e.target.value);

        let type = e.target.value;

        console.log(e.target.dataset.final);

        $(document).find('.type-wrapper').hide();
        $(document).find('#type-wrapper-' + e.target.value).show();

        switch (e.target.value) {
            case 3: $(document).find("#")
        }

        let conditionElem = $(document).find('#condition-' + type);

        console.log(conditionElem);

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

    //Watch structure changes and disable submit button when conditioned structure was changed
    $(document).on('change', '#structure', (e) => {

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

        console.log("CONDITION CHANGE");
        console.log(e.target.dataset);

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

        console.log(problemType);

        console.log('CONDITION TYPE: ' + conditionType);
        console.log('ACCESSOR: ' + accessor);

        let url = "";
        let problemId = null;

        if(e.target.dataset.edit){
            problemId = $(document).find('#problem-id').val();
            console.log(problemId);
            url = '?problem_id=' + problemId + '&do=CondValidation';
        }
        else{
            url = '?do=CondValidation';
        }

        console.log('PROBLEM ID:' + problemId);

        $.nette.ajax({
            type: 'POST',
            url: url,
            data: {
                'conditionType': conditionType,
                'accessor': accessor,
                'body': body,
                'problemId': problemId,
                'problemType': problemType,
                'variable': variable
            },
            success: (payload) => {

                console.log('DONE');
                console.log(payload);

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
    console.log(type);

    /*let problemId = $(document).find('#edit-card #edit-problem-id').val();
    console.log('PROBLEM ID: ' + problemId);*/

    $(document).find('.type-wrapper').hide();
    $(document).find('#type-wrapper-' + type).show();

    //Show only corresponding conditions
    /*$(document).find('.condition').each((i, e) => {
            let key = $(e).data().key;
            console.log($(e).data());
            if ($(e).val() == 0)
                $(document).find("#edit-condition-validation-btn-" + key).hide();
        }
    );*/

    //Handle type change in edit card
    /*$(document).on('change', '#type', (e) => {

        console.log(e.target.value);

        //Get all condition types corresponding to the selected problem type
        $.nette.ajax({
            type: 'GET',
            url: '?problem_id=' + problemId + '&do=getTypeConditions',
            data: {
                'problemTypeId': e.target.value
            },
            success: (payload) => {

                console.log('DONE');
                console.log(payload);

                //Show corresponding condition types and remove selected conditions
                $(document).find('#edit-card .edit-cond-wrapper').each((i, e) => {
                    let key = $(e).data().key;
                    for (let i = 0; i < payload.length; i++) {
                        if (payload[i]['condition_type_id'] !== key) {
                            $(document).find('#edit-condition-' + key).val(0);
                            $(document).find('#edit-cond-wrapper-' + key).hide();
                        } else {
                            $(document).find('#edit-cond-wrapper-' + key).show();
                        }
                    }
                });

            }
        });

    });*/

    //If prototype structure was changed and prototype can have conditions, disable Save button and set conditions_valid to false
    $(document).on('change', '#edit-structure', (e) => {

        //Get all condition types corresponding to the selected problem type
        /*$.nette.ajax({
            type: 'GET',
            url: '?problem_id=' + problemId + '&do=getTypeConditions',
            data: {
                'problemTypeId': $(document).find("#type").val()
            },
            success: (payload) => {

                if (payload.length > 0) {
                    $(document).find('#edit-submit').attr('disabled', true).addClass('disabled');
                    $(document).find('#edit-conditions-valid').val(0);
                }

            }
        });*/

    });

    //Disable edit-submit button if non-validated condition appears
    $(document).on('change', '.edit-condition', (e) => {
        //console.log(e.target.value);
        //console.log(e.target.dataset.key);
        let key = e.target.dataset.key;
        console.log(e.target.dataset);
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


    $(document).on('click', '.edit-condition-validation-btn', (e) => {

        let structure = $(document).find('#edit-structure').val();
        console.log(structure);
        let conditionType = e.target.dataset.key;
        let accessor = $(document).find('#edit-condition-' + conditionType).val();

        console.log('CONDITION TYPE: ' + conditionType);
        console.log('ACCESSOR: ' + accessor);

        /*$.nette.ajax({
            type: 'GET',
            url: 'edit?problem_id=' + problemId + '&do=CondValidation',
            data: {
                'conditionType': conditionType,
                'accessor': accessor,
                'structure': structure
            },
            success: (payload) => {

                console.log('DONE');
                console.log(payload);

                if (payload.result) {

                    //Enable edit button
                    $(document).find('#edit-submit').attr('disabled', false);
                    $(document).find('#edit-submit').removeClass('disabled');

                    //Set condition_valid to true for specific condition
                    console.log(conditionType);
                    $(document).find('#edit-condition-valid-' + conditionType).val(1);
                    $(document).find('#edit-conditions-valid').val(1);

                }

            }
        });*/

    });

});

let filters = {};
let selectedProblems = {};
let problemsCnt = 1;

$(document).ready(() => {

    console.log('READY');

    $(document).on('click', '.problem-select', (e) => {
        let problemId = e.target.dataset.problemId;
        if (!filters[problemId]) {
            filters[problemId] = {};
            filters[problemId]['filters'] = {};
        }
        filters[problemId]['selected'] = e.target.value;
        console.log(filters);
    });

    $(document).on('click', '.btn-add', (e) => {

        console.log(e.target.dataset.problem);
        console.log('#problem-' + (parseInt(e.target.dataset.problem) + 1));

        $('#problem-' + (parseInt(e.target.dataset.problem) + 1)).slideToggle();
        $('#btn-add-' + e.target.dataset.problem).hide();
        $('#btn-remove-' + e.target.dataset.problem).hide();

        problemsCnt++;

        $('#problemsCnt').val(problemsCnt);

    });

    $(document).on('click', '.btn-remove', (e) => {

        console.log(e.target.dataset.problem);

        $('#problem-' + (e.target.dataset.problem)).slideToggle();
        $('#btn-add-' + (parseInt(e.target.dataset.problem) - 1)).show();
        $('#btn-remove-' + (parseInt(e.target.dataset.problem) - 1)).show();

        problemsCnt--;

        $('#problemsCnt').val(problemsCnt);

    });

    $(document).on('change', '.filter', (e) => {

        console.log(e.target);
        console.log('Problem ID: ' + e.target.dataset.problemId);
        console.log('Filter type: ' + e.target.dataset.filterType);
        console.log('Filter value: ' + e.target.value);

        let problemId = e.target.dataset.problemId;
        let filterType = e.target.dataset.filterType;
        let filterVal = e.target.value;

        if (!filters[problemId]) {
            filters[problemId] = {};
            filters[problemId]['filters'] = {};
        }

        filters[problemId]['filters'][filterType] = filterVal;
        console.log($('#problem_' + problemId).val());
        filters[problemId]['selected'] = $('#problem_' + problemId).val();

        console.log(filters);

        $.nette.ajax({
            type: 'GET',
            url: '?do=filterChange',
            data: {
                'filters': filters,
                'problemsCnt': problemsCnt
            },
            success: (payload) => {
                console.log(payload);
            }
        });

    });

});

//Handle card heading chevrons toggle
$(document).ready(() => {

    $(document).on('click', '.heading-test-logo, .heading-filters', (e) => {
        $(e.target).children().toggleClass('d-none');
    });

    $(document).find('.logo-img').click((e) => {

        console.log(e.target.dataset);

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
        console.log("CLICKED");
        $(document).find("#result-wrapper").toggle();
    })
});

//Handle change password inputs for user update
$(document).ready(() => {
    $(document).on("click", "#change-password-switch", () => {
        $(document).find("#change-password-wrapper").fadeToggle();
    });
});