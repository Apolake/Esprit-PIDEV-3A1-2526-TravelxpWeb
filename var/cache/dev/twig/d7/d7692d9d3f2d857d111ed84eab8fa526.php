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

/* admin/_operations.html.twig */
class __TwigTemplate_aaac176d85cb8a9d3e3386038ef2f1ad extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/_operations.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/_operations.html.twig"));

        // line 1
        yield "<section class=\"glass-card admin-hub\">
    <div class=\"admin-hub-head\">
        <p class=\"eyebrow\">Admin Menu</p>
        <h2>Operations</h2>
    </div>
    <div class=\"admin-ops-grid\">
        <a class=\"action-card admin-op-card ";
        // line 7
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 7, $this->source); })()) == "users")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_user_index");
        yield "\">
            <p class=\"eyebrow\">Users</p>
            <h3>User CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 11
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 11, $this->source); })()) == "gamification")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_gamification_index");
        yield "\">
            <p class=\"eyebrow\">Gamification</p>
            <h3>Quest & stats CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 15
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 15, $this->source); })()) == "properties")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_property_index");
        yield "\">
            <p class=\"eyebrow\">Properties</p>
            <h3>Property CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 19
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 19, $this->source); })()) == "offers")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_offer_index");
        yield "\">
            <p class=\"eyebrow\">Offers</p>
            <h3>Offer CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 23
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 23, $this->source); })()) == "services")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_service_index");
        yield "\">
            <p class=\"eyebrow\">Services</p>
            <h3>Service CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 27
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 27, $this->source); })()) == "bookings")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_booking_index");
        yield "\">
            <p class=\"eyebrow\">Bookings</p>
            <h3>Booking CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 31
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 31, $this->source); })()) == "trips")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_trip_index");
        yield "\">
            <p class=\"eyebrow\">Trips</p>
            <h3>Trip CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card ";
        // line 35
        yield ((((isset($context["active"]) || array_key_exists("active", $context) ? $context["active"] : (function () { throw new RuntimeError('Variable "active" does not exist.', 35, $this->source); })()) == "activities")) ? ("active") : (""));
        yield "\" href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_activity_index");
        yield "\">
            <p class=\"eyebrow\">Activities</p>
            <h3>Activity CRUD</h3>
        </a>
    </div>
</section>
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
        return "admin/_operations.html.twig";
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
        return array (  119 => 35,  110 => 31,  101 => 27,  92 => 23,  83 => 19,  74 => 15,  65 => 11,  56 => 7,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<section class=\"glass-card admin-hub\">
    <div class=\"admin-hub-head\">
        <p class=\"eyebrow\">Admin Menu</p>
        <h2>Operations</h2>
    </div>
    <div class=\"admin-ops-grid\">
        <a class=\"action-card admin-op-card {{ active == 'users' ? 'active' : '' }}\" href=\"{{ path('app_user_index') }}\">
            <p class=\"eyebrow\">Users</p>
            <h3>User CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'gamification' ? 'active' : '' }}\" href=\"{{ path('app_admin_gamification_index') }}\">
            <p class=\"eyebrow\">Gamification</p>
            <h3>Quest & stats CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'properties' ? 'active' : '' }}\" href=\"{{ path('admin_property_index') }}\">
            <p class=\"eyebrow\">Properties</p>
            <h3>Property CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'offers' ? 'active' : '' }}\" href=\"{{ path('admin_offer_index') }}\">
            <p class=\"eyebrow\">Offers</p>
            <h3>Offer CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'services' ? 'active' : '' }}\" href=\"{{ path('admin_service_index') }}\">
            <p class=\"eyebrow\">Services</p>
            <h3>Service CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'bookings' ? 'active' : '' }}\" href=\"{{ path('admin_booking_index') }}\">
            <p class=\"eyebrow\">Bookings</p>
            <h3>Booking CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'trips' ? 'active' : '' }}\" href=\"{{ path('admin_trip_index') }}\">
            <p class=\"eyebrow\">Trips</p>
            <h3>Trip CRUD</h3>
        </a>
        <a class=\"action-card admin-op-card {{ active == 'activities' ? 'active' : '' }}\" href=\"{{ path('admin_activity_index') }}\">
            <p class=\"eyebrow\">Activities</p>
            <h3>Activity CRUD</h3>
        </a>
    </div>
</section>
", "admin/_operations.html.twig", "C:\\Users\\nafti\\Downloads\\symfony\\Esprit-PIDEV-3A1-2526-TravelxpWeb\\templates\\admin\\_operations.html.twig");
    }
}
