<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Field\AbstractField;

class StringField extends AbstractField
{
    /**
     * @return mixed|void
     */
    public function render()
    {
        return $this->getValue();
    }
}