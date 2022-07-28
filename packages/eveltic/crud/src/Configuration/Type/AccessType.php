<?php

namespace Eveltic\Crud\Configuration\Type;

use Eveltic\Crud\Exception\AccessException;
use Closure;

class AccessType implements TypeInterface
{
    /**
     * @var string
     */
    private $method = '';
    /**
     * @var bool|null
     */
    private $access = true;
    /**
     * @var array
     */
    private $roles = [];
    /**
     * @var string|null
     */
    private $callback;
    /**
     * @var array
     */
    private $allowedMethods = [
        'index',
        'create',
        'edit',
        'view',
        'remove',
        'export',
        'paginate',
        'search',
        'order',
        'clone',
    ];
    /**
     * @var array
     */
    private $allowedCallbackMethods = [
      'edit',
      'view',
      'remove',
      'export',
      'clone',
    ];


    /**
     * Access constructor.
     * @param string $method
     * @param bool|null $access
     * @param array|null $roles
     * @param Closure|null $callback
     * @throws AccessException
     */
    public function __construct(string $method, ?bool $access = true, ?array $roles = [], ?Closure $callback = null)
    {
        $this->method = $this->validateMethod($method);
        $this->access = $access;
        $this->roles = $roles;
        $this->callback = $this->validateCallback($callback);
    }

    public function getIdentifier(): string
    {
        return $this->method;
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
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $this->validateMethod($method);
        return $this;
    }

    /**
     * @param string $method
     * @return string
     * @throws AccessException
     */
    private function validateMethod(string $method): string
    {
        if (!in_array($method, $this->allowedMethods)) throw new AccessException(sprintf('The method %s is not allowed, the only allowed methods are: [%s]', $method, implode(',', $this->allowedMethods)));
        return $method;
    }

    /**
     * @return bool|null
     */
    public function getAccess(): ?bool
    {
        return $this->access;
    }

    /**
     * @param bool|null $access
     * @return self
     */
    public function setAccess(?bool $access): self
    {
        $this->access = $access;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Closure|string|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param Closure|null $callback
     * @return Closure|null
     * @throws AccessException
     */
    private function validateCallback(?Closure $callback): ?Closure
    {
        if (empty($this->getMethod())) throw new AccessException('You must define the access method before define the callback');
        if (!empty($callback) AND !in_array($this->getMethod(), $this->allowedCallbackMethods)) throw new AccessException(sprintf('The method %s is not allowed to have a callback method, the only allowed methods are: [%s]', $this->getMethod(), implode(',', $this->allowedCallbackMethods)));
        return $callback;
    }

    /**
     * @param Closure|null $callback
     * @return self
     * @throws AccessException
     */
    public function setCallback(?Closure $callback): self
    {
        $this->callback = $this->validateCallback($callback);
        return $this;
    }
}
