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

            /* FIXME
             editor.on('BeforeSetcontent', function(event){
             //console.log(event);
             event.content = t._convert_codes_to_imgs(event.content);
             //console.log('post');
             });
             */

            //replace shortcode as its inserted into editor (which uses the exec command)
            editor.onExecCommand.add(function (ed, cmd) {
                if (cmd === 'mceInsertContent') {
                    var bm = tinyMCE.activeEditor.selection.getBookmark();
                    tinyMCE.activeEditor.setContent(t._convert_codes_to_imgs(tinyMCE.activeEditor.getContent()));
                    tinyMCE.activeEditor.selection.moveToBookmark(bm);
                }
            });

            /* FIXME
             editor.on('ExecCommand', function(e) {
             console.log('ExecCommand event', e);
             something happens
             });
             */

            //replace the image back to shortcode on save
            editor.onPostProcess.add(function (ed, o) {
                if (o.get)
                    o.content = t._convert_imgs_to_codes(o.content);
            });

            // We need to restore these properties again in mobile devices
            // because we change them to stop background scrolling once modal is open.
            var $body = jQuery('body');
            var bodyProps = {
                'overflow': $body.css('overflow'),
                'position': $body.css('position')
            };

            editor.addButton('zrdn_buttons', {
                title: 'Zip Recipes',
                image: url + '/../images/zip-recipes-icon-20.png',
                onclick: function () {
                    var recipe_id = null;

                    var recipe = editor.dom.select('img.amd-zlrecipe-recipe')[0];
                    if (recipe) {
                        editor.selection.select(recipe);
                        recipe_id = /amd-zlrecipe-recipe-([0-9]+)/i.exec(editor.selection.getNode().id);

                        // If we don't collapse selection, when a recipe is updated, the image placeholder
                        // get selected and the image toolbar appears even above the modal which can be quite
                        // confusing as it's tempting to click X and make the recipe disappear altogether.
                        // I tried to save the bookmark before we select but just calling  editor.selection.getBookmark()
                        //  breaks recipe insertion (not updates thought).
                        // So we settle on collapse!
                        editor.selection.collapse();
                    }
                    var iframe_url = baseurl + '/wp-admin/media-upload.php?recipe_post_id=' + ((recipe_id) ? '1-' + recipe_id[1] : post_id) + '&type=z_recipe&tab=amd_zlrecipe&TB_iframe=true&width=640&height=523&post_id='+post_id;
                    var modal_width = Math.min(780, Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
                        - 20 // width buffer
                    );
                    var modal_height = Math.min(600,
                        Math.max(document.documentElement.clientHeight, window.innerHeight || 0) -
                        36*3 - // subtract height of title bar. Multiplied by 2 since it does some centering.
                        jQuery(window.parent.document.getElementById('wpadminbar')).height() // subtract header bar of WP admin
                    );
                    editor.windowManager.open({
                        title: recipe ? 'Edit Recipe' : 'Add a Recipe' ,
                        url: iframe_url,
                        // make it full screen if on mobile or set a maximum of 700x600
                        width: modal_width,
                        height: modal_height,
                        onClose: function(e) {

                            // We only change overflow and position props of body
                            // on screens smaller than 780. So, we don't need to revert this for all screens.
                            if (modal_width < 780) // is mobile
                            {
                                // restore these props since they're set from the iframe when popup is created
                                $body.css('overflow', bodyProps.overflow);
                                $body.css('position', bodyProps.position);
                            }
                        }
                    }, {
                        //	Windows Parameters/Arguments
                        editor: editor,
                        width: modal_width
                    });
                }
            });
        },
        _convert_codes_to_imgs: function (co) {
            if (co.indexOf('amd-zlrecipe-recipe') !== -1 ){
                return co.replace(/\[amd-zlrecipe-recipe:([0-9]+)\]/g, function (a, b) {
                    return '<img id="amd-zlrecipe-recipe-' + b + '" class="amd-zlrecipe-recipe" src="' + plugindir + '/images/zip-recipes-placeholder.png" alt="" />';
                });
            } else {
                return co.replace(/\[zrdn-recipe id="([0-9]+)"\]/g, function (a, b) {
                    console.log("recipe id "+b);
                    return '<img id="amd-zlrecipe-recipe-' + b + '" class="amd-zlrecipe-recipe" src="' + plugindir + '/images/zip-recipes-placeholder.png" alt="" />';
                });
            }
        },
        _convert_imgs_to_codes: function (co) {
            return co.replace(/\<img[^>]*?\sid="amd-zlrecipe-recipe-([0-9]+)[^>]*?\>/g, function (a, b) {
                return '[zrdn-recipe id="' + b + '"]';
            });
        },
        getInfo: function () {
            return {
                longname: "Zip Recipes Plugin",
                author: 'RogierLankhorst',
                authorurl: 'https://www.ziprecipes.net/',
                infourl: 'https://www.ziprecipes.net/',
                version: "6.0"
            };
        }
    });

    tinymce.PluginManager.add('zrdn_plugin', tinymce.plugins.zrdnEditRecipe);

})();
