<?php

/* settings.twig */
class __TwigTemplate_776e4247de25943e500fdc0cdee3b496a6ab9547b928b020fcfa797e97c508d5 extends Twig_Template
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
        echo "<style>
  .form-table label { line-height: 2.5; }
  hr { border: none; border-top: 1px; margin: 30px 0; }
</style>
<div class=\"wrap\">
  <form method=\"post\" action=\"\" id=\"zlrecipe_settings_form\" name=\"zlrecipe_settings_form\">
    <h2><img src=\"";
        // line 7
        echo twig_escape_filter($this->env, ($context["zrdn_icon"] ?? null), "html", null, true);
        echo "\" /> Zip Recipes Settings</h2>
      For full customization options, see the <a href=\"https://www.ziprecipes.net/docs/installing/\" target=\"_blank\">Instructions document</a>.
      <style>
        .zrdn-promo {
          background-color:white;
          padding:30px;
          margin:20px;
        }
        .zrdn-promo ul {
          list-style-type: circle;
          margin-left: 30px;}
      </style>
      <div class=\"zrdn-promo\">
        <h3>";
        // line 20
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Like the plugin, or need more features? Check out the premium features:", "zip-recipes")), "html", null, true);
        echo "</h3>
        <ul>
          <li>";
        // line 22
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Cool looking themes", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 23
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Author setting for Google", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 24
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Ratings", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 25
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Recipe index", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 26
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Nutrition calculation", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 27
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Search by ingredients", "zip-recipes")), "html", null, true);
        echo "</li>
          <li>";
        // line 28
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('__')->getCallable(), array("Recipe picture gallery", "zip-recipes")), "html", null, true);
        echo "</li>
      </ul>
        <a target=\"_blank\" href=\"https://ziprecipes.net/\" class=\"button button-primary\">Check premium</a>
      </div>
      ";
        // line 32
        echo ($context["extensions_settings"] ?? null);
        echo "
      <table class=\"form-table\">
        <tr valign=\"top\">
          <th scope=\"row\">
            Custom Print Button
            <br />
            (Optional)
          </th>
          <td>
            <input type=\"hidden\" name=\"action\" value=\"update_settings\" />
            <input placeholder=\"URL to custom Print button image\" type=\"text\" name=\"custom-print-image\" value=\"";
        // line 42
        echo twig_escape_filter($this->env, ($context["custom_print_image"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" />
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Zip Recipes Plugin Link</th>
          <td><label><input type=\"checkbox\" name=\"zrecipe-attribution-hide\" value=\"Hide\" ";
        // line 47
        echo ($context["zrecipe_attribution_hide"] ?? null);
        echo " /> Don't show plugin link</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Printed Output: Recipe Permalink</th>
          <td><label><input type=\"checkbox\" name=\"printed-permalink-hide\" value=\"Hide\" ";
        // line 51
        echo ($context["printed_permalink_hide"] ?? null);
        echo " /> Don't show permalink in printed output</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Printed Output: Copyright Statement</th>
          <td><input type=\"text\" name=\"printed-copyright-statement\" value=\"";
        // line 55
        echo twig_escape_filter($this->env, ($context["printed_copyright_statement"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" /></td>
        </tr>
        ";
        // line 57
        echo ($context["author_section"] ?? null);
        echo "
      </table>

      <hr />
      <h3>Recipe Card Look & Feel</h3>
      <table class=\"form-table\">
        <tr valign=\"top\">
          <th scope=\"row\">Stylesheet</th>
          <td><label><input type=\"checkbox\" name=\"stylesheet\" value=\"zlrecipe-std\" ";
        // line 65
        echo ($context["stylesheet"] ?? null);
        echo " /> Use Zip Recipes style</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Recipe Title</th>
          <td><label><input type=\"checkbox\" name=\"recipe-title-hide\" value=\"Hide\" ";
        // line 69
        echo ($context["recipe_title_hide"] ?? null);
        echo " /> Don't show Recipe Title in post (still shows in print view)</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Print Button</th>
          <td><label><input type=\"checkbox\" name=\"print-link-hide\" value=\"Hide\" ";
        // line 73
        echo ($context["print_link_hide"] ?? null);
        echo " /> Don't show Print Button</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Image Width</th>
          <td><label><input type=\"text\" name=\"image-width\" value=\"";
        // line 77
        echo twig_escape_filter($this->env, ($context["image_width"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" /> pixels</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Image Display</th>
          <td>
            <label><input type=\"checkbox\" name=\"image-hide\" value=\"Hide\" ";
        // line 82
        echo ($context["image_hide"] ?? null);
        echo " /> Don't show Image in post</label>
            <br />
            <label><input type=\"checkbox\" name=\"image-hide-print\" value=\"Hide\" ";
        // line 84
        echo ($context["image_hide_print"] ?? null);
        echo " /> Don't show Image in print view</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Border Style</th>
          <td>
            <select name=\"outer-border-style\">";
        // line 90
        echo ($context["obs"] ?? null);
        echo "</select>
          </td>
        </tr>
      </table>
      <hr />
      <h3>Ingredients</h3>
      <table class=\"form-table\">
        <tr valign=\"top\">
          <th scope=\"row\">'Ingredients' Label</th>
          <td>
            <label><input type=\"checkbox\" name=\"ingredient-label-hide\" value=\"Hide\" ";
        // line 100
        echo ($context["ingredient_label_hide"] ?? null);
        echo " /> Don't show Ingredients label</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">'Ingredients' List Type</th>
          <td>
            <input type=\"radio\" id=\"ingredient-list-type-l\" name=\"ingredient-list-type\" value=\"l\" ";
        // line 106
        echo ($context["ing_l"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-l\">List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-ol\" name=\"ingredient-list-type\" value=\"ol\" ";
        // line 109
        echo ($context["ing_ol"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-ol\">Numbered List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-ul\" name=\"ingredient-list-type\" value=\"ul\" ";
        // line 112
        echo ($context["ing_ul"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-ul\">Bulleted List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-p\" name=\"ingredient-list-type\" value=\"p\" ";
        // line 115
        echo ($context["ing_p"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-p\">Paragraphs</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-div\" name=\"ingredient-list-type\" value=\"div\" ";
        // line 118
        echo ($context["ing_div"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-div\">Divs</label>
          </td>
        </tr>
      </table>

      <hr />

      <h3>Instructions</h3>
      <table class=\"form-table\">
        <tr valign=\"top\">
          <th scope=\"row\">'Instructions' Label</th>
          <td>
            <label><input type=\"checkbox\" name=\"instruction-label-hide\" value=\"Hide\" ";
        // line 131
        echo ($context["instruction_label_hide"] ?? null);
        echo " /> Don't show Instructions label</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">'Instructions' List Type</th>
          <td>
            <input type=\"radio\" id=\"instruction-list-type-l\" name=\"instruction-list-type\" value=\"l\" ";
        // line 137
        echo ($context["ins_l"] ?? null);
        echo " />
            <label for=\"instruction-list-type-l\">List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-ol\" name=\"instruction-list-type\" value=\"ol\" ";
        // line 140
        echo ($context["ins_ol"] ?? null);
        echo " />
            <label for=\"instruction-list-type-ol\">Numbered List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-ul\" name=\"instruction-list-type\" value=\"ul\" ";
        // line 143
        echo ($context["ins_ul"] ?? null);
        echo " />
            <label for=\"instruction-list-type-ul\">Bulleted List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-p\" name=\"instruction-list-type\" value=\"p\" ";
        // line 146
        echo ($context["ins_p"] ?? null);
        echo " />
            <label for=\"instruction-list-type-p\">Paragraphs</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-div\" name=\"instruction-list-type\" value=\"div\" ";
        // line 149
        echo ($context["ins_div"] ?? null);
        echo " />
            <label for=\"instruction-list-type-div\">Divs</label>
          </td>
        </tr>
      </table>
      <hr />
      <h3>Other Options</h3>
      <table class=\"form-table\">
          ";
        // line 157
        echo ($context["other_options"] ?? null);
        echo "
      </table>

      <p><input type=\"submit\" name=\"submit\" id=\"submit\" class=\"button-primary\" value=\"Save Changes\"></p>
    </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "settings.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  279 => 157,  268 => 149,  262 => 146,  256 => 143,  250 => 140,  244 => 137,  235 => 131,  219 => 118,  213 => 115,  207 => 112,  201 => 109,  195 => 106,  186 => 100,  173 => 90,  164 => 84,  159 => 82,  151 => 77,  144 => 73,  137 => 69,  130 => 65,  119 => 57,  114 => 55,  107 => 51,  100 => 47,  92 => 42,  79 => 32,  72 => 28,  68 => 27,  64 => 26,  60 => 25,  56 => 24,  52 => 23,  48 => 22,  43 => 20,  27 => 7,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "settings.twig", "/Applications/MAMP/htdocs/ziprecipes/wp-content/plugins/zip-recipes-free/views/settings.twig");
    }
}
