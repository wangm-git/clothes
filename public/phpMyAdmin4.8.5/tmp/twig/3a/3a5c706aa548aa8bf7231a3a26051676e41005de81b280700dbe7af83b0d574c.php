<?php

/* display/results/value_display.twig */
class __TwigTemplate_a1e652c16afd74805025d23d22f729d332531489cc2a2bf9f3b9dc5f6c6ce2e7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<td class=\"left ";
        echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
        echo ((($context["condition_field"] ?? null)) ? (" condition") : (""));
        echo "\">
    ";
        // line 2
        echo ($context["value"] ?? null);
        echo "
</td>
";
    }

    public function getTemplateName()
    {
        return "display/results/value_display.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  25 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "display/results/value_display.twig", "D:\\phpstudy_pro\\WWW\\my_laravel\\public\\phpMyAdmin4.8.5\\templates\\display\\results\\value_display.twig");
    }
}
