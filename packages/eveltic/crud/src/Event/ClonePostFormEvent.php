<?php

namespace Eveltic\Crud\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ClonePostFormEvent extends Event
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
     * @var Object
     */
    private object $form;

    /**
     * ClonePostFormEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param Object $configuration
     * @param Object $object
     * @param Object $form
     */
    public function __construct(string $controller, string $method, string $bundle, Object $configuration, Object $object, Object $form)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->configuration = $configuration;
        $this->object = $object;
        $this->form = $form;
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
     * @return ClonePostFormEvent
     */
    public function setController(string $controller): ClonePostFormEvent
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
     * @return ClonePostFormEvent
     */
    public function setMethod(string $method): ClonePostFormEvent
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
     * @return ClonePostFormEvent
     */
    public function setBundle(string $bundle): ClonePostFormEvent
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
     * @return ClonePostFormEvent
     */
    public function setConfiguration(Object $configuration): ClonePostFormEvent
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
     * @return ClonePostFormEvent
     */
    public function setObject(Object $object): ClonePostFormEvent
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return Object
     */
    public function getForm(): Object
    {
        return $this->form;
    }

    /**
     * @param Object $form
     * @return ClonePostFormEvent
     */
    public function setForm(Object $form): ClonePostFormEvent
    {
        $this->form = $form;
        return $this;
    }
}