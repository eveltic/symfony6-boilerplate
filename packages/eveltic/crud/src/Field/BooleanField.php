<?php
namespace App\Manager\Crud\Field;


use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BooleanField
 * @package App\Manager\Crud\Field
 */
class BooleanField extends AbstractField
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * BooleanField constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string|void
     */
    public function render()
    {
        return $this->translator->trans(var_export($this->getValue(), true));
    }
}