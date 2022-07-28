<?php
namespace App\Manager\Crud\Field;


/**
 * Class TimeField
 * @package App\Manager\Crud\Field
 */
class TimeField extends AbstractField
{
    /**
     * @return false|mixed|string|void
     */
    public function render()
    {
        return $this->getValue() instanceof \DateTime ? date_format($this->getValue(), 'H:i:s') : $this->getValue();
    }
}