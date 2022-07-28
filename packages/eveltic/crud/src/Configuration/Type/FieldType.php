<?php

namespace Eveltic\Crud\Configuration\Type;

use Eveltic\Crud\Exception\FieldException;
use Eveltic\Crud\Field\ArrayField;
use Eveltic\Crud\Field\BooleanField;
use Eveltic\Crud\Field\DateField;
use Eveltic\Crud\Field\DatetimeField;
use Eveltic\Crud\Field\FontAwesomeField;
use Eveltic\Crud\Field\IpField;
use Eveltic\Crud\Field\StringField;
use Eveltic\Crud\Field\TimeField;
use Eveltic\Crud\Field\TranslateField;
use Closure;

class FieldType implements TypeInterface
{
    /**
     * @var string
     */
    public string $field;
    /**
     * @var string|null
     */
    private Closure|string|null $type;
    /**
     * @var string|null
     */
    private ?string $title;
    /**
     * @var bool|null
     */
    private ?bool $searchable;
    /**
     * @var bool|null
     */
    private ?bool $sortable;
    /**
     * @var bool|null
     */
    private ?bool $translatable;
    /**
     * @var array|null
     */
    private ?array $roles = [];
    /**
     * @var array
     */
    private array $allowedTypes = [
        Closure::class,
        ArrayField::class,
        BooleanField::class,
        DateField::class,
        DatetimeField::class,
        StringField::class,
        TimeField::class,
        TranslateField::class,
        IpField::class,
        FontAwesomeField::class,
    ];
    /**
     * @var bool|null
     */
    private ?bool $raw;
    /**
     * @var array|null
     */
    private ?array $options;

    /**
     * Field constructor.
     * @param $field
     * @param null $type
     * @param string|null $title
     * @param bool|null $searchable
     * @param bool|null $sortable
     * @param bool|null $translatable
     * @param array|null $roles
     * @param bool|null $raw
     * @param array|null $options
     * @throws FieldException
     */
    public function __construct($field, $type = null, ?string $title = null, ?bool $searchable = true, ?bool $sortable = true, ?bool $translatable = false, ?array $roles = [], ?bool $raw = true, ?array $options = [])
    {
        $this->field = $field;
        $this->type = $this->validateType($type);
        $this->title = $title ?? $field;
        $this->searchable = $searchable;
        $this->sortable = $sortable;
        $this->translatable = $translatable;
        $this->roles = $roles;
        $this->raw = $raw;
        $this->options = $options;
    }
    
        /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->field;
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

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return self
     */
    public function setField($field): self
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return Closure|string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return self
     * @throws FieldException
     */
    public function setType($type): self
    {
        $this->type = $this->validateType($type);
        return $this;
    }

    /**
     * @param $type
     * @return Closure|string
     * @throws FieldException
     */
    private function validateType($type): Closure|string
    {
        if(!in_array((is_object($type) ? get_class($type) : ($type)), $this->allowedTypes)) throw new FieldException(sprintf('The type %s is not allowed, the only allowed types are: [%s]', $type, implode(',', $this->allowedTypes)));
        return $type;
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
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     * @return self
     */
    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * @param bool $sortable
     * @return self
     */
    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;
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
     * @return bool|null
     */
    public function getRaw(): ?bool
    {
        return $this->raw;
    }

    /**
     * @param bool|null $raw
     * @return self
     */
    public function setRaw(?bool $raw): self
    {
        $this->raw = $raw;
        return $this;
    }
}
