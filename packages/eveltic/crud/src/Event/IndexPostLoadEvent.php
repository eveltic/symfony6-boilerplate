<?php

namespace Eveltic\Crud\Event;

use Symfony\Contracts\EventDispatcher\Event;

class IndexPostLoadEvent extends Event
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
    private object $crud;

    /**
     * IndexPostLoadEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param Object $crud
     */
    public function __construct(string $controller, string $method, string $bundle, Object $crud)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->crud = $crud;
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
     * @return IndexPostLoadEvent
     */
    public function setController(string $controller): IndexPostLoadEvent
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
     * @return IndexPostLoadEvent
     */
    public function setMethod(string $method): IndexPostLoadEvent
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
     * @return IndexPostLoadEvent
     */
    public function setBundle(string $bundle): IndexPostLoadEvent
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * @return Object
     */
    public function getCrud(): Object
    {
        return $this->crud;
    }

    /**
     * @param Object $crud
     * @return IndexPostLoadEvent
     */
    public function setCrud(Object $crud): IndexPostLoadEvent
    {
        $this->crud = $crud;
        return $this;
    }
}