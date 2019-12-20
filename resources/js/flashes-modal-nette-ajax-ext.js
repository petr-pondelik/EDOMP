// Nette AJAX extension that implements showing flash messages in Bootstrap modals

$.nette.ext('flashes-modal', {

    init: function () {
        let $modal = $('#flashes-modal');
        console.log($modal.data());
        if ($modal.data('show') === true) {
            console.log('SHOW FLASH MODAL');
            $modal.modal('show');
        }
    },

    success: function (jqXHR, status, settings) {

        if (typeof settings.responseJSON.snippets != 'undefined') {
            var $snippet = settings.responseJSON.snippets['snippet-flashesModal-flashesSnippet'];
        }
        if (!$snippet) {
            return;
        }

        let $modal = $('#flashes-modal');
        if ($modal.find('.modal-content').html().trim().length !== 0) {
            $modal.modal('show');
        } else {
            $modal.modal('hide');
        }
    }
});