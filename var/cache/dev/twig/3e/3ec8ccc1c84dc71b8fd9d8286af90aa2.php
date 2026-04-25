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

/* activity/show.html.twig */
class __TwigTemplate_632fff08a4d5a762ee98917f3a891e90 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "activity/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "activity/show.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Activity details") : ("Activity details"));
        
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
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_activity_") : ("activity_"));
        // line 7
        yield "    ";
        $context["joined"] = (isset($context["isJoined"]) || array_key_exists("isJoined", $context) ? $context["isJoined"] : (function () { throw new RuntimeError('Variable "isJoined" does not exist.', 7, $this->source); })());
        // line 8
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 8, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 9
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "activities"]);
            yield "
    ";
        }
        // line 11
        yield "
    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">";
        // line 14
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 14, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu") : ("Front Office"));
        yield "</p>
            <h1>";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 15, $this->source); })()), "title", [], "any", false, false, false, 15), "html", null, true);
        yield "</h1>
            <p class=\"muted\">";
        // line 16
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 16, $this->source); })()), "description", [], "any", false, false, false, 16)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 16, $this->source); })()), "description", [], "any", false, false, false, 16), "html", null, true)) : ("No description provided."));
        yield "</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"";
        // line 19
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 19, $this->source); })()) . "index"));
        yield "\">Back to list</a>
            ";
        // line 20
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 20, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 21
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_activity_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 21, $this->source); })()), "id", [], "any", false, false, false, 21)]), "html", null, true);
            yield "\">Edit</a>
            ";
        } elseif ((($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 22
(isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 22, $this->source); })()), "user", [], "any", false, false, false, 22)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 23
            yield "                ";
            if ((($tmp = (isset($context["joined"]) || array_key_exists("joined", $context) ? $context["joined"] : (function () { throw new RuntimeError('Variable "joined" does not exist.', 23, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 24
                yield "                    <form method=\"post\" class=\"inline-form\" action=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_leave", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 24, $this->source); })()), "id", [], "any", false, false, false, 24)]), "html", null, true);
                yield "\">
                        <input type=\"hidden\" name=\"_token\" value=\"";
                // line 25
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("leave_activity_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 25, $this->source); })()), "id", [], "any", false, false, false, 25))), "html", null, true);
                yield "\">
                        <button class=\"btn\" type=\"submit\">Leave activity</button>
                    </form>
                ";
            } elseif ((($tmp =             // line 28
(isset($context["canJoinTrip"]) || array_key_exists("canJoinTrip", $context) ? $context["canJoinTrip"] : (function () { throw new RuntimeError('Variable "canJoinTrip" does not exist.', 28, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 29
                yield "                    <form method=\"post\" class=\"inline-form\" action=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_join", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 29, $this->source); })()), "id", [], "any", false, false, false, 29)]), "html", null, true);
                yield "\">
                        <input type=\"hidden\" name=\"_token\" value=\"";
                // line 30
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("join_activity_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 30, $this->source); })()), "id", [], "any", false, false, false, 30))), "html", null, true);
                yield "\">
                        <button class=\"btn btn-primary\" type=\"submit\">Join activity</button>
                    </form>
                ";
            } else {
                // line 34
                yield "                    <span class=\"pill\">Join the trip first</span>
                ";
            }
            // line 36
            yield "            ";
        }
        // line 37
        yield "        </div>
    </section>

    <section class=\"glass-card\">
        <dl class=\"details-grid\">
            <dt>Type</dt><dd>";
        // line 42
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 42, $this->source); })()), "type", [], "any", false, false, false, 42)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 42, $this->source); })()), "type", [], "any", false, false, false, 42), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Status</dt><dd>";
        // line 43
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 43, $this->source); })()), "status", [], "any", false, false, false, 43)), "html", null, true);
        yield "</dd>
            <dt>Trip</dt><dd>";
        // line 44
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 44, $this->source); })()), "trip", [], "any", false, false, false, 44)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 44, $this->source); })()), "trip", [], "any", false, false, false, 44), "tripName", [], "any", false, false, false, 44), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Date</dt><dd>";
        // line 45
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 45, $this->source); })()), "activityDate", [], "any", false, false, false, 45)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 45, $this->source); })()), "activityDate", [], "any", false, false, false, 45), "Y-m-d"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Start time</dt><dd>";
        // line 46
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 46, $this->source); })()), "startTime", [], "any", false, false, false, 46)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 46, $this->source); })()), "startTime", [], "any", false, false, false, 46), "H:i"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>End time</dt><dd>";
        // line 47
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 47, $this->source); })()), "endTime", [], "any", false, false, false, 47)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 47, $this->source); })()), "endTime", [], "any", false, false, false, 47), "H:i"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Location</dt><dd>";
        // line 48
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 48, $this->source); })()), "locationName", [], "any", false, false, false, 48)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 48, $this->source); })()), "locationName", [], "any", false, false, false, 48), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Transport</dt><dd>";
        // line 49
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 49, $this->source); })()), "transportType", [], "any", false, false, false, 49)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 49, $this->source); })()), "transportType", [], "any", false, false, false, 49), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Cost</dt><dd>";
        // line 50
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 50, $this->source); })()), "currency", [], "any", false, false, false, 50), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber((((CoreExtension::getAttribute($this->env, $this->source, ($context["activity"] ?? null), "costAmount", [], "any", true, true, false, 50) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 50, $this->source); })()), "costAmount", [], "any", false, false, false, 50)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 50, $this->source); })()), "costAmount", [], "any", false, false, false, 50)) : (0)), 2, ".", ","), "html", null, true);
        yield "</dd>
            <dt>XP earned</dt><dd>";
        // line 51
        yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["activity"] ?? null), "xpEarned", [], "any", true, true, false, 51) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 51, $this->source); })()), "xpEarned", [], "any", false, false, false, 51)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 51, $this->source); })()), "xpEarned", [], "any", false, false, false, 51), "html", null, true)) : (0));
        yield "</dd>
            <dt>Participants</dt><dd>";
        // line 52
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["activity"]) || array_key_exists("activity", $context) ? $context["activity"] : (function () { throw new RuntimeError('Variable "activity" does not exist.', 52, $this->source); })()), "participants", [], "any", false, false, false, 52)), "html", null, true);
        yield "</dd>
            <dt>Your status</dt><dd>";
        // line 53
        yield (((($tmp = (isset($context["joined"]) || array_key_exists("joined", $context) ? $context["joined"] : (function () { throw new RuntimeError('Variable "joined" does not exist.', 53, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Joined") : ("Not joined"));
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
        return "activity/show.html.twig";
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
        return array (  235 => 53,  231 => 52,  227 => 51,  221 => 50,  217 => 49,  213 => 48,  209 => 47,  205 => 46,  201 => 45,  197 => 44,  193 => 43,  189 => 42,  182 => 37,  179 => 36,  175 => 34,  168 => 30,  163 => 29,  161 => 28,  155 => 25,  150 => 24,  147 => 23,  145 => 22,  140 => 21,  138 => 20,  134 => 19,  128 => 16,  124 => 15,  120 => 14,  115 => 11,  109 => 9,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Activity details' : 'Activity details' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_activity_' : 'activity_' %}
    {% set joined = isJoined %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'activities'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>{{ activity.title }}</h1>
            <p class=\"muted\">{{ activity.description ?: 'No description provided.' }}</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"{{ path(routePrefix ~ 'index') }}\">Back to list</a>
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path('admin_activity_edit', {'id': activity.id}) }}\">Edit</a>
            {% elseif app.user %}
                {% if joined %}
                    <form method=\"post\" class=\"inline-form\" action=\"{{ path('activity_leave', {'id': activity.id}) }}\">
                        <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('leave_activity_' ~ activity.id) }}\">
                        <button class=\"btn\" type=\"submit\">Leave activity</button>
                    </form>
                {% elseif canJoinTrip %}
                    <form method=\"post\" class=\"inline-form\" action=\"{{ path('activity_join', {'id': activity.id}) }}\">
                        <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('join_activity_' ~ activity.id) }}\">
                        <button class=\"btn btn-primary\" type=\"submit\">Join activity</button>
                    </form>
                {% else %}
                    <span class=\"pill\">Join the trip first</span>
                {% endif %}
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <dl class=\"details-grid\">
            <dt>Type</dt><dd>{{ activity.type ?: '—' }}</dd>
            <dt>Status</dt><dd>{{ activity.status|capitalize }}</dd>
            <dt>Trip</dt><dd>{{ activity.trip ? activity.trip.tripName : '—' }}</dd>
            <dt>Date</dt><dd>{{ activity.activityDate ? activity.activityDate|date('Y-m-d') : '—' }}</dd>
            <dt>Start time</dt><dd>{{ activity.startTime ? activity.startTime|date('H:i') : '—' }}</dd>
            <dt>End time</dt><dd>{{ activity.endTime ? activity.endTime|date('H:i') : '—' }}</dd>
            <dt>Location</dt><dd>{{ activity.locationName ?: '—' }}</dd>
            <dt>Transport</dt><dd>{{ activity.transportType ?: '—' }}</dd>
            <dt>Cost</dt><dd>{{ activity.currency }} {{ (activity.costAmount ?? 0)|number_format(2, '.', ',') }}</dd>
            <dt>XP earned</dt><dd>{{ activity.xpEarned ?? 0 }}</dd>
            <dt>Participants</dt><dd>{{ activity.participants|length }}</dd>
            <dt>Your status</dt><dd>{{ joined ? 'Joined' : 'Not joined' }}</dd>
        </dl>
    </section>
{% endblock %}
", "activity/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\activity\\show.html.twig");
    }
}
