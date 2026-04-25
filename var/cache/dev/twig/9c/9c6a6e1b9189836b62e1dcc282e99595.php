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

/* blog/pdf_comments.html.twig */
class __TwigTemplate_3beb4c45f06465710f3bf2824dc028f4 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/pdf_comments.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "blog/pdf_comments.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { margin-bottom: 8px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        .comment { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; margin-bottom: 8px; }
        .comment-head { font-size: 11px; color: #6b7280; margin-bottom: 6px; }
        .footer { margin-top: 18px; font-size: 10px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>Comments Export</h1>
    <div class=\"meta\">
        <div>Blog: ";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["blog"]) || array_key_exists("blog", $context) ? $context["blog"] : (function () { throw new RuntimeError('Variable "blog" does not exist.', 17, $this->source); })()), "title", [], "any", false, false, false, 17), "html", null, true);
        yield "</div>
        <div>Generated on: ";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate((isset($context["generatedAt"]) || array_key_exists("generatedAt", $context) ? $context["generatedAt"] : (function () { throw new RuntimeError('Variable "generatedAt" does not exist.', 18, $this->source); })()), "Y-m-d H:i:s"), "html", null, true);
        yield "</div>
    </div>

    ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["comments"]) || array_key_exists("comments", $context) ? $context["comments"] : (function () { throw new RuntimeError('Variable "comments" does not exist.', 21, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["comment"]) {
            // line 22
            yield "        <div class=\"comment\">
            <div class=\"comment-head\">
                ";
            // line 24
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 24)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "author", [], "any", false, false, false, 24), "username", [], "any", false, false, false, 24), "html", null, true)) : ("Unknown user"));
            yield " • ";
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "createdAt", [], "any", false, false, false, 24)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "createdAt", [], "any", false, false, false, 24), "Y-m-d H:i"), "html", null, true)) : ("—"));
            yield " • Likes ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "likesCount", [], "any", false, false, false, 24), "html", null, true);
            yield " • Dislikes ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "dislikesCount", [], "any", false, false, false, 24), "html", null, true);
            yield "
            </div>
            <div>";
            // line 26
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comment"], "content", [], "any", false, false, false, 26), "html", null, true);
            yield "</div>
        </div>
    ";
            $context['_iterated'] = true;
        }
        // line 28
        if (!$context['_iterated']) {
            // line 29
            yield "        <p>No comments found for this blog.</p>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['comment'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        yield "
    <div class=\"footer\">TravelXP Blog Comments PDF Export</div>
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
        return "blog/pdf_comments.html.twig";
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
        return array (  112 => 31,  105 => 29,  103 => 28,  96 => 26,  85 => 24,  81 => 22,  76 => 21,  70 => 18,  66 => 17,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { margin-bottom: 8px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        .comment { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; margin-bottom: 8px; }
        .comment-head { font-size: 11px; color: #6b7280; margin-bottom: 6px; }
        .footer { margin-top: 18px; font-size: 10px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>Comments Export</h1>
    <div class=\"meta\">
        <div>Blog: {{ blog.title }}</div>
        <div>Generated on: {{ generatedAt|date('Y-m-d H:i:s') }}</div>
    </div>

    {% for comment in comments %}
        <div class=\"comment\">
            <div class=\"comment-head\">
                {{ comment.author ? comment.author.username : 'Unknown user' }} • {{ comment.createdAt ? comment.createdAt|date('Y-m-d H:i') : '—' }} • Likes {{ comment.likesCount }} • Dislikes {{ comment.dislikesCount }}
            </div>
            <div>{{ comment.content }}</div>
        </div>
    {% else %}
        <p>No comments found for this blog.</p>
    {% endfor %}

    <div class=\"footer\">TravelXP Blog Comments PDF Export</div>
</body>
</html>
", "blog/pdf_comments.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\blog\\pdf_comments.html.twig");
    }
}
