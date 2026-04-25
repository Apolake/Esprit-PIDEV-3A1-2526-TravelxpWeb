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

/* blog/show.html.twig */
class __TwigTemplate_39b13a8dd5f8189621259e9705301cb3 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/show.html.twig"));

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

        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 3, $this->source); })()), "title", [], "any", false, false, false, 3), "html", null, true);
        
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
        $context["translationLanguages"] = [["value" => "en", "label" => "English"], ["value" => "fr", "label" => "French"], ["value" => "ar", "label" => "Arabic"], ["value" => "es", "label" => "Spanish"], ["value" => "de", "label" => "German"]];
        // line 13
        yield "
    <section class=\"glass-card\">
        <p class=\"eyebrow\">";
        // line 15
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 15, $this->source); })()), "author", [], "any", false, false, false, 15)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 15, $this->source); })()), "author", [], "any", false, false, false, 15), "username", [], "any", false, false, false, 15), "html", null, true)) : ("Unknown user"));
        yield "</p>
        <h1>";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 16, $this->source); })()), "title", [], "any", false, false, false, 16), "html", null, true);
        yield "</h1>
        <p class=\"muted\">Posted on ";
        // line 17
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 17, $this->source); })()), "publishedAt", [], "any", false, false, false, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 17, $this->source); })()), "publishedAt", [], "any", false, false, false, 17), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield " • ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["readTime"]) || array_key_exists("readTime", $context) ? $context["readTime"] : (function () { throw new RuntimeError('Variable "readTime" does not exist.', 17, $this->source); })()), "html", null, true);
        yield " min to read</p>

        ";
        // line 19
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 19, $this->source); })()), "imageUrl", [], "any", false, false, false, 19)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 20
            yield "            <div class=\"blog-show-image-wrap\">
                <img class=\"blog-show-image\" src=\"";
            // line 21
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 21, $this->source); })()), "imageUrl", [], "any", false, false, false, 21), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 21, $this->source); })()), "title", [], "any", false, false, false, 21), "html", null, true);
            yield "\">
            </div>
        ";
        }
        // line 24
        yield "
        <div class=\"blog-content\">
            ";
        // line 26
        yield Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 26, $this->source); })()), "content", [], "any", false, false, false, 26), "html", null, true));
        yield "
        </div>

        <div class=\"reaction-bar\">
            ";
        // line 30
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 30, $this->source); })()), "user", [], "any", false, false, false, 30)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 31
            yield "                <form method=\"post\" class=\"inline-form\" action=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_react", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 31, $this->source); })()), "id", [], "any", false, false, false, 31), "type" => "like"]), "html", null, true);
            yield "\">
                    <input type=\"hidden\" name=\"_token\" value=\"";
            // line 32
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken((("react_blog_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 32, $this->source); })()), "id", [], "any", false, false, false, 32)) . "_like")), "html", null, true);
            yield "\">
                    <button type=\"submit\" class=\"btn btn-sm reaction-btn ";
            // line 33
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 33, $this->source); })()), "hasLikedBy", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 33, $this->source); })()), "user", [], "any", false, false, false, 33)], "method", false, false, false, 33)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
            yield "\">👍 ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 33, $this->source); })()), "likesCount", [], "any", false, false, false, 33), "html", null, true);
            yield "</button>
                </form>
                <form method=\"post\" class=\"inline-form\" action=\"";
            // line 35
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_react", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 35, $this->source); })()), "id", [], "any", false, false, false, 35), "type" => "dislike"]), "html", null, true);
            yield "\">
                    <input type=\"hidden\" name=\"_token\" value=\"";
            // line 36
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken((("react_blog_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 36, $this->source); })()), "id", [], "any", false, false, false, 36)) . "_dislike")), "html", null, true);
            yield "\">
                    <button type=\"submit\" class=\"btn btn-sm reaction-btn ";
            // line 37
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 37, $this->source); })()), "hasDislikedBy", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 37, $this->source); })()), "user", [], "any", false, false, false, 37)], "method", false, false, false, 37)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
            yield "\">👎 ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 37, $this->source); })()), "dislikesCount", [], "any", false, false, false, 37), "html", null, true);
            yield "</button>
                </form>
            ";
        } else {
            // line 40
            yield "                <span class=\"pill\">👍 ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 40, $this->source); })()), "likesCount", [], "any", false, false, false, 40), "html", null, true);
            yield "</span>
                <span class=\"pill\">👎 ";
            // line 41
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 41, $this->source); })()), "dislikesCount", [], "any", false, false, false, 41), "html", null, true);
            yield "</span>
            ";
        }
        // line 43
        yield "        </div>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"";
        // line 46
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_index");
        yield "\">Back to blogs</a>
            <a class=\"btn\" href=\"";
        // line 47
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_export_pdf", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 47, $this->source); })()), "id", [], "any", false, false, false, 47)]), "html", null, true);
        yield "\">Export Blog PDF</a>
            <a class=\"btn\" href=\"";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_comments_export_pdf", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 48, $this->source); })()), "id", [], "any", false, false, false, 48)]), "html", null, true);
        yield "\">Export Comments PDF</a>
            <button
                type=\"button\"
                class=\"btn\"
                data-ai-summarize-btn=\"true\"
                data-ai-summarize-url=\"";
        // line 53
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_ai_summarize", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 53, $this->source); })()), "id", [], "any", false, false, false, 53)]), "html", null, true);
        yield "\"
                data-ai-summarize-target=\"#blog-summary-output\"
                data-ai-summarize-token=\"";
        // line 55
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("blog_ai_summarize_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 55, $this->source); })()), "id", [], "any", false, false, false, 55))), "html", null, true);
        yield "\"
            >
                AI Summarize
            </button>
            <button
                type=\"button\"
                class=\"btn\"
                data-translate-btn=\"true\"
                data-translate-url=\"";
        // line 63
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_translate", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 63, $this->source); })()), "id", [], "any", false, false, false, 63)]), "html", null, true);
        yield "\"
                data-translate-original=\"";
        // line 64
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 64, $this->source); })()), "content", [], "any", false, false, false, 64), "html_attr");
        yield "\"
                data-translate-target=\"#translation-panel\"
                data-translate-token=\"";
        // line 66
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("blog_translate_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 66, $this->source); })()), "id", [], "any", false, false, false, 66))), "html", null, true);
        yield "\"
            >
                Translate
            </button>
            ";
        // line 70
        if ((($tmp = (isset($context["canManageBlog"]) || array_key_exists("canManageBlog", $context) ? $context["canManageBlog"] : (function () { throw new RuntimeError('Variable "canManageBlog" does not exist.', 70, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 71
            yield "                <a class=\"btn btn-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 71, $this->source); })()), "id", [], "any", false, false, false, 71)]), "html", null, true);
            yield "\">Edit</a>
                ";
            // line 72
            yield Twig\Extension\CoreExtension::include($this->env, $context, "blog/_delete_form.html.twig", ["blog" => (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 72, $this->source); })())]);
            yield "
            ";
        }
        // line 74
        yield "        </div>

        <section id=\"blog-summary-output\" class=\"tool-panel\" hidden></section>
        <section id=\"translation-panel\" class=\"tool-panel\" hidden>
            <h3>Translation</h3>
            <div class=\"row-actions\">
                <select data-translate-language=\"true\" class=\"tool-select\">
                    ";
        // line 81
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["translationLanguages"]) || array_key_exists("translationLanguages", $context) ? $context["translationLanguages"] : (function () { throw new RuntimeError('Variable "translationLanguages" does not exist.', 81, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 82
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["language"], "value", [], "any", false, false, false, 82), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["language"], "label", [], "any", false, false, false, 82), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['language'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 84
        yield "                </select>
                <button type=\"button\" class=\"btn btn-sm\" data-translate-run=\"true\">Translate now</button>
            </div>
            <div class=\"tool-two-col\">
                <div>
                    <h4>Original Text</h4>
                    <p data-translate-original-text=\"true\">";
        // line 90
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 90, $this->source); })()), "content", [], "any", false, false, false, 90), "html", null, true);
        yield "</p>
                </div>
                <div>
                    <h4>Translated Text</h4>
                    <p data-translate-result=\"true\" class=\"muted\">No translation yet.</p>
                </div>
            </div>
        </section>
    </section>

    <section class=\"glass-card\">
        <h2>Comments</h2>

        <form class=\"filters-grid\" method=\"get\" action=\"";
        // line 103
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_show", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 103, $this->source); })()), "id", [], "any", false, false, false, 103)]), "html", null, true);
        yield "\">
            <div>
                <label for=\"comment-sort\">Sort comments</label>
                <select id=\"comment-sort\" name=\"commentSort\">
                    <option value=\"latest\" ";
        // line 107
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 107, $this->source); })()), "sort", [], "any", false, false, false, 107) == "latest")) {
            yield "selected";
        }
        yield ">Latest first</option>
                    <option value=\"oldest\" ";
        // line 108
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 108, $this->source); })()), "sort", [], "any", false, false, false, 108) == "oldest")) {
            yield "selected";
        }
        yield ">Oldest first</option>
                    <option value=\"most_liked\" ";
        // line 109
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 109, $this->source); })()), "sort", [], "any", false, false, false, 109) == "most_liked")) {
            yield "selected";
        }
        yield ">Most liked</option>
                    <option value=\"least_liked\" ";
        // line 110
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 110, $this->source); })()), "sort", [], "any", false, false, false, 110) == "least_liked")) {
            yield "selected";
        }
        yield ">Least liked</option>
                </select>
            </div>
            <div>
                <label for=\"comment-author\">Author</label>
                <select id=\"comment-author\" name=\"commentAuthorId\">
                    <option value=\"\">All authors</option>
                    ";
        // line 117
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["commentAuthors"]) || array_key_exists("commentAuthors", $context) ? $context["commentAuthors"] : (function () { throw new RuntimeError('Variable "commentAuthors" does not exist.', 117, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["author"]) {
            // line 118
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "id", [], "any", false, false, false, 118), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 118, $this->source); })()), "authorId", [], "any", false, false, false, 118) == (CoreExtension::getAttribute($this->env, $this->source, $context["author"], "id", [], "any", false, false, false, 118) . ""))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "username", [], "any", false, false, false, 118), "html", null, true);
            yield "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['author'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 120
        yield "                </select>
            </div>
            <div>
                <label for=\"comment-from-date\">From date</label>
                <input id=\"comment-from-date\" type=\"date\" name=\"commentFromDate\" value=\"";
        // line 124
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 124, $this->source); })()), "fromDate", [], "any", false, false, false, 124), "html", null, true);
        yield "\">
            </div>
            <div>
                <label for=\"comment-to-date\">To date</label>
                <input id=\"comment-to-date\" type=\"date\" name=\"commentToDate\" value=\"";
        // line 128
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentFilters"]) || array_key_exists("commentFilters", $context) ? $context["commentFilters"] : (function () { throw new RuntimeError('Variable "commentFilters" does not exist.', 128, $this->source); })()), "toDate", [], "any", false, false, false, 128), "html", null, true);
        yield "\">
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"";
        // line 132
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("blog_show", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 132, $this->source); })()), "id", [], "any", false, false, false, 132)]), "html", null, true);
        yield "\">Reset</a>
            </div>
        </form>

        ";
        // line 136
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 136, $this->source); })()), "user", [], "any", false, false, false, 136)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 137
            yield "            ";
            yield             $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["commentForm"]) || array_key_exists("commentForm", $context) ? $context["commentForm"] : (function () { throw new RuntimeError('Variable "commentForm" does not exist.', 137, $this->source); })()), 'form_start', ["attr" => ["class" => "stack-form", "novalidate" => "novalidate"]]);
            yield "
                ";
            // line 138
            yield $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentForm"]) || array_key_exists("commentForm", $context) ? $context["commentForm"] : (function () { throw new RuntimeError('Variable "commentForm" does not exist.', 138, $this->source); })()), "content", [], "any", false, false, false, 138), 'row');
            yield "
                <div class=\"row-actions\">
                    <button
                        class=\"btn btn-sm\"
                        type=\"button\"
                        data-grammar-btn=\"true\"
                        data-grammar-target=\"#";
            // line 144
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["commentForm"]) || array_key_exists("commentForm", $context) ? $context["commentForm"] : (function () { throw new RuntimeError('Variable "commentForm" does not exist.', 144, $this->source); })()), "content", [], "any", false, false, false, 144), "vars", [], "any", false, false, false, 144), "id", [], "any", false, false, false, 144), "html", null, true);
            yield "\"
                        data-grammar-url=\"";
            // line 145
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_tool_grammar");
            yield "\"
                        data-grammar-token=\"";
            // line 146
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken("comment_grammar_tool"), "html", null, true);
            yield "\"
                    >
                        Fix Grammar
                    </button>
                </div>
                <button class=\"btn btn-primary\" type=\"submit\">Add comment</button>
            ";
            // line 152
            yield             $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["commentForm"]) || array_key_exists("commentForm", $context) ? $context["commentForm"] : (function () { throw new RuntimeError('Variable "commentForm" does not exist.', 152, $this->source); })()), 'form_end');
            yield "
        ";
        } else {
            // line 154
            yield "            <p class=\"muted\">Please <a href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\">log in</a> to comment.</p>
        ";
        }
        // line 156
        yield "
        <div class=\"comments-stack\">
            ";
        // line 158
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["comments"]) || array_key_exists("comments", $context) ? $context["comments"] : (function () { throw new RuntimeError('Variable "comments" does not exist.', 158, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["comment"]) {
            // line 159
            yield "                <article class=\"comment-card\">
                    <div class=\"comment-head\">
                        <p class=\"eyebrow\">";
            // line 161
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 161)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 161), "username", [], "any", false, false, false, 161), "html", null, true)) : ("Unknown user"));
            yield "</p>
                        <div class=\"comment-meta-right\">
                            <span class=\"pill\">";
            // line 163
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "createdAt", [], "any", false, false, false, 163)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "createdAt", [], "any", false, false, false, 163), "Y-m-d H:i"), "html", null, true)) : ("—"));
            yield "</span>
                            ";
            // line 164
            $context["sentiment"] = (((CoreExtension::getAttribute($this->env, $this->source, ($context["sentiments"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 164), [], "array", true, true, false, 164) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["sentiments"]) || array_key_exists("sentiments", $context) ? $context["sentiments"] : (function () { throw new RuntimeError('Variable "sentiments" does not exist.', 164, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 164), [], "array", false, false, false, 164)))) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["sentiments"]) || array_key_exists("sentiments", $context) ? $context["sentiments"] : (function () { throw new RuntimeError('Variable "sentiments" does not exist.', 164, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 164), [], "array", false, false, false, 164)) : (null));
            // line 165
            yield "                            ";
            if ((($tmp = (isset($context["sentiment"]) || array_key_exists("sentiment", $context) ? $context["sentiment"] : (function () { throw new RuntimeError('Variable "sentiment" does not exist.', 165, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 166
                yield "                                <span class=\"pill sentiment-pill ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["sentiment"]) || array_key_exists("sentiment", $context) ? $context["sentiment"] : (function () { throw new RuntimeError('Variable "sentiment" does not exist.', 166, $this->source); })()), "class", [], "any", false, false, false, 166), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["sentiment"]) || array_key_exists("sentiment", $context) ? $context["sentiment"] : (function () { throw new RuntimeError('Variable "sentiment" does not exist.', 166, $this->source); })()), "label", [], "any", false, false, false, 166), "html", null, true);
                yield " ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["sentiment"]) || array_key_exists("sentiment", $context) ? $context["sentiment"] : (function () { throw new RuntimeError('Variable "sentiment" does not exist.', 166, $this->source); })()), "emoji", [], "any", false, false, false, 166), "html", null, true);
                yield "</span>
                            ";
            }
            // line 168
            yield "                        </div>
                    </div>
                    <p>";
            // line 170
            yield Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "content", [], "any", false, false, false, 170), "html", null, true));
            yield "</p>

                    <section id=\"comment-translation-panel-";
            // line 172
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 172), "html", null, true);
            yield "\" class=\"tool-panel comment-translation-panel\" hidden>
                        <h4>Translate comment</h4>
                        <div class=\"row-actions\">
                            <select data-translate-language=\"true\" class=\"tool-select\">
                                ";
            // line 176
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["translationLanguages"]) || array_key_exists("translationLanguages", $context) ? $context["translationLanguages"] : (function () { throw new RuntimeError('Variable "translationLanguages" does not exist.', 176, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 177
                yield "                                    <option value=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["language"], "value", [], "any", false, false, false, 177), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["language"], "label", [], "any", false, false, false, 177), "html", null, true);
                yield "</option>
                                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['language'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 179
            yield "                            </select>
                            <button type=\"button\" class=\"btn btn-sm\" data-translate-run=\"true\">Translate now</button>
                        </div>
                        <div class=\"tool-two-col\">
                            <div>
                                <h4>Original Text</h4>
                                <p data-translate-original-text=\"true\">";
            // line 185
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "content", [], "any", false, false, false, 185), "html", null, true);
            yield "</p>
                            </div>
                            <div>
                                <h4>Translated Text</h4>
                                <p data-translate-result=\"true\" class=\"muted\">No translation yet.</p>
                            </div>
                        </div>
                    </section>

                    <div class=\"reaction-bar\">
                        ";
            // line 195
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 195, $this->source); })()), "user", [], "any", false, false, false, 195)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 196
                yield "                            <form method=\"post\" class=\"inline-form\" action=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_react", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 196), "type" => "like"]), "html", null, true);
                yield "\">
                                <input type=\"hidden\" name=\"_token\" value=\"";
                // line 197
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken((("react_comment_" . CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 197)) . "_like")), "html", null, true);
                yield "\">
                                <button type=\"submit\" class=\"btn btn-sm reaction-btn ";
                // line 198
                yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "hasLikedBy", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 198, $this->source); })()), "user", [], "any", false, false, false, 198)], "method", false, false, false, 198)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
                yield "\">👍 ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "likesCount", [], "any", false, false, false, 198), "html", null, true);
                yield "</button>
                            </form>
                            <form method=\"post\" class=\"inline-form\" action=\"";
                // line 200
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_react", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 200), "type" => "dislike"]), "html", null, true);
                yield "\">
                                <input type=\"hidden\" name=\"_token\" value=\"";
                // line 201
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken((("react_comment_" . CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 201)) . "_dislike")), "html", null, true);
                yield "\">
                                <button type=\"submit\" class=\"btn btn-sm reaction-btn ";
                // line 202
                yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "hasDislikedBy", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 202, $this->source); })()), "user", [], "any", false, false, false, 202)], "method", false, false, false, 202)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("active") : (""));
                yield "\">👎 ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "dislikesCount", [], "any", false, false, false, 202), "html", null, true);
                yield "</button>
                            </form>
                        ";
            } else {
                // line 205
                yield "                            <span class=\"pill\">👍 ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "likesCount", [], "any", false, false, false, 205), "html", null, true);
                yield "</span>
                            <span class=\"pill\">👎 ";
                // line 206
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "dislikesCount", [], "any", false, false, false, 206), "html", null, true);
                yield "</span>
                        ";
            }
            // line 208
            yield "
                        ";
            // line 209
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 209, $this->source); })()), "user", [], "any", false, false, false, 209) && ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN") || (CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 209) && (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 209), "id", [], "any", false, false, false, 209) == CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 209, $this->source); })()), "user", [], "any", false, false, false, 209), "id", [], "any", false, false, false, 209)))))) {
                // line 210
                yield "                            <a class=\"btn btn-sm\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 210)]), "html", null, true);
                yield "\">Edit</a>
                            <form method=\"post\" class=\"inline-form\" action=\"";
                // line 211
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 211)]), "html", null, true);
                yield "\" onsubmit=\"return confirm('Delete this comment?');\">
                                <input type=\"hidden\" name=\"_token\" value=\"";
                // line 212
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("delete_comment_" . CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 212))), "html", null, true);
                yield "\">
                                <button class=\"btn btn-sm btn-danger\" type=\"submit\">Delete</button>
                            </form>
                        ";
            }
            // line 216
            yield "
                        <button
                            type=\"button\"
                            class=\"btn btn-sm\"
                            data-translate-btn=\"true\"
                            data-translate-url=\"";
            // line 221
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("comment_translate", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 221)]), "html", null, true);
            yield "\"
                            data-translate-original=\"";
            // line 222
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "content", [], "any", false, false, false, 222), "html_attr");
            yield "\"
                            data-translate-target=\"#comment-translation-panel-";
            // line 223
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 223), "html", null, true);
            yield "\"
                            data-translate-token=\"";
            // line 224
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("comment_translate_" . CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "id", [], "any", false, false, false, 224))), "html", null, true);
            yield "\"
                        >
                            Translate
                        </button>
                    </div>
                </article>
            ";
            $context['_iterated'] = true;
        }
        // line 230
        if (!$context['_iterated']) {
            // line 231
            yield "                <p class=\"empty-state\">No comments yet.</p>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['comment'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 233
        yield "        </div>
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
        return "blog/show.html.twig";
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
        return array (  623 => 233,  616 => 231,  614 => 230,  603 => 224,  599 => 223,  595 => 222,  591 => 221,  584 => 216,  577 => 212,  573 => 211,  568 => 210,  566 => 209,  563 => 208,  558 => 206,  553 => 205,  545 => 202,  541 => 201,  537 => 200,  530 => 198,  526 => 197,  521 => 196,  519 => 195,  506 => 185,  498 => 179,  487 => 177,  483 => 176,  476 => 172,  471 => 170,  467 => 168,  457 => 166,  454 => 165,  452 => 164,  448 => 163,  443 => 161,  439 => 159,  434 => 158,  430 => 156,  424 => 154,  419 => 152,  410 => 146,  406 => 145,  402 => 144,  393 => 138,  388 => 137,  386 => 136,  379 => 132,  372 => 128,  365 => 124,  359 => 120,  344 => 118,  340 => 117,  328 => 110,  322 => 109,  316 => 108,  310 => 107,  303 => 103,  287 => 90,  279 => 84,  268 => 82,  264 => 81,  255 => 74,  250 => 72,  245 => 71,  243 => 70,  236 => 66,  231 => 64,  227 => 63,  216 => 55,  211 => 53,  203 => 48,  199 => 47,  195 => 46,  190 => 43,  185 => 41,  180 => 40,  172 => 37,  168 => 36,  164 => 35,  157 => 33,  153 => 32,  148 => 31,  146 => 30,  139 => 26,  135 => 24,  127 => 21,  124 => 20,  122 => 19,  115 => 17,  111 => 16,  107 => 15,  103 => 13,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ blog.title }}{% endblock %}

{% block body %}
    {% set translationLanguages = [
        {'value': 'en', 'label': 'English'},
        {'value': 'fr', 'label': 'French'},
        {'value': 'ar', 'label': 'Arabic'},
        {'value': 'es', 'label': 'Spanish'},
        {'value': 'de', 'label': 'German'},
    ] %}

    <section class=\"glass-card\">
        <p class=\"eyebrow\">{{ blog.author ? blog.author.username : 'Unknown user' }}</p>
        <h1>{{ blog.title }}</h1>
        <p class=\"muted\">Posted on {{ blog.publishedAt ? blog.publishedAt|date('Y-m-d H:i') : '—' }} • {{ readTime }} min to read</p>

        {% if blog.imageUrl %}
            <div class=\"blog-show-image-wrap\">
                <img class=\"blog-show-image\" src=\"{{ blog.imageUrl }}\" alt=\"{{ blog.title }}\">
            </div>
        {% endif %}

        <div class=\"blog-content\">
            {{ blog.content|nl2br }}
        </div>

        <div class=\"reaction-bar\">
            {% if app.user %}
                <form method=\"post\" class=\"inline-form\" action=\"{{ path('blog_react', {'id': blog.id, 'type': 'like'}) }}\">
                    <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('react_blog_' ~ blog.id ~ '_like') }}\">
                    <button type=\"submit\" class=\"btn btn-sm reaction-btn {{ blog.hasLikedBy(app.user) ? 'active' : '' }}\">👍 {{ blog.likesCount }}</button>
                </form>
                <form method=\"post\" class=\"inline-form\" action=\"{{ path('blog_react', {'id': blog.id, 'type': 'dislike'}) }}\">
                    <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('react_blog_' ~ blog.id ~ '_dislike') }}\">
                    <button type=\"submit\" class=\"btn btn-sm reaction-btn {{ blog.hasDislikedBy(app.user) ? 'active' : '' }}\">👎 {{ blog.dislikesCount }}</button>
                </form>
            {% else %}
                <span class=\"pill\">👍 {{ blog.likesCount }}</span>
                <span class=\"pill\">👎 {{ blog.dislikesCount }}</span>
            {% endif %}
        </div>

        <div class=\"row-actions\">
            <a class=\"btn\" href=\"{{ path('blog_index') }}\">Back to blogs</a>
            <a class=\"btn\" href=\"{{ path('blog_export_pdf', {'id': blog.id}) }}\">Export Blog PDF</a>
            <a class=\"btn\" href=\"{{ path('blog_comments_export_pdf', {'id': blog.id}) }}\">Export Comments PDF</a>
            <button
                type=\"button\"
                class=\"btn\"
                data-ai-summarize-btn=\"true\"
                data-ai-summarize-url=\"{{ path('blog_ai_summarize', {'id': blog.id}) }}\"
                data-ai-summarize-target=\"#blog-summary-output\"
                data-ai-summarize-token=\"{{ csrf_token('blog_ai_summarize_' ~ blog.id) }}\"
            >
                AI Summarize
            </button>
            <button
                type=\"button\"
                class=\"btn\"
                data-translate-btn=\"true\"
                data-translate-url=\"{{ path('blog_translate', {'id': blog.id}) }}\"
                data-translate-original=\"{{ blog.content|e('html_attr') }}\"
                data-translate-target=\"#translation-panel\"
                data-translate-token=\"{{ csrf_token('blog_translate_' ~ blog.id) }}\"
            >
                Translate
            </button>
            {% if canManageBlog %}
                <a class=\"btn btn-primary\" href=\"{{ path('blog_edit', {'id': blog.id}) }}\">Edit</a>
                {{ include('blog/_delete_form.html.twig', {blog: blog}) }}
            {% endif %}
        </div>

        <section id=\"blog-summary-output\" class=\"tool-panel\" hidden></section>
        <section id=\"translation-panel\" class=\"tool-panel\" hidden>
            <h3>Translation</h3>
            <div class=\"row-actions\">
                <select data-translate-language=\"true\" class=\"tool-select\">
                    {% for language in translationLanguages %}
                        <option value=\"{{ language.value }}\">{{ language.label }}</option>
                    {% endfor %}
                </select>
                <button type=\"button\" class=\"btn btn-sm\" data-translate-run=\"true\">Translate now</button>
            </div>
            <div class=\"tool-two-col\">
                <div>
                    <h4>Original Text</h4>
                    <p data-translate-original-text=\"true\">{{ blog.content }}</p>
                </div>
                <div>
                    <h4>Translated Text</h4>
                    <p data-translate-result=\"true\" class=\"muted\">No translation yet.</p>
                </div>
            </div>
        </section>
    </section>

    <section class=\"glass-card\">
        <h2>Comments</h2>

        <form class=\"filters-grid\" method=\"get\" action=\"{{ path('blog_show', {'id': blog.id}) }}\">
            <div>
                <label for=\"comment-sort\">Sort comments</label>
                <select id=\"comment-sort\" name=\"commentSort\">
                    <option value=\"latest\" {% if commentFilters.sort == 'latest' %}selected{% endif %}>Latest first</option>
                    <option value=\"oldest\" {% if commentFilters.sort == 'oldest' %}selected{% endif %}>Oldest first</option>
                    <option value=\"most_liked\" {% if commentFilters.sort == 'most_liked' %}selected{% endif %}>Most liked</option>
                    <option value=\"least_liked\" {% if commentFilters.sort == 'least_liked' %}selected{% endif %}>Least liked</option>
                </select>
            </div>
            <div>
                <label for=\"comment-author\">Author</label>
                <select id=\"comment-author\" name=\"commentAuthorId\">
                    <option value=\"\">All authors</option>
                    {% for author in commentAuthors %}
                        <option value=\"{{ author.id }}\" {% if commentFilters.authorId == (author.id ~ '') %}selected{% endif %}>{{ author.username }}</option>
                    {% endfor %}
                </select>
            </div>
            <div>
                <label for=\"comment-from-date\">From date</label>
                <input id=\"comment-from-date\" type=\"date\" name=\"commentFromDate\" value=\"{{ commentFilters.fromDate }}\">
            </div>
            <div>
                <label for=\"comment-to-date\">To date</label>
                <input id=\"comment-to-date\" type=\"date\" name=\"commentToDate\" value=\"{{ commentFilters.toDate }}\">
            </div>
            <div class=\"row-actions\">
                <button class=\"btn btn-sm btn-primary\" type=\"submit\">Apply</button>
                <a class=\"btn btn-sm\" href=\"{{ path('blog_show', {'id': blog.id}) }}\">Reset</a>
            </div>
        </form>

        {% if app.user %}
            {{ form_start(commentForm, {'attr': {'class': 'stack-form', 'novalidate': 'novalidate'}}) }}
                {{ form_row(commentForm.content) }}
                <div class=\"row-actions\">
                    <button
                        class=\"btn btn-sm\"
                        type=\"button\"
                        data-grammar-btn=\"true\"
                        data-grammar-target=\"#{{ commentForm.content.vars.id }}\"
                        data-grammar-url=\"{{ path('comment_tool_grammar') }}\"
                        data-grammar-token=\"{{ csrf_token('comment_grammar_tool') }}\"
                    >
                        Fix Grammar
                    </button>
                </div>
                <button class=\"btn btn-primary\" type=\"submit\">Add comment</button>
            {{ form_end(commentForm) }}
        {% else %}
            <p class=\"muted\">Please <a href=\"{{ path('app_login') }}\">log in</a> to comment.</p>
        {% endif %}

        <div class=\"comments-stack\">
            {% for comment in comments %}
                <article class=\"comment-card\">
                    <div class=\"comment-head\">
                        <p class=\"eyebrow\">{{ comment.author ? comment.author.username : 'Unknown user' }}</p>
                        <div class=\"comment-meta-right\">
                            <span class=\"pill\">{{ comment.createdAt ? comment.createdAt|date('Y-m-d H:i') : '—' }}</span>
                            {% set sentiment = sentiments[comment.id] ?? null %}
                            {% if sentiment %}
                                <span class=\"pill sentiment-pill {{ sentiment.class }}\">{{ sentiment.label }} {{ sentiment.emoji }}</span>
                            {% endif %}
                        </div>
                    </div>
                    <p>{{ comment.content|nl2br }}</p>

                    <section id=\"comment-translation-panel-{{ comment.id }}\" class=\"tool-panel comment-translation-panel\" hidden>
                        <h4>Translate comment</h4>
                        <div class=\"row-actions\">
                            <select data-translate-language=\"true\" class=\"tool-select\">
                                {% for language in translationLanguages %}
                                    <option value=\"{{ language.value }}\">{{ language.label }}</option>
                                {% endfor %}
                            </select>
                            <button type=\"button\" class=\"btn btn-sm\" data-translate-run=\"true\">Translate now</button>
                        </div>
                        <div class=\"tool-two-col\">
                            <div>
                                <h4>Original Text</h4>
                                <p data-translate-original-text=\"true\">{{ comment.content }}</p>
                            </div>
                            <div>
                                <h4>Translated Text</h4>
                                <p data-translate-result=\"true\" class=\"muted\">No translation yet.</p>
                            </div>
                        </div>
                    </section>

                    <div class=\"reaction-bar\">
                        {% if app.user %}
                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('comment_react', {'id': comment.id, 'type': 'like'}) }}\">
                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('react_comment_' ~ comment.id ~ '_like') }}\">
                                <button type=\"submit\" class=\"btn btn-sm reaction-btn {{ comment.hasLikedBy(app.user) ? 'active' : '' }}\">👍 {{ comment.likesCount }}</button>
                            </form>
                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('comment_react', {'id': comment.id, 'type': 'dislike'}) }}\">
                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('react_comment_' ~ comment.id ~ '_dislike') }}\">
                                <button type=\"submit\" class=\"btn btn-sm reaction-btn {{ comment.hasDislikedBy(app.user) ? 'active' : '' }}\">👎 {{ comment.dislikesCount }}</button>
                            </form>
                        {% else %}
                            <span class=\"pill\">👍 {{ comment.likesCount }}</span>
                            <span class=\"pill\">👎 {{ comment.dislikesCount }}</span>
                        {% endif %}

                        {% if app.user and (is_granted('ROLE_ADMIN') or comment.author and comment.author.id == app.user.id) %}
                            <a class=\"btn btn-sm\" href=\"{{ path('comment_edit', {'id': comment.id}) }}\">Edit</a>
                            <form method=\"post\" class=\"inline-form\" action=\"{{ path('comment_delete', {'id': comment.id}) }}\" onsubmit=\"return confirm('Delete this comment?');\">
                                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete_comment_' ~ comment.id) }}\">
                                <button class=\"btn btn-sm btn-danger\" type=\"submit\">Delete</button>
                            </form>
                        {% endif %}

                        <button
                            type=\"button\"
                            class=\"btn btn-sm\"
                            data-translate-btn=\"true\"
                            data-translate-url=\"{{ path('comment_translate', {'id': comment.id}) }}\"
                            data-translate-original=\"{{ comment.content|e('html_attr') }}\"
                            data-translate-target=\"#comment-translation-panel-{{ comment.id }}\"
                            data-translate-token=\"{{ csrf_token('comment_translate_' ~ comment.id) }}\"
                        >
                            Translate
                        </button>
                    </div>
                </article>
            {% else %}
                <p class=\"empty-state\">No comments yet.</p>
            {% endfor %}
        </div>
    </section>
{% endblock %}
", "blog/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\blog\\show.html.twig");
    }
}
