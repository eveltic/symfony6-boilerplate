<?php

namespace Eveltic\Crud\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Eveltic\Crud\Configuration\CrudConfiguration;
use Eveltic\Crud\Configuration\Group\AccessGroup;
use Eveltic\Crud\Configuration\Type\AccessType;
use Eveltic\Crud\Exception\AccessDeniedException;
use Eveltic\Crud\Exception\ControllerException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

trait CrudTrait
{
    private $configuration;

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'entity_manager' => EntityManagerInterface::class,
            'translator' => TranslatorInterface::class,
            'event_dispatcher' => EventDispatcherInterface::class,
        ]);
    }

    protected function checkCrudConfiguration()
    {
        if (!method_exists($this, 'configureCrud')) throw new ControllerException(sprintf('You must overwrite the configureCrud method in the "%s" controller in order to configure all needed crud settings.', static::class));
        $method = new \ReflectionMethod($this, 'configureCrud');
        if (empty($method->getReturnType()) or $method->getReturnType()->getName() !== CrudConfiguration::class) throw new ControllerException(sprintf('The configureCrud method in the "%s" controller must return a "%s" return type and be type hinted.', static::class, CrudConfiguration::class));
        if (!$method->isProtected()) throw new ControllerException(sprintf('The configureCrud method in the "%s" controller must be defined as protected', static::class));
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->container->get('event_dispatcher');
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get('entity_manager');
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->container->get('translator');
    }

    protected function getConfiguration(): CrudConfiguration
    {
        return $this->configuration = $this->configureCrud();
    }

    protected function getQueryBuilderMainAlias(Object $configuration): string
    {
        return $configuration->getConfiguration('querybuilder')->getRootAliases()[0];
    }

    private function checkMethodAccess(string $method, CrudConfiguration $crudConfiguration): void
    {
        if ($crudConfiguration->getConfiguration('accessgroup') instanceof AccessGroup and $crudConfiguration->getConfiguration('accessgroup')->getChilds($method) instanceof AccessType and ($crudConfiguration->getConfiguration('accessgroup')->getChilds($method)->getAccess() !== true or (!empty($crudConfiguration->getConfiguration('accessgroup')->getChilds($method)->getRoles()) and in_array(true, array_map([$this, 'isGranted'], $crudConfiguration->getConfiguration('accessgroup')->getChilds($method)->getRoles()), true) !== true))) {
            throw new AccessDeniedException($this->getTranslator()->trans('Access denied to %entity% %action%', ['%entity%' => $this->getQueryBuilderMainAlias($crudConfiguration), '%action%' => $method]));
        }
    }

    /** 
     * Allow to inject methods in the main crud controller methods
     * All methods used to be injectors must begin with __ and the
     * main method name, such as __index, __edit, __clone, ...
     * @param string $sMethod
     * @return Mixed
     * @throws ReflectionException
     */
    private function getInjectMethods(string $sMethod): mixed
    {
        $bInjectMethodExists = isset(array_flip(get_class_methods($this))['__' . $sMethod]);
        if($bInjectMethodExists === true AND (new \ReflectionMethod($this, '__' . $sMethod))->isPrivate()){
            return $this->{'__' . $sMethod}();
        }
        return null;
    }

    private function dispatchEvent($configuration, $event, $method, $object)
    {
        $oEvent = new $event($configuration->getConfiguration('bundle'), $configuration->getConfiguration('controller'), $configuration->getConfiguration('method'), $configuration, $object);
        $this->getEventDispatcher()->dispatch($oEvent, $method);
        return $object;
    }
}
