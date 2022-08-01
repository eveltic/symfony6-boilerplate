<?php

namespace Eveltic\Crud\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ViewPostFormEvent extends Event
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
     * ViewPostFormEvent constructor.
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
     * @return ViewPostFormEvent
     */
    public function setController(string $controller): ViewPostFormEvent
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
     * @return ViewPostFormEvent
     */
    public function setMethod(string $method): ViewPostFormEvent
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
     * @return ViewPostFormEvent
     */
    public function setBundle(string $bundle): ViewPostFormEvent
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
     * @return ViewPostFormEvent
     */
    public function setConfiguration(Object $configuration): ViewPostFormEvent
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
     * @return ViewPostFormEvent
     */
    public function setObject(Object $object): ViewPostFormEvent
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
     * @return ViewPostFormEvent
     */
    public function setForm(Object $form): ViewPostFormEvent
    {
        $this->form = $form;
        return $this;
    }
}