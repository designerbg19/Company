<?php

namespace App\Form;

use App\Entity\ProfessionalAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessionalAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility')
            ->add('firstname')
            ->add('lastname')
            ->add('emailPro')
            ->add('birthDate')
            ->add('country')
            ->add('address')
            ->add('webSite')
            ->add('phone')
            ->add('codePostal')
            ->add('mediaManager')
            ->add('tva')
            ->add('siren')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => ProfessionalAccount::class,
        ));
    }
}
