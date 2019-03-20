<?php

/* create-update-recipe.twig */
class __TwigTemplate_8aee5e300faeb74a7d121bfc21d3c6aa9246a8d8019c33a75d3f29a265ab0275 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<!--suppress HtmlUnknownTarget -->
<head>
    <link rel=\"stylesheet\" href=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["pluginurl"] ?? null), "html", null, true);
        echo "/styles/zlrecipe-dlog.css?version=2\" type=\"text/css\" media=\"all\" />
    <link rel=\"stylesheet\" href=\"";
        // line 5
        echo twig_escape_filter($this->env, ($context["pluginurl"] ?? null), "html", null, true);
        echo "/styles/bulma-minireset-generic.css?version=1.1\" type=\"text/css\" media=\"all\" />
    <link rel=\"stylesheet\" href=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["pluginurl"] ?? null), "html", null, true);
        echo "/styles/bulma.css?version=2.1\" type=\"text/css\" media=\"all\" />
    <script src=\"https://use.fontawesome.com/5ada765508.js\"></script>
    <script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js\"></script>
    ";
        // line 9
        echo ($context["header_tags"] ?? null);
        echo "
    <script type=\"text/javascript\">
        var args = top.tinymce.activeEditor.windowManager.getParams();
        var editor = args['editor'];
        var modal_width = args['width'];

        function amdZLRecipeSubmitForm() {
            var title = document.forms['recipe_form']['recipe_title'].value;

            if (title === null || title === '') {
                var \$recipeTitleContainer = jQuery('#title-container');
                \$recipeTitleContainer.append('<p class=\"zrdn-help zrdn-is-danger\">You must enter a title for your recipe.</p>');

                return false;
            }

            var \$ingredientsContainer = \$('#ingredients-container');
            var \$ingredientsTextarea = \$ingredientsContainer.find('textarea');
            var ingredients = \$ingredientsTextarea.val();
            if (ingredients === null || ingredients === '' || ingredients === undefined) {
                \$ingredientsContainer.append('<p class=\"zrdn-help zrdn-is-danger\">You must enter at least one ingredient.</p>');

                return false;
            }
            var shortcode = '[amd-zlrecipe-recipe:";
        // line 33
        echo twig_escape_filter($this->env, ($context["recipe_id"] ?? null), "html", null, true);
        echo "]';

            // we're required to select the recipe if it exists, otherwise a new one is inserted on
            // each update
            var recipe = editor.dom.select('img.amd-zlrecipe-recipe')[0];
            if (recipe) {
                editor.selection.select(recipe);
            }

            editor.execCommand('mceInsertContent', false, shortcode);
            top.tinymce.activeEditor.windowManager.close(window);
        }

        \$(document).ready(function () {
            var \$moreOptions = \$('#more-options');
            var \$moreOptionsToggle = \$('#more-options-toggle');
            \$moreOptions.hide();
            \$moreOptionsToggle.click(function () {
                if (\$moreOptions.is(':visible')) {
                    \$moreOptions.hide(400);
                    \$moreOptionsToggle.html('<span class=\"zrdn-icon\"><i class=\"fa fa-plus\"></i></span><span>More options</span>');
                } else {
                    \$moreOptions.show(400, function () {
                        jQuery('html, body').animate({
                            scrollTop: \$moreOptions.offset().top
                        }, 250);
                    });
                    \$moreOptionsToggle.html('<span class=\"zrdn-icon\"><i class=\"fa fa-minus\"></i></span><span>Fewer options</span>');
                }

                return false;
            });

            \$('#upload-btn').click(function (e) {
                e.preventDefault();
                window.parent.zrdnAddImageHandler(zrdnRecipeImageSelected);
            });

            \$('#skip-button').click(function (e) {
                e.preventDefault();
                Cookies.set('skip-registration', 1, {expires: 7, path: '/'});
                \$(this).closest('form').remove();
            });

            var recipe_image_url = \$('#recipe_image').val();
            if (recipe_image_url)
            {
                zrdnRecipeImageSelected({url: recipe_image_url});
            }

            // Set focus to title because in WP4.3 focus remains in the tinyMCE editor
            \$title = \$('#recipe-title');
            \$title.focus();

            // Set data-caption attr of summary textarea to false if user types in it or if there's text in it.
            // We keep track of contents of summary (aka description) field to know whether we
            // should set the description to image caption when an image with a caption is set.
            var \$recipeSummaryTextarea = \$('textarea#summary');
            // when data is added to summary, set caption to false or if it has a value
            \$recipeSummaryTextarea.keyup(function ()
            {
                \$recipeSummaryTextarea.data('caption', 'false');
            });
            if (\$recipeSummaryTextarea.val())
            {
                \$recipeSummaryTextarea.data('caption', 'false');
            }

            /**
             * Auto Clean extra spaces
             *
             * On paste in textarea removes extra spaces and lines
             */
            \$(\".clean-on-paste\").on('paste', function (e) {
                var \$elem = \$(this);
                // setTimeout is required here because paste event is triggered before content is pasted
                //  in element.
                window.setTimeout(function () {
                    var lines = \$elem.val().split(/\\n/);
                    var texts = [];
                    for (var i=0; i < lines.length; i++) {
                        if (/\\S/.test(lines[i])) {
                            texts.push(\$.trim(lines[i]));
                        }
                    }
                    var n = texts.join(\"\\n\");
                    \$elem.val(n);
                }, 500);
            });

        });


        function zrdnRecipeImageSelected(imageData) {
            // This will return the selected image from the Media Uploader, the result is an object

            // Get image container
            // Show it
            // Set \$image src to url
            // scale image
            if (imageData.url)
            {
                var \$imageContainer = \$('#recipe-image-preview-container'),
                        \$uploadImageContainer = \$('#upload-recipe-image-button-container');

                // Set UI state
                \$imageContainer.show();
                \$uploadImageContainer.hide();

                \$('#recipe_image').val(imageData.url);

                var \$image = \$('#recipe-image-preview').
                        attr('src', imageData.url);

                var defaultHeight = 100;

                // width seems to be set proportionally!
                \$image.height(defaultHeight);

                // Set Recipe Summary (aka description) to be the same as image caption if image caption exists and
                //  if summary hasn't been set by user already (i.e. data-caption attr of summary textarea is true)
                var \$recipeSummaryTextarea = \$('textarea#summary');
                if (imageData.caption && \$recipeSummaryTextarea.data('caption') === true)
                {
                    \$recipeSummaryTextarea.val(imageData.caption);
                    \$('#more-options').show(400);
                }
            }
        }

        jQuery(function () {
            // These are needed to prevent background from scrolling when modal is open on mobile
            // They get reset onClose of the modal.
            var \$parentBody = jQuery(window.parent.document.body);

            if (modal_width < 780) // is mobile
            {
                \$parentBody.css('overflow', 'hidden');
                \$parentBody.css('position', 'fixed');
            }

            jQuery('#amd-zlrecipe-uploader').height(jQuery(window.document).height());
        });

        function zrdnResetImage()
        {
            var \$imageContainer = \$('#recipe-image-preview-container'),
                    \$uploadImageContainer = \$('#upload-recipe-image-button-container');

            \$imageContainer.hide();
            \$uploadImageContainer.show();

            \$('#recipe_image').val('');
        }
    </script>
    ";
        // line 188
        if (($context["post_info"] ?? null)) {
            // line 189
            echo "        <script>
            /**
             * This form lives in a modal. The way it works is that client-side validation happens
             * after the form is submitted (after the reload). This is totally backwards but I'm leaving it in
             * for now since the proper fix requires an AJAX submit to happen.
             * If we make validation happen on submit, we don't know when to close the popup window and can
             * kill the submission process if we close too quickly. The way to fix this is do an AJAX request
             * with form data in which case we know when submit is finished and if it was valid.
             *
             * This is only added when the request is POSTed. Meaning, validation only happens upon form submission.
             * It can go two ways: fail client side validation or it succeeds and closes the modal.
             *
             * This is weird as it probably already saves anything you pass as a recipe before client validation stars.
             * ***********TODO****: change this to use AJAX.
             */
            window.onload = amdZLRecipeSubmitForm;
        </script>
    ";
        }
        // line 207
        echo "</head>

<body id=\"amd-zlrecipe-uploader\">
    ";
        // line 211
        echo "        <form id=\"recipe-form\" class=\"entry-wrapper\" enctype='multipart/form-data' method='post' action='' name='recipe_form'>
            ";
        // line 212
        if (($context["registration_required"] ?? null)) {
            // line 213
            echo "                <h3 >Please <a href=\"";
            echo ($context["recipe_url"] ?? null);
            echo "&register=1\">register</a> ZipRecipes plugin, it is free.</h3>
            ";
        }
        // line 215
        echo "            <div id=\"recipe-form-elements\">
                <input type='hidden' name='recipe_post_id' value='";
        // line 216
        echo ($context["id"] ?? null);
        echo "' />
                <input type='hidden' name='recipe_id' value='";
        // line 217
        echo ($context["recipe_id"] ?? null);
        echo "' />

                <!-- Title and image -->
                <div class=\"zrdn-columns zrdn-is-mobile\">
                    <div class=\"zrdn-column zrdn-is-three-quarters-tablet zrdn-is-two-thirds-mobile\">
                        <div class=\"zrdn-field\">
                            <label for=\"recipe-title\" class=\"zrdn-label\">Title</label>
                            <div class=\"zrdn-control\" id=\"title-container\">
                                <input class=\"zrdn-input zrdn-is-small\" type=\"text\" id=\"recipe-title\" name='recipe_title' value='";
        // line 225
        echo ($context["recipe_title"] ?? null);
        echo "' placeholder=\"Recipe title\">
                            </div>
                        </div>
                        ";
        // line 228
        echo ($context["author_section"] ?? null);
        echo "
                    </div>
                    <div class=\"zrdn-column\">
                        <label class=\"zrdn-label\">Image</label>
                        ";
        // line 232
        if (($context["is_featured_post_image"] ?? null)) {
            // line 233
            echo "                            <input type='hidden' id=\"recipe_image\" name='recipe_image' value='' />
                        ";
        } else {
            // line 235
            echo "                            <input type='hidden' id=\"recipe_image\" name='recipe_image' value='";
            echo ($context["recipe_image"] ?? null);
            echo "' />
                        ";
        }
        // line 236
        echo "                        
                        <div id=\"upload-recipe-image-button-container\">
                            <a class=\"zrdn-button zrdn-is-small\" id=\"upload-btn\" href=\"#\">Add Image</a>
                        </div>
                        <div id=\"recipe-image-preview-container\" style=\"display: none;\">
                            <img id=\"recipe-image-preview\" src=\"\" style=\"display: block\" />
                            <a href=\"javascript:zrdnResetImage()\">Remove Image</a>
                        </div>
                    </div>
                </div>
                <!-- Title and image end -->

                <div id=\"ingredients-container\" class=\"zrdn-field\">
                    <label class=\"zrdn-label\" for=\"ingredients\">";
        // line 249
        echo call_user_func_array($this->env->getFunction('__')->getCallable(), array("Ingredients", "zip-recipes"));
        echo "</label>
                    <p class=\"zrdn-help\">
                        ";
        // line 251
        echo call_user_func_array($this->env->getFunction('__')->getCallable(), array("Put each ingredient on a separate line.  There is no need to use bullets for your ingredients.<br />
                        You can also create labels, hyperlinks, bold/italic effects and even add images!", "zip-recipes"));
        // line 252
        echo "
                        <br />
                        <a href=\"https://www.ziprecipes.net/docs/installing/\" target=\"_blank\">Learn how here</a>
                    </p>
                    <div class=\"zrdn-control\">
                        <textarea class=\"zrdn-textarea clean-on-paste\" name='ingredients' id='ingredients'>";
        // line 257
        echo ($context["ingredients"] ?? null);
        echo "</textarea>
                    </div>
                </div>

                <div class=\"zrdn-field\">
                    <label class=\"zrdn-label\" for=\"instructions\">";
        // line 262
        echo call_user_func_array($this->env->getFunction('__')->getCallable(), array("Instructions", "zip-recipes"));
        echo "</label>
                    <p class=\"zrdn-help\">
                        Press return after each instruction. There is no need to number your instructions.<br />
                        You can also create labels, hyperlinks, bold/italic effects and even add images!<br />
                        <a href=\"https://www.ziprecipes.net/docs/installing/\" target=\"_blank\">Learn how here</a>
                    </p>
                    <div class=\"zrdn-control\">
                        <textarea class=\"zrdn-textarea clean-on-paste\" id=\"instructions\" name='instructions'>";
        // line 269
        echo ($context["instructions"] ?? null);
        echo "</textarea>
                    </div>
                </div>

                ";
        // line 273
        echo ($context["yield_section"] ?? null);
        echo "

                <div id='more-options'>
                    <hr />
                    <div class=\"zrdn-columns zrdn-is-mobile\">
                        <div class=\"zrdn-column\">
                            <div class=\"zrdn-field\">
                                <label for=\"category\" class=\"zrdn-label\">Category</label>
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" id=\"category\" placeholder=\"e.g. appetizer, entree, etc.\" type='text' name='category' value='";
        // line 282
        echo ($context["category"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                        <div class=\"zrdn-column\">
                            <div class=\"zrdn-field\">
                                <label for=\"cuisine\" class=\"zrdn-label\">Cuisine</label>
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" placeholder=\"e.g. French, Ethiopian, etc.\" type='text' id=\"cuisine\" name='cuisine' value='";
        // line 290
        echo ($context["cuisine"] ?? null);
        echo "' /></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field\">
                        <label class=\"zrdn-label\" for=\"summary\">Description</label>
                        <div class=\"zrdn-control\">
                            <textarea class=\"zrdn-textarea\" id='summary' name='summary' data-caption=\"true\">";
        // line 299
        echo ($context["summary"] ?? null);
        echo "</textarea>
                        </div>
                    </div>

                    <div class=\"zrdn-columns zrdn-is-tablet\">
                        <div class=\"zrdn-column\">
                            <label for=\"prep_hours\" class=\"zrdn-label\">Prep Time</label>
                            <div class=\"zrdn-field zrdn-is-grouped\">
                                <div>
                                    <input class=\"zrdn-input zrdn-is-small\" type='number' min=\"0\" id=\"prep_hours\" name='prep_time_hours' value='";
        // line 308
        echo ($context["prep_time_hours"] ?? null);
        echo "' />hours
                                </div>
                                <div>
                                    <input class=\"zrdn-input zrdn-is-small\" type='number' min=\"0\" id=\"prep_minutes\" name='prep_time_minutes' value='";
        // line 311
        echo ($context["prep_time_minutes"] ?? null);
        echo "' />minutes
                                </div>
                            </div>
                        </div>
                        <div class=\"zrdn-column\">
                            <label for=\"cook_hours\" class=\"zrdn-label\">Cook Time</label>
                            <div class=\"zrdn-field zrdn-is-grouped\">
                                <div>
                                    <input class=\"zrdn-input zrdn-is-small\" type='number' min=\"0\" id=\"cook_hours\" name='cook_time_hours' value='";
        // line 319
        echo ($context["cook_time_hours"] ?? null);
        echo "' />hours
                                </div>
                                <div>
                                    <input class=\"zrdn-input zrdn-is-small\" type='number' min=\"0\" id=\"cook_minutes\" name='cook_time_minutes' value='";
        // line 322
        echo ($context["cook_time_minutes"] ?? null);
        echo "' />minutes
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field\">
                        <label class=\"zrdn-label\" for=\"notes\">Notes</label>
                        <div class=\"zrdn-control\">
                            <textarea class=\"zrdn-textarea\" id=\"notes\" name='notes'>";
        // line 331
        echo ($context["notes"] ?? null);
        echo "</textarea>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal zrdn-is-mobile\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"serving_size\">Serving Size</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" id=\"serving_size\" type='text' name='serving_size' value='";
        // line 342
        echo ($context["serving_size"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"calories\">Calories</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"calories\" name='calories' value='";
        // line 355
        echo ($context["calories"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"carbs\">Carbs</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id='carbs' name='carbs' value='";
        // line 368
        echo ($context["carbs"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal zrdn-is-mobile\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"protein\">Protein</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"protein\" name='protein' value='";
        // line 381
        echo ($context["protein"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"fiber\">Fiber</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"fiber\" name='fiber' value='";
        // line 394
        echo ($context["fiber"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"sugar\">Sugar</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"sugar\" name='sugar' value='";
        // line 407
        echo ($context["sugar"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"sodium\">Sodium</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"sodium\" name='sodium' value='";
        // line 420
        echo ($context["sodium"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"fat\">Fat</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"fat\" name='fat' value='";
        // line 433
        echo ($context["fat"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"saturated_fat\">Saturated Fat</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"saturated_fat\" name='saturated_fat' value='";
        // line 446
        echo ($context["saturated_fat"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"trans_fat\">Trans. Fat</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"trans_fat\" name='trans_fat' value='";
        // line 459
        echo ($context["trans_fat"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=\"zrdn-field zrdn-is-horizontal\">
                        <div class=\"zrdn-field-label\">
                            <label class=\"zrdn-label\" for=\"cholesterol\">Cholesterol</label>
                        </div>
                        <div class=\"zrdn-field-body\">
                            <div class=\"zrdn-field zrdn-is-narrow\">
                                <div class=\"zrdn-control\">
                                    <input class=\"zrdn-input zrdn-is-small\" type='text' id=\"cholesterol\" name='cholesterol' value='";
        // line 472
        echo ($context["cholesterol"] ?? null);
        echo "' />
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                </div>
            </div>

            <div class=\"bottom-bar\">

                <div class=\"zrdn-is-pulled-left\">
                    <a href='#' id='more-options-toggle' class=\"zrdn-button zrdn-is-small\">
                        <span class=\"zrdn-icon\"><i class=\"fa fa-plus\"></i></span>
                        <span>More options</span>
                    </a>
                </div>
                <div class=\"zrdn-is-pulled-right\">
                    <input class=\"zrdn-button zrdn-is-primary\" type='submit' value='";
        // line 490
        echo ($context["submit"] ?? null);
        echo "' name='add-recipe-button' />
                </div>
            </div>
        </form>
    ";
        // line 495
        echo "</body>
";
    }

    public function getTemplateName()
    {
        return "create-update-recipe.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  643 => 495,  636 => 490,  615 => 472,  599 => 459,  583 => 446,  567 => 433,  551 => 420,  535 => 407,  519 => 394,  503 => 381,  487 => 368,  471 => 355,  455 => 342,  441 => 331,  429 => 322,  423 => 319,  412 => 311,  406 => 308,  394 => 299,  382 => 290,  371 => 282,  359 => 273,  352 => 269,  342 => 262,  334 => 257,  327 => 252,  324 => 251,  319 => 249,  304 => 236,  298 => 235,  294 => 233,  292 => 232,  285 => 228,  279 => 225,  268 => 217,  264 => 216,  261 => 215,  255 => 213,  253 => 212,  250 => 211,  245 => 207,  225 => 189,  223 => 188,  65 => 33,  38 => 9,  32 => 6,  28 => 5,  24 => 4,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "create-update-recipe.twig", "/Applications/MAMP/htdocs/ziprecipes/wp-content/plugins/zip-recipes-free/views/create-update-recipe.twig");
    }
}
