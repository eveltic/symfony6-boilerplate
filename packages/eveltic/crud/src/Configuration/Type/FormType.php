<?php
namespace Eveltic\Crud\Configuration\Type;


use Eveltic\Crud\Exception\FormException;

class FormType implements TypeInterface
{
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var string
     */
    private $class = '';
    /**
     * @var string
     */
    private $entity = '';
    /**
     * @var array
     */
    private $allowedNames = [
        'create',
        'edit',
        'view',
        'clone',
    ];

    /**
     * Form constructor.
     * @param string $name
     * @param string|null $class
     */
    public function __construct(string $name, string $class, string $entity)
    {
        $this->class = $class;
        $this->name = $this->validateName($name);
        $this->entity = $this->validateEntity($entity, $this->name);
    }

    public function getIdentifier(): string
    {
        return $this->name;
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
        $this->name = $this->validateName($name);
        return $this;
    }

    /**
     * @param string $entity
     * @param string $name
     * @return string
     * @throws FormException
     * @throws \ReflectionException
     */
    public function validateEntity(string $entity, string $name)
    {
        if($name === 'clone'){
            $oReflection = new \ReflectionClass($entity);
            if(!$oReflection->hasMethod('__clone')){
                throw new FormException(sprintf('The entity %s does not have a __clone method, you must implement it in order to allow entity cloning', $entity));
            }
        }
        return $entity;
    }

    /**
     * @param string $name
     * @return string
     * @throws FormException
     */
    private function validateName(string $name): string
    {
        if(!in_array($name, $this->allowedNames)) throw new FormException(sprintf('The name %s is not allowed, the only allowed names are: [%s]', $name, implode(',', $this->allowedNames)));
        return $name;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return self
     */
    public function setClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return self
     */
    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }
}