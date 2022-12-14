(($) => {

    $(document).ready(() => {

        let $problemSelectedAreaPlaceholder = '<p class="mb-0 text-center text-muted">Kliknutím na karty úloh nacházející se níže lze provést manuální výběr úloh.<br>Při provedení manuálního výběru budou generátorem úlohy voleny POUZE Z PROVEDENÉHO VÝBĚRU.</p>';

        // Move problem from problem stack into problem selected area
        $(document).on('click', '.problem-stack li', function () {

            console.log('MOVE FROM STACK INTO SELECTED');

            let $item = $(this);
            let problemKey = $item.data('problemKey');
            let problemId = $item.data('problemId');
            let $problemSelectedArea = $(document).find('#problem-select-area-' + problemKey);
            let selectedCnt = $('ul', $problemSelectedArea).length;

            console.log($problemSelectedArea);

            console.log('KEY: ' + problemKey);
            console.log('CLICKED ' + problemId);
            console.log('SELECTED CNT: ' + selectedCnt);

            $(this).fadeOut(() => {

                $problemSelectedArea.find('p').remove();

                if(selectedCnt){
                    let $list = $problemSelectedArea.find('ul');
                    $item.appendTo($list).fadeIn();
                }
                else{
                    // Append problem into problem selected area
                    $problemSelectedArea.append($item);
                    let $list = $('<ul class="list-unstyled m-0">').appendTo($problemSelectedArea);
                    $item.appendTo($list).fadeIn();
                }

                addProblem(problemKey, problemId);

            });

        });

        // Add problem to the corresponding multi-select
        function addProblem(problemKey, problemId) {
            console.log('ADD PROBLEM');

            let $problemsElement = $('#problem-' + problemKey);
            let selectedProblems = $problemsElement.val();

            selectedProblems = (!selectedProblems) ? [] : JSON.parse(selectedProblems);

            selectedProblems.push(problemId);
            console.log(selectedProblems);
            $problemsElement.val(JSON.stringify(selectedProblems));

            $problemsElement.trigger('change');
        }

        // Move problem from problem selected area into problem stack
        $(document).on('click', '.problem-select-area li', function () {

            console.log('MOVE FROM SELECTED INTO STACK');

            let $item = $(this);
            let problemKey = $item.data('problemKey');
            let problemId = $item.data('problemId');
            let $problemStack = $(document).find('#problem-stack-' + problemKey);
            let $problemSelectedArea = $(document).find('#problem-select-area-' + problemKey);

            console.log($problemStack);

            console.log('KEY: ' + problemKey);
            console.log('CLICKED ' + problemId);

            $(this).fadeOut(() => {

                $item.prependTo($problemStack).fadeIn();

                console.log($('li', $problemSelectedArea).length);

                if(!$('li', $problemSelectedArea).length){
                    $($problemSelectedArea).append($problemSelectedAreaPlaceholder);
                }

                removeProblem(problemKey, problemId);

            });

        });

        // Remove problem from the corresponding multi-select
        function removeProblem(problemKey, problemId) {
            let $problemsElement = $('#problem-' + problemKey);
            let selectedProblems = JSON.parse($problemsElement.val());

            let inx = getIndex(selectedProblems, problemId);

            console.log(selectedProblems);
            selectedProblems.splice(inx, 1);
            console.log(selectedProblems);

            $problemsElement.val(JSON.stringify(selectedProblems));
            $problemsElement.trigger('change');
        }

        function getIndex(array, item) {
            for (let i = 0; i < array.length; i++){
                if(array[i] === item){
                    return i;
                }
            }
            return null;
        }

    });

})($);