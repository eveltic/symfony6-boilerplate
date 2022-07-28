<?php
namespace App\Manager\Crud\Field;

/**
 * Class StringField
 * @package App\Manager\Crud\Field
 */
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