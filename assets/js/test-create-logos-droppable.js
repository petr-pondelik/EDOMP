$(document).ready(() => {

    console.log('TEST-CREATE-LOGOS-DROPPABLE LOADED');

    // Select test create form's logo input
    let $testLogo = $('#test-logo');

    // Select elements for draggable and droppable areas
    let $logoDropArea = $('#logo-drop-area');
    let $logosList = $('#logos-list');

    // Placeholder paragraph for logo drop area
    let $logoDropAreaPlaceholder = '<p class="text-center text-muted">Tažením umístěte jedno z dostupných log</p>';

    // Make the logos list draggable
    $('li', $logosList).draggable({
        cancel: '',
        revert: 'invalid',
        containment: 'document',
        helper: 'clone'
    });

    // Make logo drop area droppable
    $logoDropArea.droppable({
        accept: '#logos-list > li',
        classes: {
            'ui-droppable-active': 'bg-gray'
        },
        drop: (e, ui) => {
            selectLogo(e, ui.draggable);
        }
    });

    // Function that drops logo into droppable area
    function selectLogo(e, $item) {

        console.log('DROP LOGO INTO DROP AREA');

        // Let logo dissapear from draggable list
        $item.fadeOut(() => {

            let selectedCnt = $('ul', $logoDropArea).length;

            console.log(selectedCnt);

            // If there already exists selected logo, switch old selected logo with newly selected logo
            if(selectedCnt){
                let $switchedItem = $logoDropArea.find('li');
                $logosList.append($switchedItem);
                let $list = $logoDropArea.find('ul');
                $item.appendTo($list).fadeIn();
            }
            else{
                // Append logo into droppable area
                $logoDropArea.find('p').remove();
                let $list = $('<ul class="list-unstyled">').appendTo($logoDropArea);
                $item.appendTo($list).fadeIn();
            }

            setLogo();

        });

    }

    // Make the logos list droppable, accepting items from logos droppable area
    $logosList.droppable({
        accept: '#logo-drop-area li',
        classes: {
            'ui-droppable-active': 'bg-gray'
        },
        drop: (e, ui) => {
            unselectLogo(ui.draggable);
        }
    });

    // Function that drops logo back into logos list
    function unselectLogo($item){

        console.log('DROP LOGO INTO LOGOS LIST');

        $item.fadeOut(() => {
            $logoDropArea.append();
            $item.appendTo($logosList).fadeIn();
            $logoDropArea.find('ul').remove();
            $logoDropArea.append($logoDropAreaPlaceholder);
            resetLogo();
        });

    }

    // Function that sets test's logo input
    function setLogo() {
        console.log("SETTING TEST'S LOGO");
        let logoId = $logoDropArea.find('img').data('logoId');
        $testLogo.val(logoId);
        console.log($testLogo.val());
    }

    // Function that resets test's logo input
    function resetLogo() {
        console.log("RESETTING TEST'S LOGO");
        $testLogo.val('');
    }

});