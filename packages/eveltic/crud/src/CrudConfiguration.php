<?php

namespace Eveltic\Crud;


use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Eveltic\Crud\Exception\ConfigurationException;

final class CrudConfiguration
{
    private $configuration = [];
    private $allowedConfigurations = [
        QueryBuilder::class,
        //Fields::class,
        //Accesses::class,
        //Buttons::class,
        //Actions::class,
        //Texts::class,
        //Forms::class,
    ];

    public function __construct(object ...$configuration)
    {
        $this->setCrudControllerMetadata();

        foreach ($configuration as $key => $value) {
            $sClassName = explode('\\', get_class($value));
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
        
        $this->configuration['data']['controller']['class'] = $controllerClass;
        $this->configuration['data']['controller']['method'] = debug_backtrace()[3]['function'];;
        $this->configuration['data']['controller']['route'] = $routeArguments['name'];
        $this->configuration['data']['controller']['name'] = strtolower(str_replace('Controller', '', array_slice(explode('\\', $controllerClass), -1)[0]));
        $this->configuration['data']['controller']['bundle'] = explode('\\', $controllerClass)[1] === 'Controller' ? 'app' : strtolower(explode('\\', $controllerClass)[2]);
        $this->configuration['data']['template']['folder'] = ($this->configuration['data']['controller']['bundle'] === 'app' ? '' . rtrim(implode('/', $aControllerClassRoute), '/') : '@' . ucfirst(rtrim(implode('/', $aControllerClassRoute), '/'))) . '/';
        //$this->configuration['data']['template']['error'] = array_filter(explode('/', $this->configuration['data']['template']['folder']));array_pop($this->configuration['data']['template']['error']);$this->configuration['data']['template']['error'] = implode('/', $this->configuration['data']['template']['error']) . '/error/';
        // TODO: Elimina el ultimo elemento del array $aControllerClassRoute, o cambialo por "error" y haz el join
        $this->configuration['data']['template']['error'] = array_filter(explode('/', $this->configuration['data']['template']['folder']));array_pop($this->configuration['data']['template']['error']);$this->configuration['data']['template']['error'] = implode('/', $this->configuration['data']['template']['error']) . '/error/';
        
        

        dump($this->configuration);
        dump($controllerClass);
        exit;


        /* Read from annotations */
        $oAnnotationReader = new AnnotationReader();
        $oRouteAnnotation = $oAnnotationReader->getClassAnnotation(new \ReflectionClass($sControllerClass), 'Symfony\Component\Routing\Annotation\Route');

        if (empty($oRouteAnnotation)) throw new ConfigurationException('Cannot determine the route annotation of the controller class');
        $aRouteName = array_filter(explode('_', $oRouteAnnotation->getName()));
        /* Get processed data from controller metadata */
//        $this->configuration['route_main_name'] = $oRouteAnnotation->getName();
//        $this->configuration['controller'] = strtolower(substr(array_values(array_slice(explode('\\', $sControllerClass), -1))[0], 0, -10));
//        $this->configuration['class'] = $sControllerClass;
//        $this->configuration['method'] = debug_backtrace()[4]['function'];
//        $this->configuration['bundle'] = explode('\\', $sControllerClass)[1] === 'Controller' ? 'app' : strtolower(explode('\\', $sControllerClass)[2]);
//        $this->configuration['route_template_folder'] = ($this->configuration['bundle'] === 'app' ? '' : '@' . ucfirst($this->configuration['bundle']) . '/') . rtrim(implode('/', $aRouteName), '/') . '/';
//        $this->configuration['route_template_folder'] = ($this->configuration['bundle'] === 'app' ? '' . rtrim(implode('/', $aRouteName), '/') : '@' . ucfirst(rtrim(implode('/', $aRouteName), '/'))) . '/';
//        $this->configuration['route_template_error_folder'] = array_filter(explode('/', $this->configuration['route_template_folder']));array_pop($this->configuration['route_template_error_folder']);$this->configuration['route_template_error_folder'] = implode('/', $this->configuration['route_template_error_folder']) . '/error/';

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
