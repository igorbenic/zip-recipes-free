<?php

/* macros.twig */
class __TwigTemplate_857869c97eca487b44373c520f2b3c648386df6914dbd01423fd06dd0fc63660 extends Twig_Template
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
        // line 33
        echo "
";
        // line 76
        echo "
";
    }

    // line 1
    public function getregister($__wp_version__ = null, $__installed_plugins__ = null, $__home_url__ = null, $__return_to_url__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "wp_version" => $__wp_version__,
            "installed_plugins" => $__installed_plugins__,
            "home_url" => $__home_url__,
            "return_to_url" => $__return_to_url__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "    <h3>Register Zip Recipes Free</h3>
    Please register your plugin so we can email you news about updates to Zip Recipes, including tips and tricks on how to use it.
    Registering also helps us troubleshoot any problems you may encounter. When you register, we will
    automatically receive your blog's web address, WordPress version, and names of installed plugins.
    <table class=\"form-table\">
        <tr valign=\"top\">
            <th scope=\"row\" style=\"padding: 0\"><label for=\"first_name\">First Name:</label></th>
            <td style=\"padding: 0\"><input type=\"text\" id=\"first_name\" name=\"first_name\" class=\"regular-text\" required autofocus /></td>
        </tr>
        <tr valign=\"top\">
            <th scope=\"row\" style=\"padding: 0\"><label for=\"last_name\">Last Name:</label></th>
            <td style=\"padding: 0\"><input type=\"text\" id=\"last_name\" name=\"last_name\" class=\"regular-text\" required /></td>
        </tr>
        <tr valign=\"top\">
            <th scope=\"row\" style=\"padding: 0\"><label for=\"email\">Email:</label></th>
            <td style=\"padding: 0\">
                <input type=\"email\" id=\"email\" name=\"email\" class=\"regular-text\" required />
                <input type=\"hidden\" id=\"wp-version\" name=\"wp_version\" value=\"";
            // line 19
            echo twig_escape_filter($this->env, ($context["wp_version"] ?? null), "html", null, true);
            echo "\" />
                <input type=\"hidden\" id=\"plugins\" name=\"plugins\" value=\"";
            // line 20
            echo twig_escape_filter($this->env, ($context["installed_plugins"] ?? null), "html", null, true);
            echo "\" />
                <input type=\"hidden\" id=\"blog-url\" name=\"blog_url\" value=\"";
            // line 21
            echo twig_escape_filter($this->env, ($context["home_url"] ?? null), "html", null, true);
            echo "\" />
                <input type=\"hidden\" id=\"return-url\" name=\"return-url\" value=\"";
            // line 22
            echo twig_escape_filter($this->env, ($context["return_to_url"] ?? null), "html", null, true);
            echo "\" />
                <input type=\"hidden\" name=\"action\" value=\"zrdn-register\" />
            </td>
        </tr>
    </table>
    <p style=\"text-align: right\">
        <a href=\"";
            // line 28
            echo ($context["return_to_url"] ?? null);
            echo "&skip_registration=1\" id=\"skip-button\" class=\"button-skip\"
           style=\"border: 0; background: none;cursor: pointer;color:#777777;margin-right:10px;\">Skip</a>
        <input type=\"submit\" id=\"register_button\" class=\"button-primary\" value=\"Register\">
    </p>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 34
    public function getscripts($__registration_url__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "registration_url" => $__registration_url__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 35
            echo "    <script type=\"text/javascript\">
        jQuery(document).ready(function () {
            var \$form = jQuery(\"#zlrecipe_settings_form\");

            \$form.on(\"submit\", function () {
                var \$registerButton = jQuery(\"#register_button\");
                \$registerButton.val(\"Registering...\");
                \$registerButton.attr(\"disabled\", true);

                var postUrl = \"";
            // line 44
            echo ($context["registration_url"] ?? null);
            echo "\";

                jQuery.post(postUrl, \$form.serialize(), function (data) {
                    var jsonData = JSON.parse(data);

                    if (jsonData.status === \"failure\") {
                        for (var elementID in jsonData.errors) {
                            if (jsonData.errors.hasOwnProperty(elementID)) {
                                var errors = jsonData.errors[elementID];
                                for (var i = 0; i < errors.length; i++) {
                                    jQuery(\"#\" + elementID).parent().append(\"<br/>\" + errors[i]);
                                }
                            }
                        }

                        \$registerButton.val(\"Register\");
                        \$registerButton.attr(\"disabled\", false);
                    }
                    else {
                        // unbind current event hanlder and resubmit the form
                        \$form.off(\"submit\");


                        \$form.get(0).submit();
                    }
                });

                return false;
            });
        });
    </script>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 77
    public function getrecipe_attr($__attr_name__ = null, $__attr_value__ = null, $__amp_on__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "attr_name" => $__attr_name__,
            "attr_value" => $__attr_value__,
            "amp_on" => $__amp_on__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 78
            echo "    ";
            if ( !($context["amp_on"] ?? null)) {
                // line 79
                echo "        ";
                echo twig_escape_filter($this->env, ($context["attr_name"] ?? null), "html", null, true);
                if (($context["attr_value"] ?? null)) {
                    echo "=\"";
                    echo twig_escape_filter($this->env, ($context["attr_value"] ?? null), "html", null, true);
                    echo "\"";
                }
                // line 80
                echo "    ";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "macros.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  197 => 80,  189 => 79,  186 => 78,  172 => 77,  125 => 44,  114 => 35,  102 => 34,  82 => 28,  73 => 22,  69 => 21,  65 => 20,  61 => 19,  42 => 2,  27 => 1,  22 => 76,  19 => 33,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "macros.twig", "/Applications/MAMP/htdocs/rogierlankhorst/wp-content/plugins/zip-recipes/views/macros.twig");
    }
}
