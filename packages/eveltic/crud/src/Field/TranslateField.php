<?php
namespace App\Manager\Crud\Field;


use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslateField
 * @package App\Manager\Crud\Field
 */
class TranslateField extends AbstractField
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string|void
     */
    public function render()
    {
        $value = $this->getArray($this->getValue());
        return $this->translator->trans($value['message'], (isset($value['variables']) ? $value['variables'] : []));
    }

    /**
     * @param $mixed
     * @return array|mixed
     */
    private function getArray($mixed)
    {
        if (is_array($mixed)) {
            $aResult = $mixed;
        } else if ($this->is_json($mixed)) {
            $aResult = json_decode($mixed);
        } else {
            $aResult = [];
        }

        /* Check array structure */
        if (count($aResult) != 2 OR !isset($aResult['message']) OR (isset($aResult['variables']) AND !is_array($aResult['variables']))) {
            return ['message' => '[ERROR] The value is malformed, the structure is an associative array with the keys "message" and "variables", being this last one an associative array with the translatable variables', 'variables' => []];
        } else {
            return $aResult;
        }
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
}