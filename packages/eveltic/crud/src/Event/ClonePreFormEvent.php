<?php

namespace Eveltic\Crud\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ClonePreFormEvent extends Event
{
    /**
     * @var string
     */
    private string $controller = '';
    /**
     * @var string
     */
    private string $method = '';
    /**
     * @var string
     */
    private string $bundle = '';
    /**
     * @var Object
     */
    private object $configuration;
    /**
     * @var Object
     */
    private object $object;

    /**
     * ClonePreFormEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param Object $configuration
     * @param Object $object
     */
    public function __construct(string $controller, string $method, string $bundle, Object $configuration, Object $object)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->configuration = $configuration;
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return ClonePreFormEvent
     */
    public function setController(string $controller): ClonePreFormEvent
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return ClonePreFormEvent
     */
    public function setMethod(string $method): ClonePreFormEvent
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getBundle(): string
    {
        return $this->bundle;
    }

    /**
     * @param string $bundle
     * @return ClonePreFormEvent
     */
    public function setBundle(string $bundle): ClonePreFormEvent
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * @return Object
     */
    public function getConfiguration(): Object
    {
        return $this->configuration;
    }

    /**
     * @param Object $configuration
     * @return ClonePreFormEvent
     */
    public function setConfiguration(Object $configuration): ClonePreFormEvent
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return Object
     */
    public function getObject(): Object
    {
        return $this->object;
    }

    /**
     * @param Object $object
     * @return ClonePreFormEvent
     */
    public function setObject(Object $object): ClonePreFormEvent
    {
        $this->object = $object;
        return $this;
    }
}