(($) => {

    $(document).ready(() => {

        console.log('TEST-CREATE-LOGOS-DROPPABLE LOADED');

        // Select test create form's logo input
        let $testLogo = $('#test-logo');

        // Select elements for draggable and droppable areas
        let $logoDropArea = $('#logo-drop-area');
        let $logosList = $('#logos-list');

        // Placeholder paragraph for logo drop area
        let dropAreaPlaceholderSelector = '#drop-area-placeholder';
        let logoDropAreaPlaceholder = 'Tažením do této oblasti či kliknutím na jedno z dostupných log proveďte výběr loga.';
        let logoDropAreaPlaceholderActive = 'Kliknutím na zvolené logo či jeho tažením do seznamu dostupných log zrušíte výběr.<br>Tažením do této oblasti či kliknutím na jedno z dostupných log provedete výměnu zvoleného loga.';

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
                'ui-droppable-active': 'bg-grey-middle'
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
                if (selectedCnt) {
                    let $switchedItem = $logoDropArea.find('li');
                    $logosList.append($switchedItem);
                    let $list = $logoDropArea.find('ul');
                    $item.appendTo($list).fadeIn();
                    $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholderActive);
                } else {
                    // Append logo into droppable area
                    $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholderActive);
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
                'ui-droppable-active': 'bg-grey-middle'
            },
            drop: (e, ui) => {
                unselectLogo(ui.draggable);
            }
        });

        // Function that drops logo back into logos list
        function unselectLogo($item) {

            console.log('DROP LOGO INTO LOGOS LIST');

            $item.fadeOut(() => {
                $logoDropArea.append();
                $item.appendTo($logosList).fadeIn();
                $logoDropArea.find('ul').remove();
                $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholder);
                resetLogo();
            });

        }

        // Function that sets test's logo input
        function setLogo() {
            console.log("SETTING TEST'S LOGO");
            let logoId = $logoDropArea.find('.test-logo').data('logoId');
            $testLogo.val(logoId);
        }

        // Function that resets test's logo input
        function resetLogo() {
            console.log("RESETTING TEST'S LOGO");
            $testLogo.val('');
        }

        // ===============================================
        // Drag & Drop fallback for mobile devices (by click)

        $(document).on('click', '#logos-list li', function () {

            let $item = $(this);
            $(this).fadeOut(() => {
                let selectedCnt = $('ul', $logoDropArea).length;

                // If there already exists selected logo, switch old selected logo with newly selected logo
                if (selectedCnt) {
                    let $switchedItem = $logoDropArea.find('li');
                    $logosList.append($switchedItem);
                    let $list = $logoDropArea.find('ul');
                    $item.appendTo($list).fadeIn();
                    $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholderActive);
                } else {
                    // Append logo into droppable area
                    $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholderActive);
                    let $list = $('<ul class="list-unstyled">').appendTo($logoDropArea);
                    $item.appendTo($list).fadeIn();
                }

                setLogo();
            });

        });

        $(document).on('click', '#logo-drop-area li', function () {

            let $item = $(this);

            $item.fadeOut(() => {
                $logoDropArea.append();
                $item.appendTo($logosList).fadeIn();
                $logoDropArea.find('ul').remove();
                $logoDropArea.find(dropAreaPlaceholderSelector).html(logoDropAreaPlaceholder);
                resetLogo();
            });

        });

    });

})($);