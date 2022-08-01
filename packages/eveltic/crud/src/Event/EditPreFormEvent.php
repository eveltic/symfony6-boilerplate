<?php

namespace Eveltic\Crud\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EditPreFormEvent extends Event
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
     * EditPreFormEvent constructor.
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
     * @return EditPreFormEvent
     */
    public function setController(string $controller): EditPreFormEvent
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
     * @return EditPreFormEvent
     */
    public function setMethod(string $method): EditPreFormEvent
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
     * @return EditPreFormEvent
     */
    public function setBundle(string $bundle): EditPreFormEvent
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
     * @return EditPreFormEvent
     */
    public function setConfiguration(Object $configuration): EditPreFormEvent
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
     * @return EditPreFormEvent
     */
    public function setObject(Object $object): EditPreFormEvent
    {
        $this->object = $object;
        return $this;
    }
}