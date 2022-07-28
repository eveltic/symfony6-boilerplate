<?php
namespace Eveltic\Crud\Configuration\Type;


use Eveltic\Crud\Exception\ButtonException;

class ButtonType implements TypeInterface
{
    /**
     * @var string
     */
    private string $button = '';
    /**
     * @var string
     */
    private string $title = '';
    /**
     * @var string|null
     */
    private ?string $icon = '';
    /**
     * @var null|string|array
     */
    private null|string|array $route = null;
    /**
     * @var bool|null
     */
    private ?bool $translatable = true;
    /**
     * @var array|null
     */
    private ?array $roles = [];
    /**
     * @var array|null
     */
    private ?array $options = [];

    /**
     * Button constructor.
     * @param string $button
     * @param string $title
     * @param string|null $icon
     * @param null|string|array $route
     * @param bool|null $translatable
     * @param array|null $roles
     * @param array|null $options
     * @throws ButtonException
     */
    public function __construct(string $button, string $title, ?string $icon = null, null|string|array $route = null, ?bool $translatable = null, ?array $roles = [], ?array $options = [])
    {
        $this->button = $button;
        $this->title = $title;
        $this->icon = $icon;
        $this->route = $this->validateRoute($route);
        $this->translatable = $translatable;
        $this->roles = $roles;
        $this->options = $options;
    }

    public function getIdentifier(): string
    {
        return $this->button;
    }

    /**
     * @return string
     */
    public function getButton(): string
    {
        return $this->button;
    }

    /**
     * @param string $button
     * @return self
     */
    public function setButton(string $button): self
    {
        $this->button = $button;
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
     * @return null|string|array
     */
    public function getRoute(): null|string|array
    {
        return $this->route;
    }

    /**
     * @param null|string|array $route
     * @return self
     * @throws ButtonException
     */
    public function setRoute(null|string|array $route): self
    {
        $this->route = $this->validateRoute($route);
        return $this;
    }

    /**
     * @param null|array|string $route
     * @return array|string
     * @throws ButtonException
     */
    private function validateRoute(null|array|string $route): array|string
    {
        if(empty($route)) throw new ButtonException('The route must declared');

        if(is_array($route)){
            if(!isset($route['name'])) throw new ButtonException('The route must declare a name');
            if((count($route) > 1 AND !isset($route['params'])) OR count($route) > 2) throw new ButtonException('The route can only declare route and params keys');
        } else {
            if(!filter_var($route, FILTER_VALIDATE_URL)) throw new ButtonException('The url must be valid');
        }
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

    /**
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     * @return self
     */
    public function setOptions(?array $options): self
    {
        $this->options = $options;
        return $this;
    }
}