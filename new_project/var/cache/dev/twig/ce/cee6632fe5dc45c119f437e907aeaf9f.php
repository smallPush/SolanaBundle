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

/* solana_contract/show.html.twig */
class __TwigTemplate_59cd294a5784c2a3290cefb1819a4e74 extends Template
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
            'javascripts' => [$this, 'block_javascripts'],
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
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "solana_contract/show.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 3, $this->source); })()), "title", [], "any", false, false, false, 3), "html", null, true);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 6
        yield "    <h1>";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 6, $this->source); })()), "title", [], "any", false, false, false, 6), "html", null, true);
        yield "</h1>

    ";
        // line 9
        yield "    ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 9, $this->source); })()), "flashes", [], "any", false, false, false, 9));
        foreach ($context['_seq'] as $context["label"] => $context["messages"]) {
            // line 10
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable($context["messages"]);
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 11
                yield "            <div class=\"alert alert-";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["label"], "html", null, true);
                yield "\">
                ";
                // line 12
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                yield "
            </div>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 15
            yield "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['label'], $context['messages'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        yield "
    <dl class=\"row\">
        <dt class=\"col-sm-3\">Descripción</dt>
        <dd class=\"col-sm-9\">";
        // line 19
        yield Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 19, $this->source); })()), "description", [], "any", false, false, false, 19), "html", null, true));
        yield "</dd>

        <dt class=\"col-sm-3\">Estado</dt>
        <dd class=\"col-sm-9\">";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 22, $this->source); })()), "status", [], "any", false, false, false, 22), "html", null, true);
        yield "</dd>

        <dt class=\"col-sm-3\">Monto</dt>
        <dd class=\"col-sm-9\">";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 25, $this->source); })()), "amount", [], "any", false, false, false, 25), "html", null, true);
        yield " SOL</dd>

        <dt class=\"col-sm-3\">Donante</dt>
        <dd class=\"col-sm-9\">";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 28, $this->source); })()), "donor", [], "any", false, false, false, 28), "email", [], "any", false, false, false, 28), "html", null, true);
        yield " (";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 28, $this->source); })()), "donorWallet", [], "any", false, false, false, 28), "html", null, true);
        yield ")</dd>

        <dt class=\"col-sm-3\">Voluntario</dt>
        <dd class=\"col-sm-9\">";
        // line 31
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 31, $this->source); })()), "volunteer", [], "any", false, false, false, 31), "email", [], "any", false, false, false, 31), "html", null, true);
        yield " (";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 31, $this->source); })()), "volunteerWallet", [], "any", false, false, false, 31), "html", null, true);
        yield ")</dd>
    </dl>

    ";
        // line 35
        yield "    <div class=\"mt-4\">
        ";
        // line 37
        yield "        ";
        if (((($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_DONOR") && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 37, $this->source); })()), "user", [], "any", false, false, false, 37) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 37, $this->source); })()), "donor", [], "any", false, false, false, 37))) && CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 37, $this->source); })()), "status", [], "any", false, false, false, 37), ["pending", "validated_volunteer"])) || (($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_VOLUNTEER") && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 37, $this->source); })()), "user", [], "any", false, false, false, 37) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 37, $this->source); })()), "volunteer", [], "any", false, false, false, 37))) && CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 37, $this->source); })()), "status", [], "any", false, false, false, 37), ["pending", "validated_donor"])))) {
            // line 38
            yield "            <form method=\"post\" action=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_solana_contract_validate", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 38, $this->source); })()), "id", [], "any", false, false, false, 38)]), "html", null, true);
            yield "\" onsubmit=\"return confirm('¿Estás seguro de que quieres validar este contrato?');\">
                <button class=\"btn btn-warning\">Validar Contrato</button>
            </form>
        ";
        }
        // line 42
        yield "
        ";
        // line 44
        yield "        ";
        if ((($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_DONOR") && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 44, $this->source); })()), "user", [], "any", false, false, false, 44) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 44, $this->source); })()), "donor", [], "any", false, false, false, 44))) && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 44, $this->source); })()), "status", [], "any", false, false, false, 44) == "ready_for_signature"))) {
            // line 45
            yield "             <button id=\"solana-sign-button\"
                     class=\"btn btn-success\"
                     data-to=\"";
            // line 47
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 47, $this->source); })()), "volunteerWallet", [], "any", false, false, false, 47), "html", null, true);
            yield "\"
                     data-amount=\"";
            // line 48
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 48, $this->source); })()), "amount", [], "any", false, false, false, 48), "html", null, true);
            yield "\"
                     data-node-id=\"";
            // line 49
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["contract"]) || array_key_exists("contract", $context) ? $context["contract"] : (function () { throw new RuntimeError('Variable "contract" does not exist.', 49, $this->source); })()), "id", [], "any", false, false, false, 49), "html", null, true);
            yield "\">
                 Firmar y Ejecutar Transacción con Wallet
             </button>
        ";
        }
        // line 53
        yield "    </div>

    <a href=\"";
        // line 55
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_solana_contract_index");
        yield "\" class=\"btn btn-link mt-3\">Volver a la lista</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 58
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 59
        yield "    ";
        yield from $this->yieldParentBlock("javascripts", $context, $blocks);
        yield "
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "solana_contract/show.html.twig";
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
        return array (  224 => 59,  214 => 58,  204 => 55,  200 => 53,  193 => 49,  189 => 48,  185 => 47,  181 => 45,  178 => 44,  175 => 42,  167 => 38,  164 => 37,  161 => 35,  153 => 31,  145 => 28,  139 => 25,  133 => 22,  127 => 19,  122 => 16,  116 => 15,  107 => 12,  102 => 11,  97 => 10,  92 => 9,  86 => 6,  76 => 5,  59 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ contract.title }}{% endblock %}

{% block body %}
    <h1>{{ contract.title }}</h1>

    {# Flash Messages #}
    {% for label, messages in app.flashes %}
        {% for message in messages %}
            <div class=\"alert alert-{{ label }}\">
                {{ message }}
            </div>
        {% endfor %}
    {% endfor %}

    <dl class=\"row\">
        <dt class=\"col-sm-3\">Descripción</dt>
        <dd class=\"col-sm-9\">{{ contract.description|nl2br }}</dd>

        <dt class=\"col-sm-3\">Estado</dt>
        <dd class=\"col-sm-9\">{{ contract.status }}</dd>

        <dt class=\"col-sm-3\">Monto</dt>
        <dd class=\"col-sm-9\">{{ contract.amount }} SOL</dd>

        <dt class=\"col-sm-3\">Donante</dt>
        <dd class=\"col-sm-9\">{{ contract.donor.email }} ({{ contract.donorWallet }})</dd>

        <dt class=\"col-sm-3\">Voluntario</dt>
        <dd class=\"col-sm-9\">{{ contract.volunteer.email }} ({{ contract.volunteerWallet }})</dd>
    </dl>

    {# --- Botones de Acción --- #}
    <div class=\"mt-4\">
        {# Botón de Validación #}
        {% if (is_granted('ROLE_DONOR') and app.user == contract.donor and contract.status in ['pending', 'validated_volunteer']) or (is_granted('ROLE_VOLUNTEER') and app.user == contract.volunteer and contract.status in ['pending', 'validated_donor']) %}
            <form method=\"post\" action=\"{{ path('app_solana_contract_validate', {'id': contract.id}) }}\" onsubmit=\"return confirm('¿Estás seguro de que quieres validar este contrato?');\">
                <button class=\"btn btn-warning\">Validar Contrato</button>
            </form>
        {% endif %}

        {# Botón para Firmar (se mostrará con JS) #}
        {% if is_granted('ROLE_DONOR') and app.user == contract.donor and contract.status == 'ready_for_signature' %}
             <button id=\"solana-sign-button\"
                     class=\"btn btn-success\"
                     data-to=\"{{ contract.volunteerWallet }}\"
                     data-amount=\"{{ contract.amount }}\"
                     data-node-id=\"{{ contract.id }}\">
                 Firmar y Ejecutar Transacción con Wallet
             </button>
        {% endif %}
    </div>

    <a href=\"{{ path('app_solana_contract_index') }}\" class=\"btn btn-link mt-3\">Volver a la lista</a>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {# Webpack Encore will automatically include app.js here thanks to the base template #}
{% endblock %}
", "solana_contract/show.html.twig", "/home/ruben/repositories/SolanaBundle/new_project/templates/solana_contract/show.html.twig");
    }
}
