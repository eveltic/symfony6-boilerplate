<?php

namespace Eveltic\Crud\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TranslationTrait
{
    #[ORM\Column]
    private array $_translations = [];


    public function get_Translations(?string $locale = null, ?string $key = null): null|array
    {
        if(empty($locale) AND empty($key)){
            return $this->_translations;
        } else {
            if(empty($key) AND !empty($locale)){
                return isset($this->_translations[$locale]) ? $this->_translations[$locale] : [];
            } else if(!empty($key) AND !empty($locale)){
                return isset($this->_translations[$locale][$key]) ? $this->_translations[$locale][$key] : null;
            } else if(!empty($key) AND empty($locale)){
                $aReturn = [];
                foreach($this->_translations as $locale => $aValues){
                    foreach($aValues as $sKey => $sValue){
                        if($key === $sKey){
                            $aReturn[$locale] = $sValue;
                        }
                    }
                }
                return $aReturn;
            } else {
                return null;
            }
        }
    }

    public function set_Translations(array $_translations): self
    {
        $this->_translations = $_translations;

        return $this;
    }
}
