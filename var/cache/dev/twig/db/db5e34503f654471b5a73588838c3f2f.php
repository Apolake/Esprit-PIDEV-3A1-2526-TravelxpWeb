<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* service/show.html.twig */
class __TwigTemplate_652d11a5ea718164d195a03b8851a0b2 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "service/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "service/show.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Service") : ("Service Details"));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 6
        yield "    ";
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_service_") : ("service_"));
        // line 7
        yield "    <section class=\"glass-card\">
        <p class=\"eyebrow\">";
        // line 8
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 8, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu") : ("Front Office"));
        yield "</p>
        <h1>";
        // line 9
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 9, $this->source); })()), "providerName", [], "any", false, false, false, 9), "html", null, true);
        yield "</h1>

        <dl class=\"details-grid\">
            <dt>Service type</dt><dd>";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 12, $this->source); })()), "serviceType", [], "any", false, false, false, 12), "html", null, true);
        yield "</dd>
            <dt>Price</dt><dd>\$";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 13, $this->source); })()), "price", [], "any", false, false, false, 13), 2, ".", ","), "html", null, true);
        yield "</dd>
            <dt>Available</dt><dd>";
        // line 14
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 14, $this->source); })()), "isAvailable", [], "any", false, false, false, 14)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Yes") : ("No"));
        yield "</dd>
            <dt>Eco-friendly</dt><dd>";
        // line 15
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 15, $this->source); })()), "ecoFriendly", [], "any", false, false, false, 15)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Yes") : ("No"));
        yield "</dd>
            <dt>Description</dt><dd>";
        // line 16
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 16, $this->source); })()), "description", [], "any", false, false, false, 16)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 16, $this->source); })()), "description", [], "any", false, false, false, 16), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Created</dt><dd>";
        // line 17
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 17, $this->source); })()), "createdAt", [], "any", false, false, false, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 17, $this->source); })()), "createdAt", [], "any", false, false, false, 17), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Updated</dt><dd>";
        // line 18
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 18, $this->source); })()), "updatedAt", [], "any", false, false, false, 18)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 18, $this->source); })()), "updatedAt", [], "any", false, false, false, 18), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</dd>
        </dl>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"";
        // line 22
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 22, $this->source); })()) . "index"));
        yield "\">Back to list</a>
            ";
        // line 23
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 23, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 24
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 24, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 24, $this->source); })()), "id", [], "any", false, false, false, 24)]), "html", null, true);
            yield "\">Edit</a>
                ";
            // line 25
            yield Twig\Extension\CoreExtension::include($this->env, $context, "service/_delete_form.html.twig", ["routePrefix" => (isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 25, $this->source); })()), "service" => (isset($context["service"]) || array_key_exists("service", $context) ? $context["service"] : (function () { throw new RuntimeError('Variable "service" does not exist.', 25, $this->source); })())]);
            yield "
            ";
        }
        // line 27
        yield "        </div>
    </section>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "service/show.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  163 => 27,  158 => 25,  153 => 24,  151 => 23,  147 => 22,  140 => 18,  136 => 17,  132 => 16,  128 => 15,  124 => 14,  120 => 13,  116 => 12,  110 => 9,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Service' : 'Service Details' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_service_' : 'service_' %}
    <section class=\"glass-card\">
        <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
        <h1>{{ service.providerName }}</h1>

        <dl class=\"details-grid\">
            <dt>Service type</dt><dd>{{ service.serviceType }}</dd>
            <dt>Price</dt><dd>\${{ service.price|number_format(2, '.', ',') }}</dd>
            <dt>Available</dt><dd>{{ service.isAvailable ? 'Yes' : 'No' }}</dd>
            <dt>Eco-friendly</dt><dd>{{ service.ecoFriendly ? 'Yes' : 'No' }}</dd>
            <dt>Description</dt><dd>{{ service.description ?: '—' }}</dd>
            <dt>Created</dt><dd>{{ service.createdAt ? service.createdAt|date('Y-m-d H:i') : '—' }}</dd>
            <dt>Updated</dt><dd>{{ service.updatedAt ? service.updatedAt|date('Y-m-d H:i') : '—' }}</dd>
        </dl>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"{{ path(routePrefix ~ 'index') }}\">Back to list</a>
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'edit', {'id': service.id}) }}\">Edit</a>
                {{ include('service/_delete_form.html.twig', {routePrefix: routePrefix, service: service}) }}
            {% endif %}
        </div>
    </section>
{% endblock %}
", "service/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\service\\show.html.twig");
    }
}
