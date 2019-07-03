jQuery(document).ready(function ($) {

    var args = top.tinymce.activeEditor.windowManager.getParams();
    var editor = args['editor'];
    var modal_width = args['width'];
    //move image to normal form
    if ($('.zrdn-recipe-image').length){
        var image = $('.zrdn-recipe-image').parent().html();
        $('#general').prepend('<div id="zrdn-popup-image">'+image+'</div>');
        $('#zrdn-popup-image').append('<div class="zrdn-edit-image-text">' +
            zrdn_editor.str_click_to_edit_image +
            '</div>');
    }

    $(document).on('click','.zrdn-recipe-save-button input',function(){
        //first, save this recipe
        var recipe_id = false;
        var formdata = $('#recipe-settings').serializeArray();
        $.ajax({
            type: "POST",
            url: zrdn_editor.admin_url,
            dataType: 'json',
            data: ({
                formdata : formdata,
                nonce : zrdn_editor.nonce,
                action: 'zrdn_update_recipe_from_popup',
            }),
            success: function (response) {
                console.log(response);
                if (response.success) {
                    recipe_id = response.recipe_id;
                    //classic only, so we use the classic shortcode
                    var shortcode = '[amd-zlrecipe-recipe:'+recipe_id+']';

                    // we're required to select the recipe if it exists, otherwise a new one is inserted on
                    // each update
                    var recipe = editor.dom.select('img.amd-zlrecipe-recipe')[0];
                    if (recipe) {
                        editor.selection.select(recipe);
                    }

                    editor.execCommand('mceInsertContent', false, shortcode);
                    top.tinymce.activeEditor.windowManager.close(window);
                }
            }
        });

    });

});