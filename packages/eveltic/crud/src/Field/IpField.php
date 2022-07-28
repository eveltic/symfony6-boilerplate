<?php
namespace App\Manager\Crud\Field;

use Darsyn\IP\Formatter\NativeFormatter;
use Darsyn\IP\Version\Multi;

/**
 * Class IpField
 * @package App\Manager\Crud\Field
 */
class IpField extends AbstractField
{
    /**
     *
     */
    public function render()
    {
        return $this->getValue()->getProtocolAppropriateAddress();
    }

    /**
     * @param $value
     * @return string
     */
    public function searchTransformer($value)
    {
        try {
            $ip = Multi::factory($value);
            return $ip->getBinary();
        } catch (\Exception $exception) {
            return '';
        }
    }
}