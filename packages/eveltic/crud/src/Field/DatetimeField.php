<?php
namespace App\Manager\Crud\Field;


/**
 * Class DatetimeField
 * @package App\Manager\Crud\Field
 */
class DatetimeField extends AbstractField
{
    /**
     * @return false|mixed|string|void
     */
    public function render()
    {
        return $this->getValue() instanceof \DateTime ? date_format($this->getValue(), 'Y-m-d H:i:s') : $this->getValue();
    }
}