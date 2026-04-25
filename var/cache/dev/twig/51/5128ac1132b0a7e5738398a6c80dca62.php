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

/* admin_gamification/index.html.twig */
class __TwigTemplate_86265afcf9f6719884311834456eab9b extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin_gamification/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin_gamification/index.html.twig"));

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

        yield "Admin Menu - Gamification";
        
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
        yield Twig\Extension\CoreExtension::include($this->env, $context, "admin/_operations.html.twig", ["active" => "gamification"]);
        yield "

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Admin Menu</p>
            <h1>Gamification control center</h1>
            <p class=\"muted\">Manage quests and user progression from the centralized admin hub.</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"";
        // line 15
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_index");
        yield "\">User management</a>
            <a class=\"btn btn-primary\" href=\"";
        // line 16
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_quest_new");
        yield "\">Create quest</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <h2>Quests</h2>
        </div>
        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 24
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index");
        yield "\">
            <input type=\"hidden\" name=\"userQ\" value=\"";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 25, $this->source); })()), "q", [], "any", false, false, false, 25), "html", null, true);
        yield "\">
            <input type=\"hidden\" name=\"userSort\" value=\"";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 26, $this->source); })()), "sort", [], "any", false, false, false, 26), "html", null, true);
        yield "\">
            <input type=\"hidden\" name=\"userDirection\" value=\"";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 27, $this->source); })()), "direction", [], "any", false, false, false, 27), "html", null, true);
        yield "\">
            <div>
                <label for=\"quest-q\">Search quest</label>
                <input id=\"quest-q\" type=\"search\" name=\"questQ\" value=\"";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 30, $this->source); })()), "q", [], "any", false, false, false, 30), "html", null, true);
        yield "\" placeholder=\"title, description or id\">
            </div>
            <div>
                <label for=\"quest-status\">Status</label>
                <select id=\"quest-status\" name=\"questStatus\">
                    <option value=\"\">All</option>
                    <option value=\"active\" ";
        // line 36
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 36, $this->source); })()), "status", [], "any", false, false, false, 36) == "active")) {
            yield "selected";
        }
        yield ">Active</option>
                    <option value=\"inactive\" ";
        // line 37
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 37, $this->source); })()), "status", [], "any", false, false, false, 37) == "inactive")) {
            yield "selected";
        }
        yield ">Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"quest-sort\">Sort</label>
                <select id=\"quest-sort\" name=\"questSort\">
                    <option value=\"updatedAt\" ";
        // line 43
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 43, $this->source); })()), "sort", [], "any", false, false, false, 43) == "updatedAt")) {
            yield "selected";
        }
        yield ">Updated</option>
                    <option value=\"createdAt\" ";
        // line 44
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 44, $this->source); })()), "sort", [], "any", false, false, false, 44) == "createdAt")) {
            yield "selected";
        }
        yield ">Created</option>
                    <option value=\"title\" ";
        // line 45
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 45, $this->source); })()), "sort", [], "any", false, false, false, 45) == "title")) {
            yield "selected";
        }
        yield ">Title</option>
                    <option value=\"goal\" ";
        // line 46
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 46, $this->source); })()), "sort", [], "any", false, false, false, 46) == "goal")) {
            yield "selected";
        }
        yield ">Goal</option>
                    <option value=\"rewardXp\" ";
        // line 47
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 47, $this->source); })()), "sort", [], "any", false, false, false, 47) == "rewardXp")) {
            yield "selected";
        }
        yield ">Reward XP</option>
                </select>
            </div>
            <div>
                <label for=\"quest-direction\">Direction</label>
                <select id=\"quest-direction\" name=\"questDirection\">
                    <option value=\"DESC\" ";
        // line 53
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 53, $this->source); })()), "direction", [], "any", false, false, false, 53) == "DESC")) {
            yield "selected";
        }
        yield ">Descending</option>
                    <option value=\"ASC\" ";
        // line 54
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 54, $this->source); })()), "direction", [], "any", false, false, false, 54) == "ASC")) {
            yield "selected";
        }
        yield ">Ascending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 59
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index", ["userQ" => CoreExtension::getAttribute($this->env, $this->source,         // line 60
(isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 60, $this->source); })()), "q", [], "any", false, false, false, 60), "userSort" => CoreExtension::getAttribute($this->env, $this->source,         // line 61
(isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 61, $this->source); })()), "sort", [], "any", false, false, false, 61), "userDirection" => CoreExtension::getAttribute($this->env, $this->source,         // line 62
(isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 62, $this->source); })()), "direction", [], "any", false, false, false, 62)]), "html", null, true);
        // line 63
        yield "\">Reset</a>
            </div>
        </form>
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Goal</th>
                        <th>Reward XP</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                ";
        // line 79
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["quests"]) || array_key_exists("quests", $context) ? $context["quests"] : (function () { throw new RuntimeError('Variable "quests" does not exist.', 79, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["quest"]) {
            // line 80
            yield "                    <tr>
                        <td>";
            // line 81
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "title", [], "any", false, false, false, 81), "html", null, true);
            yield "</td>
                        <td>";
            // line 82
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "goal", [], "any", false, false, false, 82), "html", null, true);
            yield "</td>
                        <td>";
            // line 83
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "rewardXp", [], "any", false, false, false, 83), "html", null, true);
            yield "</td>
                        <td><span class=\"pill\">";
            // line 84
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "isActive", [], "any", false, false, false, 84)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Active") : ("Inactive"));
            yield "</span></td>
                        <td>";
            // line 85
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "updatedAt", [], "any", false, false, false, 85)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "updatedAt", [], "any", false, false, false, 85), "Y-m-d H:i"), "html", null, true)) : ("—"));
            yield "</td>
                        <td class=\"row-actions\">
                            <a class=\"btn btn-sm\" href=\"";
            // line 87
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_quest_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "id", [], "any", false, false, false, 87)]), "html", null, true);
            yield "\">Edit</a>
                            <form class=\"inline-form\" method=\"post\" action=\"";
            // line 88
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_quest_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "id", [], "any", false, false, false, 88)]), "html", null, true);
            yield "\" onsubmit=\"return confirm('Delete this quest?');\">
                                <input type=\"hidden\" name=\"_token\" value=\"";
            // line 89
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("delete_quest" . CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "id", [], "any", false, false, false, 89))), "html", null, true);
            yield "\">
                                <button class=\"btn btn-sm btn-danger\" type=\"submit\">Delete</button>
                            </form>
                        </td>
                    </tr>
                ";
            $context['_iterated'] = true;
        }
        // line 94
        if (!$context['_iterated']) {
            // line 95
            yield "                    <tr><td colspan=\"6\" class=\"empty-state\">No quests found.</td></tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['quest'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 97
        yield "                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <h2>User gamification stats</h2>
        </div>
        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 106
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index");
        yield "\">
            <input type=\"hidden\" name=\"questQ\" value=\"";
        // line 107
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 107, $this->source); })()), "q", [], "any", false, false, false, 107), "html", null, true);
        yield "\">
            <input type=\"hidden\" name=\"questStatus\" value=\"";
        // line 108
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 108, $this->source); })()), "status", [], "any", false, false, false, 108), "html", null, true);
        yield "\">
            <input type=\"hidden\" name=\"questSort\" value=\"";
        // line 109
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 109, $this->source); })()), "sort", [], "any", false, false, false, 109), "html", null, true);
        yield "\">
            <input type=\"hidden\" name=\"questDirection\" value=\"";
        // line 110
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 110, $this->source); })()), "direction", [], "any", false, false, false, 110), "html", null, true);
        yield "\">
            <div>
                <label for=\"user-q\">Search user</label>
                <input id=\"user-q\" type=\"search\" name=\"userQ\" value=\"";
        // line 113
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 113, $this->source); })()), "q", [], "any", false, false, false, 113), "html", null, true);
        yield "\" placeholder=\"username, email or id\">
            </div>
            <div>
                <label for=\"user-sort\">Sort</label>
                <select id=\"user-sort\" name=\"userSort\">
                    <option value=\"username\" ";
        // line 118
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 118, $this->source); })()), "sort", [], "any", false, false, false, 118) == "username")) {
            yield "selected";
        }
        yield ">Username</option>
                    <option value=\"email\" ";
        // line 119
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 119, $this->source); })()), "sort", [], "any", false, false, false, 119) == "email")) {
            yield "selected";
        }
        yield ">Email</option>
                    <option value=\"xp\" ";
        // line 120
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 120, $this->source); })()), "sort", [], "any", false, false, false, 120) == "xp")) {
            yield "selected";
        }
        yield ">XP</option>
                    <option value=\"level\" ";
        // line 121
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 121, $this->source); })()), "sort", [], "any", false, false, false, 121) == "level")) {
            yield "selected";
        }
        yield ">Level</option>
                    <option value=\"streak\" ";
        // line 122
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 122, $this->source); })()), "sort", [], "any", false, false, false, 122) == "streak")) {
            yield "selected";
        }
        yield ">Streak</option>
                    <option value=\"updatedAt\" ";
        // line 123
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 123, $this->source); })()), "sort", [], "any", false, false, false, 123) == "updatedAt")) {
            yield "selected";
        }
        yield ">Updated</option>
                </select>
            </div>
            <div>
                <label for=\"user-direction\">Direction</label>
                <select id=\"user-direction\" name=\"userDirection\">
                    <option value=\"ASC\" ";
        // line 129
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 129, $this->source); })()), "direction", [], "any", false, false, false, 129) == "ASC")) {
            yield "selected";
        }
        yield ">Ascending</option>
                    <option value=\"DESC\" ";
        // line 130
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["userFilters"]) || array_key_exists("userFilters", $context) ? $context["userFilters"] : (function () { throw new RuntimeError('Variable "userFilters" does not exist.', 130, $this->source); })()), "direction", [], "any", false, false, false, 130) == "DESC")) {
            yield "selected";
        }
        yield ">Descending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 135
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index", ["questQ" => CoreExtension::getAttribute($this->env, $this->source,         // line 136
(isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 136, $this->source); })()), "q", [], "any", false, false, false, 136), "questStatus" => CoreExtension::getAttribute($this->env, $this->source,         // line 137
(isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 137, $this->source); })()), "status", [], "any", false, false, false, 137), "questSort" => CoreExtension::getAttribute($this->env, $this->source,         // line 138
(isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 138, $this->source); })()), "sort", [], "any", false, false, false, 138), "questDirection" => CoreExtension::getAttribute($this->env, $this->source,         // line 139
(isset($context["questFilters"]) || array_key_exists("questFilters", $context) ? $context["questFilters"] : (function () { throw new RuntimeError('Variable "questFilters" does not exist.', 139, $this->source); })()), "direction", [], "any", false, false, false, 139)]), "html", null, true);
        // line 140
        yield "\">Reset</a>
            </div>
        </form>
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>XP</th>
                        <th>Level</th>
                        <th>Streak</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                ";
        // line 156
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["users"]) || array_key_exists("users", $context) ? $context["users"] : (function () { throw new RuntimeError('Variable "users" does not exist.', 156, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 157
            yield "                    <tr>
                        <td>";
            // line 158
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 158), "html", null, true);
            yield "</td>
                        <td>";
            // line 159
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "email", [], "any", false, false, false, 159), "html", null, true);
            yield "</td>
                        <td>";
            // line 160
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "xp", [], "any", false, false, false, 160), "html", null, true);
            yield "</td>
                        <td>";
            // line 161
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "level", [], "any", false, false, false, 161), "html", null, true);
            yield "</td>
                        <td>";
            // line 162
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "streak", [], "any", false, false, false, 162), "html", null, true);
            yield "</td>
                        <td>
                            <a class=\"btn btn-sm\" href=\"";
            // line 164
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_user_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["user"], "id", [], "any", false, false, false, 164)]), "html", null, true);
            yield "\">Edit stats</a>
                        </td>
                    </tr>
                ";
            $context['_iterated'] = true;
        }
        // line 167
        if (!$context['_iterated']) {
            // line 168
            yield "                    <tr><td colspan=\"6\" class=\"empty-state\">No users found.</td></tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['user'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 170
        yield "                </tbody>
            </table>
        </div>
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
        return "admin_gamification/index.html.twig";
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
        return array (  470 => 170,  463 => 168,  461 => 167,  453 => 164,  448 => 162,  444 => 161,  440 => 160,  436 => 159,  432 => 158,  429 => 157,  424 => 156,  406 => 140,  404 => 139,  403 => 138,  402 => 137,  401 => 136,  400 => 135,  390 => 130,  384 => 129,  373 => 123,  367 => 122,  361 => 121,  355 => 120,  349 => 119,  343 => 118,  335 => 113,  329 => 110,  325 => 109,  321 => 108,  317 => 107,  313 => 106,  302 => 97,  295 => 95,  293 => 94,  283 => 89,  279 => 88,  275 => 87,  270 => 85,  266 => 84,  262 => 83,  258 => 82,  254 => 81,  251 => 80,  246 => 79,  228 => 63,  226 => 62,  225 => 61,  224 => 60,  223 => 59,  213 => 54,  207 => 53,  196 => 47,  190 => 46,  184 => 45,  178 => 44,  172 => 43,  161 => 37,  155 => 36,  146 => 30,  140 => 27,  136 => 26,  132 => 25,  128 => 24,  117 => 16,  113 => 15,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Admin Menu - Gamification{% endblock %}

{% block body %}
    {{ include('admin/_operations.html.twig', {active: 'gamification'}) }}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Admin Menu</p>
            <h1>Gamification control center</h1>
            <p class=\"muted\">Manage quests and user progression from the centralized admin hub.</p>
        </div>
        <div class=\"actions\">
            <a class=\"btn\" href=\"{{ path('app_user_index') }}\">User management</a>
            <a class=\"btn btn-primary\" href=\"{{ path('app_admin_gamification_quest_new') }}\">Create quest</a>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <h2>Quests</h2>
        </div>
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path('app_admin_gamification_index') }}\">
            <input type=\"hidden\" name=\"userQ\" value=\"{{ userFilters.q }}\">
            <input type=\"hidden\" name=\"userSort\" value=\"{{ userFilters.sort }}\">
            <input type=\"hidden\" name=\"userDirection\" value=\"{{ userFilters.direction }}\">
            <div>
                <label for=\"quest-q\">Search quest</label>
                <input id=\"quest-q\" type=\"search\" name=\"questQ\" value=\"{{ questFilters.q }}\" placeholder=\"title, description or id\">
            </div>
            <div>
                <label for=\"quest-status\">Status</label>
                <select id=\"quest-status\" name=\"questStatus\">
                    <option value=\"\">All</option>
                    <option value=\"active\" {% if questFilters.status == 'active' %}selected{% endif %}>Active</option>
                    <option value=\"inactive\" {% if questFilters.status == 'inactive' %}selected{% endif %}>Inactive</option>
                </select>
            </div>
            <div>
                <label for=\"quest-sort\">Sort</label>
                <select id=\"quest-sort\" name=\"questSort\">
                    <option value=\"updatedAt\" {% if questFilters.sort == 'updatedAt' %}selected{% endif %}>Updated</option>
                    <option value=\"createdAt\" {% if questFilters.sort == 'createdAt' %}selected{% endif %}>Created</option>
                    <option value=\"title\" {% if questFilters.sort == 'title' %}selected{% endif %}>Title</option>
                    <option value=\"goal\" {% if questFilters.sort == 'goal' %}selected{% endif %}>Goal</option>
                    <option value=\"rewardXp\" {% if questFilters.sort == 'rewardXp' %}selected{% endif %}>Reward XP</option>
                </select>
            </div>
            <div>
                <label for=\"quest-direction\">Direction</label>
                <select id=\"quest-direction\" name=\"questDirection\">
                    <option value=\"DESC\" {% if questFilters.direction == 'DESC' %}selected{% endif %}>Descending</option>
                    <option value=\"ASC\" {% if questFilters.direction == 'ASC' %}selected{% endif %}>Ascending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path('app_admin_gamification_index', {
                    userQ: userFilters.q,
                    userSort: userFilters.sort,
                    userDirection: userFilters.direction
                }) }}\">Reset</a>
            </div>
        </form>
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Goal</th>
                        <th>Reward XP</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                {% for quest in quests %}
                    <tr>
                        <td>{{ quest.title }}</td>
                        <td>{{ quest.goal }}</td>
                        <td>{{ quest.rewardXp }}</td>
                        <td><span class=\"pill\">{{ quest.isActive ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ quest.updatedAt ? quest.updatedAt|date('Y-m-d H:i') : '—' }}</td>
                        <td class=\"row-actions\">
                            <a class=\"btn btn-sm\" href=\"{{ path('app_admin_gamification_quest_edit', {id: quest.id}) }}\">Edit</a>
                            <form class=\"inline-form\" method=\"post\" action=\"{{ path('app_admin_gamification_quest_delete', {id: quest.id}) }}\" onsubmit=\"return confirm('Delete this quest?');\">
                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete_quest' ~ quest.id) }}\">
                                <button class=\"btn btn-sm btn-danger\" type=\"submit\">Delete</button>
                            </form>
                        </td>
                    </tr>
                {% else %}
                    <tr><td colspan=\"6\" class=\"empty-state\">No quests found.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <h2>User gamification stats</h2>
        </div>
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path('app_admin_gamification_index') }}\">
            <input type=\"hidden\" name=\"questQ\" value=\"{{ questFilters.q }}\">
            <input type=\"hidden\" name=\"questStatus\" value=\"{{ questFilters.status }}\">
            <input type=\"hidden\" name=\"questSort\" value=\"{{ questFilters.sort }}\">
            <input type=\"hidden\" name=\"questDirection\" value=\"{{ questFilters.direction }}\">
            <div>
                <label for=\"user-q\">Search user</label>
                <input id=\"user-q\" type=\"search\" name=\"userQ\" value=\"{{ userFilters.q }}\" placeholder=\"username, email or id\">
            </div>
            <div>
                <label for=\"user-sort\">Sort</label>
                <select id=\"user-sort\" name=\"userSort\">
                    <option value=\"username\" {% if userFilters.sort == 'username' %}selected{% endif %}>Username</option>
                    <option value=\"email\" {% if userFilters.sort == 'email' %}selected{% endif %}>Email</option>
                    <option value=\"xp\" {% if userFilters.sort == 'xp' %}selected{% endif %}>XP</option>
                    <option value=\"level\" {% if userFilters.sort == 'level' %}selected{% endif %}>Level</option>
                    <option value=\"streak\" {% if userFilters.sort == 'streak' %}selected{% endif %}>Streak</option>
                    <option value=\"updatedAt\" {% if userFilters.sort == 'updatedAt' %}selected{% endif %}>Updated</option>
                </select>
            </div>
            <div>
                <label for=\"user-direction\">Direction</label>
                <select id=\"user-direction\" name=\"userDirection\">
                    <option value=\"ASC\" {% if userFilters.direction == 'ASC' %}selected{% endif %}>Ascending</option>
                    <option value=\"DESC\" {% if userFilters.direction == 'DESC' %}selected{% endif %}>Descending</option>
                </select>
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path('app_admin_gamification_index', {
                    questQ: questFilters.q,
                    questStatus: questFilters.status,
                    questSort: questFilters.sort,
                    questDirection: questFilters.direction
                }) }}\">Reset</a>
            </div>
        </form>
        <div class=\"table-wrap\">
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>XP</th>
                        <th>Level</th>
                        <th>Streak</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                {% for user in users %}
                    <tr>
                        <td>{{ user.username }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.xp }}</td>
                        <td>{{ user.level }}</td>
                        <td>{{ user.streak }}</td>
                        <td>
                            <a class=\"btn btn-sm\" href=\"{{ path('app_admin_gamification_user_edit', {id: user.id}) }}\">Edit stats</a>
                        </td>
                    </tr>
                {% else %}
                    <tr><td colspan=\"6\" class=\"empty-state\">No users found.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </section>
{% endblock %}
", "admin_gamification/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\admin_gamification\\index.html.twig");
    }
}
