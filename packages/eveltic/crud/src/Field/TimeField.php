<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Field\AbstractField;

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