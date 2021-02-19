jQuery(document).ready(function($) {
    $('body > :not(#zrdn-recipe-container)').hide(); //hide all nodes directly under the body
    $('#zrdn-recipe-container').appendTo('body');

    var fields = zrdnGetUrlParameters();
    var recipeField = getFieldByName(fields, 'id');
    var recipe_id = recipeField.value;
    enableImageEditor(fields);

    function zrdnGetUrlParameters() {
        var sPageURL = window.location.href;
        var queryString = sPageURL.split('?');
        if (queryString.length === 1) return false;

        var zrdn_variables = queryString[1].split('&');
        var zrdn_key_values = [];
        for (var key in zrdn_variables) {
            if (zrdn_variables.hasOwnProperty(key)) {
                var output = zrdn_variables[key].split('=');
                var field = new Object();
                field.name = output[0];
                field.value = decodeURIComponent( output[1] );
                if (field.name !== 'mode' && field.name !== 'recipe_image' ) {
                    zrdn_key_values.push(field);
                }
            }
        }

        return zrdn_key_values;
    }

    function getFieldByName(fields, name){
        for (var key in fields) {
            if (fields.hasOwnProperty(key)) {
                var field = fields[key];
                if (field.name === name) {
                    return field;
                }
            }
        }
        return false;
    }


    function enableImageEditor(fields){
        if (!recipe_id) return;

        $('.zrdn-element_recipe_image').append('<div class="zrdn-image-controls"><div class="zrdn-edit-image-text"><div class="dashicons dashicons-edit"></div></div></div>');
        var link = '<span class="zrdn-image-controls-divider">|</span><a href="#" class="zrdn_remove_image"><div class="dashicons dashicons-trash"></div></a>';
        $('.zrdn-element_recipe_image .zrdn-image-controls').append(link);

        var media_uploader = null;
        var zrdn_img;
        $(document).on( 'click','.zrdn-element_recipe_image img, .dashicons-edit', function()
        {
            if ($(this).hasClass('dashicons-edit')){
                zrdn_img = $(this).closest('.zrdn-recipe_image').find('img');
            } else {
                zrdn_img = $(this);
            }

            media_uploader = wp.media({
                frame:    "post",
                state:    "insert",
                multiple: false
            });

            media_uploader.on("insert", function(){
                var length = media_uploader.state().get("selection").length;
                var images = media_uploader.state().get("selection").models;
                for(var iii = 0; iii < length; iii++)
                {
                    var thumbnail_id = images[iii].id;
                    var image;
                    if (images[iii].attributes.sizes.hasOwnProperty('zrdn_recipe_image_main')) {
                        image = images[iii].attributes.sizes['zrdn_recipe_image_main'];
                    } else if(images[iii].attributes.sizes.hasOwnProperty('zrdn_recipe_image')) {
                        image = images[iii].attributes.sizes['zrdn_recipe_image'];
                    } else if(images[iii].attributes.sizes.hasOwnProperty('large')) {
                        image = images[iii].attributes.sizes['large'];
                    } else {
                        image = images[iii].attributes.sizes['full'];
                    }
                    var image_url = image['url'];

                    //save new value
                    $.ajax({
                        type: "POST",
                        url: zrdn_editor_preview.admin_url,
                        dataType: 'json',
                        data: ({
                            recipe_id : recipe_id,
                            recipe_image_id : thumbnail_id,
                            recipe_image : image_url,
                            action: 'zrdn_update_recipe_image',
                        }),
                        success: function (response) {
                            if (response.success) {
                                zrdn_img.attr('src', image_url);
                                zrdn_img.attr('srcset', image_url);
                                $('input[name=zrdn_recipe_image_id]', window.parent.document).val(thumbnail_id);
                                $('.zrdn-preview-snippet', window.parent.document).attr('src', image_url);
                            }
                        }
                    });

                }
            });

            media_uploader.open();
        });

        /**
         * remove image
         */
        $(document).on('click','.zrdn_remove_image, .dashicons-trash',function(event){
            event.preventDefault();

            var image = $(this).closest('.zrdn-element_recipe_image').find('img');
            $.ajax({
                type: "POST",
                url: zrdn_editor_preview.admin_url,
                dataType: 'json',
                data: ({
                    recipe_id : recipe_id,
                    nonce : zrdn_editor_preview.nonce,
                    action: 'zrdn_clear_image',
                }),
                success: function (response) {
                    if (response.success) {
                        image.attr('src', zrdn_editor_preview.default_image);
                        image.attr('srcset', zrdn_editor_preview.default_image);
                        // $('input[name=zrdn_recipe_image_id]').val(0);
                        $('input[name=zrdn_recipe_image_id]', window.parent.document).val(0);
                        $('.zrdn-preview-snippet', window.parent.document).remove();
                    } else {
                        image.parent().append(' Clearing image failed...');
                    }
                }
            });
        });

    }
});