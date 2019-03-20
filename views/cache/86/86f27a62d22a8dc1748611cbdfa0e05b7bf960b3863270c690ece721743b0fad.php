<?php

/* recipe.twig */
class __TwigTemplate_16f63ce5cfcd56e7e0914b293733e99db9879a0ba02efaabaeb457850567512a extends Twig_Template
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
        // line 2
        $context["recipe_props"] = $this->loadTemplate("macros.twig", "recipe.twig", 2);
        // line 3
        echo "
<div id=\"zlrecipe-container-";
        // line 4
        echo twig_escape_filter($this->env, ($context["recipe_id"] ?? null), "html", null, true);
        echo "\" class=\"zlrecipe-container-border\" ";
        if (($context["border_style"] ?? null)) {
            echo "style=\"border: ";
            echo ($context["border_style"] ?? null);
            echo ";\"";
        }
        echo ">
    ";
        // line 5
        if (($context["jsonld"] ?? null)) {
            // line 6
            echo "        <script type=\"application/ld+json\">
            ";
            // line 7
            echo ($context["jsonld"] ?? null);
            echo "
        </script>
    ";
        }
        // line 10
        echo "<div ";
        echo $context["recipe_props"]->getrecipe_attr("itemtype", "http://schema.org/Recipe", ($context["amp_on"] ?? null));
        echo "
    ";
        // line 11
        echo $context["recipe_props"]->getrecipe_attr("itemscope", false, ($context["amp_on"] ?? null));
        echo " >
<div id=\"zlrecipe-container\" class=\"serif zlrecipe\">
  <div id=\"zlrecipe-innerdiv\">
    ";
        // line 14
        if (($context["recipe_actions"] ?? null)) {
            // line 15
            echo "    <div style=\"text-align: right;\">
        ";
            // line 16
            echo ($context["recipe_actions"] ?? null);
            echo "
    </div>
    ";
        }
        // line 19
        echo "    <div class=\"item b-b\">

      ";
        // line 22
        echo "      ";
        if ((($context["print_hide"] ?? null) != "Hide")) {
            // line 23
            echo "        <div class=\"zlrecipe-print-link fl-r\">
          ";
            // line 24
            if (($context["custom_print_image"] ?? null)) {
                // line 25
                echo "            <a class=\"print-link\" title=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Print this recipe", "zip-recipes")), "html", null, true);
                echo "\" href=\"javascript:void(0);\" onclick=\"zlrPrint('zlrecipe-container-";
                echo twig_escape_filter($this->env, ($context["recipe_id"] ?? null), "html", null, true);
                echo "', '";
                echo twig_escape_filter($this->env, ($context["ZRDN_PLUGIN_URL"] ?? null), "html", null, true);
                echo "'); return false\" rel=\"nofollow\">
              <img src=\"";
                // line 26
                echo twig_escape_filter($this->env, ($context["custom_print_image"] ?? null), "html", null, true);
                echo "\">
            </a>
          ";
            } else {
                // line 29
                echo "            <a class=\"butn-link\" title=\"";
                echo __("Print this recipe", 'zip-recipes');
                echo "\" href=\"javascript:void(0);\" onclick=\"zlrPrint('zlrecipe-container-";
                echo twig_escape_filter($this->env, ($context["recipe_id"] ?? null), "html", null, true);
                echo "', '";
                echo twig_escape_filter($this->env, ($context["ZRDN_PLUGIN_URL"] ?? null), "html", null, true);
                echo "'); return false\" rel=\"nofollow\">
              ";
                // line 30
                echo __("Print", 'zip-recipes');
                // line 31
                echo "            </a>
          ";
            }
            // line 33
            echo "        </div>
      ";
        }
        // line 35
        echo "
      ";
        // line 37
        echo "      <div id=\"zlrecipe-title\"
          ";
        // line 38
        echo $context["recipe_props"]->getrecipe_attr("itemprop", "name", ($context["amp_on"] ?? null));
        echo "
        class=\"b-b h-1 strong ";
        // line 39
        if (($context["title_hide"] ?? null)) {
            echo "texthide";
        }
        echo "\" >";
        echo twig_escape_filter($this->env, ($context["recipe_title"] ?? null), "html", null, true);
        echo "</div>
    </div>

    ";
        // line 43
        echo "    <div class=\"zlmeta zlclear\">
      <div class=\"fl-l width-50\">
        ";
        // line 45
        echo ($context["recipe_rating"] ?? null);
        echo "

        ";
        // line 48
        echo "        ";
        if (($context["prep_time"] ?? null)) {
            // line 49
            echo "          <p id=\"zlrecipe-prep-time\">
            ";
            // line 50
            if ((($context["prep_time_label_hide"] ?? null) != "Hide")) {
                // line 51
                echo "              ";
                echo __("Prep Time:", 'zip-recipes');
                // line 52
                echo "            ";
            }
            // line 53
            echo "            <span
                ";
            // line 54
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "prepTime", ($context["amp_on"] ?? null));
            echo "
                ";
            // line 55
            echo $context["recipe_props"]->getrecipe_attr("content", ($context["prep_time_raw"] ?? null), ($context["amp_on"] ?? null));
            echo "
            >";
            // line 56
            echo twig_escape_filter($this->env, ($context["prep_time"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 59
        echo "
        ";
        // line 60
        if (($context["cook_time"] ?? null)) {
            // line 61
            echo "          <p id=\"zlrecipe-cook-time\">
            ";
            // line 62
            if ((($context["cook_time_label_hide"] ?? null) != "Hide")) {
                // line 63
                echo "              ";
                echo __("Cook Time:", 'zip-recipes');
                // line 64
                echo "            ";
            }
            // line 65
            echo "            <span
                ";
            // line 66
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "cookTime", ($context["amp_on"] ?? null));
            echo "
                ";
            // line 67
            echo $context["recipe_props"]->getrecipe_attr("content", ($context["cook_time_raw"] ?? null), ($context["amp_on"] ?? null));
            echo "
            >";
            // line 68
            echo twig_escape_filter($this->env, ($context["cook_time"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 71
        echo "
        ";
        // line 72
        if (($context["total_time"] ?? null)) {
            // line 73
            echo "          <p id=\"zlrecipe-total-time\">
            ";
            // line 74
            if ((($context["total_time_label_hide"] ?? null) != "Hide")) {
                // line 75
                echo "              ";
                echo __("Total Time:", 'zip-recipes');
                // line 76
                echo "            ";
            }
            // line 77
            echo "            <span
                ";
            // line 78
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "totalTime", ($context["amp_on"] ?? null));
            echo "
                ";
            // line 79
            echo $context["recipe_props"]->getrecipe_attr("content", ($context["total_time_raw"] ?? null), ($context["amp_on"] ?? null));
            echo "
            >";
            // line 80
            echo twig_escape_filter($this->env, ($context["total_time"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 83
        echo "            
        ";
        // line 84
        if (($context["category"] ?? null)) {
            // line 85
            echo "          <p id=\"zlrecipe-category\">
            ";
            // line 86
            if ((($context["category_label_hide"] ?? null) != "Hide")) {
                // line 87
                echo "              ";
                echo __("Category:", 'zip-recipes');
                // line 88
                echo "            ";
            }
            // line 89
            echo "            <span
                ";
            // line 90
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "recipeCategory", ($context["amp_on"] ?? null));
            echo "
                ";
            // line 91
            echo $context["recipe_props"]->getrecipe_attr("content", ($context["category"] ?? null), ($context["amp_on"] ?? null));
            echo "
            >";
            // line 92
            echo twig_escape_filter($this->env, ($context["category"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 94
        echo "            
            
         ";
        // line 96
        if (($context["cuisine"] ?? null)) {
            // line 97
            echo "          <p id=\"zlrecipe-cuisine\">
            ";
            // line 98
            if ((($context["cuisine_label_hide"] ?? null) != "Hide")) {
                // line 99
                echo "              ";
                echo __("Cuisine:", 'zip-recipes');
                // line 100
                echo "            ";
            }
            // line 101
            echo "            <span
                ";
            // line 102
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "recipeCuisine", ($context["amp_on"] ?? null));
            echo "
                ";
            // line 103
            echo $context["recipe_props"]->getrecipe_attr("content", ($context["cuisine"] ?? null), ($context["amp_on"] ?? null));
            echo "
            >";
            // line 104
            echo twig_escape_filter($this->env, ($context["cuisine"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 107
        echo "
        ";
        // line 108
        echo ($context["author_section"] ?? null);
        echo "

        ";
        // line 111
        echo "      </div>

      <div class=\"fl-l width-50\">
        ";
        // line 115
        echo "
        ";
        // line 116
        if (($context["yield"] ?? null)) {
            // line 117
            echo "          <p id=\"zlrecipe-yield\">
            ";
            // line 118
            if ((($context["yield_label_hide"] ?? null) != "Hide")) {
                // line 119
                echo "              ";
                echo __("Yield:", 'zip-recipes');
                // line 120
                echo "            ";
            }
            // line 121
            echo "            <span ";
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "recipeYield", ($context["amp_on"] ?? null));
            echo ">";
            echo twig_escape_filter($this->env, ($context["yield"] ?? null), "html", null, true);
            echo "</span>
          </p>
        ";
        }
        // line 124
        echo "
        ";
        // line 125
        if (($context["nutritional_info"] ?? null)) {
            // line 126
            echo "          <div id=\"zlrecipe-nutrition\"
                  ";
            // line 127
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "nutrition", ($context["amp_on"] ?? null));
            echo "
                  ";
            // line 128
            echo $context["recipe_props"]->getrecipe_attr("itemscope", false, ($context["amp_on"] ?? null));
            echo "
                  ";
            // line 129
            echo $context["recipe_props"]->getrecipe_attr("itemtype", "http://schema.org/NutritionInformation", ($context["amp_on"] ?? null));
            echo ">

          ";
            // line 131
            if (($context["serving_size"] ?? null)) {
                // line 132
                echo "              <p id=\"zlrecipe-serving-size\">
                ";
                // line 133
                if ((($context["serving_size_label_hide"] ?? null) != "Hide")) {
                    // line 134
                    echo "                  ";
                    echo __("Serving Size:", 'zip-recipes');
                    // line 135
                    echo "                ";
                }
                // line 136
                echo "                <span
                        ";
                // line 137
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "servingSize", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["serving_size"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 140
            echo "
            ";
            // line 141
            if (($context["calories"] ?? null)) {
                // line 142
                echo "              <p id=\"zlrecipe-calories\">
                ";
                // line 143
                if ((($context["calories_label_hide"] ?? null) != "Hide")) {
                    // line 144
                    echo "                  ";
                    echo __("Calories per serving:", 'zip-recipes');
                    // line 145
                    echo "                ";
                }
                // line 146
                echo "                <span
                        ";
                // line 147
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "calories", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["calories"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 150
            echo "
            ";
            // line 151
            if (($context["fat"] ?? null)) {
                // line 152
                echo "              <p id=\"zlrecipe-fat\">
                ";
                // line 153
                if ((($context["fat_label_hide"] ?? null) != "Hide")) {
                    // line 154
                    echo "                  ";
                    echo __("Fat per serving:", 'zip-recipes');
                    // line 155
                    echo "                ";
                }
                // line 156
                echo "                <span
                        ";
                // line 157
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "fatContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["fat"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 160
            echo "
            ";
            // line 161
            if (($context["saturated_fat"] ?? null)) {
                // line 162
                echo "              <p id=\"zlrecipe-saturated-fat\">
                ";
                // line 163
                if ((($context["saturated_fat_label_hide"] ?? null) != "Hide")) {
                    // line 164
                    echo "                  ";
                    echo __("Saturated fat per serving:", 'zip-recipes');
                    // line 165
                    echo "                ";
                }
                // line 166
                echo "                <span
                        ";
                // line 167
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "saturatedFatContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["saturated_fat"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 170
            echo "
            ";
            // line 171
            if (($context["carbs"] ?? null)) {
                // line 172
                echo "              <p id=\"zlrecipe-carbs\">
                ";
                // line 173
                if ((($context["carbs_label_hide"] ?? null) != "Hide")) {
                    // line 174
                    echo "                  ";
                    echo __("Carbs per serving:", 'zip-recipes');
                    // line 175
                    echo "                ";
                }
                // line 176
                echo "                <span
                        ";
                // line 177
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "carbohydrateContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["carbs"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 180
            echo "
            ";
            // line 181
            if (($context["protein"] ?? null)) {
                // line 182
                echo "              <p id=\"zlrecipe-protein\">
                ";
                // line 183
                if ((($context["protein_label_hide"] ?? null) != "Hide")) {
                    // line 184
                    echo "                  ";
                    echo __("Protein per serving:", 'zip-recipes');
                    // line 185
                    echo "                ";
                }
                // line 186
                echo "                <span
                        ";
                // line 187
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "proteinContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["protein"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 190
            echo "
            ";
            // line 191
            if (($context["fiber"] ?? null)) {
                // line 192
                echo "              <p id=\"zlrecipe-fiber\">
                ";
                // line 193
                if ((($context["fiber_label_hide"] ?? null) != "Hide")) {
                    // line 194
                    echo "                  ";
                    echo __("Fiber per serving:", 'zip-recipes');
                    // line 195
                    echo "                ";
                }
                // line 196
                echo "                <span
                        ";
                // line 197
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "fiberContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["fiber"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 200
            echo "
            ";
            // line 201
            if (($context["sugar"] ?? null)) {
                // line 202
                echo "              <p id=\"zlrecipe-sugar\">
                ";
                // line 203
                if ((($context["sugar_label_hide"] ?? null) != "Hide")) {
                    // line 204
                    echo "                  ";
                    echo __("Sugar per serving:", 'zip-recipes');
                    // line 205
                    echo "                ";
                }
                // line 206
                echo "                <span
                        ";
                // line 207
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "sugarContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["sugar"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 210
            echo "
            ";
            // line 211
            if (($context["sodium"] ?? null)) {
                // line 212
                echo "              <p id=\"zlrecipe-sodium\">
                ";
                // line 213
                if ((($context["sodium_label_hide"] ?? null) != "Hide")) {
                    // line 214
                    echo "                  ";
                    echo __("Sodium per serving:", 'zip-recipes');
                    // line 215
                    echo "                ";
                }
                // line 216
                echo "                <span
                        ";
                // line 217
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "sodiumContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["sodium"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 220
            echo "                
            ";
            // line 221
            if (($context["trans_fat"] ?? null)) {
                // line 222
                echo "              <p id=\"zlrecipe-trans_fat\">
                ";
                // line 223
                if ((($context["trans_fat_label_hide"] ?? null) != "Hide")) {
                    // line 224
                    echo "                  ";
                    echo __("Trans fat per serving:", 'zip-recipes');
                    // line 225
                    echo "                ";
                }
                // line 226
                echo "                <span
                        ";
                // line 227
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "transfatContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["trans_fat"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 230
            echo "                
            ";
            // line 231
            if (($context["cholesterol"] ?? null)) {
                // line 232
                echo "              <p id=\"zlrecipe-cholesterol\">
                ";
                // line 233
                if ((($context["cholesterol_label_hide"] ?? null) != "Hide")) {
                    // line 234
                    echo "                  ";
                    echo __("Cholesterol per serving:", 'zip-recipes');
                    // line 235
                    echo "                ";
                }
                // line 236
                echo "                <span
                        ";
                // line 237
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "cholesterolContent", ($context["amp_on"] ?? null));
                echo ">";
                echo twig_escape_filter($this->env, ($context["cholesterol"] ?? null), "html", null, true);
                echo "</span>
              </p>
            ";
            }
            // line 240
            echo "
          </div>
        ";
        }
        // line 243
        echo "
        ";
        // line 245
        echo "      </div>
      <div class=\"zlclear\">
      </div>
    </div>

    ";
        // line 251
        echo "    ";
        if (($this->getAttribute(($context["image_attributes"] ?? null), "url", array()) || ($context["summary"] ?? null))) {
            // line 252
            echo "      <div class=\"img-desc-wrap\">
          ";
            // line 253
            if ($this->getAttribute(($context["image_attributes"] ?? null), "url", array())) {
                // line 254
                echo "              <p class=\"t-a-c
                    ";
                // line 255
                if (((($context["image_hide"] ?? null) == "Hide") || ($context["is_featured_post_image"] ?? null))) {
                    echo "hide-card";
                }
                // line 256
                echo "                    ";
                if ((($context["image_hide_print"] ?? null) == "Hide")) {
                    echo "hide-print";
                }
                // line 257
                echo "                    \"
              >
                  <img class=\"photo\"
                          ";
                // line 260
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "image", ($context["amp_on"] ?? null));
                echo " src=\"";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["image_attributes"] ?? null), "url", array()), "html", null, true);
                echo "\" ";
                if ($this->getAttribute(($context["image_attributes"] ?? null), "srcset", array())) {
                    echo "srcset=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["image_attributes"] ?? null), "srcset", array()), "html", null, true);
                    echo "\"";
                }
                echo " ";
                if ($this->getAttribute(($context["image_attributes"] ?? null), "sizes", array())) {
                    echo "sizes=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["image_attributes"] ?? null), "sizes", array()), "html", null, true);
                    echo "\"";
                }
                echo " title=\"";
                echo twig_escape_filter($this->env, ($context["recipe_title"] ?? null), "html", null, true);
                echo "\"
                          ";
                // line 261
                if (($context["recipe_title"] ?? null)) {
                    echo " alt=\"";
                    echo twig_escape_filter($this->env, ($context["recipe_title"] ?? null), "html", null, true);
                    echo "\" ";
                }
                // line 262
                echo "                          ";
                if (($context["image_width"] ?? null)) {
                    // line 263
                    echo "                              style=\"";
                    if (($context["image_width"] ?? null)) {
                        echo "width: ";
                        echo twig_escape_filter($this->env, ($context["image_width"] ?? null), "html", null, true);
                        echo "px;";
                    }
                    echo "\"
                          ";
                }
                // line 264
                echo " />
              </p>
          ";
            }
            // line 267
            echo "
          ";
            // line 268
            if (($context["summary"] ?? null)) {
                // line 269
                echo "              <div id=\"zlrecipe-summary\"
                      ";
                // line 270
                echo $context["recipe_props"]->getrecipe_attr("itemprop", "description", ($context["amp_on"] ?? null));
                echo ">
                  ";
                // line 271
                echo ($context["summary_rich"] ?? null);
                echo "
              </div>
          ";
            }
            // line 274
            echo "      </div>
    ";
        }
        // line 276
        echo "
    ";
        // line 278
        echo "    ";
        // line 279
        echo "    ";
        // line 280
        echo "    ";
        if ((($context["ingredient_label_hide"] ?? null) != "Hide")) {
            // line 281
            echo "      <p id=\"zlrecipe-ingredients\" class=\"h-4 strong\">
        ";
            // line 282
            echo __("Ingredients", 'zip-recipes');
            // line 283
            echo "      </p>
    ";
        }
        // line 285
        echo "
    ";
        // line 286
        $context["ingredientsHtmlListElem"] = ($context["ingredient_list_type"] ?? null);
        // line 287
        echo "    ";
        // line 288
        echo "    ";
        if ((($context["ingredient_list_type"] ?? null) == "l")) {
            $context["ingredientsHtmlListElem"] = "ul";
        }
        // line 289
        echo "    ";
        if (((($context["ingredient_list_type"] ?? null) == "p") || (($context["ingredient_list_type"] ?? null) == "div"))) {
            $context["ingredientsHtmlListElem"] = "span";
        }
        // line 290
        echo "
    ";
        // line 291
        $context["ingredientsHtmlListChildElem"] = "li";
        // line 292
        echo "    ";
        if ((($context["ingredient_list_type"] ?? null) == "p")) {
            $context["ingredientsHtmlListChildElem"] = "p";
        }
        // line 293
        echo "    ";
        if ((($context["ingredient_list_type"] ?? null) == "div")) {
            $context["ingredientsHtmlListChildElem"] = "div";
        }
        // line 294
        echo "
    ";
        // line 295
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["nested_ingredients"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["ingredient_list"]) {
            // line 296
            echo "      <";
            echo twig_escape_filter($this->env, ($context["ingredientsHtmlListElem"] ?? null), "html", null, true);
            echo " id=\"zlrecipe-ingredients-list\">
        ";
            // line 297
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["ingredient_list"]);
            foreach ($context['_seq'] as $context["_key"] => $context["ingredient"]) {
                // line 298
                echo "          ";
                if (($this->getAttribute($context["ingredient"], "type", array()) == "image")) {
                    // line 299
                    echo "            <img class=\"";
                    if (($context["image_hide_print"] ?? null)) {
                        echo "hide-print";
                    }
                    echo " ";
                    if (($context["image_hide"] ?? null)) {
                        echo "hide-card";
                    }
                    echo "\" src=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "url", array()), "html", null, true);
                    echo "\" ";
                    if ($this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "srcset", array())) {
                        echo "srcset=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "srcset", array()), "html", null, true);
                        echo "\"";
                    }
                    echo " ";
                    if ($this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "sizes", array())) {
                        echo "sizes=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "sizes", array()), "html", null, true);
                        echo "\"";
                    }
                    echo " ";
                    if ($this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "title", array())) {
                        echo " alt=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["ingredient"], "attributes", array()), "title", array()), "html", null, true);
                        echo "\" ";
                    }
                    echo " />
          ";
                } elseif (($this->getAttribute(                // line 300
$context["ingredient"], "type", array()) == "subtitle")) {
                    // line 301
                    echo "            ";
                    // line 302
                    echo "            ";
                    // line 303
                    echo "            <div class=\"";
                    if ((($context["ingredient_list_type"] ?? null) == "l")) {
                        echo "ingredient no-bullet-label";
                    } else {
                        echo "ingredient-label";
                    }
                    echo "\">
              ";
                    // line 304
                    echo $this->getAttribute($context["ingredient"], "content", array());
                    echo "
            </div>
          ";
                } else {
                    // line 307
                    echo "            <";
                    echo twig_escape_filter($this->env, ($context["ingredientsHtmlListChildElem"] ?? null), "html", null, true);
                    echo " class=\"ingredient ";
                    if ((($context["ingredient_list_type"] ?? null) == "l")) {
                        echo "no-bullet";
                    }
                    echo "\"
              ";
                    // line 308
                    echo $context["recipe_props"]->getrecipe_attr("itemprop", "recipeIngredient", ($context["amp_on"] ?? null));
                    echo ">
              ";
                    // line 309
                    echo $this->getAttribute($context["ingredient"], "content", array());
                    echo "
            </";
                    // line 310
                    echo twig_escape_filter($this->env, ($context["ingredientsHtmlListChildElem"] ?? null), "html", null, true);
                    echo ">
          ";
                }
                // line 312
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ingredient'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 313
            echo "      </";
            echo twig_escape_filter($this->env, ($context["ingredientsHtmlListElem"] ?? null), "html", null, true);
            echo ">
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ingredient_list'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 315
        echo "
    ";
        // line 317
        echo "    ";
        // line 318
        echo "    ";
        // line 319
        echo "    ";
        if ((($context["instruction_label_hide"] ?? null) != "Hide")) {
            // line 320
            echo "      <p id=\"zlrecipe-instructions\" class=\"h-4 strong\">";
            echo __("Instructions", 'zip-recipes');
            echo "</p>
    ";
        }
        // line 322
        echo "
    ";
        // line 323
        $context["instructionsHtmlListElem"] = ($context["instruction_list_type"] ?? null);
        // line 324
        echo "    ";
        // line 325
        echo "    ";
        if ((($context["instruction_list_type"] ?? null) == "l")) {
            $context["instructionsHtmlListElem"] = "ul";
        }
        // line 326
        echo "    ";
        if (((($context["instruction_list_type"] ?? null) == "p") || (($context["instruction_list_type"] ?? null) == "div"))) {
            $context["instructionsHtmlListElem"] = "span";
        }
        // line 327
        echo "
    ";
        // line 329
        echo "    ";
        $context["instructionsHtmlChildElem"] = "li";
        // line 330
        echo "    ";
        if ((($context["instruction_list_type"] ?? null) == "p")) {
            $context["instructionsHtmlChildElem"] = "p";
        }
        // line 331
        echo "    ";
        if ((($context["instruction_list_type"] ?? null) == "div")) {
            $context["instructionsHtmlChildElem"] = "div";
        }
        // line 332
        echo "
    ";
        // line 333
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["nested_instructions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["instruction_list"]) {
            // line 334
            echo "        <";
            echo twig_escape_filter($this->env, ($context["instructionsHtmlListElem"] ?? null), "html", null, true);
            echo " id=\"zlrecipe-instructions-list\" class=\"instructions\">
        ";
            // line 335
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["instruction_list"]);
            foreach ($context['_seq'] as $context["_key"] => $context["instruction"]) {
                // line 336
                echo "          ";
                if (($this->getAttribute($context["instruction"], "type", array()) == "image")) {
                    // line 337
                    echo "            <img class=\"";
                    if (($context["image_hide_print"] ?? null)) {
                        echo "hide-print";
                    }
                    echo " ";
                    if (($context["image_hide"] ?? null)) {
                        echo "hide-card";
                    }
                    echo "\" src=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "url", array()), "html", null, true);
                    echo "\" ";
                    if ($this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "srcset", array())) {
                        echo "srcset=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "srcset", array()), "html", null, true);
                        echo "\"";
                    }
                    echo " ";
                    if ($this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "sizes", array())) {
                        echo "sizes=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "sizes", array()), "html", null, true);
                        echo "\"";
                    }
                    echo " ";
                    if ($this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "title", array())) {
                        echo " alt=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["instruction"], "attributes", array()), "title", array()), "html", null, true);
                        echo "\" ";
                    }
                    echo " />
          ";
                } elseif (($this->getAttribute(                // line 338
$context["instruction"], "type", array()) == "subtitle")) {
                    // line 339
                    echo "            ";
                    // line 340
                    echo "            ";
                    // line 341
                    echo "            <div class=\"";
                    if ((($context["instruction_list_type"] ?? null) == "l")) {
                        echo "instruction no-bullet-label";
                    } else {
                        echo "instruction-label";
                    }
                    echo "\">
              ";
                    // line 342
                    echo $this->getAttribute($context["instruction"], "content", array());
                    echo "
            </div>
          ";
                } else {
                    // line 345
                    echo "            <";
                    echo twig_escape_filter($this->env, ($context["instructionsHtmlChildElem"] ?? null), "html", null, true);
                    echo " class=\"instruction ";
                    if ((($context["instruction_list_type"] ?? null) == "l")) {
                        echo "no-bullet";
                    }
                    echo "\"
              ";
                    // line 346
                    echo $context["recipe_props"]->getrecipe_attr("itemprop", "recipeInstructions", ($context["amp_on"] ?? null));
                    echo ">
            ";
                    // line 347
                    echo $this->getAttribute($context["instruction"], "content", array());
                    echo "
            </";
                    // line 348
                    echo twig_escape_filter($this->env, ($context["instructionsHtmlChildElem"] ?? null), "html", null, true);
                    echo ">
          ";
                }
                // line 350
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['instruction'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 351
            echo "      </";
            echo twig_escape_filter($this->env, ($context["instructionsHtmlListElem"] ?? null), "html", null, true);
            echo ">
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['instruction_list'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 353
        echo "

    ";
        // line 356
        echo "    ";
        if (($context["notes"] ?? null)) {
            // line 357
            echo "      ";
            if ((($context["notes_label_hide"] ?? null) != "Hide")) {
                // line 358
                echo "        <p id=\"zlrecipe-notes\" class=\"h-4 strong\">";
                echo __("Notes", 'zip-recipes');
                echo "</p>
      ";
            }
            // line 360
            echo "      <div id=\"zlrecipe-notes-list\">
       ";
            // line 361
            echo ($context["formatted_notes"] ?? null);
            echo "
      </div>
    ";
        }
        // line 364
        echo "    ";
        echo ($context["nutrition_label"] ?? null);
        echo "

    ";
        // line 367
        echo "    ";
        if ((($context["attribution_hide"] ?? null) != "Hide")) {
            // line 368
            echo "      <div class=\"zl-linkback\">Recipe Management Powered by <a title=\"Zip Recipes Plugin\" href=\"https://www.ziprecipes.net\" rel=\"nofollow\" target=\"_blank\">Zip Recipes Plugin</a></div>
    ";
        }
        // line 370
        echo "    <div class=\"ziprecipes-plugin\" style=\"display: none;\">";
        echo twig_escape_filter($this->env, ($context["version"] ?? null), "html", null, true);
        echo "</div>

    ";
        // line 373
        echo "    ";
        if ((($context["print_permalink_hide"] ?? null) != "Hide")) {
            // line 374
            echo "      <a id=\"zl-printed-permalink\" href=\"";
            echo twig_escape_filter($this->env, ($context["permalink"] ?? null), "html", null, true);
            echo "\" title=\"Permalink to Recipe\">";
            echo twig_escape_filter($this->env, ($context["permalink"] ?? null), "html", null, true);
            echo "</a>
    ";
        }
        // line 376
        echo "  </div>

  ";
        // line 379
        echo "  ";
        if (($context["copyright"] ?? null)) {
            // line 380
            echo "    <div id=\"zl-printed-copyright-statement\"
            ";
            // line 381
            echo $context["recipe_props"]->getrecipe_attr("itemprop", "copyrightHolder", ($context["amp_on"] ?? null));
            echo ">";
            echo twig_escape_filter($this->env, ($context["copyright"] ?? null), "html", null, true);
            echo "</div>
  ";
        }
        // line 383
        echo "  </div>
</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "recipe.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1153 => 383,  1146 => 381,  1143 => 380,  1140 => 379,  1136 => 376,  1128 => 374,  1125 => 373,  1119 => 370,  1115 => 368,  1112 => 367,  1106 => 364,  1100 => 361,  1097 => 360,  1091 => 358,  1088 => 357,  1085 => 356,  1081 => 353,  1072 => 351,  1066 => 350,  1061 => 348,  1057 => 347,  1053 => 346,  1044 => 345,  1038 => 342,  1029 => 341,  1027 => 340,  1025 => 339,  1023 => 338,  992 => 337,  989 => 336,  985 => 335,  980 => 334,  976 => 333,  973 => 332,  968 => 331,  963 => 330,  960 => 329,  957 => 327,  952 => 326,  947 => 325,  945 => 324,  943 => 323,  940 => 322,  934 => 320,  931 => 319,  929 => 318,  927 => 317,  924 => 315,  915 => 313,  909 => 312,  904 => 310,  900 => 309,  896 => 308,  887 => 307,  881 => 304,  872 => 303,  870 => 302,  868 => 301,  866 => 300,  835 => 299,  832 => 298,  828 => 297,  823 => 296,  819 => 295,  816 => 294,  811 => 293,  806 => 292,  804 => 291,  801 => 290,  796 => 289,  791 => 288,  789 => 287,  787 => 286,  784 => 285,  780 => 283,  778 => 282,  775 => 281,  772 => 280,  770 => 279,  768 => 278,  765 => 276,  761 => 274,  755 => 271,  751 => 270,  748 => 269,  746 => 268,  743 => 267,  738 => 264,  728 => 263,  725 => 262,  719 => 261,  699 => 260,  694 => 257,  689 => 256,  685 => 255,  682 => 254,  680 => 253,  677 => 252,  674 => 251,  667 => 245,  664 => 243,  659 => 240,  651 => 237,  648 => 236,  645 => 235,  642 => 234,  640 => 233,  637 => 232,  635 => 231,  632 => 230,  624 => 227,  621 => 226,  618 => 225,  615 => 224,  613 => 223,  610 => 222,  608 => 221,  605 => 220,  597 => 217,  594 => 216,  591 => 215,  588 => 214,  586 => 213,  583 => 212,  581 => 211,  578 => 210,  570 => 207,  567 => 206,  564 => 205,  561 => 204,  559 => 203,  556 => 202,  554 => 201,  551 => 200,  543 => 197,  540 => 196,  537 => 195,  534 => 194,  532 => 193,  529 => 192,  527 => 191,  524 => 190,  516 => 187,  513 => 186,  510 => 185,  507 => 184,  505 => 183,  502 => 182,  500 => 181,  497 => 180,  489 => 177,  486 => 176,  483 => 175,  480 => 174,  478 => 173,  475 => 172,  473 => 171,  470 => 170,  462 => 167,  459 => 166,  456 => 165,  453 => 164,  451 => 163,  448 => 162,  446 => 161,  443 => 160,  435 => 157,  432 => 156,  429 => 155,  426 => 154,  424 => 153,  421 => 152,  419 => 151,  416 => 150,  408 => 147,  405 => 146,  402 => 145,  399 => 144,  397 => 143,  394 => 142,  392 => 141,  389 => 140,  381 => 137,  378 => 136,  375 => 135,  372 => 134,  370 => 133,  367 => 132,  365 => 131,  360 => 129,  356 => 128,  352 => 127,  349 => 126,  347 => 125,  344 => 124,  335 => 121,  332 => 120,  329 => 119,  327 => 118,  324 => 117,  322 => 116,  319 => 115,  314 => 111,  309 => 108,  306 => 107,  300 => 104,  296 => 103,  292 => 102,  289 => 101,  286 => 100,  283 => 99,  281 => 98,  278 => 97,  276 => 96,  272 => 94,  266 => 92,  262 => 91,  258 => 90,  255 => 89,  252 => 88,  249 => 87,  247 => 86,  244 => 85,  242 => 84,  239 => 83,  233 => 80,  229 => 79,  225 => 78,  222 => 77,  219 => 76,  216 => 75,  214 => 74,  211 => 73,  209 => 72,  206 => 71,  200 => 68,  196 => 67,  192 => 66,  189 => 65,  186 => 64,  183 => 63,  181 => 62,  178 => 61,  176 => 60,  173 => 59,  167 => 56,  163 => 55,  159 => 54,  156 => 53,  153 => 52,  150 => 51,  148 => 50,  145 => 49,  142 => 48,  137 => 45,  133 => 43,  123 => 39,  119 => 38,  116 => 37,  113 => 35,  109 => 33,  105 => 31,  103 => 30,  94 => 29,  88 => 26,  79 => 25,  77 => 24,  74 => 23,  71 => 22,  67 => 19,  61 => 16,  58 => 15,  56 => 14,  50 => 11,  45 => 10,  39 => 7,  36 => 6,  34 => 5,  24 => 4,  21 => 3,  19 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "recipe.twig", "/Applications/MAMP/htdocs/ziprecipes/wp-content/plugins/zip-recipes-free/views/recipe.twig");
    }
}
