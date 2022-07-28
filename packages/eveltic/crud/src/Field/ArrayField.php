<?php
namespace App\Manager\Crud\Field;


/**
 * Class ArrayField
 * @package App\Manager\Crud\Field
 */
class ArrayField extends AbstractField
{
    /**
     * @return string|void
     */
    public function render()
    {
        $value = $this->getArray($this->getValue());
        return (is_array($value) AND !$this->is_array_multidimensional($value))
                ? $this->renderSimpleArray($value)
                : $this->renderMultidimensionalArray($value);
    }

    /**
     * @param array $array
     * @return string
     */
    private function renderMultidimensionalArray(array $array): string
    {
        return $this->getArrayAsString($array);
    }

    /**
     * @param array $array
     * @return string
     */
    private function renderSimpleArray(array $array): string
    {
        return $this->is_array_associative($array) ? $this->getAssociativeArrayAsString($array) : $this->getArrayAsString($array);
    }

    /**
     * @param $callback
     * @param $array
     * @return array
     */
    private function arrayMapAssociative($callback, $array)
    {
        $aTmp = [];
        foreach ($array as $key => $value){
            $aTmp[$key] = $callback($key, $value);
        }
        return $aTmp;
    }

    /**
     * @param $mixed
     * @return array|mixed|void
     */
    private function getArray($mixed)
    {
        if(is_array($mixed)) return $mixed;
        if($this->is_json($mixed)) return json_decode($mixed);
        if(is_object($mixed)) return (array) $mixed;
        return var_dump($mixed);
    }

    /**
     * @param $array
     * @return string
     */
    private function getAssociativeArrayAsString($array)
    {
        return implode(', ',$this->arrayMapAssociative(function($k, $v){return "$k [$v]";},$array));
    }

    /**
     * @param $array
     * @return string
     */
    private function getArrayAsString($array)
    {
        $sReturn = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sReturn .= '[' . $this->{__FUNCTION__}($value) . '], ';
            } else {
                $sReturn .= $value . ', ';
            }
        }
        return rtrim($sReturn, ', ');
    }

    /**
     * @param array $array
     * @return bool
     */
    private function is_array_associative(array $array)
    {
        if ([] === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @param $string
     * @return bool
     */
    private function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $array
     * @return bool
     */
    private function is_array_multidimensional($array)
    {
        foreach ($array as $vector) if (is_array($vector)) return TRUE;
        return FALSE;
    }
}