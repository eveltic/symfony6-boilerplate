<?php
namespace App\Manager\Crud\Field;


/**
 * Class DateField
 * @package App\Manager\Crud\Field
 */
class DateField extends AbstractField
{
    /**
     * @return false|mixed|string
     */
    public function render()
    {
        return $this->getValue() instanceof \DateTime ? date_format($this->getValue(), 'Y-m-d') : $this->getValue();
    }
}