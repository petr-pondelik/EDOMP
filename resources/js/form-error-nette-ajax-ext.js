// Nette AJAX extension that implements animated scrolling onto given form-error item

$.nette.ext('error-scrolling', {

    success: function (payload) {
        if (payload.formError) {

            let scrollTo = "[name='" + payload.formError.data.name + "']";
            console.log($(document).find());

            // Check case of multi-select error
            if (!$(document).find(scrollTo).length) {
                scrollTo = "[name='" + payload.formError.data.name + '[]' + "']";
            }

            // Check case of submit error
            if (!$(document).find(scrollTo).length) {
                scrollTo = "[name='" + '_' + payload.formError.data.name + "']";
            }

            console.log($(document).find(scrollTo));

            $("html, body").animate({ scrollTop: ($(document).find(scrollTo).offset().top - 100) }, 1000);
        }
    }

});