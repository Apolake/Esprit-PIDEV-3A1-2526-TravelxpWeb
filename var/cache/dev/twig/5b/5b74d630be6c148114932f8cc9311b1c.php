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

/* blog/pdf_blog.html.twig */
class __TwigTemplate_777cbd73d6c0963e6b9940de608db03b extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/pdf_blog.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/pdf_blog.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1f2937; }
        h1 { margin-bottom: 8px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        .section { margin-top: 14px; line-height: 1.6; white-space: pre-wrap; }
        .footer { margin-top: 24px; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .badge { display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 15, $this->source); })()), "title", [], "any", false, false, false, 15), "html", null, true);
        yield "</h1>
    <div class=\"meta\">
        <div>Author: ";
        // line 17
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 17, $this->source); })()), "author", [], "any", false, false, false, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 17, $this->source); })()), "author", [], "any", false, false, false, 17), "username", [], "any", false, false, false, 17), "html", null, true)) : ("Unknown user"));
        yield "</div>
        <div>Published: ";
        // line 18
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 18, $this->source); })()), "publishedAt", [], "any", false, false, false, 18)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 18, $this->source); })()), "publishedAt", [], "any", false, false, false, 18), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</div>
        <div>Read time: ";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["readTime"]) || array_key_exists("readTime", $context) ? $context["readTime"] : (function () { throw new RuntimeError('Variable "readTime" does not exist.', 19, $this->source); })()), "html", null, true);
        yield " min</div>
        <div>
            <span class=\"badge\">Likes: ";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 21, $this->source); })()), "likesCount", [], "any", false, false, false, 21), "html", null, true);
        yield "</span>
            <span class=\"badge\">Dislikes: ";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 22, $this->source); })()), "dislikesCount", [], "any", false, false, false, 22), "html", null, true);
        yield "</span>
            <span class=\"badge\">Comments: ";
        // line 23
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 23, $this->source); })()), "comments", [], "any", false, false, false, 23)), "html", null, true);
        yield "</span>
        </div>
    </div>

    <div class=\"section\">";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 27, $this->source); })()), "content", [], "any", false, false, false, 27), "html", null, true);
        yield "</div>

    <div class=\"footer\">
        Generated on ";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate((isset($context["generatedAt"]) || array_key_exists("generatedAt", $context) ? $context["generatedAt"] : (function () { throw new RuntimeError('Variable "generatedAt" does not exist.', 30, $this->source); })()), "Y-m-d H:i:s"), "html", null, true);
        yield "
    </div>
</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "blog/pdf_blog.html.twig";
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
        return array (  103 => 30,  97 => 27,  90 => 23,  86 => 22,  82 => 21,  77 => 19,  73 => 18,  69 => 17,  64 => 15,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1f2937; }
        h1 { margin-bottom: 8px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        .section { margin-top: 14px; line-height: 1.6; white-space: pre-wrap; }
        .footer { margin-top: 24px; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .badge { display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>{{ blog.title }}</h1>
    <div class=\"meta\">
        <div>Author: {{ blog.author ? blog.author.username : 'Unknown user' }}</div>
        <div>Published: {{ blog.publishedAt ? blog.publishedAt|date('Y-m-d H:i') : '—' }}</div>
        <div>Read time: {{ readTime }} min</div>
        <div>
            <span class=\"badge\">Likes: {{ blog.likesCount }}</span>
            <span class=\"badge\">Dislikes: {{ blog.dislikesCount }}</span>
            <span class=\"badge\">Comments: {{ blog.comments|length }}</span>
        </div>
    </div>

    <div class=\"section\">{{ blog.content }}</div>

    <div class=\"footer\">
        Generated on {{ generatedAt|date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
", "blog/pdf_blog.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\blog\\pdf_blog.html.twig");
    }
}
