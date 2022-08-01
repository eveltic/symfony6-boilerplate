<?php

namespace Eveltic\Crud\Event;


class RemovePostDeleteEvent
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
     * RemovePostDeleteEvent constructor.
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
     * @return RemovePostDeleteEvent
     */
    public function setController(string $controller): RemovePostDeleteEvent
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
     * @return RemovePostDeleteEvent
     */
    public function setMethod(string $method): RemovePostDeleteEvent
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
     * @return RemovePostDeleteEvent
     */
    public function setBundle(string $bundle): RemovePostDeleteEvent
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
     * @return RemovePostDeleteEvent
     */
    public function setCrud(Object $crud): RemovePostDeleteEvent
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
     * @return RemovePostDeleteEvent
     */
    public function setObject(Object $object): RemovePostDeleteEvent
    {
        $this->object = $object;
        return $this;
    }
}