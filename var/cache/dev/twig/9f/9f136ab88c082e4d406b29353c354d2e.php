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

/* trip/index.html.twig */
class __TwigTemplate_e590f78bc259d771fc5ba1f80f964337 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "trip/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "trip/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Trips") : ("Trips"));
        
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
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_trip_") : ("trip_"));
        // line 7
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "trips"]);
            yield "
    ";
        }
        // line 10
        yield "
    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">";
        // line 13
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 13, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu") : ("Front Office"));
        yield "</p>
            <h1>Trip management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Full CRUD for trips, planning details, and participation.") : ("Discover trips, join or leave participation, and track your joined plans."));
        yield "</p>
        </div>
        <div class=\"actions\">
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_trip_new");
            yield "\">Create trip</a>
            ";
        }
        // line 21
        yield "        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 25
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 25, $this->source); })()) . "index"));
        yield "\">
            <div>
                <label for=\"trip-q\">Search</label>
                <input id=\"trip-q\" type=\"search\" name=\"q\" value=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 28, $this->source); })()), "q", [], "any", false, false, false, 28), "html", null, true);
        yield "\" placeholder=\"trip, origin, destination\">
            </div>
            <div>
                <label for=\"trip-status\">Status</label>
                <select id=\"trip-status\" name=\"status\">
                    <option value=\"\">All statuses</option>
                    ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(["PLANNED", "ONGOING", "COMPLETED", "DONE", "CANCELLED"]);
        foreach ($context['_seq'] as $context["_key"] => $context["st"]) {
            // line 35
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["st"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 35, $this->source); })()), "status", [], "any", false, false, false, 35) == $context["st"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), $context["st"]), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['st'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        yield "                </select>
            </div>
            <div>
                <label for=\"trip-destination\">Destination</label>
                <select id=\"trip-destination\" name=\"destination\">
                    <option value=\"\">All destinations</option>
                    ";
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["destinations"]) || array_key_exists("destinations", $context) ? $context["destinations"] : (function () { throw new RuntimeError('Variable "destinations" does not exist.', 43, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["destination"]) {
            // line 44
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["destination"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 44, $this->source); })()), "destination", [], "any", false, false, false, 44) == $context["destination"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["destination"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['destination'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "                </select>
            </div>
            ";
        // line 48
        if ((($tmp =  !(isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 48, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 49
            yield "                <div>
                    <label for=\"trip-my\">My trips</label>
                    <select id=\"trip-my\" name=\"myTrips\">
                        <option value=\"\">All trips</option>
                        <option value=\"1\" ";
            // line 53
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 53, $this->source); })()), "myTrips", [], "any", false, false, false, 53) == "1")) {
                yield "selected";
            }
            yield ">Joined only</option>
                    </select>
                </div>
            ";
        }
        // line 57
        yield "            <div>
                <label for=\"trip-sort\">Sort</label>
                <select id=\"trip-sort\" name=\"sort\">
                    <option value=\"newest\" ";
        // line 60
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 60, $this->source); })()), "sort", [], "any", false, false, false, 60) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 61
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 61, $this->source); })()), "sort", [], "any", false, false, false, 61) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"name_asc\" ";
        // line 62
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 62, $this->source); })()), "sort", [], "any", false, false, false, 62) == "name_asc")) {
            yield "selected";
        }
        yield ">Name A-Z</option>
                    <option value=\"name_desc\" ";
        // line 63
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 63, $this->source); })()), "sort", [], "any", false, false, false, 63) == "name_desc")) {
            yield "selected";
        }
        yield ">Name Z-A</option>
                    <option value=\"date_asc\" ";
        // line 64
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 64, $this->source); })()), "sort", [], "any", false, false, false, 64) == "date_asc")) {
            yield "selected";
        }
        yield ">Start date ↑</option>
                    <option value=\"date_desc\" ";
        // line 65
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 65, $this->source); })()), "sort", [], "any", false, false, false, 65) == "date_desc")) {
            yield "selected";
        }
        yield ">Start date ↓</option>
                    <option value=\"budget_asc\" ";
        // line 66
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 66, $this->source); })()), "sort", [], "any", false, false, false, 66) == "budget_asc")) {
            yield "selected";
        }
        yield ">Budget ↑</option>
                    <option value=\"budget_desc\" ";
        // line 67
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 67, $this->source); })()), "sort", [], "any", false, false, false, 67) == "budget_desc")) {
            yield "selected";
        }
        yield ">Budget ↓</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 72
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 72, $this->source); })()) . "index"));
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\">
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Route</th>
                        <th>Date range</th>
                        <th>Status</th>
                        <th>Budget</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 92
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["trips"]) || array_key_exists("trips", $context) ? $context["trips"] : (function () { throw new RuntimeError('Variable "trips" does not exist.', 92, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["trip"]) {
            // line 93
            yield "                        ";
            $context["joined"] = CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 93), (isset($context["joinedTripIds"]) || array_key_exists("joinedTripIds", $context) ? $context["joinedTripIds"] : (function () { throw new RuntimeError('Variable "joinedTripIds" does not exist.', 93, $this->source); })()));
            // line 94
            yield "                        <tr>
                            <td>";
            // line 95
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "tripName", [], "any", false, false, false, 95), "html", null, true);
            yield "</td>
                            <td>";
            // line 96
            yield ((CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "origin", [], "any", false, false, false, 96)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "origin", [], "any", false, false, false, 96), "html", null, true)) : ("—"));
            yield " → ";
            yield ((CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "destination", [], "any", false, false, false, 96)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "destination", [], "any", false, false, false, 96), "html", null, true)) : ("—"));
            yield "</td>
                            <td>";
            // line 97
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "startDate", [], "any", false, false, false, 97)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "startDate", [], "any", false, false, false, 97), "Y-m-d"), "html", null, true)) : ("—"));
            yield " → ";
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "endDate", [], "any", false, false, false, 97)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "endDate", [], "any", false, false, false, 97), "Y-m-d"), "html", null, true)) : ("—"));
            yield "</td>
                            <td><span class=\"pill\">";
            // line 98
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "status", [], "any", false, false, false, 98)), "html", null, true);
            yield "</span></td>
                            <td>";
            // line 99
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "currency", [], "any", false, false, false, 99), "html", null, true);
            yield " ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber((((CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "budgetAmount", [], "any", true, true, false, 99) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "budgetAmount", [], "any", false, false, false, 99)))) ? (CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "budgetAmount", [], "any", false, false, false, 99)) : (0)), 2, ".", ","), "html", null, true);
            yield "</td>
                            <td>";
            // line 100
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "participants", [], "any", false, false, false, 100)), "html", null, true);
            yield "</td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"";
            // line 102
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 102, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 102)]), "html", null, true);
            yield "\">Show</a>
                                ";
            // line 103
            if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 103, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 104
                yield "                                    <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_trip_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 104)]), "html", null, true);
                yield "\">Edit</a>
                                ";
            } else {
                // line 106
                yield "                                    ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 106, $this->source); })()), "user", [], "any", false, false, false, 106)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 107
                    yield "                                        ";
                    if ((($tmp = (isset($context["joined"]) || array_key_exists("joined", $context) ? $context["joined"] : (function () { throw new RuntimeError('Variable "joined" does not exist.', 107, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 108
                        yield "                                            <form method=\"post\" class=\"inline-form\" action=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_leave", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 108)]), "html", null, true);
                        yield "\">
                                                <input type=\"hidden\" name=\"_token\" value=\"";
                        // line 109
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("leave_trip_" . CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 109))), "html", null, true);
                        yield "\">
                                                <button class=\"btn btn-sm\" type=\"submit\">Leave</button>
                                            </form>
                                        ";
                    } else {
                        // line 113
                        yield "                                            <form method=\"post\" class=\"inline-form\" action=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_join", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 113)]), "html", null, true);
                        yield "\">
                                                <input type=\"hidden\" name=\"_token\" value=\"";
                        // line 114
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("join_trip_" . CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 114))), "html", null, true);
                        yield "\">
                                                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Join</button>
                                            </form>
                                        ";
                    }
                    // line 118
                    yield "                                    ";
                }
                // line 119
                yield "                                ";
            }
            // line 120
            yield "                            </td>
                        </tr>
                    ";
            $context['_iterated'] = true;
        }
        // line 122
        if (!$context['_iterated']) {
            // line 123
            yield "                        <tr><td colspan=\"7\" class=\"empty-state\">No trips found.</td></tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['trip'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 125
        yield "                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        ";
        // line 131
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 131, $this->source); })()), "routeName" => ((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 131, $this->source); })()) . "index")]);
        yield "
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
        return "trip/index.html.twig";
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
        return array (  417 => 131,  409 => 125,  402 => 123,  400 => 122,  394 => 120,  391 => 119,  388 => 118,  381 => 114,  376 => 113,  369 => 109,  364 => 108,  361 => 107,  358 => 106,  352 => 104,  350 => 103,  346 => 102,  341 => 100,  335 => 99,  331 => 98,  325 => 97,  319 => 96,  315 => 95,  312 => 94,  309 => 93,  304 => 92,  281 => 72,  271 => 67,  265 => 66,  259 => 65,  253 => 64,  247 => 63,  241 => 62,  235 => 61,  229 => 60,  224 => 57,  215 => 53,  209 => 49,  207 => 48,  203 => 46,  188 => 44,  184 => 43,  176 => 37,  161 => 35,  157 => 34,  148 => 28,  142 => 25,  136 => 21,  130 => 19,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Trips' : 'Trips' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_trip_' : 'trip_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'trips'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Trip management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Full CRUD for trips, planning details, and participation.' : 'Discover trips, join or leave participation, and track your joined plans.' }}</p>
        </div>
        <div class=\"actions\">
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path('admin_trip_new') }}\">Create trip</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"trip-q\">Search</label>
                <input id=\"trip-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"trip, origin, destination\">
            </div>
            <div>
                <label for=\"trip-status\">Status</label>
                <select id=\"trip-status\" name=\"status\">
                    <option value=\"\">All statuses</option>
                    {% for st in ['PLANNED','ONGOING','COMPLETED','DONE','CANCELLED'] %}
                        <option value=\"{{ st }}\" {% if filters.status == st %}selected{% endif %}>{{ st|capitalize }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"trip-destination\">Destination</label>
                <select id=\"trip-destination\" name=\"destination\">
                    <option value=\"\">All destinations</option>
                    {% for destination in destinations %}
                        <option value=\"{{ destination }}\" {% if filters.destination == destination %}selected{% endif %}>{{ destination }}</option>
                    {% endfor %}
                </select>
            </div>
            {% if not isAdmin %}
                <div>
                    <label for=\"trip-my\">My trips</label>
                    <select id=\"trip-my\" name=\"myTrips\">
                        <option value=\"\">All trips</option>
                        <option value=\"1\" {% if filters.myTrips == '1' %}selected{% endif %}>Joined only</option>
                    </select>
                </div>
            {% endif %}
            <div>
                <label for=\"trip-sort\">Sort</label>
                <select id=\"trip-sort\" name=\"sort\">
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"name_asc\" {% if filters.sort == 'name_asc' %}selected{% endif %}>Name A-Z</option>
                    <option value=\"name_desc\" {% if filters.sort == 'name_desc' %}selected{% endif %}>Name Z-A</option>
                    <option value=\"date_asc\" {% if filters.sort == 'date_asc' %}selected{% endif %}>Start date ↑</option>
                    <option value=\"date_desc\" {% if filters.sort == 'date_desc' %}selected{% endif %}>Start date ↓</option>
                    <option value=\"budget_asc\" {% if filters.sort == 'budget_asc' %}selected{% endif %}>Budget ↑</option>
                    <option value=\"budget_desc\" {% if filters.sort == 'budget_desc' %}selected{% endif %}>Budget ↓</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'index') }}\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\">
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Route</th>
                        <th>Date range</th>
                        <th>Status</th>
                        <th>Budget</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for trip in trips %}
                        {% set joined = trip.id in joinedTripIds %}
                        <tr>
                            <td>{{ trip.tripName }}</td>
                            <td>{{ trip.origin ?: '—' }} → {{ trip.destination ?: '—' }}</td>
                            <td>{{ trip.startDate ? trip.startDate|date('Y-m-d') : '—' }} → {{ trip.endDate ? trip.endDate|date('Y-m-d') : '—' }}</td>
                            <td><span class=\"pill\">{{ trip.status|capitalize }}</span></td>
                            <td>{{ trip.currency }} {{ (trip.budgetAmount ?? 0)|number_format(2, '.', ',') }}</td>
                            <td>{{ trip.participants|length }}</td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': trip.id}) }}\">Show</a>
                                {% if isAdmin %}
                                    <a class=\"btn btn-sm\" href=\"{{ path('admin_trip_edit', {'id': trip.id}) }}\">Edit</a>
                                {% else %}
                                    {% if app.user %}
                                        {% if joined %}
                                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('trip_leave', {'id': trip.id}) }}\">
                                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('leave_trip_' ~ trip.id) }}\">
                                                <button class=\"btn btn-sm\" type=\"submit\">Leave</button>
                                            </form>
                                        {% else %}
                                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('trip_join', {'id': trip.id}) }}\">
                                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('join_trip_' ~ trip.id) }}\">
                                                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Join</button>
                                            </form>
                                        {% endif %}
                                    {% endif %}
                                {% endif %}
                            </td>
                        </tr>
                    {% else %}
                        <tr><td colspan=\"7\" class=\"empty-state\">No trips found.</td></tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "trip/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\trip\\index.html.twig");
    }
}
