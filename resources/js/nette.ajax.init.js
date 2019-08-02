(($) => {
    $(document).ready(() => {
        console.log('NETTE AJAX INIT');
        $.nette.init();
        $(document).find('.no-ajax').netteAjaxOff();
    });
})($);