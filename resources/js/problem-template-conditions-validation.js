(($) => {

    $(document).ready(() => {
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
                        'idHidden': problemId,
                        'conditionType': conditionType,
                        'conditionAccessor': accessor,
                        'body': body,
                        'type': problemType,
                        'variable': variable
                    },
                    'problemId': problemId,
                    'preserveValidation': true
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
    })

})($);