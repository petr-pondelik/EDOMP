import * as FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

(($, imagePreview, fileValidateType) => {

    console.log('FILEPOND CONFIG');

    $.fn.filepond.registerPlugin(imagePreview);
    $.fn.filepond.registerPlugin(fileValidateType);

    let href = $(location).attr('href');

    if(href.includes('/logo/update')) {
        let logoId = $(document).find("#logo-id").val();
        $.fn.filepond.setOptions({
            allowMultiple: false,
            server: {
                process: '?logo_id=' + logoId + '&do=updateLogoFile',
                revert: '?logo_id=' + logoId + '&do=revertLogoFileUpdate'
            },
            allowImagePreview: true,
            allowFileTypeValidation: true,
            acceptedFileTypes: [
                'image/*',
                'application/pdf'
            ],
            labelIdle: 'Umístěte či <span class="filepond--label-action"> zvolte </span> soubor.'
        });
    }
    else{
        $.fn.filepond.setOptions({
            allowMultiple: false,
            server: {
                process: '?do=uploadLogoFile',
                revert: '?do=revertLogoFileUpload'
            },
            allowImagePreview: true,
            allowFileTypeValidation: true,
            acceptedFileTypes: [
                'image/*',
                'application/pdf'
            ],
            labelIdle: 'Umístěte či <span class="filepond--label-action"> zvolte </span> soubor.'
        });
    }

    $('.file-pond-input').filepond();

})($, FilePondPluginImagePreview, FilePondPluginFileValidateType);