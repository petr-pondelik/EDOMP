(($) => {

    $(document).ready(() => {

        console.log('PROBLEM TEMPLATE TYPE VALIDATION LOADED');

        $(document).on('click', '.btn-type-validation', (e) => {

            console.log('CLICKED');

            let body = $(document).find('#body').val();
            let variable = $(document).find('#variable').val();
            let problemId = null;

            let url = '?do=TypeValidation';

            $.nette.ajax({
                type: 'POST',
                url: url,
                data: {
                    'data': {
                        'idHidden': problemId,
                        'body': body,
                        'variable': variable
                    },
                    'preserveValidation': true
                },
                success: (payload) => {

                    console.log(payload);

                    if (payload.result) {

                        console.log('SUCCESS');

                        // //Enable create button
                        // $(document).find('#submit-btn').attr('disabled', false);
                        // $(document).find('#submit-btn').removeClass('disabled');
                        //
                        // //Set condition_valid to true for specific condition
                        // console.log(conditionType);
                        // $(document).find('#condition-valid-' + conditionType).val(1);
                        // $(document).find('#conditions-valid').val(1);

                    }
                }
            });


        });

    });

})($);