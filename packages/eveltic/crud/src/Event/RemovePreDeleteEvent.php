<?php

namespace Eveltic\Crud\Event;


class RemovePreDeleteEvent
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
     * @var Object
     */
    private object $object;

    /**
     * RemovePreDeleteEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param Object $crud
     * @param Object $object
     */
    public function __construct(string $controller, string $method, string $bundle, Object $crud, Object $object)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->crud = $crud;
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
     * @return RemovePreDeleteEvent
     */
    public function setController(string $controller): RemovePreDeleteEvent
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
     * @return RemovePreDeleteEvent
     */
    public function setMethod(string $method): RemovePreDeleteEvent
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
     * @return RemovePreDeleteEvent
     */
    public function setBundle(string $bundle): RemovePreDeleteEvent
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
     * @return RemovePreDeleteEvent
     */
    public function setCrud(Object $crud): RemovePreDeleteEvent
    {
        $this->crud = $crud;
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
     * @return RemovePreDeleteEvent
     */
    public function setObject(Object $object): RemovePreDeleteEvent
    {
        $this->object = $object;
        return $this;
    }
}