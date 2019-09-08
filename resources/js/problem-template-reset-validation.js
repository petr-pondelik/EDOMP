(($) => {

    $(document).ready(() => {

        $(document).on('change', '#body', (e) => {

            let url = "";
            let problemId = null;

            if(e.target.dataset.edit){
                problemId = $(document).find('#problem-id').val();
                url = '?problem_id=' + problemId + '&do=ValidationReset';
            }
            else{
                url = '?do=ValidationReset';
            }

            $.nette.ajax({
                type: 'POST',
                url: url,
                data: {
                    'data': {
                        'id': problemId
                    }
                },
                success: (payload) => {

                }
            })

        });

    })

})($);