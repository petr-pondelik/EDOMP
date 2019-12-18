$(document).ready(() => {
    $(document).on('click', '.animate-scroll', function () {
        scrollToElem($(this));
    });
});

function scrollToElem(elem) {
    let scrollTo = elem.data('scroll-to');
    $("html, body").animate({ scrollTop: $(document).find(scrollTo).offset().top }, 1000);
}