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

/* base.html.twig */
class __TwigTemplate_29c9095116a40d5f6bf75dbfcc51843c extends Template
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

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'javascripts' => [$this, 'block_javascripts'],
            'importmap' => [$this, 'block_importmap'],
            'body_class' => [$this, 'block_body_class'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
        <link rel=\"icon\" href=\"";
        // line 7
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("icon.png"), "html", null, true);
        yield "\">
        <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
        <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
        <link href=\"https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap\" rel=\"stylesheet\">
        <script>
            (() => {
                const key = 'travelxp-theme';
                const cookieName = 'travelxp-theme=';
                let theme = null;

                try {
                    theme = window.localStorage.getItem(key);
                } catch {
                    theme = null;
                }

                if (theme !== 'light' && theme !== 'dark') {
                    const cookieEntry = document.cookie.split(';').map((entry) => entry.trim()).find((entry) => entry.startsWith(cookieName));
                    if (cookieEntry) {
                        theme = decodeURIComponent(cookieEntry.substring(cookieName.length));
                    }
                }

                if (theme !== 'light' && theme !== 'dark') {
                    theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                }

                const isLight = theme === 'light';
                document.documentElement.classList.toggle('light-theme', isLight);
                document.documentElement.dataset.theme = theme;
            })();
        </script>
        ";
        // line 39
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 42
        yield "
        ";
        // line 43
        $context["frankenphpHotReload"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 43, $this->source); })()), "request", [], "any", false, false, false, 43), "server", [], "any", false, false, false, 43), "get", ["FRANKENPHP_HOT_RELOAD"], "method", false, false, false, 43);
        // line 44
        yield "        ";
        if ((($tmp = (isset($context["frankenphpHotReload"]) || array_key_exists("frankenphpHotReload", $context) ? $context["frankenphpHotReload"] : (function () { throw new RuntimeError('Variable "frankenphpHotReload" does not exist.', 44, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 45
            yield "        <meta name=\"frankenphp-hot-reload:url\" content=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["frankenphpHotReload"]) || array_key_exists("frankenphpHotReload", $context) ? $context["frankenphpHotReload"] : (function () { throw new RuntimeError('Variable "frankenphpHotReload" does not exist.', 45, $this->source); })()), "html", null, true);
            yield "\">
        <script src=\"https://cdn.jsdelivr.net/npm/idiomorph\"></script>
        <script src=\"https://cdn.jsdelivr.net/npm/frankenphp-hot-reload/+esm\" type=\"module\"></script>
        ";
        }
        // line 49
        yield "    </head>
    <body class=\"";
        // line 50
        yield from $this->unwrap()->yieldBlock('body_class', $context, $blocks);
        yield "\">
        <script>
            document.body.classList.toggle('light-theme', document.documentElement.classList.contains('light-theme'));
            document.body.dataset.theme = document.documentElement.dataset.theme || 'dark';
        </script>
        <canvas id=\"bg-canvas\" class=\"bg-canvas\"></canvas>
        <div class=\"bg-grid\"></div>
        <div class=\"bg-glow bg-glow-a\"></div>
        <div class=\"bg-glow bg-glow-b\"></div>
        <div class=\"bg-glow bg-glow-c\"></div>

        <header class=\"topbar\">
            ";
        // line 62
        $context["currentRoute"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 62, $this->source); })()), "request", [], "any", false, false, false, 62), "attributes", [], "any", false, false, false, 62), "get", ["_route"], "method", false, false, false, 62);
        // line 63
        yield "            <a class=\"brand\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        yield "\">
                <span class=\"brand-mark\">
                    <img src=\"";
        // line 65
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("icon.png"), "html", null, true);
        yield "\" alt=\"TravelXP\">
                </span>
                <span>TravelXP</span>
            </a>
            <nav class=\"topnav\">
                <div class=\"topnav-links\">
                    <a class=\"";
        // line 71
        yield ((((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 71, $this->source); })()) == "app_home")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        yield "\">Home</a>
                    <a class=\"";
        // line 72
        yield (((is_string($_v0 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 72, $this->source); })())) && is_string($_v1 = "property_") && str_starts_with($_v0, $_v1))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("property_index");
        yield "\">Properties</a>
                    <a class=\"";
        // line 73
        yield (((is_string($_v2 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 73, $this->source); })())) && is_string($_v3 = "offer_") && str_starts_with($_v2, $_v3))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("offer_index");
        yield "\">Offers</a>
                    <a class=\"";
        // line 74
        yield (((is_string($_v4 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 74, $this->source); })())) && is_string($_v5 = "service_") && str_starts_with($_v4, $_v5))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("service_index");
        yield "\">Services</a>
                    <a class=\"";
        // line 75
        yield (((is_string($_v6 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 75, $this->source); })())) && is_string($_v7 = "booking_") && str_starts_with($_v6, $_v7))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("booking_index");
        yield "\">Bookings</a>
                    <a class=\"";
        // line 76
        yield (((is_string($_v8 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 76, $this->source); })())) && is_string($_v9 = "trip_") && str_starts_with($_v8, $_v9))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("trip_index");
        yield "\">Trips</a>
                    <a class=\"";
        // line 77
        yield (((is_string($_v10 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 77, $this->source); })())) && is_string($_v11 = "activity_") && str_starts_with($_v10, $_v11))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("activity_index");
        yield "\">Activities</a>
                    <a class=\"";
        // line 78
        yield ((((is_string($_v12 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 78, $this->source); })())) && is_string($_v13 = "blog_") && str_starts_with($_v12, $_v13)) || (is_string($_v14 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 78, $this->source); })())) && is_string($_v15 = "comment_") && str_starts_with($_v14, $_v15)))) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_index");
        yield "\">Blogs</a>
                    ";
        // line 79
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 79, $this->source); })()), "user", [], "any", false, false, false, 79)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 80
            yield "                        ";
            if (CoreExtension::inFilter("ROLE_ADMIN", CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 80, $this->source); })()), "user", [], "any", false, false, false, 80), "roles", [], "any", false, false, false, 80))) {
                // line 81
                yield "                            <a class=\"";
                yield ((((((((((is_string($_v16 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v17 = "app_user_") && str_starts_with($_v16, $_v17)) || (is_string($_v18 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v19 = "app_admin_gamification_") && str_starts_with($_v18, $_v19))) || (is_string($_v20 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v21 = "admin_property_") && str_starts_with($_v20, $_v21))) || (is_string($_v22 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v23 = "admin_offer_") && str_starts_with($_v22, $_v23))) || (is_string($_v24 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v25 = "admin_service_") && str_starts_with($_v24, $_v25))) || (is_string($_v26 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v27 = "admin_booking_") && str_starts_with($_v26, $_v27))) || (is_string($_v28 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v29 = "admin_trip_") && str_starts_with($_v28, $_v29))) || (is_string($_v30 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 81, $this->source); })())) && is_string($_v31 = "admin_activity_") && str_starts_with($_v30, $_v31)))) ? ("active") : (""));
                yield "\" href=\"";
                yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_index");
                yield "\">Admin Menu</a>
                        ";
            }
            // line 83
            yield "                        <a class=\"";
            yield (((is_string($_v32 = (isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 83, $this->source); })())) && is_string($_v33 = "app_profile_") && str_starts_with($_v32, $_v33))) ? ("active") : (""));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_profile_show");
            yield "\">Profile</a>
                        <a href=\"";
            // line 84
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_logout");
            yield "\">Logout</a>
                    ";
        } else {
            // line 86
            yield "                        <a class=\"";
            yield ((((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 86, $this->source); })()) == "app_login")) ? ("active") : (""));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\">Login</a>
                        <a class=\"btn-link ";
            // line 87
            yield ((((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 87, $this->source); })()) == "app_register")) ? ("active") : (""));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_register");
            yield "\">Create account</a>
                    ";
        }
        // line 89
        yield "                </div>
                <div class=\"topnav-actions\">
                    <button id=\"theme-toggle\" class=\"theme-toggle\" type=\"button\" aria-label=\"Toggle dark and light theme\" aria-pressed=\"false\">🌙</button>
                </div>
            </nav>
        </header>

        <main class=\"page-shell\">
            ";
        // line 97
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 97, $this->source); })()), "flashes", [], "any", false, false, false, 97));
        foreach ($context['_seq'] as $context["label"] => $context["messages"]) {
            // line 98
            yield "                ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable($context["messages"]);
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 99
                yield "                    <div class=\"flash flash-";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["label"], "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                yield "</div>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 101
            yield "            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['label'], $context['messages'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 102
        yield "            ";
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 103
        yield "        </main>
    </body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 6
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

        yield "TravelXP";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 39
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 40
        yield "            ";
        yield from $this->unwrap()->yieldBlock('importmap', $context, $blocks);
        // line 41
        yield "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 40
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_importmap(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\ImportMapRuntime')->importmap("app");
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 50
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body_class(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body_class"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body_class"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 102
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

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.html.twig";
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
        return array (  387 => 102,  365 => 50,  342 => 40,  331 => 41,  328 => 40,  315 => 39,  292 => 6,  278 => 103,  275 => 102,  269 => 101,  258 => 99,  253 => 98,  249 => 97,  239 => 89,  232 => 87,  225 => 86,  220 => 84,  213 => 83,  205 => 81,  202 => 80,  200 => 79,  194 => 78,  188 => 77,  182 => 76,  176 => 75,  170 => 74,  164 => 73,  158 => 72,  152 => 71,  143 => 65,  137 => 63,  135 => 62,  120 => 50,  117 => 49,  109 => 45,  106 => 44,  104 => 43,  101 => 42,  99 => 39,  64 => 7,  60 => 6,  53 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>{% block title %}TravelXP{% endblock %}</title>
        <link rel=\"icon\" href=\"{{ asset('icon.png') }}\">
        <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
        <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
        <link href=\"https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap\" rel=\"stylesheet\">
        <script>
            (() => {
                const key = 'travelxp-theme';
                const cookieName = 'travelxp-theme=';
                let theme = null;

                try {
                    theme = window.localStorage.getItem(key);
                } catch {
                    theme = null;
                }

                if (theme !== 'light' && theme !== 'dark') {
                    const cookieEntry = document.cookie.split(';').map((entry) => entry.trim()).find((entry) => entry.startsWith(cookieName));
                    if (cookieEntry) {
                        theme = decodeURIComponent(cookieEntry.substring(cookieName.length));
                    }
                }

                if (theme !== 'light' && theme !== 'dark') {
                    theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                }

                const isLight = theme === 'light';
                document.documentElement.classList.toggle('light-theme', isLight);
                document.documentElement.dataset.theme = theme;
            })();
        </script>
        {% block javascripts %}
            {% block importmap %}{{ importmap('app') }}{% endblock %}
        {% endblock %}

        {% set frankenphpHotReload = app.request.server.get('FRANKENPHP_HOT_RELOAD') %}
        {% if frankenphpHotReload %}
        <meta name=\"frankenphp-hot-reload:url\" content=\"{{ frankenphpHotReload }}\">
        <script src=\"https://cdn.jsdelivr.net/npm/idiomorph\"></script>
        <script src=\"https://cdn.jsdelivr.net/npm/frankenphp-hot-reload/+esm\" type=\"module\"></script>
        {% endif %}
    </head>
    <body class=\"{% block body_class %}{% endblock %}\">
        <script>
            document.body.classList.toggle('light-theme', document.documentElement.classList.contains('light-theme'));
            document.body.dataset.theme = document.documentElement.dataset.theme || 'dark';
        </script>
        <canvas id=\"bg-canvas\" class=\"bg-canvas\"></canvas>
        <div class=\"bg-grid\"></div>
        <div class=\"bg-glow bg-glow-a\"></div>
        <div class=\"bg-glow bg-glow-b\"></div>
        <div class=\"bg-glow bg-glow-c\"></div>

        <header class=\"topbar\">
            {% set currentRoute = app.request.attributes.get('_route') %}
            <a class=\"brand\" href=\"{{ path('app_home') }}\">
                <span class=\"brand-mark\">
                    <img src=\"{{ asset('icon.png') }}\" alt=\"TravelXP\">
                </span>
                <span>TravelXP</span>
            </a>
            <nav class=\"topnav\">
                <div class=\"topnav-links\">
                    <a class=\"{{ currentRoute == 'app_home' ? 'active' : '' }}\" href=\"{{ path('app_home') }}\">Home</a>
                    <a class=\"{{ currentRoute starts with 'property_' ? 'active' : '' }}\" href=\"{{ path('property_index') }}\">Properties</a>
                    <a class=\"{{ currentRoute starts with 'offer_' ? 'active' : '' }}\" href=\"{{ path('offer_index') }}\">Offers</a>
                    <a class=\"{{ currentRoute starts with 'service_' ? 'active' : '' }}\" href=\"{{ path('service_index') }}\">Services</a>
                    <a class=\"{{ currentRoute starts with 'booking_' ? 'active' : '' }}\" href=\"{{ path('booking_index') }}\">Bookings</a>
                    <a class=\"{{ currentRoute starts with 'trip_' ? 'active' : '' }}\" href=\"{{ path('trip_index') }}\">Trips</a>
                    <a class=\"{{ currentRoute starts with 'activity_' ? 'active' : '' }}\" href=\"{{ path('activity_index') }}\">Activities</a>
                    <a class=\"{{ currentRoute starts with 'blog_' or currentRoute starts with 'comment_' ? 'active' : '' }}\" href=\"{{ path('blog_index') }}\">Blogs</a>
                    {% if app.user %}
                        {% if 'ROLE_ADMIN' in app.user.roles %}
                            <a class=\"{{ currentRoute starts with 'app_user_' or currentRoute starts with 'app_admin_gamification_' or currentRoute starts with 'admin_property_' or currentRoute starts with 'admin_offer_' or currentRoute starts with 'admin_service_' or currentRoute starts with 'admin_booking_' or currentRoute starts with 'admin_trip_' or currentRoute starts with 'admin_activity_' ? 'active' : '' }}\" href=\"{{ path('app_user_index') }}\">Admin Menu</a>
                        {% endif %}
                        <a class=\"{{ currentRoute starts with 'app_profile_' ? 'active' : '' }}\" href=\"{{ path('app_profile_show') }}\">Profile</a>
                        <a href=\"{{ path('app_logout') }}\">Logout</a>
                    {% else %}
                        <a class=\"{{ currentRoute == 'app_login' ? 'active' : '' }}\" href=\"{{ path('app_login') }}\">Login</a>
                        <a class=\"btn-link {{ currentRoute == 'app_register' ? 'active' : '' }}\" href=\"{{ path('app_register') }}\">Create account</a>
                    {% endif %}
                </div>
                <div class=\"topnav-actions\">
                    <button id=\"theme-toggle\" class=\"theme-toggle\" type=\"button\" aria-label=\"Toggle dark and light theme\" aria-pressed=\"false\">🌙</button>
                </div>
            </nav>
        </header>

        <main class=\"page-shell\">
            {% for label, messages in app.flashes %}
                {% for message in messages %}
                    <div class=\"flash flash-{{ label }}\">{{ message }}</div>
                {% endfor %}
            {% endfor %}
            {% block body %}{% endblock %}
        </main>
    </body>
</html>
", "base.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\base.html.twig");
    }
}
