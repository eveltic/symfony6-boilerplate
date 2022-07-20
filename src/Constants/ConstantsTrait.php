<?php

namespace App\Constants;

trait ConstantsTrait
{
    /**
     * @param $value
     * @return string|null
     */
    public static function getConstant($value): string|null
    {
        $aConstants = array_flip((new \ReflectionClass(__CLASS__))->getConstants());
        return isset($aConstants[$value]) ? $aConstants[$value] : null;
    }

    /**
     * @return array
     */
    public static function getConstants(): array
    {
        return (new \ReflectionClass(__CLASS__))->getConstants();
    }
}
