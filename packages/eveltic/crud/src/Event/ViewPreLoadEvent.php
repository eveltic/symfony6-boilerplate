<?php

namespace Eveltic\Crud\Event;

use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

class ViewPreLoadEvent extends Event
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
     * @var int|Uuid|string
     */
    private int|Uuid|string $id;

    /**
     * ViewPreLoadEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param Object $configuration
     * @param int|Uuid|string $id
     */
    public function __construct(string $controller, string $method, string $bundle, Object $configuration, int|Uuid|string $id)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->configuration = $configuration;
        $this->id = $id;
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
     * @return ViewPreLoadEvent
     */
    public function setController(string $controller): ViewPreLoadEvent
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
     * @return ViewPreLoadEvent
     */
    public function setMethod(string $method): ViewPreLoadEvent
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
     * @return ViewPreLoadEvent
     */
    public function setBundle(string $bundle): ViewPreLoadEvent
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
     * @return ViewPreLoadEvent
     */
    public function setConfiguration(Object $configuration): ViewPreLoadEvent
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return int|Uuid|string
     */
    public function getId(): int|Uuid|string
    {
        return $this->id;
    }

    /**
     * @param int|Uuid|string $id
     * @return ViewPreLoadEvent
     */
    public function setId(int|Uuid|string $id): ViewPreLoadEvent
    {
        $this->id = $id;
        return $this;
    }
}