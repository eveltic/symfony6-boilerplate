<?php

namespace Eveltic\Crud\Event;

use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

class EditPreLoadEvent extends Event
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
     * EditPreLoadEvent constructor.
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
     * @return EditPreLoadEvent
     */
    public function setController(string $controller): EditPreLoadEvent
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
     * @return EditPreLoadEvent
     */
    public function setMethod(string $method): EditPreLoadEvent
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
     * @return EditPreLoadEvent
     */
    public function setBundle(string $bundle): EditPreLoadEvent
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
     * @return EditPreLoadEvent
     */
    public function setConfiguration(Object $configuration): EditPreLoadEvent
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
     * @return EditPreLoadEvent
     */
    public function setId(int|Uuid|string $id): EditPreLoadEvent
    {
        $this->id = $id;
        return $this;
    }
}