<?php

namespace App\Form\Frontend;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserLoginLinkFormType
 * @package App\Form\Frontend
 */
class UserLoginLinkFormType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * UserLoginLinkFormType constructor.
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
              'required' => true,
              'mapped' => false,
              'label' => 'Email',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $oData = $event->getData();
            $oForm = $event->getForm();
            $oUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $oForm->get('email')->getData()]);
            if(empty($oUser)){
                $oForm->get('email')->addError(new FormError(sprintf($this->translator->trans('The email %s does not exists'), $oForm->get('email')->getData())));
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
