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

/* user/index.html.twig */
class __TwigTemplate_baa213a4b0c0d9a034fd14500d74cf0d extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "user/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "user/index.html.twig"));

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

        yield "Admin Menu - Users";
        
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
        yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "users"]);
        yield "

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Admin Menu</p>
            <h1>User management</h1>
            <p class=\"muted\">Search, sort, filter, and maintain user accounts from the centralized admin hub.</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"";
        // line 15
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index");
        yield "\">Gamification Control</a>
            <a class=\"btn btn-primary\" href=\"";
        // line 16
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_new");
        yield "\">Create user</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <form id=\"admin-user-filters\" class=\"filters-grid\" method=\"get\" action=\"";
        // line 21
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_index");
        yield "\">
            <div>
                <label for=\"filter-q\">Search</label>
                <input id=\"filter-q\" type=\"search\" name=\"q\" value=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 24, $this->source); })()), "q", [], "any", false, false, false, 24), "html", null, true);
        yield "\" placeholder=\"username, email, bio or id\">
            </div>
            <div>
                <label for=\"filter-role\">Role</label>
                <select id=\"filter-role\" name=\"role\">
                    <option value=\"\">All roles</option>
                    <option value=\"ROLE_USER\" ";
        // line 30
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 30, $this->source); })()), "role", [], "any", false, false, false, 30) == "ROLE_USER")) {
            yield "selected";
        }
        yield ">User</option>
                    <option value=\"ROLE_ADMIN\" ";
        // line 31
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 31, $this->source); })()), "role", [], "any", false, false, false, 31) == "ROLE_ADMIN")) {
            yield "selected";
        }
        yield ">Admin</option>
                </select>
            </div>
            <div>
                <label for=\"filter-sort\">Sort by</label>
                <select id=\"filter-sort\" name=\"sort\">
                    <option value=\"createdAt\" ";
        // line 37
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 37, $this->source); })()), "sort", [], "any", false, false, false, 37) == "createdAt")) {
            yield "selected";
        }
        yield ">Created at</option>
                    <option value=\"updatedAt\" ";
        // line 38
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 38, $this->source); })()), "sort", [], "any", false, false, false, 38) == "updatedAt")) {
            yield "selected";
        }
        yield ">Updated at</option>
                    <option value=\"username\" ";
        // line 39
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 39, $this->source); })()), "sort", [], "any", false, false, false, 39) == "username")) {
            yield "selected";
        }
        yield ">Username</option>
                    <option value=\"email\" ";
        // line 40
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 40, $this->source); })()), "sort", [], "any", false, false, false, 40) == "email")) {
            yield "selected";
        }
        yield ">Email</option>
                    <option value=\"birthday\" ";
        // line 41
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 41, $this->source); })()), "sort", [], "any", false, false, false, 41) == "birthday")) {
            yield "selected";
        }
        yield ">Birthday</option>
                </select>
            </div>
            <div>
                <label for=\"filter-direction\">Direction</label>
                <select id=\"filter-direction\" name=\"direction\">
                    <option value=\"DESC\" ";
        // line 47
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 47, $this->source); })()), "direction", [], "any", false, false, false, 47) == "DESC")) {
            yield "selected";
        }
        yield ">Descending</option>
                    <option value=\"ASC\" ";
        // line 48
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 48, $this->source); })()), "direction", [], "any", false, false, false, 48) == "ASC")) {
            yield "selected";
        }
        yield ">Ascending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 53
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_index");
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\" id=\"admin-users-table\">
        ";
        // line 59
        yield Twig\Extension\CoreExtension::include($this->env, $context, "user/_table.html.twig", ["users" => (isset($context["users"]) || array_key_exists("users", $context) ? $context["users"] : (function () { throw new RuntimeError('Variable "users" does not exist.', 59, $this->source); })())]);
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
        return "user/index.html.twig";
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
        return array (  217 => 59,  208 => 53,  198 => 48,  192 => 47,  181 => 41,  175 => 40,  169 => 39,  163 => 38,  157 => 37,  146 => 31,  140 => 30,  131 => 24,  125 => 21,  117 => 16,  113 => 15,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Admin Menu - Users{% endblock %}

{% block body %}
    {{ include('admin/_operations.html.twig', {active: 'users'}) }}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Admin Menu</p>
            <h1>User management</h1>
            <p class=\"muted\">Search, sort, filter, and maintain user accounts from the centralized admin hub.</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"{{ path('app_admin_gamification_index') }}\">Gamification Control</a>
            <a class=\"btn btn-primary\" href=\"{{ path('app_user_new') }}\">Create user</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <form id=\"admin-user-filters\" class=\"filters-grid\" method=\"get\" action=\"{{ path('app_user_index') }}\">
            <div>
                <label for=\"filter-q\">Search</label>
                <input id=\"filter-q\" type=\"search\" name=\"q\" value=\"{{ filters.q }}\" placeholder=\"username, email, bio or id\">
            </div>
            <div>
                <label for=\"filter-role\">Role</label>
                <select id=\"filter-role\" name=\"role\">
                    <option value=\"\">All roles</option>
                    <option value=\"ROLE_USER\" {% if filters.role == 'ROLE_USER' %}selected{% endif %}>User</option>
                    <option value=\"ROLE_ADMIN\" {% if filters.role == 'ROLE_ADMIN' %}selected{% endif %}>Admin</option>
                </select>
            </div>
            <div>
                <label for=\"filter-sort\">Sort by</label>
                <select id=\"filter-sort\" name=\"sort\">
                    <option value=\"createdAt\" {% if filters.sort == 'createdAt' %}selected{% endif %}>Created at</option>
                    <option value=\"updatedAt\" {% if filters.sort == 'updatedAt' %}selected{% endif %}>Updated at</option>
                    <option value=\"username\" {% if filters.sort == 'username' %}selected{% endif %}>Username</option>
                    <option value=\"email\" {% if filters.sort == 'email' %}selected{% endif %}>Email</option>
                    <option value=\"birthday\" {% if filters.sort == 'birthday' %}selected{% endif %}>Birthday</option>
                </select>
            </div>
            <div>
                <label for=\"filter-direction\">Direction</label>
                <select id=\"filter-direction\" name=\"direction\">
                    <option value=\"DESC\" {% if filters.direction == 'DESC' %}selected{% endif %}>Descending</option>
                    <option value=\"ASC\" {% if filters.direction == 'ASC' %}selected{% endif %}>Ascending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path('app_user_index') }}\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"glass-card\" id=\"admin-users-table\">
        {{ include('user/_table.html.twig', {users: users}) }}
    </section>
{% endblock %}
", "user/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\user\\index.html.twig");
    }
}
