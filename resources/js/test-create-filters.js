(($) => {

    var filters = {};
    let problemsPerVariant = 1;
    let nonActiveFilterTypes = ['selected'];

    // Decide if trigger AJAX filtering
    function filterOnChange(filterType) {
        return !(nonActiveFilterTypes.includes(filterType));
    }

    // Get values from HTML MultiSelect
    function getMultiSelectValues(element) {
        let values = [];
        for (let i = 0; i < element.selectedOptions.length; i++) {
            values.push(parseInt(element.selectedOptions[i].value));
        }
        return values;
    }

    // Set selected problem IDs into filters and element
    function setSelected(payload) {
        filters[payload.selected.key]['selected'] = payload.selected.values;
        $(document).find('#problem-' + (payload.selected.key)).val(JSON.stringify(payload.selected.values));
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

            $(document).find('#problem-wrapper-' + (parseInt(e.target.dataset.problemId) + 1)).slideToggle();
            $(document).find('#btn-add-' + e.target.dataset.problemId).hide();
            $(document).find('#btn-remove-' + e.target.dataset.problemId).hide();

            problemsPerVariant++;

            $(document).find('#problemsPerVariant').val(problemsPerVariant);

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
            } else if (e.target.dataset.filterType === 'selected') {
                console.log(e.target.value);
                console.log(JSON.parse(e.target.value));
                filterVal = JSON.parse(e.target.value);
            } else {
                filterVal = getMultiSelectValues(e.target);
            }

            console.log(filters);

            if (!filters[problemId]) {
                filters[problemId] = {};
                filters[problemId]['filters'] = {};
                filters[problemId]['selected'] = {}
            }

            if (filterType !== 'selected') {

                if (filterTypeSecondary) {
                    if (!filters[problemId]['filters'][filterType]) {
                        filters[problemId]['filters'][filterType] = {};
                    }
                    filters[problemId]['filters'][filterType][filterTypeSecondary] = filterVal;
                } else {
                    filters[problemId]['filters'][filterType] = filterVal;
                }

            } else {
                filters[problemId][filterType] = filterVal;
            }

            console.log(filters);

            // Not all filter types should trigger filter request
            if (filterType && filterOnChange(filterType)) {

                console.log('SEND FILTER REQUEST');

                $.nette.ajax({
                    type: 'POST',
                    url: '?do=filterChange',
                    data: {
                        'key': problemId,
                        'filters': filters
                    },
                    success: (payload) => {
                        console.log(payload);
                        setSelected(payload);
                        console.log(filters);
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

        //
        $(document).on('click', '.problem-stack-paginator-btn', (e) => {

            let handle = e.target.dataset.handle;

            console.log('PROBLEM STACK PAGINATOR BTN CLICK');
            console.log(handle);
            console.log(filters);

            $.nette.ajax({
                type: 'GET',
                url: '?do=setFilters',
                data: {
                    'filters': filters
                },
                success: (payload) => {
                    console.log('SET FILTERS SUCCESS');
                    $.nette.ajax({
                        type: 'GET',
                        url: handle,
                        success: (payload) => {
                            console.log('PAGINATOR SUCCESS');
                        }
                    });
                }
            });

        });

    });

})($);