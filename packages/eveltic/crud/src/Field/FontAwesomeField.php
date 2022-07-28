<?php
namespace App\Manager\Crud\Field;

/**
 * Class FontAwesomeField
 * @package App\Manager\Crud\Field
 */
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