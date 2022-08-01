<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Field\AbstractField;

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