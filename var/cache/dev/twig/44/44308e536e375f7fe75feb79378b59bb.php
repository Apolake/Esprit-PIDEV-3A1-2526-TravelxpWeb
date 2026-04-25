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

/* offer/index.html.twig */
class __TwigTemplate_7297fb0485d4481f6abc3bd3feec8671 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "offer/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "offer/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Offers") : ("Offers"));
        
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
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "offers"]);
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
            <h1>Offer management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Create and manage discount campaigns with validation rules.") : ("Explore active discounts linked to properties. Offer CRUD is admin only."));
        yield "</p>
        </div>
        <div class=\"actions\">
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 19, $this->source); })()) . "new"));
            yield "\">Create offer</a>
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
                <label for=\"offer-q\">Search</label>
                <input id=\"offer-q\" type=\"search\" name=\"q\" value=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 28, $this->source); })()), "q", [], "any", false, false, false, 28), "html", null, true);
        yield "\" placeholder=\"title or description\">
            </div>
            <div>
                <label for=\"offer-property\">Property</label>
                <select id=\"offer-property\" name=\"propertyId\">
                    <option value=\"\">All properties</option>
                    ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 34, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
            // line 35
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 35), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 35, $this->source); })()), "propertyId", [], "any", false, false, false, 35) == CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 35))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "title", [], "any", false, false, false, 35), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['property'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        yield "                </select>
            </div>
            <div>
                <label for=\"offer-active\">Status</label>
                <select id=\"offer-active\" name=\"active\">
                    <option value=\"\">All</option>
                    <option value=\"1\" ";
        // line 43
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 43, $this->source); })()), "active", [], "any", false, false, false, 43) == "1")) {
            yield "selected";
        }
        yield ">Active</option>
                    <option value=\"0\" ";
        // line 44
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 44, $this->source); })()), "active", [], "any", false, false, false, 44) == "0")) {
            yield "selected";
        }
        yield ">Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"offer-min\">Min discount</label>
                <input id=\"offer-min\" type=\"number\" min=\"1\" max=\"100\" step=\"0.01\" name=\"minDiscount\" value=\"";
        // line 49
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 49, $this->source); })()), "minDiscount", [], "any", false, false, false, 49), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"offer-max\">Max discount</label>
                <input id=\"offer-max\" type=\"number\" min=\"1\" max=\"100\" step=\"0.01\" name=\"maxDiscount\" value=\"";
        // line 53
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 53, $this->source); })()), "maxDiscount", [], "any", false, false, false, 53), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"offer-valid-now\">Currently valid</label>
                <select id=\"offer-valid-now\" name=\"validNow\">
                    <option value=\"\">Any</option>
                    <option value=\"1\" ";
        // line 59
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 59, $this->source); })()), "validNow", [], "any", false, false, false, 59) == "1")) {
            yield "selected";
        }
        yield ">Yes</option>
                </select>
            </div>
            <div>
                <label for=\"offer-sort\">Sort</label>
                <select id=\"offer-sort\" name=\"sort\">
                    <option value=\"highest_discount\" ";
        // line 65
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 65, $this->source); })()), "sort", [], "any", false, false, false, 65) == "highest_discount")) {
            yield "selected";
        }
        yield ">Highest discount</option>
                    <option value=\"lowest_discount\" ";
        // line 66
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 66, $this->source); })()), "sort", [], "any", false, false, false, 66) == "lowest_discount")) {
            yield "selected";
        }
        yield ">Lowest discount</option>
                    <option value=\"newest\" ";
        // line 67
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 67, $this->source); })()), "sort", [], "any", false, false, false, 67) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 68
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 68, $this->source); })()), "sort", [], "any", false, false, false, 68) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"ending_soon\" ";
        // line 69
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 69, $this->source); })()), "sort", [], "any", false, false, false, 69) == "ending_soon")) {
            yield "selected";
        }
        yield ">Ending soon</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 74
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 74, $this->source); })()) . "index"));
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
                        <th>Property</th>
                        <th>Discount</th>
                        <th>Date range</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 93
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["offers"]) || array_key_exists("offers", $context) ? $context["offers"] : (function () { throw new RuntimeError('Variable "offers" does not exist.', 93, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["offer"]) {
            // line 94
            yield "                        <tr>
                            <td>";
            // line 95
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "title", [], "any", false, false, false, 95), "html", null, true);
            yield "</td>
                            <td>";
            // line 96
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "property", [], "any", false, false, false, 96)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "property", [], "any", false, false, false, 96), "title", [], "any", false, false, false, 96), "html", null, true)) : ("—"));
            yield "</td>
                            <td>";
            // line 97
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "discountPercentage", [], "any", false, false, false, 97), 2, ".", ","), "html", null, true);
            yield "%</td>
                            <td>";
            // line 98
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "startDate", [], "any", false, false, false, 98)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "startDate", [], "any", false, false, false, 98), "Y-m-d"), "html", null, true)) : ("—"));
            yield " → ";
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "endDate", [], "any", false, false, false, 98)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "endDate", [], "any", false, false, false, 98), "Y-m-d"), "html", null, true)) : ("—"));
            yield "</td>
                            <td><span class=\"pill\">";
            // line 99
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "isActive", [], "any", false, false, false, 99)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Active") : ("Inactive"));
            yield "</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"";
            // line 101
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 101, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "id", [], "any", false, false, false, 101)]), "html", null, true);
            yield "\">Show</a>
                                ";
            // line 102
            if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 102, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 103
                yield "                                    <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 103, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["offer"], "id", [], "any", false, false, false, 103)]), "html", null, true);
                yield "\">Edit</a>
                                ";
            }
            // line 105
            yield "                            </td>
                        </tr>
                    ";
            $context['_iterated'] = true;
        }
        // line 107
        if (!$context['_iterated']) {
            // line 108
            yield "                        <tr><td colspan=\"6\" class=\"empty-state\">No offers found.</td></tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['offer'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 110
        yield "                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        ";
        // line 116
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 116, $this->source); })()), "routeName" => ((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 116, $this->source); })()) . "index")]);
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
        return "offer/index.html.twig";
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
        return array (  349 => 116,  341 => 110,  334 => 108,  332 => 107,  326 => 105,  320 => 103,  318 => 102,  314 => 101,  309 => 99,  303 => 98,  299 => 97,  295 => 96,  291 => 95,  288 => 94,  283 => 93,  261 => 74,  251 => 69,  245 => 68,  239 => 67,  233 => 66,  227 => 65,  216 => 59,  207 => 53,  200 => 49,  190 => 44,  184 => 43,  176 => 37,  161 => 35,  157 => 34,  148 => 28,  142 => 25,  136 => 21,  130 => 19,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Offers' : 'Offers' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_offer_' : 'offer_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'offers'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Offer management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Create and manage discount campaigns with validation rules.' : 'Explore active discounts linked to properties. Offer CRUD is admin only.' }}</p>
        </div>
        <div class=\"actions\">
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'new') }}\">Create offer</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"offer-q\">Search</label>
                <input id=\"offer-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"title or description\">
            </div>
            <div>
                <label for=\"offer-property\">Property</label>
                <select id=\"offer-property\" name=\"propertyId\">
                    <option value=\"\">All properties</option>
                    {% for property in properties %}
                        <option value=\"{{ property.id }}\" {% if filters.propertyId == property.id %}selected{% endif %}>{{ property.title }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"offer-active\">Status</label>
                <select id=\"offer-active\" name=\"active\">
                    <option value=\"\">All</option>
                    <option value=\"1\" {% if filters.active == '1' %}selected{% endif %}>Active</option>
                    <option value=\"0\" {% if filters.active == '0' %}selected{% endif %}>Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"offer-min\">Min discount</label>
                <input id=\"offer-min\" type=\"number\" min=\"1\" max=\"100\" step=\"0.01\" name=\"minDiscount\" value=\"{{ filters.minDiscount }}\">
            </div>
            <div>
                <label for=\"offer-max\">Max discount</label>
                <input id=\"offer-max\" type=\"number\" min=\"1\" max=\"100\" step=\"0.01\" name=\"maxDiscount\" value=\"{{ filters.maxDiscount }}\">
            </div>
            <div>
                <label for=\"offer-valid-now\">Currently valid</label>
                <select id=\"offer-valid-now\" name=\"validNow\">
                    <option value=\"\">Any</option>
                    <option value=\"1\" {% if filters.validNow == '1' %}selected{% endif %}>Yes</option>
                </select>
            </div>
            <div>
                <label for=\"offer-sort\">Sort</label>
                <select id=\"offer-sort\" name=\"sort\">
                    <option value=\"highest_discount\" {% if filters.sort == 'highest_discount' %}selected{% endif %}>Highest discount</option>
                    <option value=\"lowest_discount\" {% if filters.sort == 'lowest_discount' %}selected{% endif %}>Lowest discount</option>
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"ending_soon\" {% if filters.sort == 'ending_soon' %}selected{% endif %}>Ending soon</option>
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
                        <th>Property</th>
                        <th>Discount</th>
                        <th>Date range</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for offer in offers %}
                        <tr>
                            <td>{{ offer.title }}</td>
                            <td>{{ offer.property ? offer.property.title : '—' }}</td>
                            <td>{{ offer.discountPercentage|number_format(2, '.', ',') }}%</td>
                            <td>{{ offer.startDate ? offer.startDate|date('Y-m-d') : '—' }} → {{ offer.endDate ? offer.endDate|date('Y-m-d') : '—' }}</td>
                            <td><span class=\"pill\">{{ offer.isActive ? 'Active' : 'Inactive' }}</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': offer.id}) }}\">Show</a>
                                {% if isAdmin %}
                                    <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'edit', {'id': offer.id}) }}\">Edit</a>
                                {% endif %}
                            </td>
                        </tr>
                    {% else %}
                        <tr><td colspan=\"6\" class=\"empty-state\">No offers found.</td></tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "offer/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\offer\\index.html.twig");
    }
}
