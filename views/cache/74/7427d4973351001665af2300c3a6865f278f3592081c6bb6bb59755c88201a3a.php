<?php

/* footer.twig */
class __TwigTemplate_75f78117e16fc22804a7a7ee9eea5523b2820812357db1d8ee072462db4739f3 extends Twig_Template
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
        echo "<style type=\"text/css\" media=\"screen\">
  #wp_editrecipebtns { position:absolute;display:block;z-index:999998; }
  #wp_editrecipebtn { margin-right:20px; }
  #wp_editrecipebtn,#wp_delrecipebtn { cursor:pointer; padding:12px;background:#010101; -moz-border-radius:8px;-khtml-border-radius:8px;-webkit-border-radius:8px;border-radius:8px; filter:alpha(opacity=80); -moz-opacity:0.8; -khtml-opacity: 0.8; opacity: 0.8; }
  #wp_editrecipebtn:hover,#wp_delrecipebtn:hover { background:#000; filter:alpha(opacity=100); -moz-opacity:1; -khtml-opacity: 1; opacity: 1; }
</style>
<script>
    ";
        // line 9
        echo "    var baseurl = '";
        echo twig_escape_filter($this->env, (isset($context["url"]) ? $context["url"] : null), "js", null, true);
        echo "';          // This variable is used by the editor plugin
    var plugindir = '";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["pluginurl"]) ? $context["pluginurl"] : null), "js", null, true);
        echo "';  // This variable is used by the editor plugin
    ";
        // line 12
        echo "</script>";
    }

    public function getTemplateName()
    {
        return "footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 12,  33 => 10,  28 => 9,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "footer.twig", "/Applications/MAMP/htdocs/ziprecipes/wp-content/plugins/zip-recipes-free/views/footer.twig");
    }
}
