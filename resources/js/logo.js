// Handle Logo Edit section file selection
$(document).ready(() => {
    $(document).on("change", "#edit-logo", (e) => {
        $(document).find("#logo-file-wrapper").toggleClass("d-none");
    });
});