<?php

namespace Eveltic\Crud\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use ReflectionClass;
use ReflectionException;

trait DeepCloneTrait
{
 /**
     * @throws ReflectionException
     * Known limitations: entities must use the annotation mapping in order to work properly
     */
    public function __clone()
    {
        /* Get primary keys from the old object */
        $aPrimaryKeys = [];
        $aProperties = (new ReflectionClass($this))->getProperties();
        foreach($aProperties as $oProperty){
            $checkPosition = stripos($oProperty->getDocComment(), '\Id');
            $bIsPrimaryKey = is_int($checkPosition) AND $checkPosition !== FALSE;
            if($bIsPrimaryKey){
                $aPrimaryKeys[] = $oProperty;
            }
        }
        /* Clear all primary keys value */
        foreach ($aPrimaryKeys as $oPrimaryKey){
            $this->{$oPrimaryKey->getName()} = null;
        }
        /* Copy all old object vars to the new one */
        foreach (get_object_vars($this) as $name => $property) {
            if ((($property instanceof ArrayCollection) or ($property instanceof PersistentCollection))) {
                $this->$name = clone $property;
            }
        }
    }
}
