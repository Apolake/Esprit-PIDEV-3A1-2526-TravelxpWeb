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

/* offer/show.html.twig */
class __TwigTemplate_7105d36de59695918090161e0f8b122c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "offer/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "offer/show.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Offer") : ("Offer Details"));
        
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
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_offer_") : ("offer_"));
        // line 7
        yield "    <section class=\"glass-card\">
        <p class=\"eyebrow\">";
        // line 8
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 8, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu") : ("Front Office"));
        yield "</p>
        <h1>";
        // line 9
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 9, $this->source); })()), "title", [], "any", false, false, false, 9), "html", null, true);
        yield "</h1>

        <dl class=\"details-grid\">
            <dt>Property</dt><dd>";
        // line 12
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 12, $this->source); })()), "property", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 12, $this->source); })()), "property", [], "any", false, false, false, 12), "title", [], "any", false, false, false, 12), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Discount</dt><dd>";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 13, $this->source); })()), "discountPercentage", [], "any", false, false, false, 13), 2, ".", ","), "html", null, true);
        yield "%</dd>
            <dt>Start date</dt><dd>";
        // line 14
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 14, $this->source); })()), "startDate", [], "any", false, false, false, 14)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 14, $this->source); })()), "startDate", [], "any", false, false, false, 14), "Y-m-d"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>End date</dt><dd>";
        // line 15
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 15, $this->source); })()), "endDate", [], "any", false, false, false, 15)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 15, $this->source); })()), "endDate", [], "any", false, false, false, 15), "Y-m-d"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Valid now</dt><dd>";
        // line 16
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 16, $this->source); })()), "validToday", [], "any", false, false, false, 16)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Yes") : ("No"));
        yield "</dd>
            <dt>Status</dt><dd>";
        // line 17
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 17, $this->source); })()), "isActive", [], "any", false, false, false, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Active") : ("Inactive"));
        yield "</dd>
            <dt>Description</dt><dd>";
        // line 18
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 18, $this->source); })()), "description", [], "any", false, false, false, 18)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 18, $this->source); })()), "description", [], "any", false, false, false, 18), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Created</dt><dd>";
        // line 19
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 19, $this->source); })()), "createdAt", [], "any", false, false, false, 19)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 19, $this->source); })()), "createdAt", [], "any", false, false, false, 19), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Updated</dt><dd>";
        // line 20
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 20, $this->source); })()), "updatedAt", [], "any", false, false, false, 20)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 20, $this->source); })()), "updatedAt", [], "any", false, false, false, 20), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</dd>
        </dl>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"";
        // line 24
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 24, $this->source); })()) . "index"));
        yield "\">Back to list</a>
            ";
        // line 25
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 25, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 26
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 26, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 26, $this->source); })()), "id", [], "any", false, false, false, 26)]), "html", null, true);
            yield "\">Edit</a>
                ";
            // line 27
            yield Twig\Extension\CoreExtension::include($this->env, $context, "offer/_delete_form.html.twig", ["routePrefix" => (isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 27, $this->source); })()), "offer" => (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 27, $this->source); })())]);
            yield "
            ";
        }
        // line 29
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
        return "offer/show.html.twig";
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
        return array (  171 => 29,  166 => 27,  161 => 26,  159 => 25,  155 => 24,  148 => 20,  144 => 19,  140 => 18,  136 => 17,  132 => 16,  128 => 15,  124 => 14,  120 => 13,  116 => 12,  110 => 9,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Offer' : 'Offer Details' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_offer_' : 'offer_' %}
    <section class=\"glass-card\">
        <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
        <h1>{{ offer.title }}</h1>

        <dl class=\"details-grid\">
            <dt>Property</dt><dd>{{ offer.property ? offer.property.title : '—' }}</dd>
            <dt>Discount</dt><dd>{{ offer.discountPercentage|number_format(2, '.', ',') }}%</dd>
            <dt>Start date</dt><dd>{{ offer.startDate ? offer.startDate|date('Y-m-d') : '—' }}</dd>
            <dt>End date</dt><dd>{{ offer.endDate ? offer.endDate|date('Y-m-d') : '—' }}</dd>
            <dt>Valid now</dt><dd>{{ offer.validToday ? 'Yes' : 'No' }}</dd>
            <dt>Status</dt><dd>{{ offer.isActive ? 'Active' : 'Inactive' }}</dd>
            <dt>Description</dt><dd>{{ offer.description ?: '—' }}</dd>
            <dt>Created</dt><dd>{{ offer.createdAt ? offer.createdAt|date('Y-m-d H:i') : '—' }}</dd>
            <dt>Updated</dt><dd>{{ offer.updatedAt ? offer.updatedAt|date('Y-m-d H:i') : '—' }}</dd>
        </dl>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"{{ path(routePrefix ~ 'index') }}\">Back to list</a>
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'edit', {'id': offer.id}) }}\">Edit</a>
                {{ include('offer/_delete_form.html.twig', {routePrefix: routePrefix, offer: offer}) }}
            {% endif %}
        </div>
    </section>
{% endblock %}
", "offer/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\offer\\show.html.twig");
    }
}
