import * as FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

$.fn.filepond.registerPlugin(FilePondPluginImagePreview);
$.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

let href = $(location).attr('href');

let logoId = null;

if(href.includes('/logo/edit')) {

    let logoId = $(document).find("#logo-id").val();

    console.log(logoId);

    $.fn.filepond.setOptions({
        allowMultiple: false,
        server: {
            process: '?logo_id=' + logoId + '&do=updateFile',
            revert: '?logo_id=' + logoId + '&do=revertFileUpdate'
        },
        allowImagePreview: true,
        allowFileTypeValidation: true,
        acceptedFileTypes: [
            'image/*',
            'application/pdf'
        ]
    });

}
else{

    $.fn.filepond.setOptions({
        allowMultiple: false,
        server: {
            process: '?do=uploadFile',
            revert: '?do=revertFileUpload'
        },
        allowImagePreview: true,
        allowFileTypeValidation: true,
        acceptedFileTypes: [
            'image/*',
            'application/pdf'
        ]
    });

}

$('.file-pond-input').filepond();