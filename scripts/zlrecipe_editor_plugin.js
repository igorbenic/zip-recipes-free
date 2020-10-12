(function () {
    tinymce.create('tinymce.plugins.zrdnEditRecipe', {
        init: function (editor, url) {
            var t = this;
            t.url = url;

            //replace shortcode before editor content set
            editor.onBeforeSetContent.add(function (ed, o) {
                o.content = t._convert_codes_to_imgs(o.content);
            });

            //replace the image back to shortcode on save
            editor.onPostProcess.add(function (ed, o) {
                if (o.get)
                    o.content = t._convert_imgs_to_codes(o.content);
            });

            editor.addButton('zrdn_buttons', {
                title: 'Create or edit recipe',
                image: url + '/../images/zip-recipes-icon-20.png',
                onclick: function () {
                    var recipe_id = null;

                    var recipe = editor.dom.select('img.amd-zlrecipe-recipe')[0];
                    if (recipe) {
                        editor.selection.select(recipe);
                        recipe_id = editor.selection.getNode().id;
                        var matches = recipe_id.match(/amd-zlrecipe-recipe-([0-9]\d*)/i);
                        recipe_id = matches[1];

                        //redirect to recipe editor
                        window.location.href = baseurl + '/wp-admin/admin.php?page=zrdn-recipes&id='+recipe_id;
                    } else {
                        //redirect to recipe editor, create new
                        var post_type = $('#post_type').val();
                        window.location.href = baseurl + '/wp-admin/admin.php?page=zrdn-recipes&action=new&post_id='+post_id+'&post_type='+post_type;
                    }
                }
            });
        },
        _convert_codes_to_imgs: function (co) {
            return co.replace(/\[amd-zlrecipe-recipe:([0-9]+)\]/g, function (a, b) {
                return '<img id="amd-zlrecipe-recipe-' + b + '" class="amd-zlrecipe-recipe" src="' + plugindir + '/images/zip-recipes-placeholder.png" alt="" />';
            });
        },
        _convert_imgs_to_codes: function (co) {
            return co.replace(/\<img[^>]*?\sid="amd-zlrecipe-recipe-([0-9]+)[^>]*?\>/g, function (a, b) {
                return '[amd-zlrecipe-recipe:' + b + ']';
            });
        },
        getInfo: function () {
            return {
                longname: "Zip Recipes",
                author: 'RogierLankhorst',
                authorurl: 'https://ziprecipes.net/',
                infourl: 'https://ziprecipes.net/',
                version: "6.0"
            };
        }
    });

    tinymce.PluginManager.add('zrdn_plugin', tinymce.plugins.zrdnEditRecipe);

})();
