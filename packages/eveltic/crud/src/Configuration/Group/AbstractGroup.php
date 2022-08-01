<?php

namespace Eveltic\Crud\Configuration\Group;

use Eveltic\Crud\Configuration\Type\TypeInterface;
use Eveltic\Crud\Exception\ConfigurationException;

abstract class AbstractGroup implements GroupInterface
{
    private $childs;

    public function __construct(object ...$childs)
    {
        if(empty(static::TYPE_CLASS)) throw new ConfigurationException(sprintf('You must define the constant TYPE_CLASS in "%s" class with the name of the child type class', static::class));
        $this->setChilds($childs);
    }

    public function getChilds(?string $key = null): mixed
    {
        return isset($this->childs[$key]) ? $this->childs[$key] : $this->childs;
    }

    public function setChilds(array $childs): self
    {
        $aChilds = [];
        $typeClassFromGroupClass = static::TYPE_CLASS;
        foreach($childs as $key => $typeClass){
            // Check that type classes implements the TypeInterface
            if(!in_array(TypeInterface::class, class_implements($typeClass))) throw new ConfigurationException(sprintf('The supplied class %s must implement %s interface', $typeClass::class, TypeInterface::class));            
            // Check that type classes passed to group class are those allowed by the TYPE_CLASS constant defined in the group class
            if($typeClass::class !== $typeClassFromGroupClass) throw new ConfigurationException(sprintf('The type class passed to %s must be %s class type', static::class, $typeClassFromGroupClass));
            // Check for duplicated type classes inside group class
            if(isset($aChilds[$typeClass->getIdentifier()])) throw new ConfigurationException(sprintf('The method %s is duplicated, please check your crud configuration', $typeClass->getIdentifier()));

            $aChilds[$typeClass->getIdentifier()] = $typeClass;
        }
        $this->childs = $aChilds;
        return $this;
    }
}
