<?php

/* register.twig */
class __TwigTemplate_589d9977edb43f93ab1dcbc704a666bfc0d6a0e1cc1f586de9ba164138b9c575 extends Twig_Template
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
        $context["macros"] = $this->loadTemplate("macros.twig", "register.twig", 1);
        // line 2
        if (($context["iframed_form"] ?? null)) {
            // line 3
            echo "<!DOCTYPE html>
    <!--suppress HtmlUnknownTarget -->
    <head>
        <link rel=\"stylesheet\" href=\"";
            // line 6
            echo twig_escape_filter($this->env, ($context["home_url"] ?? null), "html", null, true);
            echo "/wp-admin/load-styles.php?c=1&dir=ltr&load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&load%5B%5D=l10n,buttons,wp-auth-check&ver=4.5.4\" type=\"text/css\" media=\"all\" />
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js\"></script>
        <style>
            body, html {
                height: auto;
            }
            .register-in-frame form {
                margin: 0 auto;
            }
        </style>
    </head>
    <body class=\"register-in-frame\">
";
        } else {
            // line 19
            echo "    <style>
        #skip-button {
            display: none;
        }
    </style>
";
        }
        // line 25
        echo $context["macros"]->getscripts(($context["registration_url"] ?? null));
        echo "
<form method=\"post\" action=\"";
        // line 26
        echo twig_escape_filter($this->env, ($context["registration_action"] ?? null), "html", null, true);
        echo "\"
      style=\"max-width: 500px\" class=\"register-page\" id=\"zlrecipe_settings_form\" name=\"zlrecipe_settings_form\">
    ";
        // line 28
        echo $context["macros"]->getregister(($context["wp_version"] ?? null), ($context["installed_plugins"] ?? null), ($context["home_url"] ?? null), ($context["return_to_url"] ?? null));
        echo "
</form>
";
        // line 30
        if (($context["iframed_form"] ?? null)) {
            // line 31
            echo "        </body>
</html>
";
        }
    }

    public function getTemplateName()
    {
        return "register.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 31,  66 => 30,  61 => 28,  56 => 26,  52 => 25,  44 => 19,  28 => 6,  23 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "register.twig", "/Applications/MAMP/htdocs/rogierlankhorst/wp-content/plugins/zip-recipes/views/register.twig");
    }
}
