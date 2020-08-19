jQuery(document).ready(function ($) {
    var typingTimer;
    var doneTypingInterval = 800;

    $(document).on('click', '.zrdn-add-author', function () {
        var field = $('.zrdn-template').get(0).outerHTML;
        $(this).closest('.field-group').find('.zrdn-author-frame').append(field);
        $(this).closest('.field-group').find('.zrdn-author-frame .zrdn-hidden').removeClass('zrdn-hidden');
    });
    $(document).on('click', '.zrdn-delete-author', function () {
        $(this).closest('.zrdn-author-container').remove();
    });

    $.fn.outerHTML = function () {

        // IE, Chrome & Safari will comply with the non-standard outerHTML, all others (FF) will have a fall-back for cloning
        return (!this.length) ? this : (this[0].outerHTML || (
            function (el) {
                var div = document.createElement('div');
                div.appendChild(el.cloneNode(true));
                var contents = div.innerHTML;
                div = null;
                return contents;
            })(this[0]));

    };

    /**
     * Auto Clean extra spaces
     *
     * On paste in textarea removes extra spaces and lines
     */

    $(".zrdn-field-textarea").on('paste', function (e) {
        var $elem = $(this);
        // setTimeout is required here because paste event is triggered before content is pasted
        //  in element.
        window.setTimeout(function () {
            var lines = $elem.val().split(/\n/);
            var texts = [];
            for (var i = 0; i < lines.length; i++) {
                if (/\S/.test(lines[i])) {
                    texts.push($.trim(lines[i]));
                }
            }
            var n = texts.join("\n");
            $elem.val(n);
        }, 500);
    });

    /**
     * Tabs
     */

    $(document).on('click', '.zrdn-tablinks', function () {
        $(".zrdn-tablinks").removeClass('active');
        $(this).addClass('active');
        $(".zrdn-tabcontent").removeClass('active');
        $("#" + $(this).data('tab')).addClass('active');
        $('input[name=zrdn_active_tab]').val($(this).data('tab'));
    });

    /**
     *  Initialize the preview fields with placeholders
     */
    var preview = $('#zrdn-preview');
    syncPreview();
    function syncPreview(forceField){
        forceField = typeof forceField !== 'undefined' ? forceField : false;

        if (preview.length) {
        var field;
        var zrdn_variables = [];
        $('.zrdn-field input').each(function () {
            var name = $(this).attr("name");
            if (typeof name === 'undefined') return;
            field = newField(name, $(this).val() );
            zrdn_variables.push(field);
        });

        $('.zrdn-field textarea').each(function () {
            if ($(this).hasClass('wp-editor-area')) return;
            var name = $(this).attr("name");
            if (typeof name === 'undefined') return;
            field = newField(name, zrdn_parse_textarea($(this)));
            zrdn_variables.push(field);
        });

        if (forceField) {
            zrdn_variables.push(forceField);
        }

        field = newField('recipe_id', $('input[name=zrdn_recipe_id]').val());
        zrdn_variables.push(field);

        var permalink = $('input[name=zrdn_post_permalink]').val();
        if (permalink.length ===0 ) return;

        var url = permalink+'?mode=zrdn-preview';
        for (var key in zrdn_variables) {
            if (zrdn_variables.hasOwnProperty(key)) {
                field = zrdn_variables[key];
                url += ('&' + field.name + '=' + field.value);
            }
        }

        //get page from front end based on post id
        preview.html('<iframe id="preview_iframe" width="1024" height="0" src="' + url+'"></iframe>');
        preview.find('iframe').attr('src', url).on( 'load' , function () {
            var preview_iframe = document.getElementById('preview_iframe');
            resizeIframe(preview_iframe);
            preview.animate({"height": "768px"});
        });
    }
}


    // //Was needed a timeout since RTE is not initialized when this code run.
    setTimeout(function () {
        if (typeof tinymce === 'undefined') {
            return;
        }

        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].onChange.add(function (ed, e) {
                var name = ed.id;
                var field = newField(name, ed.getContent());
                syncPreview(field);
            });

            tinymce.editors[i].onKeyUp.add(function (ed, e) {
                var name = ed.id;
                var field = newField(name, ed.getContent());
                syncPreview(field);
            });
        }
    }, 1000);


    function resizeIframe( frame ) {
        var b = frame.contentWindow.document.body || frame.contentDocument.body,
            cHeight = $(b).height();

        if( frame.oHeight !== cHeight ) {
            $(frame).height( 0 );
            frame.style.height = 0;

            $(frame).height( cHeight );
            frame.style.height = parseInt(cHeight) + 200 + "px";

            frame.oHeight = cHeight;
        }

        // // Call again to check whether the content height has changed.
        // setTimeout( function() { resize( frame ); }, 250 );
    }


    /**
     * manage recipe field sync with preview
     */

    $(document).on('keyup', 'input[type=text]', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(syncPreview, doneTypingInterval);
    });

    $(document).on('keydown', 'input[type=text]', function (e) {
        clearTimeout(typingTimer);
    });

    /**
     * time
     */

    $(document).on('keyup mouseup', 'input[type=number]', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(syncPreview, doneTypingInterval);
    });

    $(document).on('keydown mousedown', 'input[type=number]', function (e) {
        clearTimeout(typingTimer);
    });

    /**
     * Textarea's are lists, so we split in arrays for lists here
     */

    $(document).on('keyup', 'textarea', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(syncPreview, doneTypingInterval);
    });

    $(document).on('keydown', 'textarea', function (e) {
        clearTimeout(typingTimer);
    });

    function newField(name, value) {
        var field = new Object();
        field.name = name.replace('zrdn_', '');
        field.value = encodeURIComponent(value);
        return field;
    }

    function zrdn_parse_textarea(obj) {

        var parentTag = 'UL';

        if (obj.attr('name').indexOf('instructions') != -1) {
            parentTag = $('input[name=zrdn_instructions_list_type]').val();
        } else {
            parentTag = $('input[name=zrdn_ingredients_list_type]').val();
        }
        var text  = obj.val();

        var placeholderHtml = '';
        var values = text.split("\n");

        //remove empty lines
        values = values.filter(Boolean);
        values.forEach(function (element) {
            //check if this is multi part
            if (element.substring(0, 1) === '!') {
                placeholderHtml += '</' + parentTag + '><b>' + element.substring(1) + '</b><' + parentTag + '>';

                //images
            } else if (element.substring(0, 1) === '%') {

                var regex = /(?:%)([http|https?:\/\/=?\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&'\(\)\*\+,;=.\/]+[a-zA-N])/g;
                var match = element.match(regex);
                if (match) {
                    var percent_image = match[0];
                    var image = percent_image.substring(1);
                    placeholderHtml += '<li>' + element.replace(percent_image, '<img style="max-width:100%" src="' + image + '">') + '</li>';
                }
            } else {
                //default
                placeholderHtml += '<li>' + element + '</li>';
            }
        });

        if (placeholderHtml.length) {
            //handle bold
            var i;
            var regex = /(\*[a-zA-Z\-\_\(\)\<\>\/\\\$\!\@\#\:\;\.\,\?\+\=]*\*)/g;
            var matches = placeholderHtml.match(regex);
            if (matches && matches.length) {
                for (i = 0; i < matches.length; i++) {
                    var match = matches[i];
                    var bold = match;
                    bold = bold.replace(/\*$/i, "</b>").replace(/^\*/i, "<b>");
                    placeholderHtml = placeholderHtml.replace(match, bold);
                }
            }
            //hyperlinks
            regex = /\[([^\]\|\[]*)\|([^\]\|\[]*)\]/g;
            matches = placeholderHtml.match(regex);
            if (matches && matches.length) {
                for (i = 0; i < matches.length; i++) {
                    placeholderHtml = placeholderHtml.replace(/\[([^\]\|\[]*)\|([^\]\|\[]*)\]/i, '<a target="_blank" href="' + '$2' + '">'+'$1'+'</a>');
                }
            }

            //italic
            regex = /(\_[a-zA-Z\-\_\(\)\<\>\/\\\$\!\@\#\:\;\.\,\?\+\=]*\_)/g;
            matches = placeholderHtml.match(regex);
            if (matches && matches.length) {
                for (i = 0; i < matches.length; i++) {
                    var match = matches[i];
                    if (match === '_placeholder_') return;

                    var bold = match;
                    bold = bold.replace(/\_$/i, "</i>").replace(/^\_/i, "<i>");
                    placeholderHtml = placeholderHtml.replace(match, bold);
                }
            }
        }

        //preserve linebreaks by transforming them to br
        console.log(parentTag);
        placeholderHtml = '<' + parentTag + '>'+placeholderHtml+'</' + parentTag + '>';
        console.log(placeholderHtml);

        return placeholderHtml;
    }







    /**
     * Rich snippets uploader
     */
    $(document).on('click','.zrdn-image-reset',function(){
        var btn = $(this);
        var container = btn.closest('.zrdn-field');
        var textField = container.find('.zrdn-image-upload-field');
        var fieldname = textField.attr('name');
        container.find('.zrdn-preview-snippet').attr('src',zrdn_editor.image_placeholder);
        $('input[name='+fieldname+'_id]').val('');
        $('input[name='+fieldname+']').val('');
    });

    $(document).on( 'click','.zrdn-image-uploader', function()
    {
        var btn = $(this);
        var container = btn.closest('.zrdn-field');
        var textField = container.find('.zrdn-image-upload-field');
        var size = textField.data('size');
        var fieldname = textField.attr('name');
        //cleanup
        container.find('.zrdn-image-resolution-warning').hide();

        media_uploader = wp.media({
            frame:    "post",
            state:    "insert",
            multiple: false
        });

        media_uploader.on("insert", function(){

            container.append( '<div class="loading-gif"></div>' );

            var length = media_uploader.state().get("selection").length;
            var images = media_uploader.state().get("selection").models;

            for(var iii = 0; iii < length; iii++)
            {
                var thumbnail_id = images[iii].id;
                var image = false;
                console.log(images[iii]);
                if (images[iii].attributes.sizes.hasOwnProperty(size)) {
                    image = images[iii].attributes.sizes[size];
                } else if(images[iii].attributes.sizes.hasOwnProperty(size+'_s')) {
                    image = images[iii].attributes.sizes[size+'_s'];
                } else if(images[iii].attributes.sizes.hasOwnProperty('thumbnail')) {
                    image = images[iii].attributes.sizes['thumbnail'];
                }

                if (image) {
                    var image_url = image['url'];
                    container.find('.zrdn-preview-snippet').attr('src',image_url);
                    $('input[name='+fieldname+'_id]').val(thumbnail_id);
                    $('input[name='+fieldname+']').val(image_url);

                } else {
                    container.find('.zrdn-image-resolution-warning').show();
                }

            }
            container.find('.loading-gif').remove();
        });

        media_uploader.open();
    });

});
