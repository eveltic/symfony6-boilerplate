<?php


namespace App\Constants;


/**
 * Class UserNoticeConstants
 * @package App\Constants
 */
class UserNoticeConstants
{
    use ConstantsTrait;

    const TYPE_SECURITY = 1;
    const TYPE_INFO = 2;
    const TYPE_DEBUG = 3;
    const TYPE_ERROR = 4;
}