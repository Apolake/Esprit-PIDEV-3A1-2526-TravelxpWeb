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

/* trip/show.html.twig */
class __TwigTemplate_26e84a2844cc553747d1318fc283a4bc extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "trip/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "trip/show.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Trip") : ("Trip Details"));
        
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
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 7
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "trips"]);
            yield "
    ";
        }
        // line 9
        yield "
    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">";
        // line 12
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 12, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu") : ("Front Office"));
        yield "</p>
            <h1>";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 13, $this->source); })()), "tripName", [], "any", false, false, false, 13), "html", null, true);
        yield "</h1>
            <p class=\"muted\">";
        // line 14
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 14, $this->source); })()), "description", [], "any", false, false, false, 14)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 14, $this->source); })()), "description", [], "any", false, false, false, 14), "html", null, true)) : ("No description provided."));
        yield "</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"";
        // line 17
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath((((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 17, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_trip_index") : ("trip_index")));
        yield "\">Back to list</a>
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_trip_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 19, $this->source); })()), "id", [], "any", false, false, false, 19)]), "html", null, true);
            yield "\">Edit</a>
                ";
            // line 20
            yield Twig\Extension\CoreExtension::include($this->env, $context, "trip/_delete_form.html.twig", ["trip" => (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 20, $this->source); })())]);
            yield "
            ";
        } else {
            // line 22
            yield "                ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 22, $this->source); })()), "user", [], "any", false, false, false, 22)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 23
                yield "                    ";
                if ((($tmp = (isset($context["isJoined"]) || array_key_exists("isJoined", $context) ? $context["isJoined"] : (function () { throw new RuntimeError('Variable "isJoined" does not exist.', 23, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 24
                    yield "                        <form method=\"post\" class=\"inline-form\" action=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_leave", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 24, $this->source); })()), "id", [], "any", false, false, false, 24)]), "html", null, true);
                    yield "\">
                            <input type=\"hidden\" name=\"_token\" value=\"";
                    // line 25
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("leave_trip_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 25, $this->source); })()), "id", [], "any", false, false, false, 25))), "html", null, true);
                    yield "\">
                            <button class=\"btn\" type=\"submit\">Leave trip</button>
                        </form>
                    ";
                } else {
                    // line 29
                    yield "                        <form method=\"post\" class=\"inline-form\" action=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_join", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 29, $this->source); })()), "id", [], "any", false, false, false, 29)]), "html", null, true);
                    yield "\">
                            <input type=\"hidden\" name=\"_token\" value=\"";
                    // line 30
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("join_trip_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 30, $this->source); })()), "id", [], "any", false, false, false, 30))), "html", null, true);
                    yield "\">
                            <button class=\"btn btn-primary\" type=\"submit\">Join trip</button>
                        </form>
                    ";
                }
                // line 34
                yield "                ";
            }
            // line 35
            yield "            ";
        }
        // line 36
        yield "        </div>
    </section>

    <section class=\"glass-card\">
        <dl class=\"details-grid\">
            <dt>Owner user ID</dt><dd>";
        // line 41
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 41, $this->source); })()), "userId", [], "any", false, false, false, 41)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 41, $this->source); })()), "userId", [], "any", false, false, false, 41), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Route</dt><dd>";
        // line 42
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 42, $this->source); })()), "origin", [], "any", false, false, false, 42)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 42, $this->source); })()), "origin", [], "any", false, false, false, 42), "html", null, true)) : ("—"));
        yield " → ";
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 42, $this->source); })()), "destination", [], "any", false, false, false, 42)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 42, $this->source); })()), "destination", [], "any", false, false, false, 42), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Date range</dt><dd>";
        // line 43
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 43, $this->source); })()), "startDate", [], "any", false, false, false, 43)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 43, $this->source); })()), "startDate", [], "any", false, false, false, 43), "Y-m-d"), "html", null, true)) : ("—"));
        yield " → ";
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 43, $this->source); })()), "endDate", [], "any", false, false, false, 43)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 43, $this->source); })()), "endDate", [], "any", false, false, false, 43), "Y-m-d"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Status</dt><dd>";
        // line 44
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 44, $this->source); })()), "status", [], "any", false, false, false, 44)), "html", null, true);
        yield "</dd>
            <dt>Budget</dt><dd>";
        // line 45
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 45, $this->source); })()), "currency", [], "any", false, false, false, 45), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber((((CoreExtension::getAttribute($this->env, $this->source, ($context["trip"] ?? null), "budgetAmount", [], "any", true, true, false, 45) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 45, $this->source); })()), "budgetAmount", [], "any", false, false, false, 45)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 45, $this->source); })()), "budgetAmount", [], "any", false, false, false, 45)) : (0)), 2, ".", ","), "html", null, true);
        yield "</dd>
            <dt>Total expenses</dt><dd>";
        // line 46
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 46, $this->source); })()), "currency", [], "any", false, false, false, 46), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber((((CoreExtension::getAttribute($this->env, $this->source, ($context["trip"] ?? null), "totalExpenses", [], "any", true, true, false, 46) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 46, $this->source); })()), "totalExpenses", [], "any", false, false, false, 46)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 46, $this->source); })()), "totalExpenses", [], "any", false, false, false, 46)) : (0)), 2, ".", ","), "html", null, true);
        yield "</dd>
            <dt>Total XP earned</dt><dd>";
        // line 47
        yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["trip"] ?? null), "totalXpEarned", [], "any", true, true, false, 47) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 47, $this->source); })()), "totalXpEarned", [], "any", false, false, false, 47)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 47, $this->source); })()), "totalXpEarned", [], "any", false, false, false, 47), "html", null, true)) : (0));
        yield "</dd>
            <dt>Participants</dt><dd>";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 48, $this->source); })()), "participants", [], "any", false, false, false, 48)), "html", null, true);
        yield "</dd>
            <dt>Notes</dt><dd>";
        // line 49
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 49, $this->source); })()), "notes", [], "any", false, false, false, 49)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 49, $this->source); })()), "notes", [], "any", false, false, false, 49), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Cover image URL</dt><dd>";
        // line 50
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 50, $this->source); })()), "coverImageUrl", [], "any", false, false, false, 50)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["trip"]) || array_key_exists("trip", $context) ? $context["trip"] : (function () { throw new RuntimeError('Variable "trip" does not exist.', 50, $this->source); })()), "coverImageUrl", [], "any", false, false, false, 50), "html", null, true)) : ("—"));
        yield "</dd>
        </dl>
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
        return "trip/show.html.twig";
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
        return array (  231 => 50,  227 => 49,  223 => 48,  219 => 47,  213 => 46,  207 => 45,  203 => 44,  197 => 43,  191 => 42,  187 => 41,  180 => 36,  177 => 35,  174 => 34,  167 => 30,  162 => 29,  155 => 25,  150 => 24,  147 => 23,  144 => 22,  139 => 20,  134 => 19,  132 => 18,  128 => 17,  122 => 14,  118 => 13,  114 => 12,  109 => 9,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Trip' : 'Trip Details' }}{% endblock %}

{% block body %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'trips'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>{{ trip.tripName }}</h1>
            <p class=\"muted\">{{ trip.description ?: 'No description provided.' }}</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"{{ path(isAdmin ? 'admin_trip_index' : 'trip_index') }}\">Back to list</a>
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path('admin_trip_edit', {'id': trip.id}) }}\">Edit</a>
                {{ include('trip/_delete_form.html.twig', {trip: trip}) }}
            {% else %}
                {% if app.user %}
                    {% if isJoined %}
                        <form method=\"post\" class=\"inline-form\" action=\"{{ path('trip_leave', {'id': trip.id}) }}\">
                            <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('leave_trip_' ~ trip.id) }}\">
                            <button class=\"btn\" type=\"submit\">Leave trip</button>
                        </form>
                    {% else %}
                        <form method=\"post\" class=\"inline-form\" action=\"{{ path('trip_join', {'id': trip.id}) }}\">
                            <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('join_trip_' ~ trip.id) }}\">
                            <button class=\"btn btn-primary\" type=\"submit\">Join trip</button>
                        </form>
                    {% endif %}
                {% endif %}
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <dl class=\"details-grid\">
            <dt>Owner user ID</dt><dd>{{ trip.userId ?: '—' }}</dd>
            <dt>Route</dt><dd>{{ trip.origin ?: '—' }} → {{ trip.destination ?: '—' }}</dd>
            <dt>Date range</dt><dd>{{ trip.startDate ? trip.startDate|date('Y-m-d') : '—' }} → {{ trip.endDate ? trip.endDate|date('Y-m-d') : '—' }}</dd>
            <dt>Status</dt><dd>{{ trip.status|capitalize }}</dd>
            <dt>Budget</dt><dd>{{ trip.currency }} {{ (trip.budgetAmount ?? 0)|number_format(2, '.', ',') }}</dd>
            <dt>Total expenses</dt><dd>{{ trip.currency }} {{ (trip.totalExpenses ?? 0)|number_format(2, '.', ',') }}</dd>
            <dt>Total XP earned</dt><dd>{{ trip.totalXpEarned ?? 0 }}</dd>
            <dt>Participants</dt><dd>{{ trip.participants|length }}</dd>
            <dt>Notes</dt><dd>{{ trip.notes ?: '—' }}</dd>
            <dt>Cover image URL</dt><dd>{{ trip.coverImageUrl ?: '—' }}</dd>
        </dl>
    </section>
{% endblock %}
", "trip/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\trip\\show.html.twig");
    }
}
