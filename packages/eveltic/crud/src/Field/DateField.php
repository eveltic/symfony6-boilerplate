<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Field\AbstractField;

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