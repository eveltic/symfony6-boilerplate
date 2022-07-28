<?php

namespace Eveltic\Crud\Configuration\Type;


use Eveltic\Crud\Exception\ActionException;

class ActionType implements TypeInterface
{
    /**
     * @var string
     */
    public $action = '';
    /**
     * @var string
     */
    private $title = '';
    /**
     * @var string|null
     */
    private $icon = '';
    /**
     * @var string|null
     */
    private $type = '';
    /**
     * @var array|null
     */
    private $route = [];
    /**
     * @var bool|null
     */
    private $translatable = true;
    /**
     * @var array|null
     */
    private $roles = [];
    /**
     * @var array
     */
    private $allowedTypes = [
        'info',
        'danger',
        'warning',
    ];

    /**
     * Action constructor.
     * @param string $action
     * @param string $title
     * @param string|null $icon
     * @param string|null $type
     * @param array|null $route
     * @param bool|null $translatable
     * @param array|null $roles
     * @throws ActionException
     */
    public function __construct(string $action, string $title, ?string $icon = null, ?string $type = 'warning', ?array $route = [], ?bool $translatable = true, ?array $roles = [])
    {
        $this->action = $action;
        $this->title = $title;
        $this->icon = $icon;
        $this->type = $this->validateType($type);
        $this->route = $this->validateRoute($route);
        $this->translatable = $translatable;
        $this->roles = $roles;
    }

    public function getIdentifier(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return self
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $this->validateType($type);
        return $this;
    }

    /**
     * @param string $type
     * @return string
     * @throws ActionException
     */
    private function validateType(string $type): string
    {
        if(!in_array($type, $this->allowedTypes)) throw new ActionException(sprintf('The type %s is not allowed, the only allowed types are: [%s]', $type, implode(',', $this->allowedTypes)));
        return $type;
    }

    /**
     * @return array|null
     */
    public function getRoute(): ?array
    {
        return $this->route;
    }

    /**
     * @param array|null $route
     * @return self
     * @throws ActionException
     */
    public function setRoute(?array $route): self
    {
        $this->route = $this->validateRoute($route);
        return $this;
    }

    /**
     * @param array $route
     * @return array
     * @throws ActionException
     */
    private function validateRoute(array $route): array
    {
        if(!isset($route['name'])) throw new ActionException('The route must declare a name');
        if((count($route) > 1 AND !isset($route['params'])) OR count($route) > 2) throw new ActionException('The route can only declare route and params keys');
        return $route;
    }

    /**
     * @return bool|null
     */
    public function getTranslatable(): ?bool
    {
        return $this->translatable;
    }

    /**
     * @param bool|null $translatable
     * @return self
     */
    public function setTranslatable(?bool $translatable): self
    {
        $this->translatable = $translatable;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param array|null $roles
     * @return self
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}
