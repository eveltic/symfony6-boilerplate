<?php
namespace Eveltic\Crud\Field;


use Eveltic\Crud\Exception\FieldException;

/**
 * Class AbstractField
 * @package App\Manager\Crud\Field
 */
class AbstractField
{
    /**
     * @var
     */
    protected $value;

    /**
     * @var
     */
    protected $row;

    /**
     * @throws FieldException
     */
    public function render()
    {
        throw new FieldException(sprintf('You must extend the render method in the %s class in order to use the field filter class', get_class($this)));
    }

    /**
     * @return mixed
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param mixed $row
     * @return AbstractField
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return AbstractField
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}