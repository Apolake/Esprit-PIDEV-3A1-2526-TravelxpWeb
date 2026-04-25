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

/* profile/show.html.twig */
class __TwigTemplate_b854e8508101684f809fbcce1ebc9a69 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "profile/show.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "profile/show.html.twig"));

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

        yield "Profile - My Account";
        
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
        $context["user"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 6, $this->source); })()), "user", [], "any", false, false, false, 6);
        // line 7
        yield "    ";
        $context["imagePath"] = (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 7, $this->source); })()), "profileImage", [], "any", false, false, false, 7)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (Twig\Extension\CoreExtension::replace(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 7, $this->source); })()), "profileImage", [], "any", false, false, false, 7), ["\\" => "/"])) : (null));
        // line 8
        yield "    ";
        $context["imageSrc"] = null;
        // line 9
        yield "    ";
        if ((($tmp = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 9, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 10
            yield "        ";
            $context["imageSrc"] = (((is_string($_v0 = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 10, $this->source); })())) && is_string($_v1 = "http") && str_starts_with($_v0, $_v1))) ? ((isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 10, $this->source); })())) : ($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl((((is_string($_v2 = (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 10, $this->source); })())) && is_string($_v3 = "/") && str_starts_with($_v2, $_v3))) ? (Twig\Extension\CoreExtension::slice($this->env->getCharset(), (isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 10, $this->source); })()), 1)) : ((isset($context["imagePath"]) || array_key_exists("imagePath", $context) ? $context["imagePath"] : (function () { throw new RuntimeError('Variable "imagePath" does not exist.', 10, $this->source); })()))))));
            // line 11
            yield "    ";
        }
        // line 12
        yield "
    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Profile</p>
            <h1>My profile</h1>
            <p class=\"muted\">Manage your account details and credentials.</p>
        </div>
        <a class=\"btn btn-primary\" href=\"";
        // line 19
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_profile_edit");
        yield "\">Edit profile</a>
    </section>

    <section class=\"glass-card\">
        <div class=\"profile-avatar-wrap\">
            ";
        // line 24
        if ((($tmp = (isset($context["imageSrc"]) || array_key_exists("imageSrc", $context) ? $context["imageSrc"] : (function () { throw new RuntimeError('Variable "imageSrc" does not exist.', 24, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 25
            yield "                <img class=\"profile-avatar\" src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["imageSrc"]) || array_key_exists("imageSrc", $context) ? $context["imageSrc"] : (function () { throw new RuntimeError('Variable "imageSrc" does not exist.', 25, $this->source); })()), "html", null, true);
            yield "\" alt=\"Profile image\" onerror=\"this.style.display='none'; this.nextElementSibling.style.display='grid';\">
            ";
        }
        // line 27
        yield "            <div class=\"profile-avatar-fallback\" ";
        if ((($tmp = (isset($context["imageSrc"]) || array_key_exists("imageSrc", $context) ? $context["imageSrc"] : (function () { throw new RuntimeError('Variable "imageSrc" does not exist.', 27, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "style=\"display:none\"";
        }
        yield ">";
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 27, $this->source); })()), "username", [], "any", false, false, false, 27)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), Twig\Extension\CoreExtension::first($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 27, $this->source); })()), "username", [], "any", false, false, false, 27))), "html", null, true)) : ("?"));
        yield "</div>
        </div>

        <dl class=\"details-grid\">
            <dt>Username</dt><dd>";
        // line 31
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 31, $this->source); })()), "username", [], "any", false, false, false, 31), "html", null, true);
        yield "</dd>
            <dt>Email</dt><dd>";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 32, $this->source); })()), "email", [], "any", false, false, false, 32), "html", null, true);
        yield "</dd>
            <dt>Birthday</dt><dd>";
        // line 33
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 33, $this->source); })()), "birthday", [], "any", false, false, false, 33)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 33, $this->source); })()), "birthday", [], "any", false, false, false, 33), "Y-m-d"), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Bio</dt><dd>";
        // line 34
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 34, $this->source); })()), "bio", [], "any", false, false, false, 34)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 34, $this->source); })()), "bio", [], "any", false, false, false, 34), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Profile image</dt><dd>";
        // line 35
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 35, $this->source); })()), "profileImage", [], "any", false, false, false, 35)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 35, $this->source); })()), "profileImage", [], "any", false, false, false, 35), "html", null, true)) : ("—"));
        yield "</dd>
            <dt>Role</dt><dd>";
        // line 36
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 36, $this->source); })()), "primaryRole", [], "any", false, false, false, 36), "html", null, true);
        yield "</dd>
            <dt>Balance</dt><dd>";
        // line 37
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 37, $this->source); })()), "balance", [], "any", false, false, false, 37), 2, ".", ","), "html", null, true);
        yield "</dd>
            <dt>Created</dt><dd>";
        // line 38
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 38, $this->source); })()), "createdAt", [], "any", false, false, false, 38)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 38, $this->source); })()), "createdAt", [], "any", false, false, false, 38), "Y-m-d H:i"), "html", null, true)) : ("—"));
        yield "</dd>
        </dl>
    </section>

    <section class=\"glass-card danger-zone\">
        <h2>Delete account</h2>
        <p class=\"muted\">This action is permanent and cannot be undone.</p>
        <form method=\"post\" action=\"";
        // line 45
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_profile_delete");
        yield "\" class=\"stack-form\" novalidate onsubmit=\"return confirm('Delete your account permanently?');\">
            <input type=\"hidden\" name=\"_token\" value=\"";
        // line 46
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken("delete_profile"), "html", null, true);
        yield "\">
            <label for=\"current_password\">Current password</label>
            <input type=\"password\" id=\"current_password\" name=\"current_password\" required autocomplete=\"current-password\">
            <button type=\"submit\" class=\"btn btn-danger\">Delete my account</button>
        </form>
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
        return "profile/show.html.twig";
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
        return array (  197 => 46,  193 => 45,  183 => 38,  179 => 37,  175 => 36,  171 => 35,  167 => 34,  163 => 33,  159 => 32,  155 => 31,  143 => 27,  137 => 25,  135 => 24,  127 => 19,  118 => 12,  115 => 11,  112 => 10,  109 => 9,  106 => 8,  103 => 7,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Profile - My Account{% endblock %}

{% block body %}
    {% set user = app.user %}
    {% set imagePath = user.profileImage ? user.profileImage|replace({'\\\\': '/'}) : null %}
    {% set imageSrc = null %}
    {% if imagePath %}
        {% set imageSrc = imagePath starts with 'http' ? imagePath : asset(imagePath starts with '/' ? imagePath|slice(1) : imagePath) %}
    {% endif %}

    <section class=\"glass-card panel-head\">
        <div>
            <p class=\"eyebrow\">Profile</p>
            <h1>My profile</h1>
            <p class=\"muted\">Manage your account details and credentials.</p>
        </div>
        <a class=\"btn btn-primary\" href=\"{{ path('app_profile_edit') }}\">Edit profile</a>
    </section>

    <section class=\"glass-card\">
        <div class=\"profile-avatar-wrap\">
            {% if imageSrc %}
                <img class=\"profile-avatar\" src=\"{{ imageSrc }}\" alt=\"Profile image\" onerror=\"this.style.display='none'; this.nextElementSibling.style.display='grid';\">
            {% endif %}
            <div class=\"profile-avatar-fallback\" {% if imageSrc %}style=\"display:none\"{% endif %}>{{ user.username ? user.username|first|upper : '?' }}</div>
        </div>

        <dl class=\"details-grid\">
            <dt>Username</dt><dd>{{ user.username }}</dd>
            <dt>Email</dt><dd>{{ user.email }}</dd>
            <dt>Birthday</dt><dd>{{ user.birthday ? user.birthday|date('Y-m-d') : '—' }}</dd>
            <dt>Bio</dt><dd>{{ user.bio ?: '—' }}</dd>
            <dt>Profile image</dt><dd>{{ user.profileImage ?: '—' }}</dd>
            <dt>Role</dt><dd>{{ user.primaryRole }}</dd>
            <dt>Balance</dt><dd>{{ user.balance|number_format(2, '.', ',') }}</dd>
            <dt>Created</dt><dd>{{ user.createdAt ? user.createdAt|date('Y-m-d H:i') : '—' }}</dd>
        </dl>
    </section>

    <section class=\"glass-card danger-zone\">
        <h2>Delete account</h2>
        <p class=\"muted\">This action is permanent and cannot be undone.</p>
        <form method=\"post\" action=\"{{ path('app_profile_delete') }}\" class=\"stack-form\" novalidate onsubmit=\"return confirm('Delete your account permanently?');\">
            <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete_profile') }}\">
            <label for=\"current_password\">Current password</label>
            <input type=\"password\" id=\"current_password\" name=\"current_password\" required autocomplete=\"current-password\">
            <button type=\"submit\" class=\"btn btn-danger\">Delete my account</button>
        </form>
    </section>
{% endblock %}
", "profile/show.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\profile\\show.html.twig");
    }
}
