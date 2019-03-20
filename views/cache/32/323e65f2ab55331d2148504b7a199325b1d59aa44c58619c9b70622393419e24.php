<?php

/* author_promo.twig */
class __TwigTemplate_af49dfa8813e6cf3363562fbfc3f5f48b92f8a15d1fed2c45238058dfc2e623f extends Twig_Template
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
        echo "<div style=\"margin: 16px; color: rgb(239, 108, 0);\">
  <div style=\"display:block; padding: 5px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 1px 1px 1px 1px #ccc;\">
      <h3 style=\"font-size: 1.1em; font-weight: bold;\">Rank Even Higher on Google</h3>
      <span style=\"color: rgb(239, 108, 0); font-size: 13px; line-height: 25px\">Google recommends an author for this recipe. you can enable this in the premium version. <a style=\"color: rgb(239, 108, 0);\" href=\"https://www.ziprecipes.net/promo/author_missing\" target=\"_blank\">Learn more.</a></span>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "author_promo.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "author_promo.twig", "/Applications/MAMP/htdocs/ziprecipes/wp-content/plugins/zip-recipes-free/views/author_promo.twig");
    }
}
