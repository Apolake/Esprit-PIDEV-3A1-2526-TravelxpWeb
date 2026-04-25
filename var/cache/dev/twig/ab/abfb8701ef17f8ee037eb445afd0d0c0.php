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

/* blog/index.html.twig */
class __TwigTemplate_ad1ed4ee289e1c62143d0bb1ce4fc2cb extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/index.html.twig"));

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

        yield "Blogs";
        
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
        yield "    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Community</p>
            <h1>Blogs & feedback</h1>
            <p class=\"muted\">Read travel stories, leave feedback, and react with likes or dislikes.</p>
        </div>
        <div class=\"actions\">
            ";
        // line 13
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 13, $this->source); })()), "user", [], "any", false, false, false, 13)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 14
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_new");
            yield "\">Write a blog</a>
            ";
        } else {
            // line 16
            yield "                <a class=\"btn\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\">Login to post</a>
            ";
        }
        // line 18
        yield "        </div>
    </section>

    <section class=\"glass-card blog-filters-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 22
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_index");
        yield "\">
            <div>
                <label for=\"blog-q\">Search</label>
                <div class=\"live-search-wrap\">
                    <input
                        id=\"blog-q\"
                        type=\"search\"
                        name=\"q\"
                        value=\"";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 30, $this->source); })()), "q", [], "any", false, false, false, 30), "html", null, true);
        yield "\"
                        placeholder=\"title, content or author\"
                        autocomplete=\"off\"
                        data-live-search=\"true\"
                        data-live-search-url=\"";
        // line 34
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_suggest");
        yield "\"
                        data-live-search-target=\"#blog-suggestions\"
                        data-live-search-loading=\"#blog-suggestions-loading\"
                    >
                    <div id=\"blog-suggestions-loading\" class=\"live-loading\" hidden>Loading...</div>
                    <div id=\"blog-suggestions\" class=\"live-suggestions\" hidden></div>
                </div>
            </div>
            <div>
                <label for=\"blog-sort\">Sort</label>
                <select id=\"blog-sort\" name=\"sort\">
                    <option value=\"latest\" ";
        // line 45
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 45, $this->source); })()), "sort", [], "any", false, false, false, 45) == "latest")) {
            yield "selected";
        }
        yield ">Latest first</option>
                    <option value=\"oldest\" ";
        // line 46
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 46, $this->source); })()), "sort", [], "any", false, false, false, 46) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest first</option>
                    <option value=\"most_liked\" ";
        // line 47
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 47, $this->source); })()), "sort", [], "any", false, false, false, 47) == "most_liked")) {
            yield "selected";
        }
        yield ">Most liked</option>
                    <option value=\"least_liked\" ";
        // line 48
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 48, $this->source); })()), "sort", [], "any", false, false, false, 48) == "least_liked")) {
            yield "selected";
        }
        yield ">Least liked</option>
                </select>
            </div>
            <div>
                <label for=\"blog-author\">Author</label>
                <select id=\"blog-author\" name=\"authorId\">
                    <option value=\"\">All authors</option>
                    ";
        // line 55
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["authors"]) || array_key_exists("authors", $context) ? $context["authors"] : (function () { throw new RuntimeError('Variable "authors" does not exist.', 55, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["author"]) {
            // line 56
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "id", [], "any", false, false, false, 56), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 56, $this->source); })()), "authorId", [], "any", false, false, false, 56) == (CoreExtension::getAttribute($this->env, $this->source, $context["author"], "id", [], "any", false, false, false, 56) . ""))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "username", [], "any", false, false, false, 56), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['author'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 58
        yield "                </select>
            </div>
            <div>
                <label for=\"blog-from-date\">From date</label>
                <input id=\"blog-from-date\" type=\"date\" name=\"fromDate\" value=\"";
        // line 62
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 62, $this->source); })()), "fromDate", [], "any", false, false, false, 62), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"blog-to-date\">To date</label>
                <input id=\"blog-to-date\" type=\"date\" name=\"toDate\" value=\"";
        // line 66
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["filters"]) || array_key_exists("filters", $context) ? $context["filters"] : (function () { throw new RuntimeError('Variable "filters" does not exist.', 66, $this->source); })()), "toDate", [], "any", false, false, false, 66), "html", null, true);
        yield "\">
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 70
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_index");
        yield "\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"blog-grid\">
        ";
        // line 76
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["blogs"]) || array_key_exists("blogs", $context) ? $context["blogs"] : (function () { throw new RuntimeError('Variable "blogs" does not exist.', 76, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["blog"]) {
            // line 77
            yield "            <article class=\"glass-card blog-card\">
                ";
            // line 78
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "imageUrl", [], "any", false, false, false, 78)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 79
                yield "                    <a class=\"blog-image-link\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_show", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "id", [], "any", false, false, false, 79)]), "html", null, true);
                yield "\">
                        <img class=\"blog-image\" src=\"";
                // line 80
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "imageUrl", [], "any", false, false, false, 80), "html", null, true);
                yield "\" alt=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "title", [], "any", false, false, false, 80), "html", null, true);
                yield "\">
                    </a>
                ";
            }
            // line 83
            yield "                <div class=\"blog-card-body\">
                    <p class=\"eyebrow\">";
            // line 84
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "author", [], "any", false, false, false, 84)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "author", [], "any", false, false, false, 84), "username", [], "any", false, false, false, 84), "html", null, true)) : ("Unknown user"));
            yield "</p>
                    <h2><a class=\"blog-title-link\" href=\"";
            // line 85
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_show", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "id", [], "any", false, false, false, 85)]), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "title", [], "any", false, false, false, 85), "html", null, true);
            yield "</a></h2>
                    <p class=\"muted\">";
            // line 86
            yield (((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "content", [], "any", false, false, false, 86)) > 180)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "content", [], "any", false, false, false, 86), 0, 180) . "..."), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "content", [], "any", false, false, false, 86), "html", null, true)));
            yield "</p>
                    <div class=\"blog-meta-row\">
                        <span class=\"pill\">";
            // line 88
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "publishedAt", [], "any", false, false, false, 88)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "publishedAt", [], "any", false, false, false, 88), "Y-m-d H:i"), "html", null, true)) : ("—"));
            yield "</span>
                        <span class=\"pill\">";
            // line 89
            yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["readTimes"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "id", [], "any", false, false, false, 89), [], "array", true, true, false, 89) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["readTimes"]) || array_key_exists("readTimes", $context) ? $context["readTimes"] : (function () { throw new RuntimeError('Variable "readTimes" does not exist.', 89, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "id", [], "any", false, false, false, 89), [], "array", false, false, false, 89)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["readTimes"]) || array_key_exists("readTimes", $context) ? $context["readTimes"] : (function () { throw new RuntimeError('Variable "readTimes" does not exist.', 89, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "id", [], "any", false, false, false, 89), [], "array", false, false, false, 89), "html", null, true)) : (1));
            yield " min to read</span>
                        <span class=\"pill\">Comments ";
            // line 90
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "comments", [], "any", false, false, false, 90)), "html", null, true);
            yield "</span>
                        <span class=\"pill\">👍 ";
            // line 91
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "likesCount", [], "any", false, false, false, 91), "html", null, true);
            yield "</span>
                        <span class=\"pill\">👎 ";
            // line 92
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["blog"], "dislikesCount", [], "any", false, false, false, 92), "html", null, true);
            yield "</span>
                    </div>
                </div>
            </article>
        ";
            $context['_iterated'] = true;
        }
        // line 96
        if (!$context['_iterated']) {
            // line 97
            yield "            <section class=\"glass-card\">
                <p class=\"empty-state\">No blog posts found yet.</p>
            </section>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['blog'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 101
        yield "    </section>

    <section class=\"glass-card\">
        ";
        // line 104
        yield Twig\Extension\CoreExtension::include($this->env, $context, "components/_pagination.html.twig", ["pagination" => (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 104, $this->source); })()), "routeName" => "blog_index"]);
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
        return "blog/index.html.twig";
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
        return array (  321 => 104,  316 => 101,  307 => 97,  305 => 96,  296 => 92,  292 => 91,  288 => 90,  284 => 89,  280 => 88,  275 => 86,  269 => 85,  265 => 84,  262 => 83,  254 => 80,  249 => 79,  247 => 78,  244 => 77,  239 => 76,  230 => 70,  223 => 66,  216 => 62,  210 => 58,  195 => 56,  191 => 55,  179 => 48,  173 => 47,  167 => 46,  161 => 45,  147 => 34,  140 => 30,  129 => 22,  123 => 18,  117 => 16,  111 => 14,  109 => 13,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Blogs{% endblock %}

{% block body %}
    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Community</p>
            <h1>Blogs & feedback</h1>
            <p class=\"muted\">Read travel stories, leave feedback, and react with likes or dislikes.</p>
        </div>
        <div class=\"actions\">
            {% if app.user %}
                <a class=\"btn btn-primary\" href=\"{{ path('blog_new') }}\">Write a blog</a>
            {% else %}
                <a class=\"btn\" href=\"{{ path('app_login') }}\">Login to post</a>
            {% endif %}
        </div>
    </section>

    <section class=\"glass-card blog-filters-card\">
        <form class=\"filters-grid\" method=\"get\" action=\"{{ path('blog_index') }}\">
            <div>
                <label for=\"blog-q\">Search</label>
                <div class=\"live-search-wrap\">
                    <input
                        id=\"blog-q\"
                        type=\"search\"
                        name=\"q\"
                        value=\"{{ filters.q }}\"
                        placeholder=\"title, content or author\"
                        autocomplete=\"off\"
                        data-live-search=\"true\"
                        data-live-search-url=\"{{ path('blog_suggest') }}\"
                        data-live-search-target=\"#blog-suggestions\"
                        data-live-search-loading=\"#blog-suggestions-loading\"
                    >
                    <div id=\"blog-suggestions-loading\" class=\"live-loading\" hidden>Loading...</div>
                    <div id=\"blog-suggestions\" class=\"live-suggestions\" hidden></div>
                </div>
            </div>
            <div>
                <label for=\"blog-sort\">Sort</label>
                <select id=\"blog-sort\" name=\"sort\">
                    <option value=\"latest\" {% if filters.sort == 'latest' %}selected{% endif %}>Latest first</option>
                    <option value=\"oldest\" {% if filters.sort == 'oldest' %}selected{% endif %}>Oldest first</option>
                    <option value=\"most_liked\" {% if filters.sort == 'most_liked' %}selected{% endif %}>Most liked</option>
                    <option value=\"least_liked\" {% if filters.sort == 'least_liked' %}selected{% endif %}>Least liked</option>
                </select>
            </div>
            <div>
                <label for=\"blog-author\">Author</label>
                <select id=\"blog-author\" name=\"authorId\">
                    <option value=\"\">All authors</option>
                    {% for author in authors %}
                        <option value=\"{{ author.id }}\" {% if filters.authorId == (author.id ~ '') %}selected{% endif %}>{{ author.username }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"blog-from-date\">From date</label>
                <input id=\"blog-from-date\" type=\"date\" name=\"fromDate\" value=\"{{ filters.fromDate }}\">
            </div>
            <div>
                <label for=\"blog-to-date\">To date</label>
                <input id=\"blog-to-date\" type=\"date\" name=\"toDate\" value=\"{{ filters.toDate }}\">
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path('blog_index') }}\">Reset</a>
            </div>
        </form>
    </section>

    <section class=\"blog-grid\">
        {% for blog in blogs %}
            <article class=\"glass-card blog-card\">
                {% if blog.imageUrl %}
                    <a class=\"blog-image-link\" href=\"{{ path('blog_show', {'id': blog.id}) }}\">
                        <img class=\"blog-image\" src=\"{{ blog.imageUrl }}\" alt=\"{{ blog.title }}\">
                    </a>
                {% endif %}
                <div class=\"blog-card-body\">
                    <p class=\"eyebrow\">{{ blog.author ? blog.author.username : 'Unknown user' }}</p>
                    <h2><a class=\"blog-title-link\" href=\"{{ path('blog_show', {'id': blog.id}) }}\">{{ blog.title }}</a></h2>
                    <p class=\"muted\">{{ blog.content|length > 180 ? blog.content|slice(0, 180) ~ '...' : blog.content }}</p>
                    <div class=\"blog-meta-row\">
                        <span class=\"pill\">{{ blog.publishedAt ? blog.publishedAt|date('Y-m-d H:i') : '—' }}</span>
                        <span class=\"pill\">{{ readTimes[blog.id] ?? 1 }} min to read</span>
                        <span class=\"pill\">Comments {{ blog.comments|length }}</span>
                        <span class=\"pill\">👍 {{ blog.likesCount }}</span>
                        <span class=\"pill\">👎 {{ blog.dislikesCount }}</span>
                    </div>
                </div>
            </article>
        {% else %}
            <section class=\"glass-card\">
                <p class=\"empty-state\">No blog posts found yet.</p>
            </section>
        {% endfor %}
    </section>

    <section class=\"glass-card\">
        {{ include('components/_pagination.html.twig', {pagination: pagination, routeName: 'blog_index'}) }}
    </section>
{% endblock %}
", "blog/index.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\blog\\index.html.twig");
    }
}
