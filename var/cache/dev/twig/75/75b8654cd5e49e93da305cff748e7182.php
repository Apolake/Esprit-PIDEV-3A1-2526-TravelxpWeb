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

/* booking/index.html.twig */
class __TwigTemplate_467bb3740210ddc13e025770a4e33046 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "booking/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "booking/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Bookings") : ("Bookings"));
        
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
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_booking_") : ("booking_"));
        // line 7
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "bookings"]);
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
            <h1>Booking management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Full CRUD over reservations, pricing, and booking lifecycle.") : ("Create bookings with automatic pricing and track your own reservations."));
        yield "</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn btn-primary\" href=\"";
        // line 18
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 18, $this->source); })()) . "new"));
        yield "\">Create booking</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 23
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 23, $this->source); })()) . "index"));
        yield "\">
            <div>
                <label for=\"booking-q\">Search</label>
                <input id=\"booking-q\" type=\"search\" name=\"q\" value=\"";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 26, $this->source); })()), "q", [], "any", false, false, false, 26), "html", null, true);
        yield "\" placeholder=\"property, user id or status\">
            </div>
            <div>
                <label for=\"booking-status\">Status</label>
                <select id=\"booking-status\" name=\"status\">
                    <option value=\"\">All statuses</option>
                    <option value=\"pending\" ";
        // line 32
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 32, $this->source); })()), "status", [], "any", false, false, false, 32) == "pending")) {
            yield "selected";
        }
        yield ">Pending</option>
                    <option value=\"confirmed\" ";
        // line 33
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 33, $this->source); })()), "status", [], "any", false, false, false, 33) == "confirmed")) {
            yield "selected";
        }
        yield ">Confirmed</option>
                    <option value=\"cancelled\" ";
        // line 34
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 34, $this->source); })()), "status", [], "any", false, false, false, 34) == "cancelled")) {
            yield "selected";
        }
        yield ">Cancelled</option>
                </select>
            </div>
            <div>
                <label for=\"booking-property\">Property</label>
                <select id=\"booking-property\" name=\"propertyId\">
                    <option value=\"\">All properties</option>
                    ";
        // line 41
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 41, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
            // line 42
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 42), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 42, $this->source); })()), "propertyId", [], "any", false, false, false, 42) == CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 42))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "title", [], "any", false, false, false, 42), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['property'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 44
        yield "                </select>
            </div>
            <div>
                <label for=\"booking-from-date\">From date</label>
                <input id=\"booking-from-date\" type=\"date\" name=\"fromDate\" value=\"";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 48, $this->source); })()), "fromDate", [], "any", false, false, false, 48), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"booking-to-date\">To date</label>
                <input id=\"booking-to-date\" type=\"date\" name=\"toDate\" value=\"";
        // line 52
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 52, $this->source); })()), "toDate", [], "any", false, false, false, 52), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"booking-min-total\">Min total</label>
                <input id=\"booking-min-total\" type=\"number\" min=\"0\" step=\"0.01\" name=\"minTotal\" value=\"";
        // line 56
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 56, $this->source); })()), "minTotal", [], "any", false, false, false, 56), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"booking-max-total\">Max total</label>
                <input id=\"booking-max-total\" type=\"number\" min=\"0\" step=\"0.01\" name=\"maxTotal\" value=\"";
        // line 60
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 60, $this->source); })()), "maxTotal", [], "any", false, false, false, 60), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"booking-future-only\">Future only</label>
                <select id=\"booking-future-only\" name=\"futureOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" ";
        // line 66
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 66, $this->source); })()), "futureOnly", [], "any", false, false, false, 66) == "1")) {
            yield "selected";
        }
        yield ">Yes</option>
                </select>
            </div>
            <div>
                <label for=\"booking-cancelled-only\">Cancelled only</label>
                <select id=\"booking-cancelled-only\" name=\"cancelledOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" ";
        // line 73
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 73, $this->source); })()), "cancelledOnly", [], "any", false, false, false, 73) == "1")) {
            yield "selected";
        }
        yield ">Yes</option>
                </select>
            </div>
            <div>
                <label for=\"booking-sort\">Sort</label>
                <select id=\"booking-sort\" name=\"sort\">
                    <option value=\"newest\" ";
        // line 79
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 79, $this->source); })()), "sort", [], "any", false, false, false, 79) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 80
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 80, $this->source); })()), "sort", [], "any", false, false, false, 80) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"date_asc\" ";
        // line 81
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 81, $this->source); })()), "sort", [], "any", false, false, false, 81) == "date_asc")) {
            yield "selected";
        }
        yield ">Date ↑</option>
                    <option value=\"date_desc\" ";
        // line 82
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 82, $this->source); })()), "sort", [], "any", false, false, false, 82) == "date_desc")) {
            yield "selected";
        }
        yield ">Date ↓</option>
                    <option value=\"total_asc\" ";
        // line 83
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 83, $this->source); })()), "sort", [], "any", false, false, false, 83) == "total_asc")) {
            yield "selected";
        }
        yield ">Total ↑</option>
                    <option value=\"total_desc\" ";
        // line 84
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 84, $this->source); })()), "sort", [], "any", false, false, false, 84) == "total_desc")) {
            yield "selected";
        }
        yield ">Total ↓</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 89
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 89, $this->source); })()) . "index"));
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\">
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>User ID</th>
                        <th>Booking date</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 110
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["bookings"]) || array_key_exists("bookings", $context) ? $context["bookings"] : (function () { throw new RuntimeError('Variable "bookings" does not exist.', 110, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["booking"]) {
            // line 111
            yield "                        <tr>
                            <td>#";
            // line 112
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "id", [], "any", false, false, false, 112), "html", null, true);
            yield "</td>
                            <td>";
            // line 113
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "property", [], "any", false, false, false, 113)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "property", [], "any", false, false, false, 113), "title", [], "any", false, false, false, 113), "html", null, true)) : ("—"));
            yield "</td>
                            <td>";
            // line 114
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "userId", [], "any", false, false, false, 114), "html", null, true);
            yield "</td>
                            <td>";
            // line 115
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "bookingDate", [], "any", false, false, false, 115)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "bookingDate", [], "any", false, false, false, 115), "Y-m-d"), "html", null, true)) : ("—"));
            yield "</td>
                            <td>";
            // line 116
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "duration", [], "any", false, false, false, 116), "html", null, true);
            yield " day(s)</td>
                            <td>\$";
            // line 117
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "totalPrice", [], "any", false, false, false, 117), 2, ".", ","), "html", null, true);
            yield "</td>
                            <td><span class=\"pill\">";
            // line 118
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "status", [], "any", false, false, false, 118)), "html", null, true);
            yield "</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"";
            // line 120
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 120, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "id", [], "any", false, false, false, 120)]), "html", null, true);
            yield "\">Show</a>
                                ";
            // line 121
            if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 121, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 122
                yield "                                    <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 122, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["booking"], "id", [], "any", false, false, false, 122)]), "html", null, true);
                yield "\">Edit</a>
                                ";
            }
            // line 124
            yield "                            </td>
                        </tr>
                    ";
            $context['_iterated'] = true;
        }
        // line 126
        if (!$context['_iterated']) {
            // line 127
            yield "                        <tr><td colspan=\"8\" class=\"empty-state\">No bookings found.</td></tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['booking'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 129
        yield "                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        ";
        // line 135
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 135, $this->source); })()), "routeName" => ((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 135, $this->source); })()) . "index")]);
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
        return "booking/index.html.twig";
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
        return array (  389 => 135,  381 => 129,  374 => 127,  372 => 126,  366 => 124,  360 => 122,  358 => 121,  354 => 120,  349 => 118,  345 => 117,  341 => 116,  337 => 115,  333 => 114,  329 => 113,  325 => 112,  322 => 111,  317 => 110,  293 => 89,  283 => 84,  277 => 83,  271 => 82,  265 => 81,  259 => 80,  253 => 79,  242 => 73,  230 => 66,  221 => 60,  214 => 56,  207 => 52,  200 => 48,  194 => 44,  179 => 42,  175 => 41,  163 => 34,  157 => 33,  151 => 32,  142 => 26,  136 => 23,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Bookings' : 'Bookings' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_booking_' : 'booking_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'bookings'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Booking management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Full CRUD over reservations, pricing, and booking lifecycle.' : 'Create bookings with automatic pricing and track your own reservations.' }}</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'new') }}\">Create booking</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"booking-q\">Search</label>
                <input id=\"booking-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"property, user id or status\">
            </div>
            <div>
                <label for=\"booking-status\">Status</label>
                <select id=\"booking-status\" name=\"status\">
                    <option value=\"\">All statuses</option>
                    <option value=\"pending\" {% if filters.status == 'pending' %}selected{% endif %}>Pending</option>
                    <option value=\"confirmed\" {% if filters.status == 'confirmed' %}selected{% endif %}>Confirmed</option>
                    <option value=\"cancelled\" {% if filters.status == 'cancelled' %}selected{% endif %}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for=\"booking-property\">Property</label>
                <select id=\"booking-property\" name=\"propertyId\">
                    <option value=\"\">All properties</option>
                    {% for property in properties %}
                        <option value=\"{{ property.id }}\" {% if filters.propertyId == property.id %}selected{% endif %}>{{ property.title }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"booking-from-date\">From date</label>
                <input id=\"booking-from-date\" type=\"date\" name=\"fromDate\" value=\"{{ filters.fromDate }}\">
            </div>
            <div>
                <label for=\"booking-to-date\">To date</label>
                <input id=\"booking-to-date\" type=\"date\" name=\"toDate\" value=\"{{ filters.toDate }}\">
            </div>
            <div>
                <label for=\"booking-min-total\">Min total</label>
                <input id=\"booking-min-total\" type=\"number\" min=\"0\" step=\"0.01\" name=\"minTotal\" value=\"{{ filters.minTotal }}\">
            </div>
            <div>
                <label for=\"booking-max-total\">Max total</label>
                <input id=\"booking-max-total\" type=\"number\" min=\"0\" step=\"0.01\" name=\"maxTotal\" value=\"{{ filters.maxTotal }}\">
            </div>
            <div>
                <label for=\"booking-future-only\">Future only</label>
                <select id=\"booking-future-only\" name=\"futureOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" {% if filters.futureOnly == '1' %}selected{% endif %}>Yes</option>
                </select>
            </div>
            <div>
                <label for=\"booking-cancelled-only\">Cancelled only</label>
                <select id=\"booking-cancelled-only\" name=\"cancelledOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" {% if filters.cancelledOnly == '1' %}selected{% endif %}>Yes</option>
                </select>
            </div>
            <div>
                <label for=\"booking-sort\">Sort</label>
                <select id=\"booking-sort\" name=\"sort\">
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"date_asc\" {% if filters.sort == 'date_asc' %}selected{% endif %}>Date ↑</option>
                    <option value=\"date_desc\" {% if filters.sort == 'date_desc' %}selected{% endif %}>Date ↓</option>
                    <option value=\"total_asc\" {% if filters.sort == 'total_asc' %}selected{% endif %}>Total ↑</option>
                    <option value=\"total_desc\" {% if filters.sort == 'total_desc' %}selected{% endif %}>Total ↓</option>
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
                        <th>ID</th>
                        <th>Property</th>
                        <th>User ID</th>
                        <th>Booking date</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for booking in bookings %}
                        <tr>
                            <td>#{{ booking.id }}</td>
                            <td>{{ booking.property ? booking.property.title : '—' }}</td>
                            <td>{{ booking.userId }}</td>
                            <td>{{ booking.bookingDate ? booking.bookingDate|date('Y-m-d') : '—' }}</td>
                            <td>{{ booking.duration }} day(s)</td>
                            <td>\${{ booking.totalPrice|number_format(2, '.', ',') }}</td>
                            <td><span class=\"pill\">{{ booking.status|capitalize }}</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': booking.id}) }}\">Show</a>
                                {% if isAdmin %}
                                    <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'edit', {'id': booking.id}) }}\">Edit</a>
                                {% endif %}
                            </td>
                        </tr>
                    {% else %}
                        <tr><td colspan=\"8\" class=\"empty-state\">No bookings found.</td></tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "booking/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\booking\\index.html.twig");
    }
}
