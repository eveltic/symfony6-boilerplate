<?php
namespace Eveltic\Crud\Field;


use Darsyn\IP\Formatter\NativeFormatter;
use Darsyn\IP\Version\Multi;
use Eveltic\Crud\Field\AbstractField;

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