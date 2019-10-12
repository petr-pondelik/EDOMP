$(document).ready(() => {

    $editor = $(document).find('#editor');

    if($editor.length){

        let aceEditor = ace.edit("editor");

        $form = $(document).find('.ace-editor-form');
        $editorSubmit = $(document).find('.ace-editor-submit');
        $templateContent = $(document).find('.template-content');

        $editorSubmit.click(() => {
            console.log(aceEditor.getValue());
            let editorVal = aceEditor.getValue();
            $templateContent.text(editorVal);
            $form.submit();
        });

    }

});