<?php

namespace Eveltic\Crud\Configuration\Type;

class TextType implements TypeInterface
{
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var string
     */
    private $method = '';
    /**
     * @var string|null
     */
    private $title = '';
    /**
     * @var bool|null
     */
    private $translatable;

    /**
     * Text constructor.
     * @param string $name
     * @param string|null $title
     */
    public function __construct(string $name, string $method = null, ?string $title = null, ?bool $translatable = false)
    {
        $this->name = $name;
        $this->method = $method;
        $this->title = $title;
        $this->translatable = $translatable;
    }

    public function getIdentifier(): string
    {
        return sprintf('%s_%s', $this->method, $this->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
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
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
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
}