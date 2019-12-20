// Utility for modal overlaying handling

let modalOverlaySelector = '.modal-overlay';

$(document).ready(() => {
    $(document).on('click', modalOverlaySelector, function () {
        console.log('MODAL OVERLAY');
        handleModalOverlay($(this));
    });
});

function handleModalOverlay(handleElem) {
    let body = $(document).find('body');
    let underlayModal = $(document).find(handleElem.data('underlay'));
    let overlayModal = $(document).find(handleElem.data('overlay'));

    underlayModal.modal('hide');

    underlayModal.on('hidden.bs.modal', function () {
        // Load up the overlaying modal
        overlayModal.modal('show')
    });

    overlayModal.on('hidden.bs.modal', function (e) {
        overlayModal.unbind();
        underlayModal.unbind();
        underlayModal.modal('show');
    });
}