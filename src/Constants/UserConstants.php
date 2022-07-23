<?php


namespace App\Constants;


/**
 * Class UserConstans
 * @package App\Constants
 */
class UserConstants
{
    use ConstantsTrait;
    
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


}