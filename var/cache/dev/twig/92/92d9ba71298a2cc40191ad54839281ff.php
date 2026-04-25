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

/* activity/index.html.twig */
class __TwigTemplate_3f46534422c1276672b522553b89b50b extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "activity/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "activity/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Activities") : ("Activities"));
        
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
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "activities"]);
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
            <h1>Activity management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Full CRUD for activities, schedules, and trip linkage.") : ("Join activities linked to trips you participate in and manage your attendance."));
        yield "</p>
        </div>
        <div class=\"actions\">
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_activity_new");
            yield "\">Create activity</a>
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
                <label for=\"activity-q\">Search</label>
                <input id=\"activity-q\" type=\"search\" name=\"q\" value=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 28, $this->source); })()), "q", [], "any", false, false, false, 28), "html", null, true);
        yield "\" placeholder=\"title, type, location, trip\">
            </div>
            <div>
                <label for=\"activity-status\">Status</label>
                <select id=\"activity-status\" name=\"status\">
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
                <label for=\"activity-type\">Type</label>
                <select id=\"activity-type\" name=\"type\">
                    <option value=\"\">All types</option>
                    ";
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["types"]) || array_key_exists("types", $context) ? $context["types"] : (function () { throw new RuntimeError('Variable "types" does not exist.', 43, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["type"]) {
            // line 44
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["type"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 44, $this->source); })()), "type", [], "any", false, false, false, 44) == $context["type"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["type"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['type'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "                </select>
            </div>
            <div>
                <label for=\"activity-trip\">Trip</label>
                <select id=\"activity-trip\" name=\"tripId\">
                    <option value=\"\">All trips</option>
                    ";
        // line 52
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["trips"]) || array_key_exists("trips", $context) ? $context["trips"] : (function () { throw new RuntimeError('Variable "trips" does not exist.', 52, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["trip"]) {
            // line 53
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 53), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 53, $this->source); })()), "tripId", [], "any", false, false, false, 53) == CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "id", [], "any", false, false, false, 53))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["trip"], "tripName", [], "any", false, false, false, 53), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['trip'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 55
        yield "                </select>
            </div>
            ";
        // line 57
        if ((($tmp =  !(isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 57, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 58
            yield "                <div>
                    <label for=\"activity-my\">My activities</label>
                    <select id=\"activity-my\" name=\"myActivities\">
                        <option value=\"\">All activities</option>
                        <option value=\"1\" ";
            // line 62
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 62, $this->source); })()), "myActivities", [], "any", false, false, false, 62) == "1")) {
                yield "selected";
            }
            yield ">Joined only</option>
                    </select>
                </div>
            ";
        }
        // line 66
        yield "            <div>
                <label for=\"activity-sort\">Sort</label>
                <select id=\"activity-sort\" name=\"sort\">
                    <option value=\"newest\" ";
        // line 69
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 69, $this->source); })()), "sort", [], "any", false, false, false, 69) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 70
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 70, $this->source); })()), "sort", [], "any", false, false, false, 70) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"title_asc\" ";
        // line 71
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 71, $this->source); })()), "sort", [], "any", false, false, false, 71) == "title_asc")) {
            yield "selected";
        }
        yield ">Title A-Z</option>
                    <option value=\"title_desc\" ";
        // line 72
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 72, $this->source); })()), "sort", [], "any", false, false, false, 72) == "title_desc")) {
            yield "selected";
        }
        yield ">Title Z-A</option>
                    <option value=\"date_asc\" ";
        // line 73
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 73, $this->source); })()), "sort", [], "any", false, false, false, 73) == "date_asc")) {
            yield "selected";
        }
        yield ">Date ↑</option>
                    <option value=\"date_desc\" ";
        // line 74
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 74, $this->source); })()), "sort", [], "any", false, false, false, 74) == "date_desc")) {
            yield "selected";
        }
        yield ">Date ↓</option>
                    <option value=\"cost_asc\" ";
        // line 75
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 75, $this->source); })()), "sort", [], "any", false, false, false, 75) == "cost_asc")) {
            yield "selected";
        }
        yield ">Cost ↑</option>
                    <option value=\"cost_desc\" ";
        // line 76
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 76, $this->source); })()), "sort", [], "any", false, false, false, 76) == "cost_desc")) {
            yield "selected";
        }
        yield ">Cost ↓</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 81
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 81, $this->source); })()) . "index"));
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\">
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Trip</th>
                        <th>Date/Time</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 102
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["activities"]) || array_key_exists("activities", $context) ? $context["activities"] : (function () { throw new RuntimeError('Variable "activities" does not exist.', 102, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["activity"]) {
            // line 103
            yield "                        ";
            $context["joined"] = CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 103), (isset($context["joinedActivityIds"]) || array_key_exists("joinedActivityIds", $context) ? $context["joinedActivityIds"] : (function () { throw new RuntimeError('Variable "joinedActivityIds" does not exist.', 103, $this->source); })()));
            // line 104
            yield "                        <tr>
                            <td>";
            // line 105
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "title", [], "any", false, false, false, 105), "html", null, true);
            yield "</td>
                            <td>";
            // line 106
            yield ((CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "type", [], "any", false, false, false, 106)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "type", [], "any", false, false, false, 106), "html", null, true)) : ("—"));
            yield "</td>
                            <td>";
            // line 107
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "trip", [], "any", false, false, false, 107)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "trip", [], "any", false, false, false, 107), "tripName", [], "any", false, false, false, 107), "html", null, true)) : ("—"));
            yield "</td>
                            <td>
                                ";
            // line 109
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "activityDate", [], "any", false, false, false, 109)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "activityDate", [], "any", false, false, false, 109), "Y-m-d"), "html", null, true)) : ("—"));
            yield "
                                ";
            // line 110
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "startTime", [], "any", false, false, false, 110)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield " ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "startTime", [], "any", false, false, false, 110), "H:i"), "html", null, true);
            }
            // line 111
            yield "                                ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "endTime", [], "any", false, false, false, 111)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield " - ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "endTime", [], "any", false, false, false, 111), "H:i"), "html", null, true);
            }
            // line 112
            yield "                            </td>
                            <td>";
            // line 113
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "currency", [], "any", false, false, false, 113), "html", null, true);
            yield " ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber((((CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "costAmount", [], "any", true, true, false, 113) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "costAmount", [], "any", false, false, false, 113)))) ? (CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "costAmount", [], "any", false, false, false, 113)) : (0)), 2, ".", ","), "html", null, true);
            yield "</td>
                            <td><span class=\"pill\">";
            // line 114
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "status", [], "any", false, false, false, 114)), "html", null, true);
            yield "</span></td>
                            <td>";
            // line 115
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "participants", [], "any", false, false, false, 115)), "html", null, true);
            yield "</td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"";
            // line 117
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 117, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 117)]), "html", null, true);
            yield "\">Show</a>
                                ";
            // line 118
            if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 118, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 119
                yield "                                    <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_activity_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 119)]), "html", null, true);
                yield "\">Edit</a>
                                ";
            } else {
                // line 121
                yield "                                    ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 121, $this->source); })()), "user", [], "any", false, false, false, 121)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 122
                    yield "                                        ";
                    if ((($tmp = (isset($context["joined"]) || array_key_exists("joined", $context) ? $context["joined"] : (function () { throw new RuntimeError('Variable "joined" does not exist.', 122, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 123
                        yield "                                            <form method=\"post\" class=\"inline-form\" action=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_leave", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 123)]), "html", null, true);
                        yield "\">
                                                <input type=\"hidden\" name=\"_token\" value=\"";
                        // line 124
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("leave_activity_" . CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 124))), "html", null, true);
                        yield "\">
                                                <button class=\"btn btn-sm\" type=\"submit\">Leave</button>
                                            </form>
                                        ";
                    } else {
                        // line 128
                        yield "                                            <form method=\"post\" class=\"inline-form\" action=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_join", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 128)]), "html", null, true);
                        yield "\">
                                                <input type=\"hidden\" name=\"_token\" value=\"";
                        // line 129
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("join_activity_" . CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "id", [], "any", false, false, false, 129))), "html", null, true);
                        yield "\">
                                                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Join</button>
                                            </form>
                                        ";
                    }
                    // line 133
                    yield "                                    ";
                }
                // line 134
                yield "                                ";
            }
            // line 135
            yield "                            </td>
                        </tr>
                    ";
            $context['_iterated'] = true;
        }
        // line 137
        if (!$context['_iterated']) {
            // line 138
            yield "                        <tr><td colspan=\"8\" class=\"empty-state\">No activities found.</td></tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['activity'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 140
        yield "                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        ";
        // line 146
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 146, $this->source); })()), "routeName" => ((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 146, $this->source); })()) . "index")]);
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
        return "activity/index.html.twig";
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
        return array (  460 => 146,  452 => 140,  445 => 138,  443 => 137,  437 => 135,  434 => 134,  431 => 133,  424 => 129,  419 => 128,  412 => 124,  407 => 123,  404 => 122,  401 => 121,  395 => 119,  393 => 118,  389 => 117,  384 => 115,  380 => 114,  374 => 113,  371 => 112,  365 => 111,  360 => 110,  356 => 109,  351 => 107,  347 => 106,  343 => 105,  340 => 104,  337 => 103,  332 => 102,  308 => 81,  298 => 76,  292 => 75,  286 => 74,  280 => 73,  274 => 72,  268 => 71,  262 => 70,  256 => 69,  251 => 66,  242 => 62,  236 => 58,  234 => 57,  230 => 55,  215 => 53,  211 => 52,  203 => 46,  188 => 44,  184 => 43,  176 => 37,  161 => 35,  157 => 34,  148 => 28,  142 => 25,  136 => 21,  130 => 19,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Activities' : 'Activities' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_activity_' : 'activity_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'activities'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Activity management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Full CRUD for activities, schedules, and trip linkage.' : 'Join activities linked to trips you participate in and manage your attendance.' }}</p>
        </div>
        <div class=\"actions\">
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path('admin_activity_new') }}\">Create activity</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"activity-q\">Search</label>
                <input id=\"activity-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"title, type, location, trip\">
            </div>
            <div>
                <label for=\"activity-status\">Status</label>
                <select id=\"activity-status\" name=\"status\">
                    <option value=\"\">All statuses</option>
                    {% for st in ['PLANNED','ONGOING','COMPLETED','DONE','CANCELLED'] %}
                        <option value=\"{{ st }}\" {% if filters.status == st %}selected{% endif %}>{{ st|capitalize }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"activity-type\">Type</label>
                <select id=\"activity-type\" name=\"type\">
                    <option value=\"\">All types</option>
                    {% for type in types %}
                        <option value=\"{{ type }}\" {% if filters.type == type %}selected{% endif %}>{{ type }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"activity-trip\">Trip</label>
                <select id=\"activity-trip\" name=\"tripId\">
                    <option value=\"\">All trips</option>
                    {% for trip in trips %}
                        <option value=\"{{ trip.id }}\" {% if filters.tripId == trip.id %}selected{% endif %}>{{ trip.tripName }}</option>
                    {% endfor %}
                </select>
            </div>
            {% if not isAdmin %}
                <div>
                    <label for=\"activity-my\">My activities</label>
                    <select id=\"activity-my\" name=\"myActivities\">
                        <option value=\"\">All activities</option>
                        <option value=\"1\" {% if filters.myActivities == '1' %}selected{% endif %}>Joined only</option>
                    </select>
                </div>
            {% endif %}
            <div>
                <label for=\"activity-sort\">Sort</label>
                <select id=\"activity-sort\" name=\"sort\">
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"title_asc\" {% if filters.sort == 'title_asc' %}selected{% endif %}>Title A-Z</option>
                    <option value=\"title_desc\" {% if filters.sort == 'title_desc' %}selected{% endif %}>Title Z-A</option>
                    <option value=\"date_asc\" {% if filters.sort == 'date_asc' %}selected{% endif %}>Date ↑</option>
                    <option value=\"date_desc\" {% if filters.sort == 'date_desc' %}selected{% endif %}>Date ↓</option>
                    <option value=\"cost_asc\" {% if filters.sort == 'cost_asc' %}selected{% endif %}>Cost ↑</option>
                    <option value=\"cost_desc\" {% if filters.sort == 'cost_desc' %}selected{% endif %}>Cost ↓</option>
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
                        <th>Title</th>
                        <th>Type</th>
                        <th>Trip</th>
                        <th>Date/Time</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for activity in activities %}
                        {% set joined = activity.id in joinedActivityIds %}
                        <tr>
                            <td>{{ activity.title }}</td>
                            <td>{{ activity.type ?: '—' }}</td>
                            <td>{{ activity.trip ? activity.trip.tripName : '—' }}</td>
                            <td>
                                {{ activity.activityDate ? activity.activityDate|date('Y-m-d') : '—' }}
                                {% if activity.startTime %} {{ activity.startTime|date('H:i') }}{% endif %}
                                {% if activity.endTime %} - {{ activity.endTime|date('H:i') }}{% endif %}
                            </td>
                            <td>{{ activity.currency }} {{ (activity.costAmount ?? 0)|number_format(2, '.', ',') }}</td>
                            <td><span class=\"pill\">{{ activity.status|capitalize }}</span></td>
                            <td>{{ activity.participants|length }}</td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': activity.id}) }}\">Show</a>
                                {% if isAdmin %}
                                    <a class=\"btn btn-sm\" href=\"{{ path('admin_activity_edit', {'id': activity.id}) }}\">Edit</a>
                                {% else %}
                                    {% if app.user %}
                                        {% if joined %}
                                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('activity_leave', {'id': activity.id}) }}\">
                                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('leave_activity_' ~ activity.id) }}\">
                                                <button class=\"btn btn-sm\" type=\"submit\">Leave</button>
                                            </form>
                                        {% else %}
                                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('activity_join', {'id': activity.id}) }}\">
                                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('join_activity_' ~ activity.id) }}\">
                                                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Join</button>
                                            </form>
                                        {% endif %}
                                    {% endif %}
                                {% endif %}
                            </td>
                        </tr>
                    {% else %}
                        <tr><td colspan=\"8\" class=\"empty-state\">No activities found.</td></tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "activity/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\activity\\index.html.twig");
    }
}
