/*
 License: GPLv3 or later
 
 Copyright 2014 Gezim Hoxha
 This code is derived from the 2.6 version build of ZipList Recipe Plugin released by ZipList Inc.:
 http://get.ziplist.com/partner-with-ziplist/wordpress-recipe-plugin/ and licensed under GPLv3 or later
 */

/*
 This file is part of Zip Recipes Plugin.
 
 Zip Recipes Plugin is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 Zip Recipes Plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Zip Recipes Plugin. If not, see <http://www.gnu.org/licenses/>.
 */

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
                image: url + '/../images/zip-recipes-icon-16.png',
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
                version: "5.0"
            };
        }
    });

    tinymce.PluginManager.add('zrdn_plugin', tinymce.plugins.zrdnEditRecipe);

})();
