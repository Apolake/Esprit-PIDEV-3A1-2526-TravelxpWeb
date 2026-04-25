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

/* property/index.html.twig */
class __TwigTemplate_418ceb7a003359c731dbf9b4d356096b extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "property/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "property/index.html.twig"));

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

        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 3, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Admin Menu - Properties") : ("Properties"));
        
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
        $context["routePrefix"] = (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 6, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("admin_property_") : ("property_"));
        // line 7
        yield "    ";
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 7, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "        ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "properties"]);
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
            <h1>Property management</h1>
            <p class=\"muted\">";
        // line 15
        yield (((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 15, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Full CRUD for property listings, pricing, and visibility.") : ("Browse available listings and rent by creating a booking. Property CRUD is admin only."));
        yield "</p>
        </div>
        <div class=\"actions\">
            ";
        // line 18
        if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 18, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 19, $this->source); })()) . "new"));
            yield "\">Create property</a>
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
                <label for=\"property-q\">Search</label>
                <input id=\"property-q\" type=\"search\" name=\"q\" value=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 28, $this->source); })()), "q", [], "any", false, false, false, 28), "html", null, true);
        yield "\" placeholder=\"title, city, country\">
            </div>
            <div>
                <label for=\"property-type\">Type</label>
                <select id=\"property-type\" name=\"propertyType\">
                    <option value=\"\">All types</option>
                    ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["types"]) || array_key_exists("types", $context) ? $context["types"] : (function () { throw new RuntimeError('Variable "types" does not exist.', 34, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["type"]) {
            // line 35
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["type"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 35, $this->source); })()), "propertyType", [], "any", false, false, false, 35) == $context["type"])) {
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
        // line 37
        yield "                </select>
            </div>
            <div>
                <label for=\"property-city\">City</label>
                <select id=\"property-city\" name=\"city\">
                    <option value=\"\">All cities</option>
                    ";
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["cities"]) || array_key_exists("cities", $context) ? $context["cities"] : (function () { throw new RuntimeError('Variable "cities" does not exist.', 43, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["city"]) {
            // line 44
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["city"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 44, $this->source); })()), "city", [], "any", false, false, false, 44) == $context["city"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["city"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['city'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "                </select>
            </div>
            <div>
                <label for=\"property-country\">Country</label>
                <select id=\"property-country\" name=\"country\">
                    <option value=\"\">All countries</option>
                    ";
        // line 52
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["countries"]) || array_key_exists("countries", $context) ? $context["countries"] : (function () { throw new RuntimeError('Variable "countries" does not exist.', 52, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["country"]) {
            // line 53
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["country"], "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 53, $this->source); })()), "country", [], "any", false, false, false, 53) == $context["country"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["country"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['country'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 55
        yield "                </select>
            </div>
            <div>
                <label for=\"property-active\">Status</label>
                <select id=\"property-active\" name=\"active\">
                    <option value=\"\">All</option>
                    <option value=\"1\" ";
        // line 61
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 61, $this->source); })()), "active", [], "any", false, false, false, 61) == "1")) {
            yield "selected";
        }
        yield ">Active</option>
                    <option value=\"0\" ";
        // line 62
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 62, $this->source); })()), "active", [], "any", false, false, false, 62) == "0")) {
            yield "selected";
        }
        yield ">Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"property-min-price\">Min price</label>
                <input id=\"property-min-price\" type=\"number\" step=\"0.01\" min=\"0\" name=\"minPrice\" value=\"";
        // line 67
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 67, $this->source); })()), "minPrice", [], "any", false, false, false, 67), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"property-max-price\">Max price</label>
                <input id=\"property-max-price\" type=\"number\" step=\"0.01\" min=\"0\" name=\"maxPrice\" value=\"";
        // line 71
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 71, $this->source); })()), "maxPrice", [], "any", false, false, false, 71), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"property-bedrooms\">Min bedrooms</label>
                <input id=\"property-bedrooms\" type=\"number\" min=\"0\" name=\"bedrooms\" value=\"";
        // line 75
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 75, $this->source); })()), "bedrooms", [], "any", false, false, false, 75), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"property-guests\">Min max guests</label>
                <input id=\"property-guests\" type=\"number\" min=\"1\" name=\"maxGuests\" value=\"";
        // line 79
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 79, $this->source); })()), "maxGuests", [], "any", false, false, false, 79), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"property-sort\">Sort</label>
                <select id=\"property-sort\" name=\"sort\">
                    <option value=\"newest\" ";
        // line 84
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 84, $this->source); })()), "sort", [], "any", false, false, false, 84) == "newest")) {
            yield "selected";
        }
        yield ">Newest</option>
                    <option value=\"oldest\" ";
        // line 85
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 85, $this->source); })()), "sort", [], "any", false, false, false, 85) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest</option>
                    <option value=\"price_asc\" ";
        // line 86
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 86, $this->source); })()), "sort", [], "any", false, false, false, 86) == "price_asc")) {
            yield "selected";
        }
        yield ">Price ↑</option>
                    <option value=\"price_desc\" ";
        // line 87
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 87, $this->source); })()), "sort", [], "any", false, false, false, 87) == "price_desc")) {
            yield "selected";
        }
        yield ">Price ↓</option>
                    <option value=\"title_asc\" ";
        // line 88
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 88, $this->source); })()), "sort", [], "any", false, false, false, 88) == "title_asc")) {
            yield "selected";
        }
        yield ">Title A-Z</option>
                    <option value=\"title_desc\" ";
        // line 89
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 89, $this->source); })()), "sort", [], "any", false, false, false, 89) == "title_desc")) {
            yield "selected";
        }
        yield ">Title Z-A</option>
                </select>
            </div>
            <div>
                <label for=\"property-currency\">Currency</label>
                <select id=\"property-currency\" name=\"currency\">
                    ";
        // line 95
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["supportedCurrencies"]) || array_key_exists("supportedCurrencies", $context) ? $context["supportedCurrencies"] : (function () { throw new RuntimeError('Variable "supportedCurrencies" does not exist.', 95, $this->source); })()));
        foreach ($context['_seq'] as $context["code"] => $context["label"]) {
            // line 96
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["code"], "html", null, true);
            yield "\" ";
            if (((isset($context["selectedCurrency"]) || array_key_exists("selectedCurrency", $context) ? $context["selectedCurrency"] : (function () { throw new RuntimeError('Variable "selectedCurrency" does not exist.', 96, $this->source); })()) == $context["code"])) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["label"], "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['code'], $context['label'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 98
        yield "                </select>
            </div>
            <div>
                <label for=\"property-view\">View</label>
                <select id=\"property-view\" name=\"view\">
                    <option value=\"grid\" ";
        // line 103
        if (((isset($context["view"]) || array_key_exists("view", $context) ? $context["view"] : (function () { throw new RuntimeError('Variable "view" does not exist.', 103, $this->source); })()) == "grid")) {
            yield "selected";
        }
        yield ">Grid</option>
                    <option value=\"table\" ";
        // line 104
        if (((isset($context["view"]) || array_key_exists("view", $context) ? $context["view"] : (function () { throw new RuntimeError('Variable "view" does not exist.', 104, $this->source); })()) == "table")) {
            yield "selected";
        }
        yield ">Table</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 109
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 109, $this->source); })()) . "index"));
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    ";
        // line 114
        if (((isset($context["view"]) || array_key_exists("view", $context) ? $context["view"] : (function () { throw new RuntimeError('Variable "view" does not exist.', 114, $this->source); })()) == "grid")) {
            // line 115
            yield "        <section class=\"quick-actions-grid\">
            ";
            // line 116
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 116, $this->source); })()));
            $context['_iterated'] = false;
            foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
                // line 117
                yield "                ";
                $context["priceLabel"] = (((CoreExtension::getAttribute($this->env, $this->source, ($context["formattedPricesByPropertyId"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 117), [], "array", true, true, false, 117) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["formattedPricesByPropertyId"]) || array_key_exists("formattedPricesByPropertyId", $context) ? $context["formattedPricesByPropertyId"] : (function () { throw new RuntimeError('Variable "formattedPricesByPropertyId" does not exist.', 117, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 117), [], "array", false, false, false, 117)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["formattedPricesByPropertyId"]) || array_key_exists("formattedPricesByPropertyId", $context) ? $context["formattedPricesByPropertyId"] : (function () { throw new RuntimeError('Variable "formattedPricesByPropertyId" does not exist.', 117, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 117), [], "array", false, false, false, 117)) : (((isset($context["currencySymbol"]) || array_key_exists("currencySymbol", $context) ? $context["currencySymbol"] : (function () { throw new RuntimeError('Variable "currencySymbol" does not exist.', 117, $this->source); })()) . $this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "pricePerNight", [], "any", false, false, false, 117), 2, ".", ","))));
                // line 118
                yield "                ";
                $context["imagePath"] = (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["property"], "images", [], "any", false, false, false, 118)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (Twig\Extension\CoreExtension::replace(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "images", [], "any", false, false, false, 118), ["\\" => "/"])) : (null));
                // line 119
                yield "                ";
                $context["imageSrc"] = (((($tmp = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ((((is_string($_v0 = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })())) && is_string($_v1 = "http") && str_starts_with($_v0, $_v1))) ? ((isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })())) : ($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl((((is_string($_v2 = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })())) && is_string($_v3 = "/") && str_starts_with($_v2, $_v3))) ? (Twig\Extension\CoreExtension::slice($this->env->getCharset(), (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })()), 1)) : ((isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 119, $this->source); })()))))))) : (null));
                // line 120
                yield "                <article class=\"glass-card\">
                    ";
                // line 121
                if ((($tmp = (isset($context["imageSrc"]) || array_key_exists("imageSrc", $context) ? $context["imageSrc"] : (function () { throw new RuntimeError('Variable "imageSrc" does not exist.', 121, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 122
                    yield "                        <img src=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["imageSrc"]) || array_key_exists("imageSrc", $context) ? $context["imageSrc"] : (function () { throw new RuntimeError('Variable "imageSrc" does not exist.', 122, $this->source); })()), "html", null, true);
                    yield "\" alt=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "title", [], "any", false, false, false, 122), "html", null, true);
                    yield "\" style=\"width:100%;height:180px;object-fit:cover;border-radius:14px;margin-bottom:0.7rem;\">
                    ";
                }
                // line 124
                yield "                    <p class=\"eyebrow\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "propertyType", [], "any", false, false, false, 124), "html", null, true);
                yield "</p>
                    <h3>";
                // line 125
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "title", [], "any", false, false, false, 125), "html", null, true);
                yield "</h3>
                    <p class=\"muted\">";
                // line 126
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "city", [], "any", false, false, false, 126), "html", null, true);
                yield ", ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "country", [], "any", false, false, false, 126), "html", null, true);
                yield "</p>
                    <p><strong>";
                // line 127
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["priceLabel"]) || array_key_exists("priceLabel", $context) ? $context["priceLabel"] : (function () { throw new RuntimeError('Variable "priceLabel" does not exist.', 127, $this->source); })()), "html", null, true);
                yield "</strong> / night</p>
                    <div class=\"row-actions\">
                        <a class=\"btn btn-sm\" href=\"";
                // line 129
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 129, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 129)]), "html", null, true);
                yield "\">Show</a>
                        ";
                // line 130
                if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 130, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 131
                    yield "                            <a class=\"btn btn-sm\" href=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 131, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 131)]), "html", null, true);
                    yield "\">Edit</a>
                        ";
                } else {
                    // line 133
                    yield "                            <a class=\"btn btn-sm btn-primary\" href=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("booking_new", ["propertyId" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 133)]), "html", null, true);
                    yield "\">Rent now</a>
                        ";
                }
                // line 135
                yield "                    </div>
                </article>
            ";
                $context['_iterated'] = true;
            }
            // line 137
            if (!$context['_iterated']) {
                // line 138
                yield "                <article class=\"glass-card empty-state\">No properties found.</article>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['property'], $context['_parent'], $context['_iterated']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 140
            yield "        </section>
    ";
        } else {
            // line 142
            yield "        <section class=\"glass-card\">
            <div class=\"table-wrap\">
                <table class=\"data-table\">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Beds</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ";
            // line 158
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 158, $this->source); })()));
            $context['_iterated'] = false;
            foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
                // line 159
                yield "                            ";
                $context["priceLabel"] = (((CoreExtension::getAttribute($this->env, $this->source, ($context["formattedPricesByPropertyId"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 159), [], "array", true, true, false, 159) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["formattedPricesByPropertyId"]) || array_key_exists("formattedPricesByPropertyId", $context) ? $context["formattedPricesByPropertyId"] : (function () { throw new RuntimeError('Variable "formattedPricesByPropertyId" does not exist.', 159, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 159), [], "array", false, false, false, 159)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["formattedPricesByPropertyId"]) || array_key_exists("formattedPricesByPropertyId", $context) ? $context["formattedPricesByPropertyId"] : (function () { throw new RuntimeError('Variable "formattedPricesByPropertyId" does not exist.', 159, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 159), [], "array", false, false, false, 159)) : (((isset($context["currencySymbol"]) || array_key_exists("currencySymbol", $context) ? $context["currencySymbol"] : (function () { throw new RuntimeError('Variable "currencySymbol" does not exist.', 159, $this->source); })()) . $this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "pricePerNight", [], "any", false, false, false, 159), 2, ".", ","))));
                // line 160
                yield "                            <tr>
                                <td>";
                // line 161
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "title", [], "any", false, false, false, 161), "html", null, true);
                yield "</td>
                                <td>";
                // line 162
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "propertyType", [], "any", false, false, false, 162), "html", null, true);
                yield "</td>
                                <td>";
                // line 163
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "city", [], "any", false, false, false, 163), "html", null, true);
                yield ", ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "country", [], "any", false, false, false, 163), "html", null, true);
                yield "</td>
                                <td>";
                // line 164
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["priceLabel"]) || array_key_exists("priceLabel", $context) ? $context["priceLabel"] : (function () { throw new RuntimeError('Variable "priceLabel" does not exist.', 164, $this->source); })()), "html", null, true);
                yield "</td>
                                <td>";
                // line 165
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "bedrooms", [], "any", false, false, false, 165), "html", null, true);
                yield "</td>
                                <td>";
                // line 166
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "maxGuests", [], "any", false, false, false, 166), "html", null, true);
                yield "</td>
                                <td><span class=\"pill\">";
                // line 167
                yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isActive", [], "any", false, false, false, 167)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Active") : ("Inactive"));
                yield "</span></td>
                                <td class=\"row-actions\">
                                    <a class=\"btn btn-sm\" href=\"";
                // line 169
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 169, $this->source); })()) . "show"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 169)]), "html", null, true);
                yield "\">Show</a>
                                    ";
                // line 170
                if ((($tmp = (isset($context["isAdmin"]) || array_key_exists("isAdmin", $context) ? $context["isAdmin"] : (function () { throw new RuntimeError('Variable "isAdmin" does not exist.', 170, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 171
                    yield "                                        <a class=\"btn btn-sm\" href=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath(((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 171, $this->source); })()) . "edit"), ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 171)]), "html", null, true);
                    yield "\">Edit</a>
                                    ";
                } else {
                    // line 173
                    yield "                                        <a class=\"btn btn-sm btn-primary\" href=\"";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("booking_new", ["propertyId" => CoreExtension::getAttribute($this->env, $this->source, $context["property"], "id", [], "any", false, false, false, 173)]), "html", null, true);
                    yield "\">Rent now</a>
                                    ";
                }
                // line 175
                yield "                                </td>
                            </tr>
                        ";
                $context['_iterated'] = true;
            }
            // line 177
            if (!$context['_iterated']) {
                // line 178
                yield "                            <tr><td colspan=\"8\" class=\"empty-state\">No properties found.</td></tr>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['property'], $context['_parent'], $context['_iterated']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 180
            yield "                    </tbody>
                </table>
            </div>
        </section>
    ";
        }
        // line 185
        yield "
    <section class=\"glass-card\">
        ";
        // line 187
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 187, $this->source); })()), "routeName" => ((isset($context["routePrefix"]) || array_key_exists("routePrefix", $context) ? $context["routePrefix"] : (function () { throw new RuntimeError('Variable "routePrefix" does not exist.', 187, $this->source); })()) . "index")]);
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
        return "property/index.html.twig";
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
        return array (  567 => 187,  563 => 185,  556 => 180,  549 => 178,  547 => 177,  541 => 175,  535 => 173,  529 => 171,  527 => 170,  523 => 169,  518 => 167,  514 => 166,  510 => 165,  506 => 164,  500 => 163,  496 => 162,  492 => 161,  489 => 160,  486 => 159,  481 => 158,  463 => 142,  459 => 140,  452 => 138,  450 => 137,  444 => 135,  438 => 133,  432 => 131,  430 => 130,  426 => 129,  421 => 127,  415 => 126,  411 => 125,  406 => 124,  398 => 122,  396 => 121,  393 => 120,  390 => 119,  387 => 118,  384 => 117,  379 => 116,  376 => 115,  374 => 114,  366 => 109,  356 => 104,  350 => 103,  343 => 98,  328 => 96,  324 => 95,  313 => 89,  307 => 88,  301 => 87,  295 => 86,  289 => 85,  283 => 84,  275 => 79,  268 => 75,  261 => 71,  254 => 67,  244 => 62,  238 => 61,  230 => 55,  215 => 53,  211 => 52,  203 => 46,  188 => 44,  184 => 43,  176 => 37,  161 => 35,  157 => 34,  148 => 28,  142 => 25,  136 => 21,  130 => 19,  128 => 18,  122 => 15,  117 => 13,  112 => 10,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ isAdmin ? 'Admin Menu - Properties' : 'Properties' }}{% endblock %}

{% block body %}
    {% set routePrefix = isAdmin ? 'admin_property_' : 'property_' %}
    {% if isAdmin %}
        {{ include('admin/_operations.html.twig', {active: 'properties'}) }}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">{{ isAdmin ? 'Admin Menu' : 'Front Office' }}</p>
            <h1>Property management</h1>
            <p class=\"muted\">{{ isAdmin ? 'Full CRUD for property listings, pricing, and visibility.' : 'Browse available listings and rent by creating a booking. Property CRUD is admin only.' }}</p>
        </div>
        <div class=\"actions\">
            {% if isAdmin %}
                <a class=\"btn btn-primary\" href=\"{{ path(routePrefix ~ 'new') }}\">Create property</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path(routePrefix ~ 'index') }}\">
            <div>
                <label for=\"property-q\">Search</label>
                <input id=\"property-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"title, city, country\">
            </div>
            <div>
                <label for=\"property-type\">Type</label>
                <select id=\"property-type\" name=\"propertyType\">
                    <option value=\"\">All types</option>
                    {% for type in types %}
                        <option value=\"{{ type }}\" {% if filters.propertyType == type %}selected{% endif %}>{{ type }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"property-city\">City</label>
                <select id=\"property-city\" name=\"city\">
                    <option value=\"\">All cities</option>
                    {% for city in cities %}
                        <option value=\"{{ city }}\" {% if filters.city == city %}selected{% endif %}>{{ city }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"property-country\">Country</label>
                <select id=\"property-country\" name=\"country\">
                    <option value=\"\">All countries</option>
                    {% for country in countries %}
                        <option value=\"{{ country }}\" {% if filters.country == country %}selected{% endif %}>{{ country }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"property-active\">Status</label>
                <select id=\"property-active\" name=\"active\">
                    <option value=\"\">All</option>
                    <option value=\"1\" {% if filters.active == '1' %}selected{% endif %}>Active</option>
                    <option value=\"0\" {% if filters.active == '0' %}selected{% endif %}>Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"property-min-price\">Min price</label>
                <input id=\"property-min-price\" type=\"number\" step=\"0.01\" min=\"0\" name=\"minPrice\" value=\"{{ filters.minPrice }}\">
            </div>
            <div>
                <label for=\"property-max-price\">Max price</label>
                <input id=\"property-max-price\" type=\"number\" step=\"0.01\" min=\"0\" name=\"maxPrice\" value=\"{{ filters.maxPrice }}\">
            </div>
            <div>
                <label for=\"property-bedrooms\">Min bedrooms</label>
                <input id=\"property-bedrooms\" type=\"number\" min=\"0\" name=\"bedrooms\" value=\"{{ filters.bedrooms }}\">
            </div>
            <div>
                <label for=\"property-guests\">Min max guests</label>
                <input id=\"property-guests\" type=\"number\" min=\"1\" name=\"maxGuests\" value=\"{{ filters.maxGuests }}\">
            </div>
            <div>
                <label for=\"property-sort\">Sort</label>
                <select id=\"property-sort\" name=\"sort\">
                    <option value=\"newest\" {% if filters.sort == 'newest' %}selected{% endif %}>Newest</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest</option>
                    <option value=\"price_asc\" {% if filters.sort == 'price_asc' %}selected{% endif %}>Price ↑</option>
                    <option value=\"price_desc\" {% if filters.sort == 'price_desc' %}selected{% endif %}>Price ↓</option>
                    <option value=\"title_asc\" {% if filters.sort == 'title_asc' %}selected{% endif %}>Title A-Z</option>
                    <option value=\"title_desc\" {% if filters.sort == 'title_desc' %}selected{% endif %}>Title Z-A</option>
                </select>
            </div>
            <div>
                <label for=\"property-currency\">Currency</label>
                <select id=\"property-currency\" name=\"currency\">
                    {% for code, label in supportedCurrencies %}
                        <option value=\"{{ code }}\" {% if selectedCurrency == code %}selected{% endif %}>{{ label }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"property-view\">View</label>
                <select id=\"property-view\" name=\"view\">
                    <option value=\"grid\" {% if view == 'grid' %}selected{% endif %}>Grid</option>
                    <option value=\"table\" {% if view == 'table' %}selected{% endif %}>Table</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'index') }}\">Reset</a>
            </div>
        </form>
    </section>

    {% if view == 'grid' %}
        <section class=\"quick-actions-grid\">
            {% for property in properties %}
                {% set priceLabel = formattedPricesByPropertyId[property.id] ?? (currencySymbol ~ (property.pricePerNight|number_format(2, '.', ','))) %}
                {% set imagePath = property.images ? property.images|replace({'\\\\': '/'}) : null %}
                {% set imageSrc = imagePath ? (imagePath starts with 'http' ? imagePath : asset(imagePath starts with '/' ? imagePath|slice(1) : imagePath)) : null %}
                <article class=\"glass-card\">
                    {% if imageSrc %}
                        <img src=\"{{ imageSrc }}\" alt=\"{{ property.title }}\" style=\"width:100%;height:180px;object-fit:cover;border-radius:14px;margin-bottom:0.7rem;\">
                    {% endif %}
                    <p class=\"eyebrow\">{{ property.propertyType }}</p>
                    <h3>{{ property.title }}</h3>
                    <p class=\"muted\">{{ property.city }}, {{ property.country }}</p>
                    <p><strong>{{ priceLabel }}</strong> / night</p>
                    <div class=\"row-actions\">
                        <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': property.id}) }}\">Show</a>
                        {% if isAdmin %}
                            <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'edit', {'id': property.id}) }}\">Edit</a>
                        {% else %}
                            <a class=\"btn btn-sm btn-primary\" href=\"{{ path('booking_new', {'propertyId': property.id}) }}\">Rent now</a>
                        {% endif %}
                    </div>
                </article>
            {% else %}
                <article class=\"glass-card empty-state\">No properties found.</article>
            {% endfor %}
        </section>
    {% else %}
        <section class=\"glass-card\">
            <div class=\"table-wrap\">
                <table class=\"data-table\">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Beds</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for property in properties %}
                            {% set priceLabel = formattedPricesByPropertyId[property.id] ?? (currencySymbol ~ (property.pricePerNight|number_format(2, '.', ','))) %}
                            <tr>
                                <td>{{ property.title }}</td>
                                <td>{{ property.propertyType }}</td>
                                <td>{{ property.city }}, {{ property.country }}</td>
                                <td>{{ priceLabel }}</td>
                                <td>{{ property.bedrooms }}</td>
                                <td>{{ property.maxGuests }}</td>
                                <td><span class=\"pill\">{{ property.isActive ? 'Active' : 'Inactive' }}</span></td>
                                <td class=\"row-actions\">
                                    <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'show', {'id': property.id}) }}\">Show</a>
                                    {% if isAdmin %}
                                        <a class=\"btn btn-sm\" href=\"{{ path(routePrefix ~ 'edit', {'id': property.id}) }}\">Edit</a>
                                    {% else %}
                                        <a class=\"btn btn-sm btn-primary\" href=\"{{ path('booking_new', {'propertyId': property.id}) }}\">Rent now</a>
                                    {% endif %}
                                </td>
                            </tr>
                        {% else %}
                            <tr><td colspan=\"8\" class=\"empty-state\">No properties found.</td></tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </section>
    {% endif %}

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: routePrefix ~ 'index'}) }}
    </section>
{% endblock %}
", "property/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\property\\index.html.twig");
    }
}
