(($) => {

    let filters = {};
    let problemsPerVariant = 1;

    // Get values from HTML MultiSelect
    function getMultiSelectValues(element) {
        let values = [];
        for (let i = 0; i < element.selectedOptions.length; i++) {
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

            $('#problem-wrapper-' + (parseInt(e.target.dataset.problemId) + 1)).slideToggle();
            $('#btn-add-' + e.target.dataset.problemId).hide();
            $('#btn-remove-' + e.target.dataset.problemId).hide();

            problemsPerVariant++;

            $('#problemsPerVariant').val(problemsPerVariant);

        });

        $(document).on('click', '.btn-remove', (e) => {

            $('#problem-wrapper-' + (e.target.dataset.problemId)).slideToggle();
            $('#btn-add-' + (parseInt(e.target.dataset.problemId) - 1)).show();
            $('#btn-remove-' + (parseInt(e.target.dataset.problemId) - 1)).show();

            problemsPerVariant--;

            $('#problemsPerVariant').val(problemsPerVariant);

        });

        $(document).on('change', '.filter', (e) => {

            let problemId = e.target.dataset.problemId;
            let filterType = e.target.dataset.filterType;
            let filterTypeSecondary = e.target.dataset.filterTypeSecondary;

            console.log(filterTypeSecondary);
            console.log(filters);

            let filterVal = null;
            if (e.target.dataset.filterType === 'isTemplate') {
                filterVal = e.target.value;
            } else {
                filterVal = getMultiSelectValues(e.target);
            }

            console.log(filters);

            if (!filters[problemId]) {
                filters[problemId] = {};
                filters[problemId]['filters'] = {};
            }

            filters[problemId]['selected'] = $('#problem-' + problemId).val();
            console.log($('#problem-' + problemId).val());

            // Select problem doesn't have set filter type --> is should not trigger filter request
            if (filterType) {

                if (filterTypeSecondary) {
                    if (!filters[problemId]['filters'][filterType]) {
                        filters[problemId]['filters'][filterType] = {};
                    }
                    filters[problemId]['filters'][filterType][filterTypeSecondary] = filterVal;
                } else {
                    filters[problemId]['filters'][filterType] = filterVal;
                }

                console.log(filters);

                $.nette.ajax({
                    type: 'POST',
                    url: '?do=filterChange',
                    data: {
                        'filters': filters
                    },
                    success: () => {
                    }
                });
            }

        });

        // Display form items based on selected problem type
        $(document).on('change', '.problem-type-filter', (e) => {

            let selectedOptions = getMultiSelectValues(e.target);
            let problemInx = e.target.dataset.problemId;
            let conditionTypes = JSON.parse(e.target.dataset.conditionTypes);

            // Display corresponding condition filters
            for (let key in conditionTypes) {
                if (conditionTypes.hasOwnProperty(key)) {
                    if (selectedOptions.indexOf(parseInt(key)) !== -1) {
                        $('#condition-type-id-' + conditionTypes[key] + '-' + problemInx).show();
                    } else {
                        $('#condition-type-id-' + conditionTypes[key] + '-' + problemInx).hide();
                    }
                }
            }

        });

    });

})($);