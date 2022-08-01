<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Field\AbstractField;

class FontAwesomeField extends AbstractField
{
    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf('<i class="%s"></i>', $this->getValue());
    }
}