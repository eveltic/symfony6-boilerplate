<?php

namespace Eveltic\Crud\Event;

use Eveltic\Crud\Configuration\CrudConfiguration;
use Symfony\Contracts\EventDispatcher\Event;

class SecurityEvent extends Event
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
     * @var CrudConfiguration
     */
    private CrudConfiguration $configuration;

    /**
     * SecurityEvent constructor.
     * @param string $controller
     * @param string $method
     * @param string $bundle
     * @param CrudConfiguration $configuration
     */
    public function __construct(string $controller, string $method, string $bundle, CrudConfiguration $configuration)
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->bundle = $bundle;
        $this->configuration = $configuration;
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
     * @return SecurityEvent
     */
    public function setController(string $controller): SecurityEvent
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
     * @return SecurityEvent
     */
    public function setMethod(string $method): SecurityEvent
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
     * @return SecurityEvent
     */
    public function setBundle(string $bundle): SecurityEvent
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * @return CrudConfiguration
     */
    public function getConfiguration(): CrudConfiguration
    {
        return $this->configuration;
    }

    /**
     * @param CrudConfiguration $configuration
     * @return SecurityEvent
     */
    public function setConfiguration(CrudConfiguration $configuration): SecurityEvent
    {
        $this->configuration = $configuration;
        return $this;
    }
}