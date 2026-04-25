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

/* service/index.html.twig */
class __TwigTemplate_4b926fa1b40a32e7ceb799a55a5e583b extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "service/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "service/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Services") : ("Services"));
        
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
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "services"]);
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
            <h1>Service management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Full CRUD for add-on services, pricing, and availability.") : ("Browse add-on services available for your travel experience. Service CRUD is admin only."));
        yield "</p>
        </div>
        <div class=\"actions\">
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 19, $this->source); })()) . "new"));
            yield "\">Create service</a>
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
                <label for=\"service-q\">Search</label>
                <input id=\"service-q\" type=\"search\" name=\"q\" value=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 28, $this->source); })()), "q", [], "any", false, false, false, 28), "html", null, true);
        yield "\" placeholder=\"provider, type or description\">
            </div>
            <div>
                <label for=\"service-type\">Type</label>
                <select id=\"service-type\" name=\"serviceType\">
                    <option value=\"\">All types</option>
                    ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["serviceTypes"]) || array_key_exists("serviceTypes", $context) ? $context["serviceTypes"] : (function () { throw new RuntimeError('Variable "serviceTypes" does not exist.', 34, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["serviceType"]) {
            // line 35
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["serviceType"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 35, $this->source); })()), "serviceType", [], "any", false, false, false, 35) == $context["serviceType"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["serviceType"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['serviceType'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        yield "                </select>
            </div>
            <div>
                <label for=\"service-available\">Available only</label>
                <select id=\"service-available\" name=\"availableOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" ";
        // line 43
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 43, $this->source); })()), "availableOnly", [], "any", false, false, false, 43) == "1")) {
            yield "selected";
        }
        yield ">Yes</option>
                </select>
            </div>
            <div>
                <label for=\"service-eco\">Eco only</label>
                <select id=\"service-eco\" name=\"ecoOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" ";
        // line 50
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 50, $this->source); })()), "ecoOnly", [], "any", false, false, false, 50) == "1")) {
            yield "selected";
        }
        yield ">Yes</option>
                </select>
            </div>
            <div>
                <label for=\"service-min-price\">Min price</label>
                <input id=\"service-min-price\" type=\"number\" min=\"0\" step=\"0.01\" name=\"minPrice\" value=\"";
        // line 55
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 55, $this->source); })()), "minPrice", [], "any", false, false, false, 55), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"service-max-price\">Max price</label>
                <input id=\"service-max-price\" type=\"number\" min=\"0\" step=\"0.01\" name=\"maxPrice\" value=\"";
        // line 59
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 59, $this->source); })()), "maxPrice", [], "any", false, false, false, 59), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"service-sort\">Sort</label>
                <select id=\"service-sort\" name=\"sort\">
                    <option value=\"newest\" ";
        // line 64
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 64, $this->source); })()), "sort", [], "any", false, false, false, 64) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 65
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 65, $this->source); })()), "sort", [], "any", false, false, false, 65) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"price_asc\" ";
        // line 66
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 66, $this->source); })()), "sort", [], "any", false, false, false, 66) == "price_asc")) {
            yield "selected";
        }
        yield ">Price ↑</option>
                    <option value=\"price_desc\" ";
        // line 67
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 67, $this->source); })()), "sort", [], "any", false, false, false, 67) == "price_desc")) {
            yield "selected";
        }
        yield ">Price ↓</option>
                    <option value=\"provider_asc\" ";
        // line 68
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 68, $this->source); })()), "sort", [], "any", false, false, false, 68) == "provider_asc")) {
            yield "selected";
        }
        yield ">Provider A-Z</option>
                    <option value=\"provider_desc\" ";
        // line 69
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 69, $this->source); })()), "sort", [], "any", false, false, false, 69) == "provider_desc")) {
            yield "selected";
        }
        yield ">Provider Z-A</option>
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
                        <th>Provider</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Eco</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ";
        // line 93
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["services"]) || array_key_exists("services", $context) ? $context["services"] : (function () { throw new RuntimeError('Variable "services" does not exist.', 93, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["service"]) {
            // line 94
            yield "                        <tr>
                            <td>";
            // line 95
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["service"], "providerName", [], "any", false, false, false, 95), "html", null, true);
            yield "</td>
                            <td>";
            // line 96
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["service"], "serviceType", [], "any", false, false, false, 96), "html", null, true);
            yield "</td>
                            <td>\$";
            // line 97
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["service"], "price", [], "any", false, false, false, 97), 2, ".", ","), "html", null, true);
            yield "</td>
                            <td><span class=\"pill\">";
            // line 98
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["service"], "isAvailable", [], "any", false, false, false, 98)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Available") : ("Unavailable"));
            yield "</span></td>
                            <td><span class=\"pill\">";
            // line 99
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["service"], "ecoFriendly", [], "any", false, false, false, 99)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Yes") : ("No"));
            yield "</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"";
            // line 101
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 101, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["service"], "id", [], "any", false, false, false, 101)]), "html", null, true);
            yield "\">Show</a>
                                ";
            // line 102
            if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 102, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 103
                yield "                                    <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 103, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["service"], "id", [], "any", false, false, false, 103)]), "html", null, true);
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
            yield "                        <tr><td colspan=\"6\" class=\"empty-state\">No services found.</td></tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['service'], $context['_parent'], $context['_iterated']);
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
        return "service/index.html.twig";
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
        return array (  347 => 116,  339 => 110,  332 => 108,  330 => 107,  324 => 105,  318 => 103,  316 => 102,  312 => 101,  307 => 99,  303 => 98,  299 => 97,  295 => 96,  291 => 95,  288 => 94,  283 => 93,  261 => 74,  251 => 69,  245 => 68,  239 => 67,  233 => 66,  227 => 65,  221 => 64,  213 => 59,  206 => 55,  196 => 50,  184 => 43,  176 => 37,  161 => 35,  157 => 34,  148 => 28,  142 => 25,  136 => 21,  130 => 19,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Services' : 'Services' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_service_' : 'service_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'services'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Service management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Full CRUD for add-on services, pricing, and availability.' : 'Browse add-on services available for your travel experience. Service CRUD is admin only.' }}</p>
        </div>
        <div class=\"actions\">
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'new') }}\">Create service</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"service-q\">Search</label>
                <input id=\"service-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"provider, type or description\">
            </div>
            <div>
                <label for=\"service-type\">Type</label>
                <select id=\"service-type\" name=\"serviceType\">
                    <option value=\"\">All types</option>
                    {% for serviceType in serviceTypes %}
                        <option value=\"{{ serviceType }}\" {% if filters.serviceType == serviceType %}selected{% endif %}>{{ serviceType }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"service-available\">Available only</label>
                <select id=\"service-available\" name=\"availableOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" {% if filters.availableOnly == '1' %}selected{% endif %}>Yes</option>
                </select>
            </div>
            <div>
                <label for=\"service-eco\">Eco only</label>
                <select id=\"service-eco\" name=\"ecoOnly\">
                    <option value=\"\">No filter</option>
                    <option value=\"1\" {% if filters.ecoOnly == '1' %}selected{% endif %}>Yes</option>
                </select>
            </div>
            <div>
                <label for=\"service-min-price\">Min price</label>
                <input id=\"service-min-price\" type=\"number\" min=\"0\" step=\"0.01\" name=\"minPrice\" value=\"{{ filters.minPrice }}\">
            </div>
            <div>
                <label for=\"service-max-price\">Max price</label>
                <input id=\"service-max-price\" type=\"number\" min=\"0\" step=\"0.01\" name=\"maxPrice\" value=\"{{ filters.maxPrice }}\">
            </div>
            <div>
                <label for=\"service-sort\">Sort</label>
                <select id=\"service-sort\" name=\"sort\">
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"price_asc\" {% if filters.sort == 'price_asc' %}selected{% endif %}>Price ↑</option>
                    <option value=\"price_desc\" {% if filters.sort == 'price_desc' %}selected{% endif %}>Price ↓</option>
                    <option value=\"provider_asc\" {% if filters.sort == 'provider_asc' %}selected{% endif %}>Provider A-Z</option>
                    <option value=\"provider_desc\" {% if filters.sort == 'provider_desc' %}selected{% endif %}>Provider Z-A</option>
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
                        <th>Provider</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Eco</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for service in services %}
                        <tr>
                            <td>{{ service.providerName }}</td>
                            <td>{{ service.serviceType }}</td>
                            <td>\${{ service.price|number_format(2, '.', ',') }}</td>
                            <td><span class=\"pill\">{{ service.isAvailable ? 'Available' : 'Unavailable' }}</span></td>
                            <td><span class=\"pill\">{{ service.ecoFriendly ? 'Yes' : 'No' }}</span></td>
                            <td class=\"row-actions\">
                                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': service.id}) }}\">Show</a>
                                {% if isAdmin %}
                                    <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'edit', {'id': service.id}) }}\">Edit</a>
                                {% endif %}
                            </td>
                        </tr>
                    {% else %}
                        <tr><td colspan=\"6\" class=\"empty-state\">No services found.</td></tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "service/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\service\\index.html.twig");
    }
}
