<?php

namespace Eveltic\Crud;


use Doctrine\ORM\QueryBuilder;
use Eveltic\Crud\Configuration\Group\AccessGroup;
use Eveltic\Crud\Configuration\Group\ActionGroup;
use Eveltic\Crud\Configuration\Group\ButtonGroup;
use Eveltic\Crud\Configuration\Group\FieldGroup;
use Eveltic\Crud\Configuration\Group\FormGroup;
use Eveltic\Crud\Configuration\Group\TextGroup;
use Eveltic\Crud\Exception\ConfigurationException;

final class CrudFactory
{
    private $configuration = [];
    private $allowedConfigurations = [
        QueryBuilder::class,
        FieldGroup::class,
        AccessGroup::class,
        ButtonGroup::class,
        ActionGroup::class,
        TextGroup::class,
        FormGroup::class,
    ];

    public function __construct(object ...$configuration)
    {
        

        $this->setCrudControllerMetadata();

        foreach ($configuration as $key => $value) {
            $sClassName = explode('\\', $value::class);
            $sClassName = strtolower(end($sClassName));
            $this->setConfiguration($sClassName, $value);
        }
    }

/**
     * @return CrudFactory
     * @throws ConfigurationException
     * @throws ReflectionException
     *
     * This method get the metadata from the controller and create
     * all the needed variables used to create the crud.
     * It's important to notice the details about the naming conventions
     * in the route names.
     * As example, app_backend_test_ means that the controller resides in the
     * src\Controller\Backend folder and it's called TestController.
     * For those controllers that live inside the bundles, the route must
     * match this schema bundlename_controllerfolder_controllername.
     *
     */
    private function setCrudControllerMetadata(): self
    {
        /* Check calling class */
        if (!isset(debug_backtrace()[2])) throw new ConfigurationException('Unable to determine source of controller calling class.');
        /* Get controller class and its data */
        $controllerClass = debug_backtrace()[2]['class'] ?? null;
        if (empty($controllerClass)) throw new ConfigurationException('Cannot determine controller class.');
        $routeArguments = !empty($routeArguments = array_filter((new \ReflectionClass($controllerClass))->getAttributes(),function ($object) {return $object->getName() == 'Symfony\Component\Routing\Annotation\Route';})) ? $routeArguments[0]->getArguments() : null;
        if (empty($routeArguments)) throw new ConfigurationException('Cannot determine the route arguments of the controller class. It must Annotation route.');
        $aControllerClassRoute = array_filter(explode('_', $routeArguments['name']));
        /* Get processed data from controller metadata */
        $this->configuration['class'] = $controllerClass;
        $this->configuration['method'] = debug_backtrace()[3]['function'];
        $this->configuration['route'] = $routeArguments['name'];
        $this->configuration['name'] = strtolower(str_replace('Controller', '', array_slice(explode('\\', $controllerClass), -1)[0]));
        $this->configuration['bundle'] = explode('\\', $controllerClass)[1] === 'Controller' ? 'app' : strtolower(explode('\\', $controllerClass)[2]);
        $this->configuration['template_folder'] = ($this->configuration['bundle'] === 'app' ? '' . rtrim(implode('/', $aControllerClassRoute), '/') : '@' . ucfirst(rtrim(implode('/', $aControllerClassRoute), '/'))) . '/';
        $this->configuration['template_error'] = array_filter(explode('/', $this->configuration['template_folder']));array_pop($this->configuration['template_error']);$this->configuration['template_error'] = implode('/', $this->configuration['template_error']) . '/error/';
        
        return $this;
    }

    public function getConfiguration(?string $name = null)
    {
        return isset($this->configuration[$name]) ? $this->configuration[$name] : $this->configuration;
    }

    public function setConfiguration(string $type, $configuration): self
    {
        $this->configuration[$type] = $this->validateConfiguration($configuration);
        return $this;
    }

    private function validateConfiguration($configuration)
    {
        if (!in_array(get_class($configuration), $this->allowedConfigurations)) throw new ConfigurationException(sprintf('The configuration type %s is not allowed, the only allowed configuration types are: [%s]', get_class($configuration), implode(',', $this->allowedConfigurations)));
        return $configuration;
    }
}
