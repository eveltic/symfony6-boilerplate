<?php


namespace App\Security;


/**
 * Class Constants
 * @package App\Security
 */
class Constants
{
    /**
     * User active
     */
    const USER_STATUS_ACTIVE = 1;
    /**
     * User inactive
     */
    const USER_STATUS_INACTIVE = 0;
    /**
     * User banned
     */
    const USER_STATUS_BANNED = -1;

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