<?php
namespace Eveltic\Crud\Field;


use Symfony\Contracts\Translation\TranslatorInterface;
use Eveltic\Crud\Field\AbstractField;

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