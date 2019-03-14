<?php

/* default_nutrition.twig */
class __TwigTemplate_67714a4269a4ba3efe50ce54b45042783d807037add42c06f24cbf7d13d14374 extends Twig_Template
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
        echo "<script type=\"text/javascript\">
    jQuery(document).ready(function () {
        jQuery('#get-nutrition').on('click', function() {
            jQuery('#nutrition-promo').show();
        });
    });
</script>
<div id=\"nutrition-promo\" style=\"display: none;\">
    ";
        // line 9
        if (($context["remote_promo"] ?? null)) {
            // line 10
            echo "        ";
            echo ($context["remote_promo"] ?? null);
            echo "
    ";
        } else {
            // line 12
            echo "        <div style=\"margin: 16px; color: rgb(239, 108, 0);\">
            <div style=\"display:block; padding: 5px; border: 1px solid #ccc;\">
                <h3 style=\"font-size: 1.1em; font-weight: bold;\">";
            // line 14
            echo twig_escape_filter($this->env, __("Nutrition data with one click!", "zip-recipes"), "html", null, true);
            echo "</h3>
                <span style=\"color: rgb(239, 108, 0); font-size: 13px; line-height: 25px\">Automatic Nutrition feature allows you to calculate the nutrition data with one click. <a style=\"color: rgb(239, 108, 0);\" href=\"https://www.ziprecipes.net/promo/nutrition_missing\" target=\"_blank\">Learn more.</a></span>
            </div>
        </div>
    ";
        }
        // line 19
        echo "</div>

<div class=\"zrdn-field\">
    <label class=\"zrdn-label\" for=\"yield\">Yields</label>
</div>
<div class=\"zrdn-field zrdn-has-addons\">
    <p class=\"zrdn-control\">
        <input class=\"zrdn-input zrdn-is-small\" type='text' id='yield' name='yield' placeholder=\"Number of servings\" value='";
        // line 26
        echo twig_escape_filter($this->env, ($context["yield"] ?? null), "html", null, true);
        echo "' />
    </p>
    <p class=\"zrdn-control\">
        <button class=\"zrdn-button zrdn-is-small\" type=\"button\" id=\"get-nutrition\">
            <span class=\"zrdn-icon zrdn-is-small\">
                <i class=\"fa fa-lock\"></i>
            </span>
            <span>";
        // line 33
        echo twig_escape_filter($this->env, __("Calculate Nutrition", "zip-recipes"), "html", null, true);
        echo "</span>
        </button>
    </p>
</div>";
    }

    public function getTemplateName()
    {
        return "default_nutrition.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 33,  58 => 26,  49 => 19,  41 => 14,  37 => 12,  31 => 10,  29 => 9,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "default_nutrition.twig", "/Applications/MAMP/htdocs/rogierlankhorst/wp-content/plugins/zip-recipes-free/views/default_nutrition.twig");
    }
}
