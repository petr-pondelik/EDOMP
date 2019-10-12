// Handle card heading chevrons toggle
$(document).ready(() => {
    $(document).on('click', '.card-header-toggle', (e) => {
        console.log('CLICK TOGGLE');
        $(e.target).children().toggleClass('d-none');
    });
});