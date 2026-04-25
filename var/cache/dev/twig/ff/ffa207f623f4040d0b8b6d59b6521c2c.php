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

/* home/index.html.twig */
class __TwigTemplate_bd28a7f6e1727b90852ac5b10c9ef042 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "home/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "home/index.html.twig"));

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

        yield "TravelXP - Home";
        
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
        yield "    <section class=\"hero glass-card hero-card\">
        <div class=\"hero-main\">
            <p class=\"eyebrow\">Front Office</p>
            <h1>TravelXP Home</h1>
            ";
        // line 10
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 10, $this->source); })()), "user", [], "any", false, false, false, 10)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 11
            yield "                <p class=\"lead\">Welcome back, ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 11, $this->source); })()), "user", [], "any", false, false, false, 11), "username", [], "any", false, false, false, 11), "html", null, true);
            yield ". Plan your stays, compare offers, add services, and book upcoming trips from one place.</p>
                <div class=\"actions\">
                    <a class=\"btn btn-primary\" href=\"";
            // line 13
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("property_index");
            yield "\">Book a Property</a>
                    <a class=\"btn\" href=\"";
            // line 14
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("offer_index");
            yield "\">Check Offers</a>
                    <a class=\"btn\" href=\"";
            // line 15
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("service_index");
            yield "\">Check Services</a>
                    <a class=\"btn\" href=\"";
            // line 16
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_index");
            yield "\">Book a Trip</a>
                </div>
            ";
        } else {
            // line 19
            yield "                <p class=\"lead\">
                    Sign in to book properties, discover offers, join trips, and track your travel progress.
                </p>
                <div class=\"actions\">
                    <a class=\"btn btn-primary\" href=\"";
            // line 23
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\">Sign in</a>
                    <a class=\"btn\" href=\"";
            // line 24
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_register");
            yield "\">Create account</a>
                </div>
            ";
        }
        // line 27
        yield "        </div>
        <div class=\"hero-side\">
            <div class=\"mini-stat\">
                <span>Current rank</span>
                <strong>";
        // line 31
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 31, $this->source); })()), "rank", [], "any", false, false, false, 31), "html", null, true);
        yield "</strong>
            </div>
            <div class=\"mini-stat\">
                <span>Daily streak</span>
                <strong>";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 35, $this->source); })()), "streak", [], "any", false, false, false, 35), "html", null, true);
        yield " days</strong>
            </div>
            <div class=\"mini-stat\">
                <span>Level</span>
                <strong>";
        // line 39
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 39, $this->source); })()), "level", [], "any", false, false, false, 39), "html", null, true);
        yield "</strong>
            </div>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <div>
                <p class=\"eyebrow\">Gamification</p>
                <h2>XP, levels, streak & quests</h2>
            </div>
            <span class=\"pill\">";
        // line 50
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 50, $this->source); })()), "rank", [], "any", false, false, false, 50), "html", null, true);
        yield "</span>
        </div>

        <div class=\"gamification-grid\">
            <article class=\"stat-card\">
                <h3>Level</h3>
                <p class=\"stat-number\">";
        // line 56
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 56, $this->source); })()), "level", [], "any", false, false, false, 56), "html", null, true);
        yield "</p>
            </article>
            <article class=\"stat-card\">
                <h3>Total XP</h3>
                <p class=\"stat-number\">";
        // line 60
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 60, $this->source); })()), "xp", [], "any", false, false, false, 60), "html", null, true);
        yield "</p>
            </article>
            <article class=\"stat-card\">
                <h3>Daily streak</h3>
                <p class=\"stat-number\">";
        // line 64
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 64, $this->source); })()), "streak", [], "any", false, false, false, 64), "html", null, true);
        yield " days</p>
            </article>
            <article class=\"stat-card\">
                <h3>Next level at</h3>
                <p class=\"stat-number\">";
        // line 68
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 68, $this->source); })()), "nextLevelXp", [], "any", false, false, false, 68), "html", null, true);
        yield " XP</p>
            </article>
        </div>

        <div class=\"xp-track\">
            <div class=\"xp-track-head\">
                <span>Level progress</span>
                <span>";
        // line 75
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 75, $this->source); })()), "progressPercent", [], "any", false, false, false, 75), "html", null, true);
        yield "%</span>
            </div>
            <div class=\"xp-bar\">
                <span style=\"width: ";
        // line 78
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 78, $this->source); })()), "progressPercent", [], "any", false, false, false, 78), "html", null, true);
        yield "%\"></span>
            </div>
        </div>

        <div class=\"quests-grid\">
            ";
        // line 83
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["gamification"]) || array_key_exists("gamification", $context) ? $context["gamification"] : (function () { throw new RuntimeError('Variable "gamification" does not exist.', 83, $this->source); })()), "quests", [], "any", false, false, false, 83));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["quest"]) {
            // line 84
            yield "                ";
            $context["percent"] = Twig\Extension\CoreExtension::round(((CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "progress", [], "any", false, false, false, 84) / CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "goal", [], "any", false, false, false, 84)) * 100), 0, "floor");
            // line 85
            yield "                <article class=\"quest-card\">
                    <header>
                        <h3>";
            // line 87
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "title", [], "any", false, false, false, 87), "html", null, true);
            yield "</h3>
                        <span class=\"pill\">";
            // line 88
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "status", [], "any", false, false, false, 88)), "html", null, true);
            yield "</span>
                    </header>
                    <p class=\"help-text\">Reward: ";
            // line 90
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "reward", [], "any", false, false, false, 90), "html", null, true);
            yield "</p>
                    <div class=\"xp-bar quest-bar\">
                        <span style=\"width: ";
            // line 92
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["percent"]) || array_key_exists("percent", $context) ? $context["percent"] : (function () { throw new RuntimeError('Variable "percent" does not exist.', 92, $this->source); })()), "html", null, true);
            yield "%\"></span>
                    </div>
                    <p class=\"muted\">";
            // line 94
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "progress", [], "any", false, false, false, 94), "html", null, true);
            yield "/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["quest"], "goal", [], "any", false, false, false, 94), "html", null, true);
            yield " completed</p>
                </article>
            ";
            $context['_iterated'] = true;
        }
        // line 96
        if (!$context['_iterated']) {
            // line 97
            yield "                <article class=\"quest-card\">
                    <header><h3>No active quests</h3></header>
                    <p class=\"muted\">Check back soon for new challenges.</p>
                </article>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['quest'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 102
        yield "        </div>
    </section>

    <section class=\"quick-actions-grid\">
        ";
        // line 106
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 106, $this->source); })()), "user", [], "any", false, false, false, 106)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 107
            yield "            <a class=\"glass-card action-card\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_profile_show");
            yield "\">
                <h3>Account center</h3>
                <p class=\"muted\">Manage your account data, password, and profile image.</p>
            </a>
        ";
        }
        // line 112
        yield "        <a class=\"glass-card action-card\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("property_index");
        yield "\">
            <h3>Property catalog</h3>
            <p class=\"muted\">Browse listings and rent through booking. Property CRUD is handled in Admin Menu.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"";
        // line 116
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("offer_index");
        yield "\">
            <h3>Offer center</h3>
            <p class=\"muted\">See available property discounts and use them automatically when booking.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"";
        // line 120
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("service_index");
        yield "\">
            <h3>Service catalog</h3>
            <p class=\"muted\">Explore available add-on services for your travel reservations.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"";
        // line 124
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("booking_index");
        yield "\">
            <h3>Booking desk</h3>
            <p class=\"muted\">Create bookings with auto-calculated totals and track your booking status.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"";
        // line 128
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_index");
        yield "\">
            <h3>Trips</h3>
            <p class=\"muted\">Browse available trips and join your preferred plan.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"";
        // line 132
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_index");
        yield "\">
            <h3>Activities</h3>
            <p class=\"muted\">Join activities from your selected trips and track participation.</p>
        </a>
        ";
        // line 136
        if ((($tmp =  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 136, $this->source); })()), "user", [], "any", false, false, false, 136)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 137
            yield "            <a class=\"glass-card action-card\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\">
                <h3>Protected login</h3>
                <p class=\"muted\">Role-aware access control with validated profile workflows.</p>
            </a>
            <a class=\"glass-card action-card\" href=\"";
            // line 141
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_register");
            yield "\">
                <h3>Fast registration</h3>
                <p class=\"muted\">Create your account with server-side validation and clean UX.</p>
            </a>
        ";
        }
        // line 146
        yield "    </section>
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
        return "home/index.html.twig";
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
        return array (  366 => 146,  358 => 141,  350 => 137,  348 => 136,  341 => 132,  334 => 128,  327 => 124,  320 => 120,  313 => 116,  305 => 112,  296 => 107,  294 => 106,  288 => 102,  278 => 97,  276 => 96,  267 => 94,  262 => 92,  257 => 90,  252 => 88,  248 => 87,  244 => 85,  241 => 84,  236 => 83,  228 => 78,  222 => 75,  212 => 68,  205 => 64,  198 => 60,  191 => 56,  182 => 50,  168 => 39,  161 => 35,  154 => 31,  148 => 27,  142 => 24,  138 => 23,  132 => 19,  126 => 16,  122 => 15,  118 => 14,  114 => 13,  108 => 11,  106 => 10,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}TravelXP - Home{% endblock %}

{% block body %}
    <section class=\"hero glass-card hero-card\">
        <div class=\"hero-main\">
            <p class=\"eyebrow\">Front Office</p>
            <h1>TravelXP Home</h1>
            {% if app.user %}
                <p class=\"lead\">Welcome back, {{ app.user.username }}. Plan your stays, compare offers, add services, and book upcoming trips from one place.</p>
                <div class=\"actions\">
                    <a class=\"btn btn-primary\" href=\"{{ path('property_index') }}\">Book a Property</a>
                    <a class=\"btn\" href=\"{{ path('offer_index') }}\">Check Offers</a>
                    <a class=\"btn\" href=\"{{ path('service_index') }}\">Check Services</a>
                    <a class=\"btn\" href=\"{{ path('trip_index') }}\">Book a Trip</a>
                </div>
            {% else %}
                <p class=\"lead\">
                    Sign in to book properties, discover offers, join trips, and track your travel progress.
                </p>
                <div class=\"actions\">
                    <a class=\"btn btn-primary\" href=\"{{ path('app_login') }}\">Sign in</a>
                    <a class=\"btn\" href=\"{{ path('app_register') }}\">Create account</a>
                </div>
            {% endif %}
        </div>
        <div class=\"hero-side\">
            <div class=\"mini-stat\">
                <span>Current rank</span>
                <strong>{{ gamification.rank }}</strong>
            </div>
            <div class=\"mini-stat\">
                <span>Daily streak</span>
                <strong>{{ gamification.streak }} days</strong>
            </div>
            <div class=\"mini-stat\">
                <span>Level</span>
                <strong>{{ gamification.level }}</strong>
            </div>
        </div>
    </section>

    <section class=\"glass-card\">
        <div class=\"panel-head\">
            <div>
                <p class=\"eyebrow\">Gamification</p>
                <h2>XP, levels, streak & quests</h2>
            </div>
            <span class=\"pill\">{{ gamification.rank }}</span>
        </div>

        <div class=\"gamification-grid\">
            <article class=\"stat-card\">
                <h3>Level</h3>
                <p class=\"stat-number\">{{ gamification.level }}</p>
            </article>
            <article class=\"stat-card\">
                <h3>Total XP</h3>
                <p class=\"stat-number\">{{ gamification.xp }}</p>
            </article>
            <article class=\"stat-card\">
                <h3>Daily streak</h3>
                <p class=\"stat-number\">{{ gamification.streak }} days</p>
            </article>
            <article class=\"stat-card\">
                <h3>Next level at</h3>
                <p class=\"stat-number\">{{ gamification.nextLevelXp }} XP</p>
            </article>
        </div>

        <div class=\"xp-track\">
            <div class=\"xp-track-head\">
                <span>Level progress</span>
                <span>{{ gamification.progressPercent }}%</span>
            </div>
            <div class=\"xp-bar\">
                <span style=\"width: {{ gamification.progressPercent }}%\"></span>
            </div>
        </div>

        <div class=\"quests-grid\">
            {% for quest in gamification.quests %}
                {% set percent = ((quest.progress / quest.goal) * 100)|round(0, 'floor') %}
                <article class=\"quest-card\">
                    <header>
                        <h3>{{ quest.title }}</h3>
                        <span class=\"pill\">{{ quest.status|capitalize }}</span>
                    </header>
                    <p class=\"help-text\">Reward: {{ quest.reward }}</p>
                    <div class=\"xp-bar quest-bar\">
                        <span style=\"width: {{ percent }}%\"></span>
                    </div>
                    <p class=\"muted\">{{ quest.progress }}/{{ quest.goal }} completed</p>
                </article>
            {% else %}
                <article class=\"quest-card\">
                    <header><h3>No active quests</h3></header>
                    <p class=\"muted\">Check back soon for new challenges.</p>
                </article>
            {% endfor %}
        </div>
    </section>

    <section class=\"quick-actions-grid\">
        {% if app.user %}
            <a class=\"glass-card action-card\" href=\"{{ path('app_profile_show') }}\">
                <h3>Account center</h3>
                <p class=\"muted\">Manage your account data, password, and profile image.</p>
            </a>
        {% endif %}
        <a class=\"glass-card action-card\" href=\"{{ path('property_index') }}\">
            <h3>Property catalog</h3>
            <p class=\"muted\">Browse listings and rent through booking. Property CRUD is handled in Admin Menu.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"{{ path('offer_index') }}\">
            <h3>Offer center</h3>
            <p class=\"muted\">See available property discounts and use them automatically when booking.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"{{ path('service_index') }}\">
            <h3>Service catalog</h3>
            <p class=\"muted\">Explore available add-on services for your travel reservations.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"{{ path('booking_index') }}\">
            <h3>Booking desk</h3>
            <p class=\"muted\">Create bookings with auto-calculated totals and track your booking status.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"{{ path('trip_index') }}\">
            <h3>Trips</h3>
            <p class=\"muted\">Browse available trips and join your preferred plan.</p>
        </a>
        <a class=\"glass-card action-card\" href=\"{{ path('activity_index') }}\">
            <h3>Activities</h3>
            <p class=\"muted\">Join activities from your selected trips and track participation.</p>
        </a>
        {% if not app.user %}
            <a class=\"glass-card action-card\" href=\"{{ path('app_login') }}\">
                <h3>Protected login</h3>
                <p class=\"muted\">Role-aware access control with validated profile workflows.</p>
            </a>
            <a class=\"glass-card action-card\" href=\"{{ path('app_register') }}\">
                <h3>Fast registration</h3>
                <p class=\"muted\">Create your account with server-side validation and clean UX.</p>
            </a>
        {% endif %}
    </section>
{% endblock %}
", "home/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\home\\index.html.twig");
    }
}
