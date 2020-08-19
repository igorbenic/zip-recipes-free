jQuery(document).ready(function ($) {
    $(".zrdn-recipe-save-button .button-primary").show();
    var args = top.tinymce.activeEditor.windowManager.getParams();
    var editor = args['editor'];
    var modal_width = args['width'];

    //prevent form submit in popup mode
    $("#recipe-settings").submit(function(){
        return false;
    });

    $('.zrdn-recipe-save-button .button.exit').each(function(e){
        var w = $('.zrdn-recipe-save-button .button.save').width()+30;
        $(this).css('right',w+'px');
    });

    $(document).on('click','.zrdn-recipe-save-button .button', function(e){
        var btn = $(this);
        var oldBtnHtml = btn.html();
        //check if any required fields are empty
        $(".is-required").each(function(e){
            if ($(this).val().length==0){
                $("#zrdn-show-field-error").show();
                e.preventDefault();
                return;
            }
        });

        //add tiny mce editors to form
        for (var i = 0; i < tinymce.editors.length; i++) {
            var id = tinymce.editors[i].id;
            var content = tinymce.editors[i].getContent();
            $('textarea[name='+id+']').val(content);
        }

        //now, save this recipe
        var recipe_id = $('input[name=zrdn_recipe_id]').val();
        var formdata = $('#recipe-settings').serializeArray();

        btn.html('...');
        btn.html('<div class="zrdn-loader zrdn-loader-white"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');

        $.ajax({
            type: "POST",
            url: zrdn_editor.admin_url,
            dataType: 'json',
            data: ({
                formdata : formdata,
                recipe_id : recipe_id,
                nonce : zrdn_editor.nonce,
                action: 'zrdn_update_recipe_from_popup',
            }),
            success: function (response) {
                if (response.success) {
                    $("#zrdn-show-field-error").hide();

                    if ($("#nutrition_save_recipe_first").length) {
                        $("#nutrition_save_recipe_first").hide();
                        $("#nutrition_action_buttons").show();
                    }
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
                    if (btn.hasClass('exit')){
                        top.tinymce.activeEditor.windowManager.close(window);
                    }
                    btn.html(oldBtnHtml);


                }
            }
        });

    });

});