(($) => {

    let filters = {};
    let problemsCnt = 1;

    // Get values from HTML MultiSelect
    function getMultiSelectValues(element){
        let values = [];
        for(let i = 0; i < element.selectedOptions.length; i++){
            values.push(parseInt(element.selectedOptions[i].value));
        }
        return values;
    }

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

            $('#problem-' + (parseInt(e.target.dataset.problemId) + 1)).slideToggle();
            $('#btn-add-' + e.target.dataset.problemId).hide();
            $('#btn-remove-' + e.target.dataset.problemId).hide();

            problemsCnt++;

            $('#problemsCnt').val(problemsCnt);

        });

        $(document).on('click', '.btn-remove', (e) => {

            $('#problem-' + (e.target.dataset.problemId)).slideToggle();
            $('#btn-add-' + (parseInt(e.target.dataset.problemId) - 1)).show();
            $('#btn-remove-' + (parseInt(e.target.dataset.problemId) - 1)).show();

            problemsCnt--;

            $('#problemsCnt').val(problemsCnt);

        });

        $(document).on('change', '.filter', (e) => {

            let problemId = e.target.dataset.problemId;
            let filterType = e.target.dataset.filterType;
            let filterTypeSecondary = e.target.dataset.filterTypeSecondary;

            console.log(filterTypeSecondary);
            console.log(filters);

            // console.log(e.target);

            let filterVal = null;
            if(e.target.dataset.filterType === 'isTemplate'){
                filterVal = e.target.value;
            } else{
                filterVal = getMultiSelectValues(e.target);
            }

            // console.log(filterVal);

            console.log(filters);

            if (!filters[problemId]) {
                filters[problemId] = {};
                filters[problemId]['filters'] = {};
            }

            // console.log($('#problem_' + problemId).val());

            filters[problemId]['selected'] = $('#problem' + problemId).val();

            // console.log($('#problem_' + problemId).val());
            // console.log(filters);

            // Select problem doesn't have set filter type --> is should not trigger filter request
            if(filterType){

                if(filterTypeSecondary){
                    if(!filters[problemId]['filters'][filterType]){
                        filters[problemId]['filters'][filterType] = {};
                    }
                    filters[problemId]['filters'][filterType][filterTypeSecondary] = filterVal;
                }
                else{
                    filters[problemId]['filters'][filterType] = filterVal;
                }

                console.log(filters);

                $.nette.ajax({
                    type: 'GET',
                    url: '?do=filterChange',
                    data: {
                        'filters': filters,
                        'problemsCnt': problemsCnt
                    },
                    success: () => {}
                });
            }

        });

        // Display form items based on selected problem type
        $(document).on('change', '.problem-type-filter', (e) => {

            let selectedOptions = getMultiSelectValues(e.target);
            let problemInx = e.target.dataset.problemId;
            let conditionTypes = JSON.parse(e.target.dataset.conditionTypes);

            // Display corresponding condition filters
            for (let key in conditionTypes){
                if(conditionTypes.hasOwnProperty(key)){
                    if(selectedOptions.indexOf(parseInt(key)) !== -1){
                        $('#condition-type-id-' + conditionTypes[key] + '-' + problemInx).show();
                    }
                    else{
                        $('#condition-type-id-' + conditionTypes[key] + '-' + problemInx).hide();
                    }
                }
            }

        });

    });

})($);