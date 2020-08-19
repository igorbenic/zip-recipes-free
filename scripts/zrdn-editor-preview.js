jQuery(document).ready(function($) {
    $('body > :not(#zrdn-recipe-container)').hide(); //hide all nodes directly under the body
    $('#zrdn-recipe-container').appendTo('body');

    var fields = zrdnGetUrlParameters();

    for (var key in fields) {
        if (fields.hasOwnProperty(key)) {
            var field = fields[key];
            //in some cases, clean up sibs
            if ($(".zrdn-element_"+field.name).prop("tagName")==='OL' || $(".zrdn-element_"+field.name).prop("tagName")==='UL'){
                $(".zrdn-element_"+field.name).nextAll().remove();
            }
            $(".zrdn-element_"+field.name).html((field.value));
        }
    }
    maybeShowNutritionLabel(fields);
    zrdn_parse_time(fields);
    zrdn_get_video_embed(fields);
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
                console.log(field.value);
                if (field.name !== 'mode' && field.name !== 'recipe_image' ) {
                    zrdn_key_values.push(field);
                }
            }
        }

        return zrdn_key_values;

    }

    function zrdn_parse_time(fields){
        var prep = new Object();
        var cook = new Object();
        var wait = new Object();
        for (var key in fields) {

            if (fields.hasOwnProperty(key)) {
                var field = fields[key];
                var name = field.name;

                if (name.indexOf('prep') != -1) {
                    if (name.indexOf('minute') !== -1) {
                        prep.minutes = field.value;
                    }
                    if (name.indexOf('hour') !== -1) {
                        prep.hours = field.value;
                    }
                }

                if (name.indexOf('cook') != -1) {
                    if (name.indexOf('minute') !== -1) {
                        cook.minutes = field.value;
                    }
                    if (name.indexOf('hour') !== -1) {
                        cook.hours = field.value;
                    }
                }

                if (name.indexOf('wait') != -1) {
                    if (name.indexOf('minute') !== -1) {
                        wait.minutes = field.value;
                    }
                    if (name.indexOf('hour') !== -1) {
                        wait.hours = field.value;
                    }
                }

            }
            $('.zrdn-element_prep_time').html(getTimeString(prep.hours, prep.minutes));
            $('.zrdn-element_cook_time').html(getTimeString(cook.hours, cook.minutes));
            $('.zrdn-element_wait_time').html(getTimeString(wait.hours, wait.minutes));
            $('.zrdn-element_total_time').html(getTimeString(parseInt(prep.hours)+parseInt(cook.hours), parseInt(prep.minutes) + parseInt(cook.minutes)));

        }
    }

    function getTimeString(hours, minutes){
        var timeString = '';
        if (hours > 0) {
            timeString += hours + ' '+zrdn_editor_preview.str_hours;
        }

        if (minutes > 0) {
            if (timeString.length > 0) minutes = ', ' + minutes;
            timeString += minutes + ' '+ zrdn_editor_preview.str_minutes;
        }
        return timeString;
    }



    /**
     * hide nutrition label if no data available
     */

    function maybeShowNutritionLabel(fields){
        if (!$("#zrdn-nutrition-label").length) return;
        var caloriesField = getFieldByName(fields, 'calories');
        var label = $("#zrdn-nutrition-label");
        if (parseInt(caloriesField.value) === 0) {
            label.hide();
        } else {
            label.show();
        }
    }


    function zrdn_get_video_embed(fields){
        var videoField = getFieldByName(fields, 'video_url');
        var video_url = videoField.value;
        $.ajax({
            type: "GET",
            url: zrdn_editor_preview.admin_url,
            dataType: 'json',
            data: ({
                video_url : video_url,
                action: 'zrdn_get_embed_code',
            }),
            success: function (response) {
                if (response.success) {
                    $(".zrdn-element_"+videoField.name).html(response.embed);
                }
            }
        });
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
        var field = getFieldByName(fields, 'recipe_id');
        var recipe_id = field.value;
        // var placeholderImg = $('.zrdn-recipe-image').outerHTML();
        $('.zrdn-element_recipe_image').append('<div class="zrdn-image-controls"><div class="zrdn-edit-image-text"><div class="dashicons dashicons-edit"></div></div></div>');

        var link = '<span class="zrdn-image-controls-divider">|</span><a href="#" class="zrdn_remove_image"><div class="dashicons dashicons-trash"></div></a>';
        $('.zrdn-element_recipe_image .zrdn-image-controls').append(link);


        var media_uploader = null;
        $(document).on( 'click','.zrdn-element_recipe_image img, .dashicons-edit', function()
        {
            var img = $(this);
            media_uploader = wp.media({
                frame:    "post",
                state:    "insert",
                multiple: false
            });

            media_uploader.on("insert", function(){
                img.wrap( '<div class="loading-gif"></div>' );
                $(img).load(function(){
                    img.unwrap();
                });
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

                    img.attr('src', image_url);
                    // put thumbnail id in hidden field
                    // $('input[name=zrdn_recipe_image_id]').val(thumbnail_id);

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

                    } else {
                        image.parent().append(' Clearing image failed...');
                    }
                }
            });
        });

    }
});