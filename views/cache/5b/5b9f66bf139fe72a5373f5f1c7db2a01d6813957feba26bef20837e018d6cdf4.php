<?php

/* settings.twig */
class __TwigTemplate_fa5bfdb4401eeac3d4494bb902b9dfe193991e1312aa95581f8c12f918e75dfc extends Twig_Template
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
      ";
        // line 9
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
        // line 19
        echo twig_escape_filter($this->env, ($context["custom_print_image"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" />
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Zip Recipes Plugin Link</th>
          <td><label><input type=\"checkbox\" name=\"zrecipe-attribution-hide\" value=\"Hide\" ";
        // line 24
        echo ($context["zrecipe_attribution_hide"] ?? null);
        echo " /> Don't show plugin link</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Printed Output: Recipe Permalink</th>
          <td><label><input type=\"checkbox\" name=\"printed-permalink-hide\" value=\"Hide\" ";
        // line 28
        echo ($context["printed_permalink_hide"] ?? null);
        echo " /> Don't show permalink in printed output</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Printed Output: Copyright Statement</th>
          <td><input type=\"text\" name=\"printed-copyright-statement\" value=\"";
        // line 32
        echo twig_escape_filter($this->env, ($context["printed_copyright_statement"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" /></td>
        </tr>
        ";
        // line 34
        echo ($context["author_section"] ?? null);
        echo "
      </table>

      <hr />
      <h3>Recipe Card Look & Feel</h3>
      <table class=\"form-table\">
        <tr valign=\"top\">
          <th scope=\"row\">Stylesheet</th>
          <td><label><input type=\"checkbox\" name=\"stylesheet\" value=\"zlrecipe-std\" ";
        // line 42
        echo ($context["stylesheet"] ?? null);
        echo " /> Use Zip Recipes style</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Recipe Title</th>
          <td><label><input type=\"checkbox\" name=\"recipe-title-hide\" value=\"Hide\" ";
        // line 46
        echo ($context["recipe_title_hide"] ?? null);
        echo " /> Don't show Recipe Title in post (still shows in print view)</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Print Button</th>
          <td><label><input type=\"checkbox\" name=\"print-link-hide\" value=\"Hide\" ";
        // line 50
        echo ($context["print_link_hide"] ?? null);
        echo " /> Don't show Print Button</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Image Width</th>
          <td><label><input type=\"text\" name=\"image-width\" value=\"";
        // line 54
        echo twig_escape_filter($this->env, ($context["image_width"] ?? null), "html", null, true);
        echo "\" class=\"regular-text\" /> pixels</label></td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Image Display</th>
          <td>
            <label><input type=\"checkbox\" name=\"image-hide\" value=\"Hide\" ";
        // line 59
        echo ($context["image_hide"] ?? null);
        echo " /> Don't show Image in post</label>
            <br />
            <label><input type=\"checkbox\" name=\"image-hide-print\" value=\"Hide\" ";
        // line 61
        echo ($context["image_hide_print"] ?? null);
        echo " /> Don't show Image in print view</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">Border Style</th>
          <td>
            <select name=\"outer-border-style\">";
        // line 67
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
        // line 77
        echo ($context["ingredient_label_hide"] ?? null);
        echo " /> Don't show Ingredients label</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">'Ingredients' List Type</th>
          <td>
            <input type=\"radio\" id=\"ingredient-list-type-l\" name=\"ingredient-list-type\" value=\"l\" ";
        // line 83
        echo ($context["ing_l"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-l\">List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-ol\" name=\"ingredient-list-type\" value=\"ol\" ";
        // line 86
        echo ($context["ing_ol"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-ol\">Numbered List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-ul\" name=\"ingredient-list-type\" value=\"ul\" ";
        // line 89
        echo ($context["ing_ul"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-ul\">Bulleted List</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-p\" name=\"ingredient-list-type\" value=\"p\" ";
        // line 92
        echo ($context["ing_p"] ?? null);
        echo " />
            <label for=\"ingredient-list-type-p\">Paragraphs</label>
            <br />
            <input type=\"radio\" id=\"ingredient-list-type-div\" name=\"ingredient-list-type\" value=\"div\" ";
        // line 95
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
        // line 108
        echo ($context["instruction_label_hide"] ?? null);
        echo " /> Don't show Instructions label</label>
          </td>
        </tr>
        <tr valign=\"top\">
          <th scope=\"row\">'Instructions' List Type</th>
          <td>
            <input type=\"radio\" id=\"instruction-list-type-l\" name=\"instruction-list-type\" value=\"l\" ";
        // line 114
        echo ($context["ins_l"] ?? null);
        echo " />
            <label for=\"instruction-list-type-l\">List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-ol\" name=\"instruction-list-type\" value=\"ol\" ";
        // line 117
        echo ($context["ins_ol"] ?? null);
        echo " />
            <label for=\"instruction-list-type-ol\">Numbered List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-ul\" name=\"instruction-list-type\" value=\"ul\" ";
        // line 120
        echo ($context["ins_ul"] ?? null);
        echo " />
            <label for=\"instruction-list-type-ul\">Bulleted List</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-p\" name=\"instruction-list-type\" value=\"p\" ";
        // line 123
        echo ($context["ins_p"] ?? null);
        echo " />
            <label for=\"instruction-list-type-p\">Paragraphs</label>
            <br />
            <input type=\"radio\" id=\"instruction-list-type-div\" name=\"instruction-list-type\" value=\"div\" ";
        // line 126
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
        // line 134
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
        return array (  232 => 134,  221 => 126,  215 => 123,  209 => 120,  203 => 117,  197 => 114,  188 => 108,  172 => 95,  166 => 92,  160 => 89,  154 => 86,  148 => 83,  139 => 77,  126 => 67,  117 => 61,  112 => 59,  104 => 54,  97 => 50,  90 => 46,  83 => 42,  72 => 34,  67 => 32,  60 => 28,  53 => 24,  45 => 19,  32 => 9,  27 => 7,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "settings.twig", "/Applications/MAMP/htdocs/rogierlankhorst/wp-content/plugins/zip-recipes/views/settings.twig");
    }
}
